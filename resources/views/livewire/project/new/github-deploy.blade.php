<div class="mt-8 flex w-full max-w-none flex-col gap-6 lg:mt-3" x-data="{ openAdvancedFrontend: false, openAdvancedBackend: false, openAdvancedCiCd: false }">
    {{-- Header --}}
    <div class="flex flex-col gap-2 border-b border-neutral-200 pb-5 dark:border-white/[0.08]">
        <div class="flex items-center gap-2">
            <span class="flex size-7 items-center justify-center rounded-md bg-neutral-900 text-white dark:bg-white dark:text-black">
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                </svg>
            </span>
            <h1 class="text-xl font-bold tracking-tight text-neutral-900 dark:text-white">Deploy with GitHub</h1>
            <span class="rounded bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-800 dark:bg-[#252526] dark:text-[#cccccc] dark:border dark:border-[#333333]">Vercel Cloud Experience</span>
        </div>
        <p class="text-sm text-neutral-600 dark:text-neutral-400">
            Import repositories directly from GitHub, deploy fullstack monorepos, frontend web applications, or backend APIs with 1-click database provisioning and automated CI/CD.
        </p>
    </div>

    {{-- Source Selection Mode Tabs --}}
    <div class="flex items-center gap-2 border-b border-neutral-200 pb-3 dark:border-white/[0.08]">
        @if ($github_apps->isNotEmpty())
            <button type="button" wire:click="setSourceTab('repos')"
                class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium transition-all {{ $source_tab === 'repos' ? 'bg-neutral-900 text-white dark:bg-white dark:text-black shadow-xs' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200 dark:bg-[#202020] dark:text-[#cccccc] dark:hover:bg-[#282828] dark:border dark:border-[#2e2e2e]' }}">
                <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                </svg>
                Your GitHub Repositories ({{ count($github_repos) }})
            </button>
        @endif

        <button type="button" wire:click="setSourceTab('url')"
            class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium transition-all {{ $source_tab === 'url' ? 'bg-neutral-900 text-white dark:bg-white dark:text-black shadow-xs' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200 dark:bg-[#202020] dark:text-[#cccccc] dark:hover:bg-[#282828] dark:border dark:border-[#2e2e2e]' }}">
            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
            Git Repository URL
        </button>

        <button type="button" wire:click="setSourceTab('templates')"
            class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium transition-all {{ $source_tab === 'templates' ? 'bg-neutral-900 text-white dark:bg-white dark:text-black shadow-xs' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200 dark:bg-[#202020] dark:text-[#cccccc] dark:hover:bg-[#282828] dark:border dark:border-[#2e2e2e]' }}">
            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
            1-Click Starter Templates ({{ count($starter_templates) }})
        </button>
    </div>

    {{-- TAB 1: Connected GitHub Repositories Browser --}}
    @if ($source_tab === 'repos')
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-white">Import from GitHub</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Search and select one of your GitHub repositories to deploy with instant setup.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="loadGithubRepositories" wire:loading.attr="disabled"
                        class="rounded-md border border-neutral-300 bg-white px-2.5 py-1 text-xs font-medium text-neutral-700 hover:bg-neutral-50 dark:border-[#333333] dark:bg-[#202020] dark:text-[#cccccc] dark:hover:bg-[#282828]">
                        <span wire:loading.remove wire:target="loadGithubRepositories">↻ Refresh</span>
                        <span wire:loading wire:target="loadGithubRepositories">Loading...</span>
                    </button>
                </div>
            </div>
            <div class="application-settings-section-body space-y-4">
                {{-- Search Bar --}}
                <div class="relative">
                    <input type="text" wire:model.live.debounce.250ms="repo_search" placeholder="Search repositories by name or description..."
                        class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 pl-9 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white" />
                    <svg class="pointer-events-none absolute left-3 top-2.5 size-4 text-neutral-400 dark:text-[#858585]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>

                {{-- Repo List --}}
                <div class="max-h-96 space-y-2 overflow-y-auto pr-1">
                    @forelse ($filtered_github_repos as $repo)
                        <div class="flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-3 transition-colors hover:border-neutral-300 dark:border-[#2b2b2b] dark:bg-[#181818] dark:hover:border-[#3e3e3e]">
                            <div class="flex flex-col gap-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-neutral-900 dark:text-white">{{ $repo['name'] }}</span>
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-medium {{ $repo['private'] ? 'bg-neutral-200 text-neutral-800 dark:bg-[#252526] dark:text-[#cccccc] dark:border dark:border-[#333333]' : 'bg-neutral-100 text-neutral-600 dark:bg-[#202020] dark:text-[#858585]' }}">
                                        {{ $repo['private'] ? 'Private' : 'Public' }}
                                    </span>
                                    <span class="text-[11px] font-mono text-neutral-400 dark:text-[#6e6e6e]">{{ $repo['default_branch'] }}</span>
                                </div>
                                @if (!empty($repo['description']))
                                    <p class="text-xs text-neutral-500 dark:text-[#858585] line-clamp-1">{{ $repo['description'] }}</p>
                                @endif
                            </div>
                            <button type="button" wire:click="selectGithubRepo('{{ $repo['html_url'] }}', '{{ $repo['default_branch'] }}')"
                                class="rounded-md bg-neutral-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                                Import
                            </button>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-neutral-300 p-8 text-center dark:border-[#333333]">
                            <p class="text-sm text-neutral-500 dark:text-[#858585]">
                                @if (!empty($repo_search))
                                    No repositories found matching "{{ $repo_search }}".
                                @else
                                    No GitHub repositories loaded. Connect your GitHub App or switch to "Git Repository URL".
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    {{-- TAB 3: Starter Templates Gallery --}}
    @if ($source_tab === 'templates')
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-white">1-Click Starter Templates</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Jumpstart a new project with pre-configured framework builds and architectures.</p>
                </div>
            </div>
            <div class="application-settings-section-body">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($starter_templates as $tpl)
                        <div class="flex flex-col justify-between rounded-lg border border-neutral-200 bg-white p-4 transition-all hover:border-neutral-400 dark:border-[#2b2b2b] dark:bg-[#181818] dark:hover:border-[#444444]">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="rounded bg-neutral-100 px-2 py-0.5 text-[10px] font-semibold text-neutral-700 dark:bg-[#252526] dark:text-[#cccccc] dark:border dark:border-[#333333]">
                                        {{ $tpl['category'] }}
                                    </span>
                                    <span class="rounded bg-neutral-200 px-1.5 py-0.2 text-[10px] font-medium text-neutral-800 dark:bg-[#282828] dark:text-[#aaaaaa]">
                                        {{ $tpl['badge'] }}
                                    </span>
                                </div>
                                <h3 class="mt-2 text-sm font-semibold text-neutral-900 dark:text-white">{{ $tpl['title'] }}</h3>
                                <p class="mt-1 text-xs text-neutral-500 dark:text-[#858585] leading-relaxed">{{ $tpl['description'] }}</p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-neutral-100 dark:border-[#262626] flex items-center justify-between">
                                <span class="text-[11px] font-mono text-neutral-400 dark:text-[#6e6e6e]">Port {{ $tpl['port'] ?? 3000 }}</span>
                                <button type="button" wire:click="selectTemplate('{{ $tpl['id'] }}')"
                                    class="rounded-md bg-neutral-900 px-3 py-1 text-xs font-semibold text-white transition-colors hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                                    Use Template
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Main Deployment Configuration Form --}}
    <form wire:submit="submit" class="flex flex-col gap-6">
        {{-- Section 1: Repository Details --}}
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-white">1. Repository & Branch</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Configure repository URL, branch, and project identifier.</p>
                </div>
            </div>
            <div class="application-settings-section-body space-y-4">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            GitHub Repository URL or Path <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.blur="repository_url" placeholder="https://github.com/owner/repo or owner/repo"
                            class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white"
                            required />
                        <p class="mt-1 text-[11px] text-neutral-500 dark:text-neutral-400">
                            E.g. <span class="font-mono text-neutral-800 dark:text-neutral-200">coollabsio/coolify-examples</span> or <span class="font-mono text-neutral-800 dark:text-neutral-200">https://github.com/my-org/my-app</span>.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            Production Branch <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="git_branch" placeholder="main"
                            class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white"
                            required />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                        Project Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="project_name" placeholder="my-app"
                        class="w-full max-w-md rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white"
                        required />
                </div>
            </div>
        </section>

        {{-- Section 2: Deployment Architecture Mode --}}
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-white">2. Deployment Architecture</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Choose whether to deploy Frontend, Backend, or both in parallel from a monorepo.</p>
                </div>
            </div>
            <div class="application-settings-section-body">
                <div class="grid gap-3 sm:grid-cols-3">
                    {{-- Fullstack Card --}}
                    <label class="relative flex cursor-pointer flex-col rounded-lg border p-4 transition-all {{ $deploy_mode === 'fullstack' ? 'border-neutral-900 bg-neutral-50 dark:border-white dark:bg-[#202020] shadow-xs ring-1 ring-neutral-900 dark:ring-white' : 'border-neutral-200 bg-white hover:border-neutral-300 dark:border-[#2b2b2b] dark:bg-[#181818] dark:hover:border-[#3e3e3e]' }}">
                        <input type="radio" wire:model.live="deploy_mode" value="fullstack" class="sr-only" />
                        <div class="flex items-center justify-between">
                            <span class="flex size-7 items-center justify-center rounded-md bg-neutral-200 dark:bg-[#282828] text-sm">
                                ⚡
                            </span>
                            <span class="size-3.5 rounded-full border {{ $deploy_mode === 'fullstack' ? 'border-4 border-neutral-900 bg-white dark:border-white dark:bg-black' : 'border-neutral-400 dark:border-[#444444]' }}"></span>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-neutral-900 dark:text-white">Fullstack (Dual Service)</h3>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Deploys both Frontend and Backend services in parallel from your monorepo and connects them automatically.
                        </p>
                    </label>

                    {{-- Frontend Only Card --}}
                    <label class="relative flex cursor-pointer flex-col rounded-lg border p-4 transition-all {{ $deploy_mode === 'frontend' ? 'border-neutral-900 bg-neutral-50 dark:border-white dark:bg-[#202020] shadow-xs ring-1 ring-neutral-900 dark:ring-white' : 'border-neutral-200 bg-white hover:border-neutral-300 dark:border-[#2b2b2b] dark:bg-[#181818] dark:hover:border-[#3e3e3e]' }}">
                        <input type="radio" wire:model.live="deploy_mode" value="frontend" class="sr-only" />
                        <div class="flex items-center justify-between">
                            <span class="flex size-7 items-center justify-center rounded-md bg-neutral-200 dark:bg-[#282828] text-sm">
                                🌐
                            </span>
                            <span class="size-3.5 rounded-full border {{ $deploy_mode === 'frontend' ? 'border-4 border-neutral-900 bg-white dark:border-white dark:bg-black' : 'border-neutral-400 dark:border-[#444444]' }}"></span>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-neutral-900 dark:text-white">Frontend Web App</h3>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Deploy Next.js, Vite, React, Vue, Svelte, Astro, or static HTML client from repository root or subfolder.
                        </p>
                    </label>

                    {{-- Backend Only Card --}}
                    <label class="relative flex cursor-pointer flex-col rounded-lg border p-4 transition-all {{ $deploy_mode === 'backend' ? 'border-neutral-900 bg-neutral-50 dark:border-white dark:bg-[#202020] shadow-xs ring-1 ring-neutral-900 dark:ring-white' : 'border-neutral-200 bg-white hover:border-neutral-300 dark:border-[#2b2b2b] dark:bg-[#181818] dark:hover:border-[#3e3e3e]' }}">
                        <input type="radio" wire:model.live="deploy_mode" value="backend" class="sr-only" />
                        <div class="flex items-center justify-between">
                            <span class="flex size-7 items-center justify-center rounded-md bg-neutral-200 dark:bg-[#282828] text-sm">
                                ⚙️
                            </span>
                            <span class="size-3.5 rounded-full border {{ $deploy_mode === 'backend' ? 'border-4 border-neutral-900 bg-white dark:border-white dark:bg-black' : 'border-neutral-400 dark:border-[#444444]' }}"></span>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-neutral-900 dark:text-white">Backend API / Service</h3>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Deploy Node.js Express, NestJS, Python FastAPI/Django, Go, PHP Laravel, or Docker container service.
                        </p>
                    </label>
                </div>
            </div>
        </section>

        {{-- Section 3: Frontend Configuration (for fullstack or frontend) --}}
        @if ($deploy_mode === 'fullstack' || $deploy_mode === 'frontend')
            <section class="application-settings-section">
                <div class="application-settings-section-header">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded bg-neutral-200 px-1.5 py-0.5 text-[10px] font-bold text-neutral-800 dark:bg-[#252526] dark:text-[#cccccc] uppercase tracking-wider">Frontend</span>
                            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">Frontend Configuration</h2>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Configure framework preset, subfolder root directory, and build settings.</p>
                    </div>
                </div>
                <div class="application-settings-section-body space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Framework Preset
                            </label>
                            <select wire:model.live="frontend_preset"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white">
                                <option value="nextjs">Next.js (App / Pages Router)</option>
                                <option value="vite-react">Vite / React</option>
                                <option value="vue">Vue 3 / Nuxt</option>
                                <option value="sveltekit">Svelte / SvelteKit</option>
                                <option value="remix">Remix</option>
                                <option value="astro">Astro</option>
                                <option value="static">Static HTML / SPA (Port 80)</option>
                                <option value="dockerfile">Dockerfile</option>
                                <option value="custom">Custom Build</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Service Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="frontend_name" placeholder="my-app-frontend"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white"
                                required />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Root / Base Directory <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="frontend_base_directory" placeholder="/frontend or /"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white"
                                required />
                            <div class="mt-1.5 flex items-center gap-1.5 text-[11px] text-neutral-500 dark:text-[#858585]">
                                <span>Quick set:</span>
                                <button type="button" wire:click="$set('frontend_base_directory', '/')" class="underline hover:text-neutral-900 dark:hover:text-white">/</button>
                                <span>•</span>
                                <button type="button" wire:click="$set('frontend_base_directory', '/frontend')" class="underline hover:text-neutral-900 dark:hover:text-white">/frontend</button>
                                <span>•</span>
                                <button type="button" wire:click="$set('frontend_base_directory', '/client')" class="underline hover:text-neutral-900 dark:hover:text-white">/client</button>
                                <span>•</span>
                                <button type="button" wire:click="$set('frontend_base_directory', '/apps/web')" class="underline hover:text-neutral-900 dark:hover:text-white">/apps/web</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Container Exposed Port <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="frontend_port" placeholder="3000"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white"
                                required />
                        </div>
                    </div>

                    {{-- Advanced Frontend Toggle --}}
                    <div class="pt-2">
                        <button type="button" @click="openAdvancedFrontend = !openAdvancedFrontend"
                            class="flex items-center gap-1.5 text-xs font-semibold text-neutral-700 hover:text-neutral-900 dark:text-[#cccccc] dark:hover:text-white">
                            <span x-text="openAdvancedFrontend ? '▾' : '▸'"></span>
                            <span>Build & Output Settings (Vercel Overrides)</span>
                        </button>

                        <div x-show="openAdvancedFrontend" x-cloak class="mt-3 space-y-3 rounded-lg border border-neutral-200 bg-neutral-50 p-4 dark:border-[#2b2b2b] dark:bg-[#141414]">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                        Build Command Override
                                    </label>
                                    <input type="text" wire:model="frontend_build_command" placeholder="npm run build"
                                        class="w-full font-mono rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                        Install Command Override
                                    </label>
                                    <input type="text" wire:model="frontend_install_command" placeholder="npm install"
                                        class="w-full font-mono rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                                </div>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                        Custom Domain (Optional)
                                    </label>
                                    <input type="text" wire:model="frontend_custom_domain" placeholder="https://app.example.com"
                                        class="w-full rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                        Healthcheck Path (Optional)
                                    </label>
                                    <input type="text" wire:model="frontend_health_check_path" placeholder="/"
                                        class="w-full font-mono rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Section 4: Backend Configuration (for fullstack or backend) --}}
        @if ($deploy_mode === 'fullstack' || $deploy_mode === 'backend')
            <section class="application-settings-section">
                <div class="application-settings-section-header">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded bg-neutral-200 px-1.5 py-0.5 text-[10px] font-bold text-neutral-800 dark:bg-[#252526] dark:text-[#cccccc] uppercase tracking-wider">Backend</span>
                            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">Backend Configuration</h2>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Configure backend framework runtime, API directory, and start command.</p>
                    </div>
                </div>
                <div class="application-settings-section-body space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Framework / Runtime Preset
                            </label>
                            <select wire:model.live="backend_preset"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white">
                                <option value="express">Node.js / Express API</option>
                                <option value="nestjs">NestJS</option>
                                <option value="fastapi">Python FastAPI (Uvicorn)</option>
                                <option value="django">Python Django</option>
                                <option value="flask">Python Flask</option>
                                <option value="go">Go Binary API</option>
                                <option value="rust">Rust (Cargo)</option>
                                <option value="laravel">PHP Laravel 11</option>
                                <option value="dockerfile">Dockerfile</option>
                                <option value="custom">Custom Build</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Service Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="backend_name" placeholder="my-app-backend"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white"
                                required />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Root / Base Directory <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="backend_base_directory" placeholder="/backend or /"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white"
                                required />
                            <div class="mt-1.5 flex items-center gap-1.5 text-[11px] text-neutral-500 dark:text-[#858585]">
                                <span>Quick set:</span>
                                <button type="button" wire:click="$set('backend_base_directory', '/')" class="underline hover:text-neutral-900 dark:hover:text-white">/</button>
                                <span>•</span>
                                <button type="button" wire:click="$set('backend_base_directory', '/backend')" class="underline hover:text-neutral-900 dark:hover:text-white">/backend</button>
                                <span>•</span>
                                <button type="button" wire:click="$set('backend_base_directory', '/server')" class="underline hover:text-neutral-900 dark:hover:text-white">/server</button>
                                <span>•</span>
                                <button type="button" wire:click="$set('backend_base_directory', '/api')" class="underline hover:text-neutral-900 dark:hover:text-white">/api</button>
                                <span>•</span>
                                <button type="button" wire:click="$set('backend_base_directory', '/apps/api')" class="underline hover:text-neutral-900 dark:hover:text-white">/apps/api</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Container Exposed Port <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="backend_port" placeholder="3001"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white"
                                required />
                        </div>
                    </div>

                    {{-- Advanced Backend Toggle --}}
                    <div class="pt-2">
                        <button type="button" @click="openAdvancedBackend = !openAdvancedBackend"
                            class="flex items-center gap-1.5 text-xs font-semibold text-neutral-700 hover:text-neutral-900 dark:text-[#cccccc] dark:hover:text-white">
                            <span x-text="openAdvancedBackend ? '▾' : '▸'"></span>
                            <span>Build & Start Commands (Overrides)</span>
                        </button>

                        <div x-show="openAdvancedBackend" x-cloak class="mt-3 space-y-3 rounded-lg border border-neutral-200 bg-neutral-50 p-4 dark:border-[#2b2b2b] dark:bg-[#141414]">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                        Start / Run Command
                                    </label>
                                    <input type="text" wire:model="backend_start_command" placeholder="npm start or uvicorn main:app"
                                        class="w-full font-mono rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                        Build Command Override
                                    </label>
                                    <input type="text" wire:model="backend_build_command" placeholder="npm run build"
                                        class="w-full font-mono rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                                </div>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                        Custom Domain (Optional)
                                    </label>
                                    <input type="text" wire:model="backend_custom_domain" placeholder="https://api.example.com"
                                        class="w-full rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                        Healthcheck Path (Optional)
                                    </label>
                                    <input type="text" wire:model="backend_health_check_path" placeholder="/health or /api/health"
                                        class="w-full font-mono rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Section 5: Database Provisioning Add-on --}}
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="rounded bg-neutral-200 px-1.5 py-0.5 text-[10px] font-bold text-neutral-800 dark:bg-[#252526] dark:text-[#cccccc] uppercase tracking-wider">Storage</span>
                        <h2 class="text-base font-semibold text-neutral-900 dark:text-white">Database Add-on (Optional)</h2>
                    </div>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Provision a managed database instance alongside your project with auto-injected connection URLs.</p>
                </div>
            </div>
            <div class="application-settings-section-body space-y-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="provision_database" wire:model.live="provision_database"
                        class="size-4 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-900 dark:border-[#444444] dark:bg-[#1f1f1f] dark:text-white dark:focus:ring-white" />
                    <label for="provision_database" class="cursor-pointer text-xs font-medium text-neutral-900 dark:text-white">
                        Provision a Database with this project
                    </label>
                </div>

                @if ($provision_database)
                    <div class="grid gap-4 sm:grid-cols-2 pt-2 border-t border-neutral-200 dark:border-[#262626]">
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Database Engine
                            </label>
                            <select wire:model.live="database_type"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white">
                                <option value="postgresql">PostgreSQL 16 (Recommended for Prisma / Drizzle)</option>
                                <option value="redis">Redis 7 (In-Memory Cache & Queues)</option>
                                <option value="mysql">MySQL 8</option>
                                <option value="mariadb">MariaDB</option>
                                <option value="mongodb">MongoDB 7</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Database Name
                            </label>
                            <input type="text" wire:model="database_name" placeholder="my-app-db"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white dark:focus:border-white dark:focus:ring-white" />
                        </div>
                    </div>
                    <div class="rounded-md bg-neutral-100 p-3 text-xs text-neutral-600 dark:bg-[#181818] dark:border dark:border-[#2b2b2b] dark:text-[#cccccc]">
                        💡 When provisioned, <span class="font-mono font-semibold text-neutral-900 dark:text-white">DATABASE_URL</span> (or <span class="font-mono font-semibold text-neutral-900 dark:text-white">REDIS_URL</span>) will be automatically injected into your application's environment variables.
                    </div>
                @endif
            </div>
        </section>

        {{-- Section 6: Environment Variables Studio --}}
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-white">3. Environment Variables & Secrets</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Manage secret keys, connection strings, and configuration variables.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="toggleSecretMasking"
                        class="rounded-md border border-neutral-300 bg-white px-2.5 py-1 text-xs font-medium text-neutral-700 hover:bg-neutral-50 dark:border-[#333333] dark:bg-[#202020] dark:text-[#cccccc] dark:hover:bg-[#282828]">
                        {{ $mask_env_secrets ? '👁 Reveal' : '🔒 Mask' }}
                    </button>
                    <button type="button" wire:click="openEnvModal"
                        class="rounded-md border border-neutral-300 bg-white px-2.5 py-1 text-xs font-medium text-neutral-700 hover:bg-neutral-50 dark:border-[#333333] dark:bg-[#202020] dark:text-[#cccccc] dark:hover:bg-[#282828]">
                        Paste .env
                    </button>
                    <button type="button" wire:click="addEnvVar"
                        class="rounded-md bg-neutral-900 px-2.5 py-1 text-xs font-medium text-white hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                        + Add Variable
                    </button>
                </div>
            </div>
            <div class="application-settings-section-body space-y-4">
                {{-- Quick Presets Bar --}}
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <span class="text-neutral-500 dark:text-[#858585]">Quick presets:</span>
                    <button type="button" wire:click="addCommonPreset('NODE_ENV', 'production')"
                        class="rounded border border-neutral-200 px-2 py-0.5 text-[11px] font-mono text-neutral-700 hover:bg-neutral-100 dark:border-[#2b2b2b] dark:bg-[#181818] dark:text-[#cccccc] dark:hover:bg-[#252526]">
                        + NODE_ENV
                    </button>
                    <button type="button" wire:click="addCommonSecret('AUTH_SECRET')"
                        class="rounded border border-neutral-200 px-2 py-0.5 text-[11px] font-mono text-neutral-700 hover:bg-neutral-100 dark:border-[#2b2b2b] dark:bg-[#181818] dark:text-[#cccccc] dark:hover:bg-[#252526]">
                        + AUTH_SECRET (32B)
                    </button>
                    <button type="button" wire:click="addCommonSecret('JWT_SECRET')"
                        class="rounded border border-neutral-200 px-2 py-0.5 text-[11px] font-mono text-neutral-700 hover:bg-neutral-100 dark:border-[#2b2b2b] dark:bg-[#181818] dark:text-[#cccccc] dark:hover:bg-[#252526]">
                        + JWT_SECRET (32B)
                    </button>
                    <button type="button" wire:click="addCommonSecret('NEXTAUTH_SECRET')"
                        class="rounded border border-neutral-200 px-2 py-0.5 text-[11px] font-mono text-neutral-700 hover:bg-neutral-100 dark:border-[#2b2b2b] dark:bg-[#181818] dark:text-[#cccccc] dark:hover:bg-[#252526]">
                        + NEXTAUTH_SECRET
                    </button>
                </div>

                {{-- Env Variables Table --}}
                <div class="space-y-2">
                    @forelse ($env_vars as $index => $var)
                        <div class="flex flex-col gap-2 rounded-md border border-neutral-200 bg-white p-2.5 sm:flex-row sm:items-center dark:border-[#2b2b2b] dark:bg-[#181818]">
                            <div class="flex-1">
                                <input type="text" wire:model="env_vars.{{ $index }}.key" placeholder="VARIABLE_NAME"
                                    class="w-full font-mono uppercase rounded border border-neutral-300 bg-white px-2.5 py-1.5 text-xs text-neutral-900 focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                            </div>

                            <div class="flex-[2] relative">
                                <input type="{{ $mask_env_secrets ? 'password' : 'text' }}" wire:model="env_vars.{{ $index }}.value" placeholder="value"
                                    class="w-full font-mono rounded border border-neutral-300 bg-white px-2.5 py-1.5 text-xs text-neutral-900 focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white" />
                            </div>

                            @if ($deploy_mode === 'fullstack')
                                <div class="w-32">
                                    <select wire:model="env_vars.{{ $index }}.target"
                                        class="w-full rounded border border-neutral-300 bg-white px-2 py-1.5 text-xs text-neutral-900 focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white">
                                        <option value="both">Both Services</option>
                                        <option value="frontend">Frontend Only</option>
                                        <option value="backend">Backend Only</option>
                                    </select>
                                </div>
                            @endif

                            <div class="flex items-center gap-1">
                                <button type="button" wire:click="generateSecretForVar({{ $index }})" title="Generate Cryptographic Secret (32 bytes)"
                                    class="rounded p-1.5 text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 dark:text-[#858585] dark:hover:bg-[#252526] dark:hover:text-white">
                                    🔑
                                </button>
                                <button type="button" wire:click="removeEnvVar({{ $index }})" title="Delete Variable"
                                    class="rounded p-1.5 text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:text-[#858585] dark:hover:bg-red-950/40 dark:hover:text-red-400">
                                    ✕
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-neutral-200 p-6 text-center text-xs text-neutral-500 dark:border-[#2b2b2b] dark:text-[#858585]">
                            No custom environment variables added yet. Click <span class="font-semibold text-neutral-900 dark:text-white">+ Add Variable</span> or <span class="font-semibold text-neutral-900 dark:text-white">Paste .env</span>.
                        </div>
                    @endforelse
                </div>

                {{-- Fullstack Auto-wire Note --}}
                @if ($deploy_mode === 'fullstack')
                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" id="auto_wire_env" wire:model="auto_wire_env"
                            class="size-4 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-900 dark:border-[#444444] dark:bg-[#1f1f1f] dark:text-white dark:focus:ring-white" />
                        <label for="auto_wire_env" class="cursor-pointer text-xs font-medium text-neutral-900 dark:text-white">
                            Auto-wire fullstack communication (Infers <span class="font-mono">NEXT_PUBLIC_API_URL</span>, <span class="font-mono">VITE_API_URL</span>, <span class="font-mono">FRONTEND_URL</span>, and <span class="font-mono">CORS_ORIGIN</span>)
                        </label>
                    </div>
                @endif
            </div>
        </section>

        {{-- Section 7: CI/CD & Automation Settings --}}
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-white">4. CI/CD & Automation</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Configure continuous deployment triggers and performance options.</p>
                </div>
            </div>
            <div class="application-settings-section-body space-y-3">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_auto_deploy" wire:model="is_auto_deploy"
                        class="size-4 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-900 dark:border-[#444444] dark:bg-[#1f1f1f] dark:text-white dark:focus:ring-white" />
                    <label for="is_auto_deploy" class="cursor-pointer text-xs text-neutral-900 dark:text-white">
                        <span class="font-semibold">Automatic Deployments on Git Push</span> — Triggers automatic build whenever new commits are pushed to the <span class="font-mono font-medium">{{ $git_branch ?: 'main' }}</span> branch.
                    </label>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_preview_deploy" wire:model="is_preview_deploy"
                        class="size-4 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-900 dark:border-[#444444] dark:bg-[#1f1f1f] dark:text-white dark:focus:ring-white" />
                    <label for="is_preview_deploy" class="cursor-pointer text-xs text-neutral-900 dark:text-white">
                        <span class="font-semibold">Pull Request Preview Deployments</span> — Automatically spins up isolated preview staging instances for incoming Pull Requests.
                    </label>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_git_shallow_clone" wire:model="is_git_shallow_clone"
                        class="size-4 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-900 dark:border-[#444444] dark:bg-[#1f1f1f] dark:text-white dark:focus:ring-white" />
                    <label for="is_git_shallow_clone" class="cursor-pointer text-xs text-neutral-900 dark:text-white">
                        <span class="font-semibold">Git Shallow Clone (5x faster checkout)</span> — Clones only the latest commit depth to speed up deployment builds.
                    </label>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="deploy_now" wire:model="deploy_now"
                        class="size-4 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-900 dark:border-[#444444] dark:bg-[#1f1f1f] dark:text-white dark:focus:ring-white" />
                    <label for="deploy_now" class="cursor-pointer text-xs text-neutral-900 dark:text-white">
                        <span class="font-semibold">Deploy Immediately on Create</span> — Start the initial build and container orchestration immediately.
                    </label>
                </div>
            </div>
        </section>

        {{-- Section 8: Submit & Summary Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-[#2b2b2b] dark:bg-[#181818]">
            <div class="flex flex-col gap-0.5">
                <span class="text-xs font-semibold text-neutral-900 dark:text-white">
                    Ready to deploy {{ $project_name ?: 'application' }}
                </span>
                <span class="text-[11px] text-neutral-500 dark:text-[#858585]">
                    Architecture: {{ ucfirst($deploy_mode) }}
                    @if ($provision_database)
                        • Database: {{ ucfirst($database_type) }}
                    @endif
                    • Branch: {{ $git_branch ?: 'main' }}
                </span>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" wire:loading.attr="disabled"
                    class="flex items-center gap-2 rounded-lg bg-neutral-900 px-6 py-2.5 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-neutral-800 disabled:opacity-50 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                    <span wire:loading.remove wire:target="submit">▲ Deploy Project</span>
                    <span wire:loading wire:target="submit">Provisioning & Deploying...</span>
                </button>
            </div>
        </div>
    </form>

    {{-- Paste .env Modal --}}
    @if ($show_env_modal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4" x-cloak>
            <div class="w-full max-w-xl rounded-xl border border-neutral-200 bg-white p-5 shadow-2xl dark:border-[#2b2b2b] dark:bg-[#181818]">
                <div class="flex items-center justify-between border-b border-neutral-200 pb-3 dark:border-[#262626]">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">Import .env File</h3>
                    <button type="button" wire:click="closeEnvModal" class="text-neutral-400 hover:text-neutral-600 dark:text-[#858585] dark:hover:text-white">✕</button>
                </div>
                <div class="my-4 space-y-2">
                    <p class="text-xs text-neutral-500 dark:text-[#858585]">
                        Paste lines in <span class="font-mono">KEY=VALUE</span> format. Comments (#) and empty lines are skipped automatically.
                    </p>
                    <textarea wire:model="raw_env_content" rows="10" placeholder="DATABASE_URL=postgres://...&#10;API_SECRET=sec_123&#10;NODE_ENV=production"
                        class="w-full font-mono rounded-md border border-neutral-300 bg-white p-3 text-xs text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none dark:border-[#333333] dark:bg-[#1f1f1f] dark:text-white"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 border-t border-neutral-200 pt-3 dark:border-[#262626]">
                    <button type="button" wire:click="closeEnvModal"
                        class="rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs font-medium text-neutral-700 hover:bg-neutral-50 dark:border-[#333333] dark:bg-[#202020] dark:text-[#cccccc] dark:hover:bg-[#282828]">
                        Cancel
                    </button>
                    <button type="button" wire:click="importRawEnv"
                        class="rounded-md bg-neutral-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                        Import Variables
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
