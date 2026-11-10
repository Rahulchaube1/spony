<?php

namespace App\Http\Controllers;

use App\Exceptions\OauthLoginException;
use App\Models\OauthSetting;
use App\Services\Auth\OauthLoginService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OauthController extends Controller
{
    public function redirect(string $provider)
    {
        $oauthSetting = OauthSetting::where('provider', $provider)->first();
        if (! $oauthSetting) {
            abort(404);
        }

        $clientId = $oauthSetting->client_id ?: config("services.{$provider}.client_id");
        $clientSecret = $oauthSetting->client_secret ?: config("services.{$provider}.client_secret");
        $baseUrl = $oauthSetting->base_url ?: config("services.{$provider}.base_url");

        if (blank($clientId) || blank($clientSecret) || ($provider === 'auth0' && blank($baseUrl))) {
            return redirect()->route('login')->with('error', 'Real '.ucfirst($provider).' OAuth is enabled. To connect to your application, please provide '.strtoupper($provider).'_CLIENT_ID and '.strtoupper($provider).'_CLIENT_SECRET in your environment or configure them under Settings -> OAuth.');
        }

        $socialiteProvider = get_socialite_provider($oauthSetting->provider);

        if (env('VERCEL') || isset($_SERVER['VERCEL']) || config('session.driver') === 'cookie') {
            $socialiteProvider = $socialiteProvider->stateless();
        }

        return $socialiteProvider->redirect();
    }

    public function callback(string $provider, OauthLoginService $oauthLoginService)
    {
        try {
            $oauthSetting = $this->enabledProvider($provider);
            $driver = get_socialite_provider($oauthSetting->provider);

            if (env('VERCEL') || isset($_SERVER['VERCEL']) || config('session.driver') === 'cookie') {
                $driver = $driver->stateless();
            }

            try {
                $oauthUser = $driver->user();
            } catch (\Laravel\Socialite\Two\InvalidStateException) {
                $oauthUser = get_socialite_provider($oauthSetting->provider)->stateless()->user();
            }

            $user = $oauthLoginService->login($oauthSetting->provider, $oauthUser, $oauthSetting);

            if ($oauthLoginService->requiresTwoFactorChallenge($user)) {
                return redirect()->route('two-factor.login');
            }

            return redirect('/');
        } catch (\Throwable $e) {
            $this->logCallbackFailure($provider, $e);

            $errorCode = match (true) {
                $e instanceof OauthLoginException => $e->userMessageKey,
                $e instanceof HttpException => 'auth.failed.oauth',
                default => 'auth.failed.callback',
            };

            $translated = __($errorCode);
            $message = ($translated !== $errorCode) ? $translated : $e->getMessage();

            return redirect()->route('login')->withErrors([$message ?: 'Authentication failed. Please try again.']);
        }
    }

    private function logCallbackFailure(string $provider, \Throwable $exception): void
    {
        auditLog('auth.oauth.callback_failed', [
            'provider' => $provider,
            'exception_class' => $exception::class,
            'reason' => $exception instanceof HttpException ? 'access_denied' : 'callback_error',
        ], 'warning');
        Log::error('OAuth callback failed.', [
            'provider' => $provider,
            'exception_class' => $exception::class,
            'exception_message' => $exception->getMessage(),
            'request_error' => request()->query('error'),
            'request_error_description' => request()->query('error_description'),
            'has_code' => request()->query->has('code'),
            'has_state' => request()->query->has('state'),
            'ip' => request()->ip(),
            'exception' => $exception,
        ]);
    }

    private function enabledProvider(string $provider): OauthSetting
    {
        $oauthSetting = OauthSetting::where('provider', $provider)->first();
        if (! $oauthSetting || ! $oauthSetting->enabled || ! $oauthSetting->couldBeEnabled()) {
            throw new HttpException(403, 'OAuth provider is not enabled');
        }

        return $oauthSetting;
    }
}
