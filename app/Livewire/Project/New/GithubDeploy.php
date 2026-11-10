<?php

namespace App\Livewire\Project\New;

use App\Actions\Database\StartDatabase;
use App\Models\Application;
use App\Models\GithubApp;
use App\Models\Project;
use App\Models\StandalonePostgresql;
use App\Rules\ValidGitBranch;
use App\Rules\ValidGitRepositoryUrl;
use App\Support\ValidationPatterns;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

class GithubDeploy extends Component
{
    use AuthorizesRequests;

    public string $type = 'github-deploy';

    public $parameters;

    public $query;

    /**
     * Source Tab:
     * - 'repos' (Browse connected GitHub repositories)
     * - 'url' (Direct Git URL or custom repo)
     * - 'templates' (Vercel-style 1-click starter templates)
     */
    public string $source_tab = 'url';

    public string $repo_search = '';

    public array $github_repos = [];

    public bool $is_loading_repos = false;

    public ?int $selected_github_app_id = null;

    public Collection $github_apps;

    // Core Git & Project details
    public string $repository_url = '';

    public string $git_branch = 'main';

    public string $project_name = '';

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

    public ?string $frontend_custom_domain = null;

    public ?string $frontend_health_check_path = null;

    // Backend Configuration
    public string $backend_name = '';

    public string $backend_base_directory = '/backend';

    public string $backend_preset = 'express';

    public string $backend_build_pack = 'railpack';

    public int $backend_port = 3001;

    public ?string $backend_start_command = null;

    public ?string $backend_build_command = null;

    public ?string $backend_install_command = null;

    public ?string $backend_custom_domain = null;

    public ?string $backend_health_check_path = null;

    // Database Provisioning Add-on
    public bool $provision_database = false;

    public string $database_type = 'postgresql';

    public string $database_name = '';

    // Environment Variables & Secrets
    public array $env_vars = [];

    public bool $show_env_modal = false;

    public string $raw_env_content = '';

    public bool $mask_env_secrets = true;

    // CI/CD & Automation options
    public bool $auto_wire_env = true;

    public bool $deploy_now = true;

    public bool $is_auto_deploy = true;

    public bool $is_preview_deploy = true;

    public bool $is_git_shallow_clone = true;

    /**
     * Curated starter templates with 1-click configuration
     */
    public array $starter_templates = [
        [
            'id' => 'nextjs-app',
            'title' => 'Next.js 14 App Router',
            'description' => 'Production-ready React framework with TypeScript, App Router & Tailwind CSS',
            'category' => 'Frontend / Fullstack',
            'badge' => 'Recommended',
            'repo' => 'https://github.com/coollabsio/coolify-examples',
            'branch' => 'main',
            'deploy_mode' => 'frontend',
            'preset' => 'nextjs',
            'port' => 3000,
            'base_directory' => '/',
            'build_command' => 'npm run build',
        ],
        [
            'id' => 'vite-react',
            'title' => 'React + Vite SPA',
            'description' => 'Blazing-fast client Single Page Application with Vite HMR & Tailwind',
            'category' => 'Frontend',
            'badge' => 'Fast',
            'repo' => 'https://github.com/coollabsio/coolify-examples',
            'branch' => 'main',
            'deploy_mode' => 'frontend',
            'preset' => 'vite-react',
            'port' => 3000,
            'base_directory' => '/',
            'build_command' => 'npm run build',
        ],
        [
            'id' => 'fullstack-monorepo',
            'title' => 'Fullstack Monorepo (Next.js + Express)',
            'description' => 'Frontend web app and backend REST API in one repo with auto-wired environment URLs',
            'category' => 'Fullstack',
            'badge' => 'Dual Service',
            'repo' => 'https://github.com/coollabsio/coolify-examples',
            'branch' => 'main',
            'deploy_mode' => 'fullstack',
            'preset' => 'nextjs',
            'base_directory' => '/frontend',
            'backend_base_directory' => '/backend',
        ],
        [
            'id' => 'express-api',
            'title' => 'Node.js Express API',
            'description' => 'Lightweight, scalable REST API service with CORS and JSON body parser',
            'category' => 'Backend',
            'badge' => 'API',
            'repo' => 'https://github.com/coollabsio/coolify-examples',
            'branch' => 'main',
            'deploy_mode' => 'backend',
            'preset' => 'express',
            'port' => 3001,
            'base_directory' => '/',
            'start_command' => 'npm start',
        ],
        [
            'id' => 'fastapi-backend',
            'title' => 'Python FastAPI Backend',
            'description' => 'Modern high-performance async Python API with automatic OpenAPI documentation',
            'category' => 'Backend',
            'badge' => 'Python',
            'repo' => 'https://github.com/coollabsio/coolify-examples',
            'branch' => 'main',
            'deploy_mode' => 'backend',
            'preset' => 'fastapi',
            'port' => 8000,
            'base_directory' => '/',
            'start_command' => 'uvicorn main:app --host 0.0.0.0 --port 8000',
        ],
        [
            'id' => 'laravel-app',
            'title' => 'Laravel 11 PHP App',
            'description' => 'Robust PHP web framework with Artisan, Eloquent ORM, and queue worker ready',
            'category' => 'Fullstack / API',
            'badge' => 'PHP',
            'repo' => 'https://github.com/coollabsio/coolify-examples',
            'branch' => 'main',
            'deploy_mode' => 'backend',
            'preset' => 'laravel',
            'port' => 8000,
            'base_directory' => '/',
        ],
        [
            'id' => 'vue-app',
            'title' => 'Vue 3 + Vite SPA',
            'description' => 'Progressive JavaScript framework with Composition API and instant build output',
            'category' => 'Frontend',
            'badge' => 'Vue',
            'repo' => 'https://github.com/coollabsio/coolify-examples',
            'branch' => 'main',
            'deploy_mode' => 'frontend',
            'preset' => 'vue',
            'port' => 3000,
            'base_directory' => '/',
            'build_command' => 'npm run build',
        ],
        [
            'id' => 'static-site',
            'title' => 'Static HTML / Tailwind',
            'description' => 'Zero-runtime static landing page served via high performance Nginx server',
            'category' => 'Static',
            'badge' => 'Port 80',
            'repo' => 'https://github.com/coollabsio/coolify-examples',
            'branch' => 'main',
            'deploy_mode' => 'frontend',
            'preset' => 'static',
            'port' => 80,
            'base_directory' => '/',
            'build_command' => null,
        ],
    ];

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

        if ($this->provision_database) {
            $rules['database_type'] = ['required', 'in:postgresql,redis,mysql,mariadb,mongodb'];
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

        if ($this->github_apps->isNotEmpty()) {
            $this->selected_github_app_id = $this->github_apps->first()->id;
            $this->source_tab = 'repos';
            $this->loadGithubRepositories();
        } else {
            $this->source_tab = 'url';
        }

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

    public function setSourceTab(string $tab): void
    {
        $this->source_tab = $tab;
        if ($tab === 'repos' && empty($this->github_repos) && $this->selected_github_app_id) {
            $this->loadGithubRepositories();
        }
    }

    public function loadGithubRepositories(): void
    {
        if (! $this->selected_github_app_id) {
            return;
        }

        $this->is_loading_repos = true;
        try {
            $githubApp = GithubApp::ownedByCurrentTeam()
                ->where('is_public', false)
                ->whereNotNull('app_id')
                ->findOrFail($this->selected_github_app_id);

            $token = generateGithubInstallationToken($githubApp);
            $result = loadRepositoryByPage($githubApp, $token, 1);
            $repos = $result['repositories'] ?? [];

            $this->github_repos = collect($repos)->map(function ($repo) {
                return [
                    'id' => $repo['id'] ?? null,
                    'name' => $repo['name'] ?? '',
                    'full_name' => $repo['full_name'] ?? '',
                    'html_url' => $repo['html_url'] ?? '',
                    'private' => (bool) ($repo['private'] ?? false),
                    'default_branch' => $repo['default_branch'] ?? 'main',
                    'description' => $repo['description'] ?? '',
                    'stargazers_count' => $repo['stargazers_count'] ?? 0,
                    'updated_at' => $repo['updated_at'] ?? '',
                ];
            })->sortBy('name')->values()->toArray();
        } catch (\Throwable $e) {
            $this->github_repos = [];
        } finally {
            $this->is_loading_repos = false;
        }
    }

    public function selectGithubRepo(string $htmlUrl, string $defaultBranch = 'main'): void
    {
        $this->repository_url = $htmlUrl;
        $this->git_branch = $defaultBranch;
        $this->updatedRepositoryUrl();
        $this->source_tab = 'url';
        $this->dispatch('success', "Selected repository: {$this->project_name}");
    }

    public function selectTemplate(string $templateId): void
    {
        foreach ($this->starter_templates as $tpl) {
            if ($tpl['id'] === $templateId) {
                $this->repository_url = $tpl['repo'];
                $this->git_branch = $tpl['branch'];
                $this->deploy_mode = $tpl['deploy_mode'];
                $this->updatedRepositoryUrl();

                if ($this->deploy_mode === 'fullstack' || $this->deploy_mode === 'frontend') {
                    $this->frontend_preset = $tpl['preset'];
                    $this->updatedFrontendPreset();
                    if (isset($tpl['base_directory'])) {
                        $this->frontend_base_directory = $tpl['base_directory'];
                    }
                    if (isset($tpl['build_command'])) {
                        $this->frontend_build_command = $tpl['build_command'];
                    }
                }

                if ($this->deploy_mode === 'fullstack' || $this->deploy_mode === 'backend') {
                    if (isset($tpl['backend_base_directory'])) {
                        $this->backend_base_directory = $tpl['backend_base_directory'];
                    }
                    if (isset($tpl['start_command'])) {
                        $this->backend_start_command = $tpl['start_command'];
                    }
                }

                $this->source_tab = 'url';
                $this->dispatch('success', "Loaded {$tpl['title']} template configuration!");
                break;
            }
        }
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
        $this->database_name = "{$baseName}-db";
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
            case 'remix':
                $this->frontend_port = 3000;
                $this->frontend_build_pack = 'railpack';
                $this->frontend_is_static = false;
                $this->frontend_build_command = 'npm run build';
                break;
            case 'nuxt':
                $this->frontend_port = 3000;
                $this->frontend_build_pack = 'railpack';
                $this->frontend_is_static = false;
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
            case 'nestjs':
                $this->backend_port = 3000;
                $this->backend_build_pack = 'railpack';
                $this->backend_build_command = 'npm run build';
                $this->backend_start_command = 'node dist/main.js';
                break;
            case 'fastapi':
            case 'python':
                $this->backend_port = 8000;
                $this->backend_build_pack = 'railpack';
                $this->backend_start_command = 'uvicorn main:app --host 0.0.0.0 --port 8000';
                break;
            case 'django':
                $this->backend_port = 8000;
                $this->backend_build_pack = 'railpack';
                $this->backend_start_command = 'gunicorn myproject.wsgi --bind 0.0.0.0:8000';
                break;
            case 'flask':
                $this->backend_port = 5000;
                $this->backend_build_pack = 'railpack';
                $this->backend_start_command = 'gunicorn app:app --bind 0.0.0.0:5000';
                break;
            case 'go':
                $this->backend_port = 8080;
                $this->backend_build_pack = 'railpack';
                break;
            case 'rust':
                $this->backend_port = 8080;
                $this->backend_build_pack = 'railpack';
                $this->backend_build_command = 'cargo build --release';
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

    public function generateSecretForVar(int $index): void
    {
        if (isset($this->env_vars[$index])) {
            $this->env_vars[$index]['value'] = bin2hex(random_bytes(32));
            $this->dispatch('success', "Generated secure 32-byte secret for {$this->env_vars[$index]['key']}!");
        }
    }

    public function addCommonSecret(string $keyName): void
    {
        $this->env_vars[] = [
            'key' => $keyName,
            'value' => bin2hex(random_bytes(32)),
            'target' => $this->deploy_mode === 'fullstack' ? 'both' : $this->deploy_mode,
        ];
        $this->dispatch('success', "Added {$keyName} with secure random token!");
    }

    public function addCommonPreset(string $key, string $value): void
    {
        $this->env_vars[] = [
            'key' => $key,
            'value' => $value,
            'target' => $this->deploy_mode === 'fullstack' ? 'both' : $this->deploy_mode,
        ];
        $this->dispatch('success', "Added {$key}={$value}");
    }

    public function toggleSecretMasking(): void
    {
        $this->mask_env_secrets = ! $this->mask_env_secrets;
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

                if (! empty(trim((string) $this->frontend_health_check_path))) {
                    $frontendApp->health_check_path = trim((string) $this->frontend_health_check_path);
                    $frontendApp->health_check_enabled = true;
                }

                $frontendApp->save();

                $frontendApp->settings->is_static = $this->frontend_is_static;
                $frontendApp->settings->is_git_shallow_clone_enabled = $this->is_git_shallow_clone;
                $frontendApp->settings->is_auto_deploy_enabled = $this->is_auto_deploy;
                $frontendApp->settings->is_preview_deployments_enabled = $this->is_preview_deploy;
                $frontendApp->settings->save();

                if (! empty(trim((string) $this->frontend_custom_domain))) {
                    $frontendApp->fqdn = trim((string) $this->frontend_custom_domain);
                } else {
                    $frontendApp->fqdn = generateUrl(server: $destination->server, random: $frontendApp->uuid);
                }
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
                if ($this->backend_install_command) {
                    $backendApp->custom_install_command = $this->backend_install_command;
                }

                if (! empty(trim((string) $this->backend_health_check_path))) {
                    $backendApp->health_check_path = trim((string) $this->backend_health_check_path);
                    $backendApp->health_check_enabled = true;
                }

                $backendApp->save();

                $backendApp->settings->is_git_shallow_clone_enabled = $this->is_git_shallow_clone;
                $backendApp->settings->is_auto_deploy_enabled = $this->is_auto_deploy;
                $backendApp->settings->is_preview_deployments_enabled = $this->is_preview_deploy;
                $backendApp->settings->save();

                if (! empty(trim((string) $this->backend_custom_domain))) {
                    $backendApp->fqdn = trim((string) $this->backend_custom_domain);
                } else {
                    $backendApp->fqdn = generateUrl(server: $destination->server, random: $backendApp->uuid);
                }
                $backendApp->save();

                $createdApplications['backend'] = $backendApp;
            }

            // 3. Provision Database if requested
            if ($this->provision_database) {
                $dbName = $this->database_name ?: "{$this->project_name}-db";
                $createdDatabase = null;

                $this->authorize('create', [StandalonePostgresql::class, $environment]);

                switch ($this->database_type) {
                    case 'postgresql':
                        $createdDatabase = create_standalone_postgresql($environment->id, $destination, ['name' => $dbName]);
                        break;
                    case 'redis':
                        $createdDatabase = create_standalone_redis($environment->id, $destination, ['name' => $dbName]);
                        break;
                    case 'mysql':
                        $createdDatabase = create_standalone_mysql($environment->id, $destination, ['name' => $dbName]);
                        break;
                    case 'mariadb':
                        $createdDatabase = create_standalone_mariadb($environment->id, $destination, ['name' => $dbName]);
                        break;
                    case 'mongodb':
                        $createdDatabase = create_standalone_mongodb($environment->id, $destination, ['name' => $dbName]);
                        break;
                }

                if ($createdDatabase) {
                    if ($this->deploy_now) {
                        StartDatabase::dispatch($createdDatabase);
                    }

                    $dbUrl = $createdDatabase->internal_db_url ?? '';
                    if ($dbUrl) {
                        $envKey = $this->database_type === 'redis' ? 'REDIS_URL' : ($this->database_type === 'mongodb' ? 'MONGODB_URI' : 'DATABASE_URL');

                        foreach ($createdApplications as $app) {
                            $app->environment_variables()->updateOrCreate(
                                ['key' => $envKey],
                                [
                                    'value' => $dbUrl,
                                    'resourceable_type' => $app->getMorphClass(),
                                    'resourceable_id' => $app->id,
                                    'is_preview' => false,
                                ]
                            );
                        }
                    }
                }
            }

            // 4. Inject Environment Variables
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

            // 5. Auto-wire fullstack communication if enabled
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

            // 6. Deploy Immediately if option selected
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
        $filteredRepos = collect($this->github_repos);
        if (! empty(trim($this->repo_search))) {
            $search = strtolower(trim($this->repo_search));
            $filteredRepos = $filteredRepos->filter(function ($repo) use ($search) {
                return str_contains(strtolower($repo['name']), $search) ||
                       str_contains(strtolower($repo['full_name']), $search) ||
                       str_contains(strtolower($repo['description'] ?? ''), $search);
            });
        }

        return view('livewire.project.new.github-deploy', [
            'filtered_github_repos' => $filteredRepos->values()->toArray(),
        ]);
    }
}
