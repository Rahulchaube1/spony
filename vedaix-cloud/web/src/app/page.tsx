'use client';

import React, { useState, useEffect } from 'react';
import {
  IconWorld,
  IconGitBranch,
  IconClock,
  IconCheck,
  IconAlertTriangle,
  IconExternalLink,
  IconCopy,
  IconTerminal2,
  IconActivity,
  IconLayersSubtract,
  IconSettings,
  IconDatabase,
  IconCpu,
  IconBolt,
  IconSearch,
  IconBell,
  IconChevronDown,
  IconPlayerPlay,
  IconEye,
  IconEyeOff,
  IconPlus,
  IconShieldCheck,
  IconServer,
  IconArrowUpRight,
  IconChartBar,
  IconSun,
  IconMoon,
  IconGitCommit,
  IconFolder,
  IconFileCode,
  IconNetwork,
  IconRefresh,
} from '@tabler/icons-react';

interface Deployment {
  id: string;
  project: string;
  branch: string;
  commit: string;
  commitMsg: string;
  author: string;
  status: 'ready' | 'building' | 'error';
  environment: 'Production' | 'Preview';
  url: string;
  duration: string;
  age: string;
}

export default function VedaixPlatform() {
  const [mounted, setMounted] = useState(false);
  const [activeTab, setActiveTab] = useState<
    'overview' | 'deployments' | 'logs' | 'analytics' | 'edge' | 'storage' | 'env' | 'domains' | 'settings'
  >('overview');
  const [copied, setCopied] = useState(false);
  const [commandOpen, setCommandOpen] = useState(false);
  const [isDeploying, setIsDeploying] = useState(false);
  const [isDark, setIsDark] = useState(true);

  // Live Go and Rust engine states
  const [goStatus, setGoStatus] = useState<any>(null);
  const [rustMetrics, setRustMetrics] = useState<any>(null);
  const [edgeExecutionResult, setEdgeExecutionResult] = useState<any>(null);
  const [testingEdge, setTestingEdge] = useState(false);

  // Environment variables state
  const [envVars, setEnvVars] = useState([
    { key: 'DATABASE_URL', value: 'postgresql://coolify:password@127.0.0.1:5432/coolify', show: false, prod: true, prev: true, dev: true },
    { key: 'REDIS_URL', value: 'redis://127.0.0.1:6379', show: false, prod: true, prev: true, dev: true },
    { key: 'VEDAIX_EDGE_SECRET', value: 'vx_sec_991840182740192841', show: false, prod: true, prev: true, dev: false },
    { key: 'NEXT_PUBLIC_API_URL', value: 'https://api.vedaix.app', show: true, prod: true, prev: true, dev: true },
    { key: 'RUST_DISPATCH_TIMEOUT', value: '500ms', show: true, prod: true, prev: true, dev: false },
  ]);
  const [newKey, setNewKey] = useState('');
  const [newVal, setNewVal] = useState('');

  // Deployments state
  const [deployments, setDeployments] = useState<Deployment[]>([
    {
      id: 'dpl_prod_9921',
      project: 'vedaix-cloud-platform',
      branch: 'main',
      commit: '7f9a2c1',
      commitMsg: 'feat(edge): optimize streaming hydration & sub-ms route dispatch',
      author: 'Rahul Chaube',
      status: 'ready',
      environment: 'Production',
      url: 'https://vedaix.app',
      duration: '13.5s',
      age: '12m ago',
    },
    {
      id: 'dpl_prev_8819',
      project: 'vedaix-cloud-platform',
      branch: 'feat/turbocache-l2',
      commit: '4a1b89e',
      commitMsg: 'perf(runner): implement Go native worker pool with zero copy buffer',
      author: 'Core Platform',
      status: 'ready',
      environment: 'Preview',
      url: 'https://vedaix-feat-turbocache.vedaix.app',
      duration: '9.2s',
      age: '2h ago',
    },
    {
      id: 'dpl_prev_7714',
      project: 'vedaix-cloud-platform',
      branch: 'refactor/rust-wasm-dispatcher',
      commit: '2b91c0f',
      commitMsg: 'chore(edge): update Rust 1.99 Anycast routing table and SIMD parsers',
      author: 'Alexandre P.',
      status: 'ready',
      environment: 'Preview',
      url: 'https://vedaix-rust-wasm.vedaix.app',
      duration: '8.4s',
      age: '5h ago',
    },
  ]);

  const [activeLogStep, setActiveLogStep] = useState<number | null>(2);

  // Mount effect to guarantee flawless client hydration
  useEffect(() => {
    setMounted(true);
  }, []);

  // Sync Dark/Light theme class on documentElement
  useEffect(() => {
    if (!mounted) return;
    const root = document.documentElement;
    if (isDark) {
      root.classList.add('dark');
    } else {
      root.classList.remove('dark');
    }
  }, [isDark, mounted]);

  // Poll Go and Rust engines via Next.js internal API routes
  useEffect(() => {
    if (!mounted) return;

    const fetchEngines = async () => {
      try {
        const goRes = await fetch('/api/runner/status');
        if (goRes.ok) setGoStatus(await goRes.json());
      } catch (e) {}

      try {
        const rustRes = await fetch('/api/edge/metrics');
        if (rustRes.ok) setRustMetrics(await rustRes.json());
      } catch (e) {}
    };

    fetchEngines();
    const interval = setInterval(fetchEngines, 4000);
    return () => clearInterval(interval);
  }, [mounted]);

  // Keyboard shortcut for Command Palette (⌘K or Ctrl+K)
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        setCommandOpen((prev) => !prev);
      }
      if (e.key === 'Escape') {
        setCommandOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  const copyUrl = (text: string) => {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(text);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  const triggerGoBuild = async () => {
    setIsDeploying(true);
    try {
      const res = await fetch('/api/builds/trigger', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          project: 'vedaix-cloud-platform',
          branch: 'main',
          commit: Math.random().toString(16).substring(2, 9),
        }),
      });
      if (res.ok) {
        const data = await res.json();
        const newDpl: Deployment = {
          id: data.id,
          project: data.project || 'vedaix-cloud-platform',
          branch: data.branch || 'main',
          commit: data.commit || '4b910ca',
          commitMsg: data.commit_msg || 'Instant trigger via Vedaix Native Runner',
          author: 'You',
          status: 'ready',
          environment: 'Production',
          url: data.url || 'https://vedaix.app',
          duration: data.duration || '5.4s',
          age: 'Just now',
        };
        setDeployments((prev) => [newDpl, ...prev]);
        setActiveTab('deployments');
      }
    } catch (e) {
      console.error(e);
    } finally {
      setIsDeploying(false);
    }
  };

  const testRustEdge = async () => {
    setTestingEdge(true);
    try {
      const res = await fetch('/api/edge/execute', { method: 'POST' });
      if (res.ok) {
        setEdgeExecutionResult(await res.json());
      }
    } catch (e) {
      console.error(e);
    } finally {
      setTestingEdge(false);
    }
  };

  return (
    <div className="min-h-screen bg-background text-foreground font-sans transition-colors duration-150">
      {/* ================= INDUSTRIAL HEADER ================= */}
      <header className="border-b border-border bg-card">
        <div className="mx-auto flex h-12 max-w-7xl items-center justify-between px-4 sm:px-6">
          <div className="flex items-center gap-3">
            {/* Vedaix Logo & Brand */}
            <a href="/" className="flex items-center gap-2 font-mono text-sm font-semibold tracking-tight">
              <img src="/vedaix-logo.png" alt="Vedaix" className="h-6 w-auto object-contain" />
              <span>VEDAIX</span>
            </a>

            <span className="text-muted-foreground text-xs font-mono">/</span>

            {/* Scope / Workspace Switcher */}
            <div className="flex items-center gap-1.5 border border-border bg-muted px-2 py-0.5 text-xs font-mono">
              <span className="size-1.5 bg-foreground"></span>
              <span>vedaix-team</span>
              <IconChevronDown className="size-3 text-muted-foreground" />
            </div>

            <span className="text-muted-foreground text-xs font-mono">/</span>

            {/* Project Pill */}
            <span className="text-xs font-mono text-foreground font-medium">vedaix-cloud-platform</span>
          </div>

          <div className="flex items-center gap-2">
            {/* Command Palette Trigger */}
            <button
              onClick={() => setCommandOpen(true)}
              className="hidden sm:flex items-center gap-2 border border-border bg-muted px-2.5 py-1 text-xs text-muted-foreground hover:border-foreground hover:text-foreground transition-colors font-mono"
            >
              <IconSearch className="size-3.5" />
              <span>Search commands, logs, vars...</span>
              <kbd className="border border-border bg-card px-1 py-0.5 text-[10px]">⌘K</kbd>
            </button>

            {/* Theme Toggle Button */}
            <button
              onClick={() => setIsDark(!isDark)}
              title={isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme'}
              className="flex size-7 items-center justify-center border border-border bg-muted text-muted-foreground hover:text-foreground hover:border-foreground transition-colors"
            >
              {isDark ? <IconSun className="size-3.5" /> : <IconMoon className="size-3.5" />}
            </button>

            {/* Notifications */}
            <button className="flex size-7 items-center justify-center border border-border bg-muted text-muted-foreground hover:text-foreground hover:border-foreground transition-colors">
              <IconBell className="size-3.5" />
            </button>

            {/* User Avatar */}
            <div className="flex size-7 items-center justify-center border border-border bg-primary text-primary-foreground font-mono text-xs font-bold">
              VX
            </div>
          </div>
        </div>

        {/* ================= SECONDARY SYSTEM NAVIGATION ================= */}
        <div className="mx-auto max-w-7xl px-4 sm:px-6">
          <nav className="flex space-x-1 overflow-x-auto text-xs font-mono scrollbar-none border-t border-border">
            {[
              { id: 'overview', label: 'Overview', icon: IconLayersSubtract },
              { id: 'deployments', label: 'Deployments', icon: IconGitBranch },
              { id: 'logs', label: 'Build Logs', icon: IconTerminal2 },
              { id: 'analytics', label: 'Telemetry & Web Vitals', icon: IconActivity },
              { id: 'edge', label: 'Edge Compute (Rust & Go)', icon: IconBolt },
              { id: 'storage', label: 'Storage & DB', icon: IconDatabase },
              { id: 'env', label: 'Env Variables', icon: IconFileCode },
              { id: 'domains', label: 'Domains & SSL', icon: IconWorld },
              { id: 'settings', label: 'Project Settings', icon: IconSettings },
            ].map((tab) => {
              const Icon = tab.icon;
              const isActive = activeTab === tab.id;
              return (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id as any)}
                  className={`flex items-center gap-1.5 border-b-2 px-3 py-2.5 whitespace-nowrap transition-colors ${
                    isActive
                      ? 'border-primary text-foreground font-semibold bg-muted/40'
                      : 'border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/20'
                  }`}
                >
                  <Icon className="size-3.5" />
                  <span>{tab.label}</span>
                </button>
              );
            })}
          </nav>
        </div>
      </header>

      {/* ================= MAIN WORKSPACE ================= */}
      <main className="mx-auto max-w-7xl px-4 py-6 sm:px-6">
        {/* ================= OVERVIEW TAB ================= */}
        {activeTab === 'overview' && (
          <div className="space-y-6">
            {/* Production Deployment Hero Block */}
            <div className="border border-border bg-card p-6">
              <div className="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div className="space-y-3">
                  <div className="flex items-center gap-2">
                    <span className="flex items-center gap-1.5 border border-border bg-muted px-2 py-0.5 text-xs font-mono font-medium">
                      <span className="size-1.5 bg-foreground"></span>
                      Production Target
                    </span>
                    <span className="text-xs text-muted-foreground font-mono">•</span>
                    <span className="text-xs text-muted-foreground font-mono">Status: Ready (200 OK)</span>
                  </div>

                  <div>
                    <h1 className="text-xl font-mono font-bold tracking-tight text-foreground flex items-center gap-2">
                      <span>vedaix.app</span>
                      <a href="https://vedaix.app" target="_blank" rel="noreferrer" className="text-muted-foreground hover:text-foreground">
                        <IconExternalLink className="size-4" />
                      </a>
                    </h1>
                    <p className="mt-1 text-xs text-muted-foreground font-mono">
                      feat(edge): optimize streaming hydration &amp; sub-ms route dispatch
                    </p>
                  </div>

                  <div className="flex flex-wrap items-center gap-4 text-xs font-mono text-muted-foreground pt-1 border-t border-border">
                    <div className="flex items-center gap-1.5">
                      <IconGitCommit className="size-3.5" />
                      <span className="text-foreground">7f9a2c1</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <IconGitBranch className="size-3.5" />
                      <span className="text-foreground">refs/heads/main</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <IconClock className="size-3.5" />
                      <span>13.5s compile time</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <IconCpu className="size-3.5" />
                      <span>Go 1.27 + Rust Native</span>
                    </div>
                  </div>
                </div>

                {/* Actions */}
                <div className="flex flex-wrap items-center gap-2">
                  <button
                    onClick={() => copyUrl('https://vedaix.app')}
                    className="flex items-center gap-1.5 border border-border bg-muted px-3 py-1.5 text-xs font-mono text-foreground hover:border-foreground transition-colors"
                  >
                    <IconCopy className="size-3.5" />
                    <span>{copied ? 'Copied' : 'Copy FQDN'}</span>
                  </button>

                  <button
                    onClick={triggerGoBuild}
                    disabled={isDeploying}
                    className="flex items-center gap-1.5 border border-primary bg-primary px-3 py-1.5 text-xs font-mono font-semibold text-primary-foreground hover:opacity-90 disabled:opacity-50 transition-opacity"
                  >
                    <IconPlayerPlay className={`size-3.5 ${isDeploying ? 'animate-spin' : ''}`} />
                    <span>{isDeploying ? 'Building Native Exec...' : 'Instant Redeploy'}</span>
                  </button>
                </div>
              </div>

              {/* Live Preview Diagnostic Terminal Frame */}
              <div className="mt-6 border border-border bg-background p-4">
                <div className="mb-2 flex items-center justify-between border-b border-border pb-2 text-[11px] font-mono text-muted-foreground">
                  <span className="flex items-center gap-2 text-foreground">
                    <span className="size-1.5 bg-foreground"></span>
                    <span>EDGE RUNTIME ACTIVE: anycast://iad1.vedaix.internal</span>
                  </span>
                  <span>HTTP/2 • TLS 1.3 • Brotli</span>
                </div>
                <div className="flex h-28 items-center justify-center border border-border bg-muted/20 text-center font-mono">
                  <div>
                    <p className="text-xs font-bold text-foreground">VEDAIX CLOUD PLATFORM CORE</p>
                    <p className="text-[11px] text-muted-foreground mt-0.5">
                      Zero Cold Starts • Multi-Language Runtime (Node.js 24 + Go 1.27 + Rust 1.99)
                    </p>
                  </div>
                </div>
              </div>
            </div>

            {/* Industrial Metrics Table */}
            <div className="grid grid-cols-1 gap-px border border-border bg-border sm:grid-cols-2 lg:grid-cols-4">
              <div className="bg-card p-4">
                <div className="flex items-center justify-between text-xs font-mono text-muted-foreground">
                  <span>Rust Edge Latency</span>
                  <IconBolt className="size-3.5" />
                </div>
                <div className="mt-2 flex items-baseline gap-2">
                  <span className="text-xl font-bold font-mono text-foreground">
                    {rustMetrics ? `${rustMetrics.avg_latency_ms} ms` : '0.42 ms'}
                  </span>
                  <span className="text-[11px] font-mono text-muted-foreground">Sub-millisecond</span>
                </div>
                <p className="mt-1 text-[10px] font-mono text-muted-foreground">320 Anycast Global PoPs</p>
              </div>

              <div className="bg-card p-4">
                <div className="flex items-center justify-between text-xs font-mono text-muted-foreground">
                  <span>Turbocache L2 Ratio</span>
                  <IconChartBar className="size-3.5" />
                </div>
                <div className="mt-2 flex items-baseline gap-2">
                  <span className="text-xl font-bold font-mono text-foreground">
                    {rustMetrics ? `${rustMetrics.cache_hit_ratio}%` : '98.6%'}
                  </span>
                  <span className="text-[11px] font-mono text-muted-foreground">+1.4%</span>
                </div>
                <p className="mt-1 text-[10px] font-mono text-muted-foreground">Fast SSD Cache Layer</p>
              </div>

              <div className="bg-card p-4">
                <div className="flex items-center justify-between text-xs font-mono text-muted-foreground">
                  <span>Go Native Runner</span>
                  <IconCpu className="size-3.5" />
                </div>
                <div className="mt-2 flex items-baseline gap-2">
                  <span className="text-xl font-bold font-mono text-foreground">
                    {goStatus ? goStatus.alloc_mb : '2.14 MB'}
                  </span>
                  <span className="text-[11px] font-mono text-muted-foreground">Zero Idle CPU</span>
                </div>
                <p className="mt-1 text-[10px] font-mono text-muted-foreground">Compiled ELF Binary</p>
              </div>

              <div className="bg-card p-4">
                <div className="flex items-center justify-between text-xs font-mono text-muted-foreground">
                  <span>Cryptographic TLS</span>
                  <IconShieldCheck className="size-3.5" />
                </div>
                <div className="mt-2 flex items-baseline gap-2">
                  <span className="text-xl font-bold font-mono text-foreground">TLSv1.3</span>
                  <span className="text-[11px] font-mono text-muted-foreground">0-RTT Active</span>
                </div>
                <p className="mt-1 text-[10px] font-mono text-muted-foreground">ACME Let's Encrypt</p>
              </div>
            </div>

            {/* Recent Deployments Table */}
            <div className="border border-border bg-card">
              <div className="flex items-center justify-between border-b border-border px-5 py-3">
                <div className="font-mono">
                  <h2 className="text-xs font-bold text-foreground">LATEST PIPELINE DEPLOYMENTS</h2>
                  <p className="text-[11px] text-muted-foreground">Continuous Git Integration</p>
                </div>
                <button
                  onClick={() => setActiveTab('deployments')}
                  className="text-xs font-mono text-muted-foreground hover:text-foreground"
                >
                  View All &rarr;
                </button>
              </div>

              <div className="divide-y divide-border font-mono text-xs">
                {deployments.map((d) => (
                  <div key={d.id} className="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-3 hover:bg-muted/30 transition-colors gap-2">
                    <div className="flex items-start gap-3">
                      <span className="size-2 bg-foreground mt-1.5 shrink-0"></span>
                      <div>
                        <div className="flex items-center gap-2">
                          <span className="font-semibold text-foreground">{d.url.replace('https://', '')}</span>
                          <span className="border border-border bg-muted px-1.5 py-0.5 text-[10px] text-muted-foreground">
                            {d.environment}
                          </span>
                        </div>
                        <p className="text-[11px] text-muted-foreground mt-0.5">
                          <span>{d.commit}</span> &bull; <span>{d.commitMsg}</span>
                        </p>
                      </div>
                    </div>

                    <div className="flex items-center gap-4 text-muted-foreground sm:justify-end text-[11px]">
                      <span>{d.duration}</span>
                      <span>{d.age}</span>
                      <button
                        onClick={() => setActiveTab('logs')}
                        className="border border-border px-2 py-0.5 text-foreground hover:border-foreground"
                      >
                        Inspect
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {/* ================= DEPLOYMENTS TAB ================= */}
        {activeTab === 'deployments' && (
          <div className="space-y-4 font-mono">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-base font-bold text-foreground">DEPLOYMENTS INVENTORY</h1>
                <p className="text-xs text-muted-foreground">Immutable release history with atomic routing</p>
              </div>
              <button
                onClick={triggerGoBuild}
                disabled={isDeploying}
                className="flex items-center gap-1.5 border border-primary bg-primary px-3 py-1.5 text-xs font-semibold text-primary-foreground hover:opacity-90"
              >
                <IconPlus className="size-3.5" />
                <span>Trigger Build</span>
              </button>
            </div>

            <div className="border border-border bg-card divide-y divide-border text-xs">
              {deployments.map((d) => (
                <div key={d.id} className="flex flex-col sm:flex-row sm:items-center justify-between p-4 hover:bg-muted/20 gap-3">
                  <div className="flex items-start gap-3">
                    <span className="size-2 bg-foreground mt-1.5"></span>
                    <div>
                      <div className="flex items-center gap-2">
                        <span className="font-semibold text-foreground">{d.url.replace('https://', '')}</span>
                        <span className="border border-border bg-muted px-1.5 text-[10px] text-muted-foreground">
                          {d.environment}
                        </span>
                        <span className="border border-border bg-muted px-1.5 text-[10px] text-muted-foreground">
                          {d.branch}
                        </span>
                      </div>
                      <p className="text-muted-foreground text-[11px] mt-1">{d.commitMsg}</p>
                    </div>
                  </div>

                  <div className="flex items-center gap-4 text-muted-foreground text-[11px]">
                    <span className="text-foreground">{d.commit}</span>
                    <span>{d.duration}</span>
                    <span>{d.age}</span>
                    <button
                      onClick={() => setActiveTab('logs')}
                      className="border border-border px-2.5 py-1 text-foreground hover:border-foreground"
                    >
                      View Logs
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ================= BUILD LOGS TAB ================= */}
        {activeTab === 'logs' && (
          <div className="space-y-4 font-mono">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-base font-bold text-foreground">STREAMING COMPILATION TERMINAL</h1>
                <p className="text-xs text-muted-foreground">Go 1.27 Build Worker &bull; Native ELF Container Pipeline</p>
              </div>
              <span className="border border-border bg-muted px-2 py-0.5 text-xs text-foreground">
                BUILD FINISHED: 13.5s
              </span>
            </div>

            <div className="border border-border bg-card text-xs">
              <div className="flex items-center justify-between border-b border-border bg-muted/30 px-4 py-2">
                <span className="font-bold text-foreground">TARGET: dpl_prod_9921 (main@7f9a2c1)</span>
                <span className="text-muted-foreground">STDOUT / STDERR PIPELINE</span>
              </div>

              <div className="divide-y divide-border p-4 space-y-2">
                {/* Step 1 */}
                <div className="border border-border p-3">
                  <div
                    onClick={() => setActiveLogStep(activeLogStep === 1 ? null : 1)}
                    className="flex cursor-pointer items-center justify-between font-semibold"
                  >
                    <span className="flex items-center gap-2">
                      <IconCheck className="size-3.5 text-foreground" />
                      <span>STEP 1: INITIALIZE NATIVE WORKER CONTAINER</span>
                    </span>
                    <span className="text-muted-foreground">1.2s</span>
                  </div>
                  {activeLogStep === 1 && (
                    <div className="mt-2 space-y-1 border-t border-border pt-2 text-[11px] text-muted-foreground pl-4">
                      <p>[Vedaix Engine] Spawning Go/Rust native runner runtime v1.4.0...</p>
                      <p>[OK] Container isolation verified via Linux cgroups v2</p>
                      <p>[OK] Turbocache layer hit rate: 94.2%</p>
                    </div>
                  )}
                </div>

                {/* Step 2 */}
                <div className="border border-border p-3">
                  <div
                    onClick={() => setActiveLogStep(activeLogStep === 2 ? null : 2)}
                    className="flex cursor-pointer items-center justify-between font-semibold"
                  >
                    <span className="flex items-center gap-2">
                      <IconCheck className="size-3.5 text-foreground" />
                      <span>STEP 2: NEXT.JS APP ROUTER COMPILATION</span>
                    </span>
                    <span className="text-muted-foreground">9.4s</span>
                  </div>
                  {activeLogStep === 2 && (
                    <div className="mt-2 space-y-1 border-t border-border pt-2 text-[11px] text-foreground pl-4">
                      <p className="font-bold">▲ Next.js 15.5.29 TurboPack</p>
                      <p className="text-muted-foreground">  - Environment: .env.production.local loaded</p>
                      <p className="text-muted-foreground">  - Compiling / (Static Page)</p>
                      <p className="text-muted-foreground">  - Compiling /api/edge (Rust/WASM Edge Worker)</p>
                      <p>✓ Generated 18 static chunks in 3.1s</p>
                      <p>✓ Optimized 4 dynamic server routes with streaming RSC support</p>
                    </div>
                  )}
                </div>

                {/* Step 3 */}
                <div className="border border-border p-3">
                  <div
                    onClick={() => setActiveLogStep(activeLogStep === 3 ? null : 3)}
                    className="flex cursor-pointer items-center justify-between font-semibold"
                  >
                    <span className="flex items-center gap-2">
                      <IconCheck className="size-3.5 text-foreground" />
                      <span>STEP 3: ATOMIC ANYCAST EDGE PROPAGATION</span>
                    </span>
                    <span className="text-muted-foreground">2.1s</span>
                  </div>
                  {activeLogStep === 3 && (
                    <div className="mt-2 space-y-1 border-t border-border pt-2 text-[11px] text-muted-foreground pl-4">
                      <p>[Edge Dispatcher] Propagating routes to 320 global Edge nodes...</p>
                      <p>[OK] TLS certificate bound (Let's Encrypt Wildcard, auto-renewed)</p>
                      <p className="text-foreground font-bold">✓ Ready at https://vedaix.app (Duration: 13.5s)</p>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
        )}

        {/* ================= ANALYTICS & WEB VITALS TAB ================= */}
        {activeTab === 'analytics' && (
          <div className="space-y-6 font-mono">
            <div>
              <h1 className="text-base font-bold text-foreground">CORE WEB VITALS &amp; REAL USER MONITORING</h1>
              <p className="text-xs text-muted-foreground">Real-time performance measurements from global visitor sessions</p>
            </div>

            <div className="grid grid-cols-1 gap-px border border-border bg-border sm:grid-cols-2 lg:grid-cols-4">
              <div className="bg-card p-4">
                <span className="text-xs text-muted-foreground">LCP (Largest Contentful Paint)</span>
                <div className="mt-2 flex items-baseline gap-2">
                  <span className="text-xl font-bold text-foreground">0.82 s</span>
                  <span className="text-[11px] text-muted-foreground">99.1% Good</span>
                </div>
                <div className="mt-2 h-1 w-full bg-muted">
                  <div className="h-full bg-foreground" style={{ width: '99.1%' }}></div>
                </div>
                <p className="mt-1 text-[10px] text-muted-foreground">Target &lt; 2.5s</p>
              </div>

              <div className="bg-card p-4">
                <span className="text-xs text-muted-foreground">INP (Interaction to Next Paint)</span>
                <div className="mt-2 flex items-baseline gap-2">
                  <span className="text-xl font-bold text-foreground">18 ms</span>
                  <span className="text-[11px] text-muted-foreground">99.8% Good</span>
                </div>
                <div className="mt-2 h-1 w-full bg-muted">
                  <div className="h-full bg-foreground" style={{ width: '99.8%' }}></div>
                </div>
                <p className="mt-1 text-[10px] text-muted-foreground">Target &lt; 200ms</p>
              </div>

              <div className="bg-card p-4">
                <span className="text-xs text-muted-foreground">CLS (Cumulative Layout Shift)</span>
                <div className="mt-2 flex items-baseline gap-2">
                  <span className="text-xl font-bold text-foreground">0.002</span>
                  <span className="text-[11px] text-muted-foreground">100% Good</span>
                </div>
                <div className="mt-2 h-1 w-full bg-muted">
                  <div className="h-full bg-foreground" style={{ width: '100%' }}></div>
                </div>
                <p className="mt-1 text-[10px] text-muted-foreground">Target &lt; 0.1</p>
              </div>

              <div className="bg-card p-4">
                <span className="text-xs text-muted-foreground">TTFB (Time to First Byte)</span>
                <div className="mt-2 flex items-baseline gap-2">
                  <span className="text-xl font-bold text-foreground">42 ms</span>
                  <span className="text-[11px] text-muted-foreground">99.4% Good</span>
                </div>
                <div className="mt-2 h-1 w-full bg-muted">
                  <div className="h-full bg-foreground" style={{ width: '99.4%' }}></div>
                </div>
                <p className="mt-1 text-[10px] text-muted-foreground">Anycast Cached</p>
              </div>
            </div>

            {/* Request Histogram */}
            <div className="border border-border bg-card p-6">
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h3 className="text-xs font-bold text-foreground">REQUEST VOLUME (LAST 24 HOURS)</h3>
                  <p className="text-[11px] text-muted-foreground">1,421,809 Total Invocations &bull; 99.98% Status 2xx</p>
                </div>
                <span className="text-xs text-muted-foreground">ACTIVE POPS: 320</span>
              </div>

              <div className="flex items-end gap-1 h-32 pt-4">
                {[30, 42, 50, 68, 75, 80, 95, 120, 110, 135, 140, 160, 150, 175, 190, 185, 210, 225, 215, 240, 260, 250, 275, 290].map((h, i) => (
                  <div key={i} className="flex-1 flex flex-col justify-end items-center">
                    <div
                      className="w-full bg-foreground/20 hover:bg-foreground transition-colors"
                      style={{ height: `${(h / 290) * 100}%` }}
                    ></div>
                  </div>
                ))}
              </div>
              <div className="flex justify-between text-[10px] text-muted-foreground mt-2 border-t border-border pt-1">
                <span>00:00 UTC</span>
                <span>06:00 UTC</span>
                <span>12:00 UTC</span>
                <span>18:00 UTC</span>
                <span>CURRENT</span>
              </div>
            </div>
          </div>
        )}

        {/* ================= EDGE FUNCTIONS TAB ================= */}
        {activeTab === 'edge' && (
          <div className="space-y-6 font-mono">
            <div>
              <h1 className="text-base font-bold text-foreground">EDGE COMPUTE &amp; RUST DISPATCHER</h1>
              <p className="text-xs text-muted-foreground">Sub-millisecond microsecond execution running on native port 9002</p>
            </div>

            {/* Live Interactive Edge Tester */}
            <div className="border border-border bg-card p-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-border pb-4">
                <div>
                  <h3 className="text-xs font-bold text-foreground flex items-center gap-2">
                    <IconBolt className="size-4" />
                    <span>RUST 1.99 NATIVE EDGE DISPATCHER (PORT 9002)</span>
                  </h3>
                  <p className="text-[11px] text-muted-foreground mt-0.5">Executes on-demand without process cold-start latency</p>
                </div>
                <button
                  onClick={testRustEdge}
                  disabled={testingEdge}
                  className="flex items-center gap-2 border border-primary bg-primary px-3 py-1.5 text-xs font-bold text-primary-foreground hover:opacity-90 transition"
                >
                  <IconPlayerPlay className={`size-3.5 ${testingEdge ? 'animate-spin' : ''}`} />
                  <span>Execute Rust Benchmark</span>
                </button>
              </div>

              {edgeExecutionResult ? (
                <div className="mt-4 border border-border bg-background p-4 text-xs">
                  <div className="flex items-center justify-between border-b border-border pb-2 text-foreground font-bold">
                    <span>✓ EXECUTION STATUS: 200 OK</span>
                    <span>LATENCY: {edgeExecutionResult.duration_us || 12} µs</span>
                  </div>
                  <pre className="mt-3 text-muted-foreground">{JSON.stringify(edgeExecutionResult, null, 2)}</pre>
                </div>
              ) : (
                <div className="mt-4 p-4 text-center text-xs text-muted-foreground border border-dashed border-border">
                  Click "Execute Rust Benchmark" to fire an asynchronous request to the running Rust daemon.
                </div>
              )}
            </div>

            {/* Runtimes Table */}
            <div className="border border-border bg-card divide-y divide-border text-xs">
              <div className="p-3 flex items-center justify-between text-muted-foreground border-b border-border">
                <span>FUNCTION ROUTE</span>
                <span>ENGINE RUNTIME</span>
                <span>COLD START</span>
                <span>DEPLOYED REGION</span>
              </div>
              {[
                { route: '/api/edge/execute', runtime: 'Rust 1.99 Native Worker', cold: '0.00 ms', region: 'Global Anycast' },
                { route: '/api/builds/trigger', runtime: 'Go 1.27 Build Worker', cold: '0.80 ms', region: 'iad1' },
                { route: '/api/analytics/stream', runtime: 'Node.js 24 Edge Stream', cold: '1.20 ms', region: 'Global Anycast' },
              ].map((fn, idx) => (
                <div key={idx} className="p-3 flex items-center justify-between">
                  <span className="font-bold text-foreground">{fn.route}</span>
                  <span className="text-muted-foreground">{fn.runtime}</span>
                  <span className="text-foreground">{fn.cold}</span>
                  <span className="text-muted-foreground">{fn.region}</span>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ================= STORAGE TAB ================= */}
        {activeTab === 'storage' && (
          <div className="space-y-6 font-mono">
            <div>
              <h1 className="text-base font-bold text-foreground">STORAGE &amp; DATABASE ENGINES</h1>
              <p className="text-xs text-muted-foreground">PostgreSQL 15 and Redis 7.2 instances managed natively on this machine</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="border border-border bg-card p-5 space-y-3">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <IconDatabase className="size-4" />
                    <span className="text-xs font-bold text-foreground">PostgreSQL 15</span>
                  </div>
                  <span className="border border-border bg-muted px-1.5 text-[10px]">127.0.0.1:5432</span>
                </div>
                <div className="border-t border-border pt-2 text-xs space-y-1 text-muted-foreground">
                  <p>Database: <span className="text-foreground">coolify</span></p>
                  <p>User: <span className="text-foreground">coolify</span></p>
                  <p>Pool Size: <span className="text-foreground">20 Max Connections</span></p>
                </div>
              </div>

              <div className="border border-border bg-card p-5 space-y-3">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <IconBolt className="size-4" />
                    <span className="text-xs font-bold text-foreground">Redis 7.2 (jemalloc)</span>
                  </div>
                  <span className="border border-border bg-muted px-1.5 text-[10px]">127.0.0.1:6379</span>
                </div>
                <div className="border-t border-border pt-2 text-xs space-y-1 text-muted-foreground">
                  <p>Engine: <span className="text-foreground">In-Memory Cache &amp; Queue</span></p>
                  <p>Memory: <span className="text-foreground">1.82 MB active</span></p>
                  <p>Status: <span className="text-foreground">Daemon Running</span></p>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* ================= ENV VARIABLES TAB ================= */}
        {activeTab === 'env' && (
          <div className="space-y-6 font-mono">
            <div>
              <h1 className="text-base font-bold text-foreground">ENVIRONMENT SECRETS &amp; CONFIGURATION</h1>
              <p className="text-xs text-muted-foreground">Injected securely into Go runner pipelines and Node.js execution</p>
            </div>

            <div className="border border-border bg-card p-5 space-y-4">
              <div className="flex flex-col sm:flex-row gap-2">
                <input
                  type="text"
                  placeholder="KEY_NAME"
                  value={newKey}
                  onChange={(e) => setNewKey(e.target.value)}
                  className="flex-1 border border-border bg-background px-3 py-1.5 text-xs text-foreground placeholder-muted-foreground focus:outline-none focus:border-foreground"
                />
                <input
                  type="text"
                  placeholder="secret_value"
                  value={newVal}
                  onChange={(e) => setNewVal(e.target.value)}
                  className="flex-1 border border-border bg-background px-3 py-1.5 text-xs text-foreground placeholder-muted-foreground focus:outline-none focus:border-foreground"
                />
                <button
                  onClick={() => {
                    if (newKey) {
                      setEnvVars([...envVars, { key: newKey, value: newVal, show: false, prod: true, prev: true, dev: true }]);
                      setNewKey('');
                      setNewVal('');
                    }
                  }}
                  className="border border-primary bg-primary px-4 py-1.5 text-xs font-bold text-primary-foreground hover:opacity-90"
                >
                  Add Variable
                </button>
              </div>

              <div className="border border-border divide-y divide-border text-xs">
                {envVars.map((v, i) => (
                  <div key={i} className="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-card gap-2">
                    <div className="flex items-center gap-3">
                      <span className="font-bold text-foreground">{v.key}</span>
                      <button
                        onClick={() => {
                          const updated = [...envVars];
                          updated[i].show = !updated[i].show;
                          setEnvVars(updated);
                        }}
                        className="text-muted-foreground hover:text-foreground"
                      >
                        {v.show ? <IconEyeOff className="size-3.5" /> : <IconEye className="size-3.5" />}
                      </button>
                    </div>
                    <div className="flex items-center gap-4 text-muted-foreground text-[11px]">
                      <span>{v.show ? v.value : '••••••••••••••••••••••••••••'}</span>
                      <div className="flex gap-1">
                        {v.prod && <span className="border border-border px-1">prod</span>}
                        {v.prev && <span className="border border-border px-1">preview</span>}
                        {v.dev && <span className="border border-border px-1">dev</span>}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {/* ================= DOMAINS TAB ================= */}
        {activeTab === 'domains' && (
          <div className="space-y-6 font-mono">
            <div>
              <h1 className="text-base font-bold text-foreground">CUSTOM DOMAINS &amp; NETWORKING</h1>
              <p className="text-xs text-muted-foreground">Assign canonical FQDNs with automated ACME certificate issuance</p>
            </div>

            <div className="border border-border bg-card p-5 space-y-4 text-xs">
              <div className="flex items-center justify-between p-3 border border-border bg-background">
                <div className="flex items-center gap-3">
                  <IconWorld className="size-4" />
                  <div>
                    <span className="font-bold text-foreground">vedaix.app</span>
                    <p className="text-[11px] text-muted-foreground">Production Target &bull; Points to refs/heads/main</p>
                  </div>
                </div>
                <span className="border border-border bg-muted px-2 py-0.5 text-[10px]">
                  SSL VALID (Let's Encrypt 2048-bit)
                </span>
              </div>
            </div>
          </div>
        )}

        {/* ================= SETTINGS TAB ================= */}
        {activeTab === 'settings' && (
          <div className="space-y-6 font-mono text-xs">
            <div>
              <h1 className="text-base font-bold text-foreground">PROJECT CONFIGURATION</h1>
              <p className="text-muted-foreground">Framework presets, build commands, and container flags</p>
            </div>

            <div className="border border-border bg-card p-5 space-y-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="text-muted-foreground block mb-1">FRAMEWORK PRESET</label>
                  <input
                    type="text"
                    disabled
                    value="Next.js 15 (App Router)"
                    className="w-full border border-border bg-background px-3 py-1.5 text-foreground"
                  />
                </div>
                <div>
                  <label className="text-muted-foreground block mb-1">BUILD COMMAND</label>
                  <input
                    type="text"
                    disabled
                    value="next build"
                    className="w-full border border-border bg-background px-3 py-1.5 text-foreground"
                  />
                </div>
                <div>
                  <label className="text-muted-foreground block mb-1">OUTPUT DIRECTORY</label>
                  <input
                    type="text"
                    disabled
                    value=".next"
                    className="w-full border border-border bg-background px-3 py-1.5 text-foreground"
                  />
                </div>
                <div>
                  <label className="text-muted-foreground block mb-1">BUILD RUNNER SERVICE</label>
                  <input
                    type="text"
                    disabled
                    value="Go 1.27 + Rust 1.99 Native (Port 9001 / 9002)"
                    className="w-full border border-border bg-background px-3 py-1.5 text-foreground"
                  />
                </div>
              </div>
            </div>
          </div>
        )}
      </main>

      {/* ================= COMMAND PALETTE MODAL (⌘K) ================= */}
      {commandOpen && (
        <div className="fixed inset-0 z-50 flex items-start justify-center pt-24 bg-black/50 backdrop-blur-sm p-4 font-mono">
          <div className="w-full max-w-lg border border-border bg-card shadow-2xl">
            <div className="flex items-center border-b border-border px-3 py-2.5">
              <IconSearch className="size-4 text-muted-foreground mr-2" />
              <input
                type="text"
                autoFocus
                placeholder="Type a command or jump to section..."
                className="w-full bg-transparent text-xs text-foreground placeholder-muted-foreground focus:outline-none"
              />
              <kbd className="border border-border bg-muted px-1.5 text-[10px]">ESC</kbd>
            </div>
            <div className="p-2 space-y-1 text-xs">
              <button
                onClick={() => { setActiveTab('overview'); setCommandOpen(false); }}
                className="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted text-foreground"
              >
                <IconLayersSubtract className="size-4" /> Go to Project Overview
              </button>
              <button
                onClick={() => { setActiveTab('deployments'); setCommandOpen(false); }}
                className="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted text-foreground"
              >
                <IconGitBranch className="size-4" /> View Deployments
              </button>
              <button
                onClick={() => { setActiveTab('logs'); setCommandOpen(false); }}
                className="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted text-foreground"
              >
                <IconTerminal2 className="size-4" /> Open Build Logs
              </button>
              <button
                onClick={() => { setActiveTab('edge'); setCommandOpen(false); }}
                className="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted text-foreground"
              >
                <IconBolt className="size-4" /> Test Rust Edge Dispatcher
              </button>
              <button
                onClick={() => { triggerGoBuild(); setCommandOpen(false); }}
                className="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted font-bold text-foreground"
              >
                <IconPlayerPlay className="size-4" /> Trigger Native Go Deployment
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
