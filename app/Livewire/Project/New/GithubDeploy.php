<?php

namespace App\Livewire\Project\New;

use App\Models\Application;
use App\Models\GithubApp;
use App\Models\Project;
use App\Rules\ValidGitBranch;
use App\Rules\ValidGitRepositoryUrl;
use App\Support\ValidationPatterns;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class GithubDeploy extends Component
{
    use AuthorizesRequests;

    public string $type = 'github-deploy';

    public $parameters;

    public $query;

    public string $repository_url = '';

    public string $git_branch = 'main';

    public string $project_name = '';

    public ?int $selected_github_app_id = null;

    public Collection $github_apps;

    /**
     * Deployment Mode:
     * - 'fullstack' (Frontend and Backend both)
     * - 'frontend' (Frontend only)
     * - 'backend' (Backend only)
     */
    public string $deploy_mode = 'fullstack';

    // Frontend Configuration
    public string $frontend_name = '';

    public string $frontend_base_directory = '/frontend';

    public string $frontend_preset = 'nextjs';

    public string $frontend_build_pack = 'railpack';

    public int $frontend_port = 3000;

    public bool $frontend_is_static = false;

    public ?string $frontend_publish_directory = null;

    public ?string $frontend_build_command = null;

    public ?string $frontend_install_command = null;

    // Backend Configuration
    public string $backend_name = '';

    public string $backend_base_directory = '/backend';

    public string $backend_preset = 'express';

    public string $backend_build_pack = 'railpack';

    public int $backend_port = 8000;

    public ?string $backend_start_command = null;

    public ?string $backend_build_command = null;

    // Environment Variables
    public array $env_vars = [];

    public bool $show_env_modal = false;

    public string $raw_env_content = '';

    // Advanced / Automation options
    public bool $auto_wire_env = true;

    public bool $deploy_now = true;

    public bool $is_git_shallow_clone = true;

    protected function rules(): array
    {
        $rules = [
            'repository_url' => ['required', 'string', new ValidGitRepositoryUrl(allowRepositoryPath: true)],
            'git_branch' => ['required', 'string', new ValidGitBranch],
            'deploy_mode' => ['required', 'in:fullstack,frontend,backend'],
            'project_name' => ['required', 'string', 'max:100'],
        ];

        if ($this->deploy_mode === 'fullstack' || $this->deploy_mode === 'frontend') {
            $rules['frontend_name'] = ['required', 'string', 'max:100'];
            $rules['frontend_base_directory'] = ValidationPatterns::directoryPathRules();
            $rules['frontend_port'] = ['required', 'numeric', 'min:1', 'max:65535'];
            $rules['frontend_build_pack'] = ['required', 'string'];
        }

        if ($this->deploy_mode === 'fullstack' || $this->deploy_mode === 'backend') {
            $rules['backend_name'] = ['required', 'string', 'max:100'];
            $rules['backend_base_directory'] = ValidationPatterns::directoryPathRules();
            $rules['backend_port'] = ['required', 'numeric', 'min:1', 'max:65535'];
            $rules['backend_build_pack'] = ['required', 'string'];
        }

        return $rules;
    }

    public function mount()
    {
        $this->parameters = get_route_parameters();
        $this->query = request()->query();

        $this->github_apps = GithubApp::ownedByCurrentTeam()
            ->where('is_public', false)
            ->whereNotNull('app_id')
            ->get();

        if (isDev() && empty($this->repository_url)) {
            $this->repository_url = 'https://github.com/coollabsio/coolify-examples';
            $this->git_branch = 'main';
            $this->updatedRepositoryUrl();
        }

        // Initialize with default common variables
        $this->env_vars = [
            ['key' => 'NODE_ENV', 'value' => 'production', 'target' => 'both'],
        ];
    }

    public function updatedRepositoryUrl(): void
    {
        $url = trim($this->repository_url);
        if (empty($url)) {
            return;
        }

        // If user entered owner/repo format, normalize to https://github.com/owner/repo
        if (preg_match('/^[a-zA-Z0-9_\.-]+\/[a-zA-Z0-9_\.-]+$/', $url)) {
            $repoName = explode('/', $url)[1] ?? 'app';
        } else {
            $path = parse_url($url, PHP_URL_PATH);
            $cleanPath = trim((string) $path, '/');
            $cleanPath = preg_replace('/\.git$/', '', $cleanPath);
            $parts = explode('/', $cleanPath);
            $repoName = end($parts) ?: 'app';
        }

        $baseName = strtolower(preg_replace('/[^a-zA-Z0-9\-_]/', '-', $repoName));
        $this->project_name = $baseName;
        $this->frontend_name = "{$baseName}-frontend";
        $this->backend_name = "{$baseName}-backend";
    }

    public function updatedDeployMode(): void
    {
        if ($this->deploy_mode === 'frontend') {
            $this->frontend_base_directory = '/';
        } elseif ($this->deploy_mode === 'backend') {
            $this->backend_base_directory = '/';
        } else {
            $this->frontend_base_directory = '/frontend';
            $this->backend_base_directory = '/backend';
        }
    }

    public function updatedFrontendPreset(): void
    {
        switch ($this->frontend_preset) {
            case 'nextjs':
                $this->frontend_port = 3000;
                $this->frontend_build_pack = 'railpack';
                $this->frontend_is_static = false;
                $this->frontend_publish_directory = null;
                $this->frontend_build_command = 'npm run build';
                break;
            case 'vite-react':
            case 'vue':
            case 'sveltekit':
                $this->frontend_port = 3000;
                $this->frontend_build_pack = 'railpack';
                $this->frontend_is_static = false;
                $this->frontend_publish_directory = null;
                $this->frontend_build_command = 'npm run build';
                break;
            case 'astro':
            case 'static':
                $this->frontend_port = 80;
                $this->frontend_build_pack = 'static';
                $this->frontend_is_static = true;
                $this->frontend_publish_directory = '/dist';
                $this->frontend_build_command = 'npm run build';
                break;
            case 'dockerfile':
                $this->frontend_build_pack = 'dockerfile';
                $this->frontend_port = 3000;
                $this->frontend_is_static = false;
                break;
            case 'custom':
            default:
                break;
        }
    }

    public function updatedBackendPreset(): void
    {
        switch ($this->backend_preset) {
            case 'express':
            case 'nodejs':
                $this->backend_port = 3001;
                $this->backend_build_pack = 'railpack';
                $this->backend_start_command = 'npm start';
                break;
            case 'fastapi':
            case 'python':
                $this->backend_port = 8000;
                $this->backend_build_pack = 'railpack';
                $this->backend_start_command = 'uvicorn main:app --host 0.0.0.0 --port 8000';
                break;
            case 'go':
                $this->backend_port = 8080;
                $this->backend_build_pack = 'railpack';
                break;
            case 'laravel':
                $this->backend_port = 8000;
                $this->backend_build_pack = 'railpack';
                break;
            case 'dockerfile':
                $this->backend_build_pack = 'dockerfile';
                $this->backend_port = 8000;
                break;
            case 'custom':
            default:
                break;
        }
    }

    public function addEnvVar(): void
    {
        $this->env_vars[] = [
            'key' => '',
            'value' => '',
            'target' => $this->deploy_mode === 'fullstack' ? 'both' : $this->deploy_mode,
        ];
    }

    public function removeEnvVar(int $index): void
    {
        unset($this->env_vars[$index]);
        $this->env_vars = array_values($this->env_vars);
    }

    public function openEnvModal(): void
    {
        $this->show_env_modal = true;
    }

    public function closeEnvModal(): void
    {
        $this->show_env_modal = false;
        $this->raw_env_content = '';
    }

    public function importRawEnv(): void
    {
        if (empty(trim($this->raw_env_content))) {
            $this->closeEnvModal();

            return;
        }

        $lines = preg_split('/\r\n|\r|\n/', $this->raw_env_content);
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $key = preg_replace('/^export\s+/', '', $key);
                $value = trim($value);

                // Strip leading/trailing quotes
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                if (! empty($key) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
                    // Update if existing or append
                    $existingIndex = null;
                    foreach ($this->env_vars as $idx => $item) {
                        if ($item['key'] === $key) {
                            $existingIndex = $idx;
                            break;
                        }
                    }

                    if ($existingIndex !== null) {
                        $this->env_vars[$existingIndex]['value'] = $value;
                    } else {
                        $this->env_vars[] = [
                            'key' => $key,
                            'value' => $value,
                            'target' => $this->deploy_mode === 'fullstack' ? 'both' : $this->deploy_mode,
                        ];
                    }
                    $count++;
                }
            }
        }

        $this->dispatch('success', "Imported {$count} environment variables!");
        $this->closeEnvModal();
    }

    public function submit()
    {
        try {
            $this->frontend_base_directory = ValidationPatterns::normalizeDirectoryPath($this->frontend_base_directory);
            $this->backend_base_directory = ValidationPatterns::normalizeDirectoryPath($this->backend_base_directory);

            $this->validate();

            $destination_uuid = $this->query['destination'] ?? null;
            $destination = find_resource_destination_for_current_team($destination_uuid);
            if (! $destination) {
                throw new \Exception('Deployment destination server not found.');
            }

            $project = Project::ownedByCurrentTeam()->where('uuid', $this->parameters['project_uuid'])->firstOrFail();
            $environment = $project->environments()->where('uuid', $this->parameters['environment_uuid'])->firstOrFail();

            $this->authorize('create', [Application::class, $environment]);
            $this->authorize('update', $destination->server);

            // Normalize repository URL
            $normalizedRepoUrl = trim($this->repository_url);
            if (preg_match('/^[a-zA-Z0-9_\.-]+\/[a-zA-Z0-9_\.-]+$/', $normalizedRepoUrl)) {
                $normalizedRepoUrl = "https://github.com/{$normalizedRepoUrl}";
            }

            $createdApplications = [];

            // 1. Create Frontend Application if selected
            if ($this->deploy_mode === 'fullstack' || $this->deploy_mode === 'frontend') {
                $frontendApp = new Application([
                    'name' => $this->frontend_name ?: "{$this->project_name}-frontend",
                    'git_repository' => $normalizedRepoUrl,
                    'git_branch' => $this->git_branch,
                    'build_pack' => $this->frontend_build_pack,
                    'base_directory' => $this->frontend_base_directory,
                    'ports_exposes' => $this->frontend_port,
                    'publish_directory' => $this->frontend_is_static ? ($this->frontend_publish_directory ?: '/dist') : null,
                    'environment_id' => $environment->id,
                    'destination_id' => $destination->id,
                    'destination_type' => $destination->getMorphClass(),
                ]);

                if ($this->frontend_build_command) {
                    $frontendApp->custom_build_command = $this->frontend_build_command;
                }
                if ($this->frontend_install_command) {
                    $frontendApp->custom_install_command = $this->frontend_install_command;
                }

                $frontendApp->save();

                $frontendApp->settings->is_static = $this->frontend_is_static;
                $frontendApp->settings->is_git_shallow_clone_enabled = $this->is_git_shallow_clone;
                $frontendApp->settings->save();

                $frontendApp->fqdn = generateUrl(server: $destination->server, random: $frontendApp->uuid);
                $frontendApp->save();

                $createdApplications['frontend'] = $frontendApp;
            }

            // 2. Create Backend Application if selected
            if ($this->deploy_mode === 'fullstack' || $this->deploy_mode === 'backend') {
                $backendApp = new Application([
                    'name' => $this->backend_name ?: "{$this->project_name}-backend",
                    'git_repository' => $normalizedRepoUrl,
                    'git_branch' => $this->git_branch,
                    'build_pack' => $this->backend_build_pack,
                    'base_directory' => $this->backend_base_directory,
                    'ports_exposes' => $this->backend_port,
                    'environment_id' => $environment->id,
                    'destination_id' => $destination->id,
                    'destination_type' => $destination->getMorphClass(),
                ]);

                if ($this->backend_start_command) {
                    $backendApp->custom_start_command = $this->backend_start_command;
                }
                if ($this->backend_build_command) {
                    $backendApp->custom_build_command = $this->backend_build_command;
                }

                $backendApp->save();

                $backendApp->settings->is_git_shallow_clone_enabled = $this->is_git_shallow_clone;
                $backendApp->settings->save();

                $backendApp->fqdn = generateUrl(server: $destination->server, random: $backendApp->uuid);
                $backendApp->save();

                $createdApplications['backend'] = $backendApp;
            }

            // 3. Inject Environment Variables
            foreach ($this->env_vars as $var) {
                $key = trim($var['key'] ?? '');
                $val = (string) ($var['value'] ?? '');
                $target = $var['target'] ?? 'both';

                if (empty($key)) {
                    continue;
                }

                if (isset($createdApplications['frontend']) && ($target === 'both' || $target === 'frontend')) {
                    $createdApplications['frontend']->environment_variables()->updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $val,
                            'resourceable_type' => $createdApplications['frontend']->getMorphClass(),
                            'resourceable_id' => $createdApplications['frontend']->id,
                            'is_preview' => false,
                        ]
                    );
                }

                if (isset($createdApplications['backend']) && ($target === 'both' || $target === 'backend')) {
                    $createdApplications['backend']->environment_variables()->updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $val,
                            'resourceable_type' => $createdApplications['backend']->getMorphClass(),
                            'resourceable_id' => $createdApplications['backend']->id,
                            'is_preview' => false,
                        ]
                    );
                }
            }

            // 4. Auto-wire fullstack communication if enabled
            if ($this->auto_wire_env && isset($createdApplications['frontend']) && isset($createdApplications['backend'])) {
                $backendUrl = "https://{$createdApplications['backend']->fqdn}";
                $frontendUrl = "https://{$createdApplications['frontend']->fqdn}";

                // Inject into frontend
                $createdApplications['frontend']->environment_variables()->updateOrCreate(
                    ['key' => 'NEXT_PUBLIC_API_URL'],
                    ['value' => $backendUrl, 'resourceable_type' => $createdApplications['frontend']->getMorphClass(), 'resourceable_id' => $createdApplications['frontend']->id, 'is_preview' => false]
                );
                $createdApplications['frontend']->environment_variables()->updateOrCreate(
                    ['key' => 'VITE_API_URL'],
                    ['value' => $backendUrl, 'resourceable_type' => $createdApplications['frontend']->getMorphClass(), 'resourceable_id' => $createdApplications['frontend']->id, 'is_preview' => false]
                );

                // Inject into backend
                $createdApplications['backend']->environment_variables()->updateOrCreate(
                    ['key' => 'FRONTEND_URL'],
                    ['value' => $frontendUrl, 'resourceable_type' => $createdApplications['backend']->getMorphClass(), 'resourceable_id' => $createdApplications['backend']->id, 'is_preview' => false]
                );
                $createdApplications['backend']->environment_variables()->updateOrCreate(
                    ['key' => 'CORS_ORIGIN'],
                    ['value' => $frontendUrl, 'resourceable_type' => $createdApplications['backend']->getMorphClass(), 'resourceable_id' => $createdApplications['backend']->id, 'is_preview' => false]
                );
            }

            // 5. Deploy Immediately if option selected
            $firstDeploymentUuid = null;
            if ($this->deploy_now) {
                foreach ($createdApplications as $app) {
                    $deployUuid = new_public_id();
                    if (! $firstDeploymentUuid) {
                        $firstDeploymentUuid = $deployUuid;
                    }
                    queue_application_deployment(application: $app, deployment_uuid: $deployUuid);
                }
            }

            $primaryApp = $createdApplications['frontend'] ?? $createdApplications['backend'];

            $this->dispatch('success', 'Project created successfully from GitHub! Deployment initiated.');

            return redirect()->route('project.application.configuration', [
                'application_uuid' => $primaryApp->uuid,
                'environment_uuid' => $environment->uuid,
                'project_uuid' => $project->uuid,
            ]);
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function render()
    {
        return view('livewire.project.new.github-deploy');
    }
}
