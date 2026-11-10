<div class="mt-8 flex w-full max-w-none flex-col gap-6 lg:mt-3" x-data="{ openAdvancedFrontend: false, openAdvancedBackend: false }">
    <div class="flex flex-col gap-2 border-b border-neutral-200 pb-5 dark:border-white/[0.08]">
        <div class="flex items-center gap-2">
            <span class="flex size-7 items-center justify-center rounded-md bg-neutral-900 text-white dark:bg-white dark:text-black">
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                </svg>
            </span>
            <h1 class="text-xl font-bold tracking-tight text-neutral-900 dark:text-white">Deploy with GitHub</h1>
            <span class="rounded bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-800 dark:text-neutral-200">Vercel Experience</span>
        </div>
        <p class="text-sm text-neutral-600 dark:text-neutral-400">
            Connect any GitHub repository and deploy fullstack monorepos, frontend web applications, or backend APIs with 1-click automatic setup.
        </p>
    </div>

    <form wire:submit="submit" class="flex flex-col gap-6">
        {{-- Section 1: Repository Connection --}}
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-white">1. Connect Repository</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Specify the GitHub repository and branch you wish to deploy.</p>
                </div>
            </div>
            <div class="application-settings-section-body space-y-4">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            GitHub Repository URL or Path <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.blur="repository_url" placeholder="https://github.com/owner/repo or owner/repo"
                            class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white"
                            required />
                        <p class="mt-1 text-[11px] text-neutral-500 dark:text-neutral-400">
                            Public and private repositories supported. E.g. <span class="font-mono text-neutral-800 dark:text-neutral-200">facebook/react</span> or <span class="font-mono text-neutral-800 dark:text-neutral-200">https://github.com/my-org/my-app</span>.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            Production Branch <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="git_branch" placeholder="main"
                            class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white"
                            required />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                        Project Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="project_name" placeholder="my-app"
                        class="w-full max-w-md rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white"
                        required />
                </div>
            </div>
        </section>

        {{-- Section 2: Deployment Architecture Mode --}}
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-white">2. Select Deployment Architecture</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Choose whether to deploy Frontend, Backend, or both from a monorepo.</p>
                </div>
            </div>
            <div class="application-settings-section-body">
                <div class="grid gap-3 sm:grid-cols-3">
                    {{-- Fullstack Card --}}
                    <label class="relative flex cursor-pointer flex-col rounded-lg border p-4 transition-all {{ $deploy_mode === 'fullstack' ? 'border-neutral-900 bg-neutral-50 dark:border-white dark:bg-neutral-800/80 shadow-xs ring-1 ring-neutral-900 dark:ring-white' : 'border-neutral-200 bg-white hover:border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:hover:border-neutral-600' }}">
                        <input type="radio" wire:model.live="deploy_mode" value="fullstack" class="sr-only" />
                        <div class="flex items-center justify-between">
                            <span class="flex size-7 items-center justify-center rounded-md bg-neutral-200 dark:bg-neutral-700">
                                ⚡
                            </span>
                            <span class="size-3.5 rounded-full border {{ $deploy_mode === 'fullstack' ? 'border-4 border-neutral-900 bg-white dark:border-white dark:bg-black' : 'border-neutral-400' }}"></span>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-neutral-900 dark:text-white">Fullstack (Dual Service)</h3>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Deploys both Frontend and Backend services in parallel from your monorepo and connects them automatically.
                        </p>
                    </label>

                    {{-- Frontend Only Card --}}
                    <label class="relative flex cursor-pointer flex-col rounded-lg border p-4 transition-all {{ $deploy_mode === 'frontend' ? 'border-neutral-900 bg-neutral-50 dark:border-white dark:bg-neutral-800/80 shadow-xs ring-1 ring-neutral-900 dark:ring-white' : 'border-neutral-200 bg-white hover:border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:hover:border-neutral-600' }}">
                        <input type="radio" wire:model.live="deploy_mode" value="frontend" class="sr-only" />
                        <div class="flex items-center justify-between">
                            <span class="flex size-7 items-center justify-center rounded-md bg-neutral-200 dark:bg-neutral-700">
                                🌐
                            </span>
                            <span class="size-3.5 rounded-full border {{ $deploy_mode === 'frontend' ? 'border-4 border-neutral-900 bg-white dark:border-white dark:bg-black' : 'border-neutral-400' }}"></span>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-neutral-900 dark:text-white">Frontend Web App</h3>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Deploy Next.js, Vite, React, Vue, Svelte, Astro, or static HTML client from repository root or subfolder.
                        </p>
                    </label>

                    {{-- Backend Only Card --}}
                    <label class="relative flex cursor-pointer flex-col rounded-lg border p-4 transition-all {{ $deploy_mode === 'backend' ? 'border-neutral-900 bg-neutral-50 dark:border-white dark:bg-neutral-800/80 shadow-xs ring-1 ring-neutral-900 dark:ring-white' : 'border-neutral-200 bg-white hover:border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:hover:border-neutral-600' }}">
                        <input type="radio" wire:model.live="deploy_mode" value="backend" class="sr-only" />
                        <div class="flex items-center justify-between">
                            <span class="flex size-7 items-center justify-center rounded-md bg-neutral-200 dark:bg-neutral-700">
                                ⚙️
                            </span>
                            <span class="size-3.5 rounded-full border {{ $deploy_mode === 'backend' ? 'border-4 border-neutral-900 bg-white dark:border-white dark:bg-black' : 'border-neutral-400' }}"></span>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-neutral-900 dark:text-white">Backend API / Service</h3>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Deploy Node.js, Express, Python FastAPI/Django, Go, PHP/Laravel, or Docker container service.
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
                            <span class="rounded bg-neutral-200 px-1.5 py-0.5 text-[10px] font-bold text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200 uppercase tracking-wider">Frontend</span>
                            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">Frontend Service Configuration</h2>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Configure framework preset, build settings, and subfolder directory.</p>
                    </div>
                </div>
                <div class="application-settings-section-body space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Framework Preset
                            </label>
                            <select wire:model.live="frontend_preset"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white">
                                <option value="nextjs">Next.js (App / Pages Router)</option>
                                <option value="vite-react">Vite / React</option>
                                <option value="vue">Vue / Nuxt</option>
                                <option value="sveltekit">Svelte / SvelteKit</option>
                                <option value="astro">Astro</option>
                                <option value="static">Static HTML / SPA (Port 80)</option>
                                <option value="dockerfile">Dockerfile</option>
                                <option value="custom">Custom Build</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Root Directory
                            </label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="frontend_base_directory" placeholder="/frontend or /"
                                    class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white" />
                            </div>
                            <div class="mt-1 flex gap-1.5 text-[11px] text-neutral-500 dark:text-neutral-400">
                                <span>Quick paths:</span>
                                <button type="button" class="underline hover:text-black dark:hover:text-white" wire:click="$set('frontend_base_directory', '/')">/</button>
                                <button type="button" class="underline hover:text-black dark:hover:text-white" wire:click="$set('frontend_base_directory', '/frontend')">/frontend</button>
                                <button type="button" class="underline hover:text-black dark:hover:text-white" wire:click="$set('frontend_base_directory', '/client')">/client</button>
                                <button type="button" class="underline hover:text-black dark:hover:text-white" wire:click="$set('frontend_base_directory', '/web')">/web</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Application Name
                            </label>
                            <input type="text" wire:model="frontend_name"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Port Exposed
                            </label>
                            <input type="number" wire:model="frontend_port"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white" />
                        </div>
                    </div>

                    {{-- Collapsible Advanced Build Options --}}
                    <div class="pt-2">
                        <button type="button" @click="openAdvancedFrontend = !openAdvancedFrontend"
                            class="flex items-center gap-1.5 text-xs font-medium text-neutral-600 hover:text-black dark:text-neutral-400 dark:hover:text-white">
                            <svg class="size-3.5 transition-transform" :class="openAdvancedFrontend && 'rotate-90'" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <span>Advanced Build & Output Settings (Optional)</span>
                        </button>
                        <div x-show="openAdvancedFrontend" x-cloak class="mt-3 grid gap-4 rounded-md border border-neutral-200 bg-neutral-50 p-3 sm:grid-cols-2 dark:border-neutral-800 dark:bg-neutral-950/40">
                            <div>
                                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Build Command</label>
                                <input type="text" wire:model="frontend_build_command" placeholder="npm run build"
                                    class="w-full rounded border border-neutral-300 bg-white px-2.5 py-1.5 text-xs dark:border-neutral-700 dark:bg-neutral-900 dark:text-white font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Install Command</label>
                                <input type="text" wire:model="frontend_install_command" placeholder="npm install"
                                    class="w-full rounded border border-neutral-300 bg-white px-2.5 py-1.5 text-xs dark:border-neutral-700 dark:bg-neutral-900 dark:text-white font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Output Directory</label>
                                <input type="text" wire:model="frontend_publish_directory" placeholder="/dist or /build"
                                    class="w-full rounded border border-neutral-300 bg-white px-2.5 py-1.5 text-xs dark:border-neutral-700 dark:bg-neutral-900 dark:text-white font-mono" />
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
                            <span class="rounded bg-neutral-200 px-1.5 py-0.5 text-[10px] font-bold text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200 uppercase tracking-wider">Backend</span>
                            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">Backend Service Configuration</h2>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Configure runtime, API entrypoint, and server port.</p>
                    </div>
                </div>
                <div class="application-settings-section-body space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Runtime / Framework Preset
                            </label>
                            <select wire:model.live="backend_preset"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white">
                                <option value="express">Node.js / Express / NestJS</option>
                                <option value="fastapi">Python / FastAPI</option>
                                <option value="python">Python / Django / Flask</option>
                                <option value="go">Go / Golang</option>
                                <option value="laravel">PHP / Laravel</option>
                                <option value="dockerfile">Dockerfile</option>
                                <option value="custom">Custom Runtime</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Root Directory
                            </label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="backend_base_directory" placeholder="/backend or /server"
                                    class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white" />
                            </div>
                            <div class="mt-1 flex gap-1.5 text-[11px] text-neutral-500 dark:text-neutral-400">
                                <span>Quick paths:</span>
                                <button type="button" class="underline hover:text-black dark:hover:text-white" wire:click="$set('backend_base_directory', '/')">/</button>
                                <button type="button" class="underline hover:text-black dark:hover:text-white" wire:click="$set('backend_base_directory', '/backend')">/backend</button>
                                <button type="button" class="underline hover:text-black dark:hover:text-white" wire:click="$set('backend_base_directory', '/server')">/server</button>
                                <button type="button" class="underline hover:text-black dark:hover:text-white" wire:click="$set('backend_base_directory', '/api')">/api</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Application Name
                            </label>
                            <input type="text" wire:model="backend_name"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Port Exposed
                            </label>
                            <input type="number" wire:model="backend_port"
                                class="w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:border-white dark:focus:ring-white" />
                        </div>
                    </div>

                    {{-- Collapsible Advanced Backend Options --}}
                    <div class="pt-2">
                        <button type="button" @click="openAdvancedBackend = !openAdvancedBackend"
                            class="flex items-center gap-1.5 text-xs font-medium text-neutral-600 hover:text-black dark:text-neutral-400 dark:hover:text-white">
                            <svg class="size-3.5 transition-transform" :class="openAdvancedBackend && 'rotate-90'" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <span>Advanced Commands (Optional)</span>
                        </button>
                        <div x-show="openAdvancedBackend" x-cloak class="mt-3 grid gap-4 rounded-md border border-neutral-200 bg-neutral-50 p-3 sm:grid-cols-2 dark:border-neutral-800 dark:bg-neutral-950/40">
                            <div>
                                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Start Command</label>
                                <input type="text" wire:model="backend_start_command" placeholder="node server.js or uvicorn main:app"
                                    class="w-full rounded border border-neutral-300 bg-white px-2.5 py-1.5 text-xs dark:border-neutral-700 dark:bg-neutral-900 dark:text-white font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Build Command</label>
                                <input type="text" wire:model="backend_build_command" placeholder="npm run build or pip install -r requirements.txt"
                                    class="w-full rounded border border-neutral-300 bg-white px-2.5 py-1.5 text-xs dark:border-neutral-700 dark:bg-neutral-900 dark:text-white font-mono" />
                            </div>
                        </div>
                    </div>

                    @if ($deploy_mode === 'fullstack')
                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" id="auto_wire_env" wire:model="auto_wire_env"
                                class="rounded border-neutral-300 text-black focus:ring-black dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-white" />
                            <label for="auto_wire_env" class="text-xs font-medium text-neutral-800 dark:text-neutral-200 cursor-pointer">
                                Auto-wire API endpoints (inject <span class="font-mono text-[11px] text-neutral-900 dark:text-white font-bold">NEXT_PUBLIC_API_URL</span> / <span class="font-mono text-[11px] text-neutral-900 dark:text-white font-bold">VITE_API_URL</span> into frontend, and <span class="font-mono text-[11px] text-neutral-900 dark:text-white font-bold">FRONTEND_URL</span> into backend)
                            </label>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- Section 5: Environment Variables --}}
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-white">3. Environment Variables</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Add key-value secrets or paste your raw .env file.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" wire:click="openEnvModal"
                        class="button text-xs py-1 px-2.5 flex items-center gap-1.5 border border-neutral-300 bg-white text-neutral-800 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700">
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3.5A1.5 1.5 0 018.5 2h3.879a1.5 1.5 0 011.06.44l3.122 3.12A1.5 1.5 0 0117 6.622V12.5a1.5 1.5 0 01-1.5 1.5h-1v-3.379a3 3 0 00-.879-2.121L10.5 5.379A3 3 0 008.379 4.5H7v-1z" />
                            <path d="M4.5 6A1.5 1.5 0 003 7.5v9A1.5 1.5 0 004.5 18h7a1.5 1.5 0 001.5-1.5v-5.879a1.5 1.5 0 00-.44-1.06L9.44 6.44A1.5 1.5 0 008.379 6H4.5z" />
                        </svg>
                        Paste .env
                    </button>
                    <button type="button" wire:click="addEnvVar"
                        class="button text-xs py-1 px-2.5 flex items-center gap-1.5 border border-neutral-300 bg-white text-neutral-800 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700">
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Add Variable
                    </button>
                </div>
            </div>
            <div class="application-settings-section-body space-y-3">
                @if (empty($env_vars))
                    <div class="rounded-lg border border-dashed border-neutral-300 p-6 text-center dark:border-neutral-800">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">No environment variables configured yet.</p>
                        <button type="button" wire:click="addEnvVar" class="mt-2 text-xs font-medium text-neutral-900 underline dark:text-white">
                            + Add first variable
                        </button>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach ($env_vars as $index => $var)
                            <div class="flex items-center gap-2">
                                <input type="text" wire:model="env_vars.{{ $index }}.key" placeholder="KEY"
                                    class="w-1/3 rounded border border-neutral-300 bg-white px-2.5 py-1.5 text-xs font-mono uppercase text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white" />
                                <input type="text" wire:model="env_vars.{{ $index }}.value" placeholder="VALUE"
                                    class="flex-1 rounded border border-neutral-300 bg-white px-2.5 py-1.5 text-xs font-mono text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white" />
                                @if ($deploy_mode === 'fullstack')
                                    <select wire:model="env_vars.{{ $index }}.target"
                                        class="rounded border border-neutral-300 bg-white px-2 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                                        <option value="both">Both</option>
                                        <option value="frontend">Frontend</option>
                                        <option value="backend">Backend</option>
                                    </select>
                                @endif
                                <button type="button" wire:click="removeEnvVar({{ $index }})"
                                    class="rounded p-1.5 text-neutral-400 hover:bg-neutral-100 hover:text-red-500 dark:hover:bg-neutral-800">
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Section 6: Action Footer --}}
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-neutral-50 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-800 dark:bg-neutral-900/60">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="deploy_now" wire:model="deploy_now"
                    class="rounded border-neutral-300 text-black focus:ring-black dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-white" />
                <label for="deploy_now" class="text-sm font-medium text-neutral-900 dark:text-white cursor-pointer">
                    Deploy immediately upon creation
                </label>
            </div>

            <button type="submit"
                class="button flex items-center justify-center gap-2 rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-black shadow-xs transition-all hover:bg-neutral-200 active:scale-98 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 19.7778H22L12 2Z" />
                </svg>
                Deploy Project
            </button>
        </div>
    </form>

    {{-- Paste .env Modal --}}
    @if ($show_env_modal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div class="w-full max-w-lg rounded-xl border border-neutral-300 bg-white p-6 shadow-xl dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between pb-3 border-b border-neutral-200 dark:border-neutral-800">
                    <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Import Environment Variables</h3>
                    <button type="button" wire:click="closeEnvModal" class="text-neutral-400 hover:text-black dark:hover:text-white">
                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 001.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>
                <p class="mt-3 text-xs text-neutral-500 dark:text-neutral-400">
                    Paste the contents of your <span class="font-mono text-neutral-800 dark:text-neutral-200">.env</span> file below. Comments and quotes are automatically parsed.
                </p>
                <textarea wire:model="raw_env_content" rows="8" placeholder="PORT=3000&#10;DATABASE_URL=postgres://...&#10;API_SECRET=my-secret-key"
                    class="mt-3 w-full rounded-md border border-neutral-300 bg-neutral-50 p-3 text-xs font-mono text-neutral-900 focus:border-neutral-900 focus:outline-none dark:border-neutral-700 dark:bg-neutral-950 dark:text-white dark:focus:border-white"></textarea>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" wire:click="closeEnvModal"
                        class="button border border-neutral-300 px-3 py-1.5 text-xs text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                        Cancel
                    </button>
                    <button type="button" wire:click="importRawEnv"
                        class="button rounded bg-black px-3 py-1.5 text-xs font-semibold text-white hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                        Import Variables
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
