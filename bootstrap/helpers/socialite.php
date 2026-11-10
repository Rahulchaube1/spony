<?php

use App\Auth\GitlabProvider;
use App\Auth\Oidc\OidcConfig;
use App\Models\OauthSetting;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\BitbucketProvider;
use Laravel\Socialite\Two\GithubProvider;
use SocialiteProviders\Discord\Provider;
use SocialiteProviders\Manager\Config;

/**
 * Callback URL built from the instance URL, never from the request Host header.
 */
function oauth_default_redirect_uri(string $provider): string
{
    $currentHost = request()->getSchemeAndHttpHost();
    if (filled($currentHost) && ! str_contains($currentHost, 'localhost')) {
        return rtrim($currentHost, '/').route('auth.callback', $provider, false);
    }

    return rtrim(base_url(), '/').route('auth.callback', $provider, false);
}

function get_socialite_provider(string $provider)
{
    $oauth_setting = OauthSetting::firstWhere('provider', $provider);

    $currentHost = request()->getSchemeAndHttpHost();
    if (filled($currentHost) && ! str_contains($currentHost, 'localhost')) {
        $oauth_setting->redirect_uri = rtrim($currentHost, '/').route('auth.callback', $provider, false);
    } elseif (! filled($oauth_setting->redirect_uri)) {
        $oauth_setting->redirect_uri = oauth_default_redirect_uri($provider);
    }

    if ($provider === 'azure') {
        $azure_config = new Config(
            $oauth_setting->client_id,
            $oauth_setting->client_secret,
            $oauth_setting->redirect_uri,
            ['tenant' => $oauth_setting->tenant],
        );

        return Socialite::driver('azure')->setConfig($azure_config);
    }

    if ($provider == 'authentik' || $provider == 'clerk' || $provider == 'auth0') {
        $baseUrl = $oauth_setting->base_url ?: config("services.{$provider}.base_url");
        $authentik_clerk_config = new Config(
            $oauth_setting->client_id ?: config("services.{$provider}.client_id"),
            $oauth_setting->client_secret ?: config("services.{$provider}.client_secret"),
            $oauth_setting->redirect_uri,
            ['base_url' => $baseUrl],
        );

        return Socialite::driver($provider)->setConfig($authentik_clerk_config);
    }

    if ($provider == 'zitadel') {
        $zitadel_config = new Config(
            $oauth_setting->client_id ?: config('services.zitadel.client_id'),
            $oauth_setting->client_secret ?: config('services.zitadel.client_secret'),
            $oauth_setting->redirect_uri,
            ['base_url' => $oauth_setting->base_url ?: config('services.zitadel.base_url')],
        );

        return Socialite::driver('zitadel')->setConfig($zitadel_config);
    }

    if ($provider === 'oidc') {
        return Socialite::driver('oidc')->setConfig(OidcConfig::fromOauthSetting($oauth_setting));
    }

    if ($provider == 'google') {
        $google_config = new Config(
            $oauth_setting->client_id ?: config('services.google.client_id'),
            $oauth_setting->client_secret ?: config('services.google.client_secret'),
            $oauth_setting->redirect_uri ?: config('services.google.redirect')
        );

        $driver = Socialite::driver('google')->setConfig($google_config);
        if (filled($oauth_setting->tenant)) {
            $driver->with(['hd' => $oauth_setting->tenant]);
        }

        return $driver;
    }

    $config = [
        'client_id' => $oauth_setting->client_id,
        'client_secret' => $oauth_setting->client_secret,
        'redirect' => $oauth_setting->redirect_uri,
    ];

    $provider_class_map = [
        'bitbucket' => BitbucketProvider::class,
        'discord' => Provider::class,
        'github' => GithubProvider::class,
        'gitlab' => GitlabProvider::class,
        'infomaniak' => SocialiteProviders\Infomaniak\Provider::class,
    ];

    $socialite = Socialite::buildProvider(
        $provider_class_map[$provider],
        $config
    );

    if ($provider == 'gitlab' && ! empty($oauth_setting->base_url)) {
        $socialite->setHost($oauth_setting->base_url);
    }

    return $socialite;
}
