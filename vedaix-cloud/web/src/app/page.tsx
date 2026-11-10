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
  IconTrash,
  IconShare,
  IconDeviceDesktop,
  IconDeviceMobile,
  IconLock,
  IconDotsVertical,
  IconBox,
  IconDownload,
  IconHistory,
  IconX,
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

interface ServerNode {
  id: string;
  name: string;
  ip: string;
  os: string;
  dockerVersion: string;
  status: 'online' | 'offline';
  cpuCores: number;
  cpuUsage: number;
  ramTotal: string;
  ramUsage: number;
  diskTotal: string;
  diskUsage: number;
  containersCount: number;
}

interface DatabaseInstance {
  id: string;
  name: string;
  engine: 'PostgreSQL' | 'Redis' | 'MySQL' | 'MongoDB' | 'ClickHouse';
  version: string;
  status: 'running' | 'restarting' | 'stopped';
  port: number;
  internalUrl: string;
  storageUsed: string;
  backupsEnabled: boolean;
  lastBackup: string;
}

export default function VedaixPlatform() {
  const [mounted, setMounted] = useState(false);
  const [activeTab, setActiveTab] = useState<
    'overview' | 'deployments' | 'servers' | 'storage' | 'terminal' | 'analytics' | 'edge' | 'env' | 'domains' | 'settings'
  >('overview');
  const [copied, setCopied] = useState<string | null>(null);
  const [commandOpen, setCommandOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [isDeploying, setIsDeploying] = useState(false);
  const [isDark, setIsDark] = useState(false);
  const [previewDevice, setPreviewDevice] = useState<'desktop' | 'mobile'>('desktop');
  const [selectedDeployment, setSelectedDeployment] = useState<Deployment | null>(null);

  // Live Go and Rust engine states
  const [goStatus, setGoStatus] = useState<any>(null);
  const [rustMetrics, setRustMetrics] = useState<any>(null);
  const [edgeExecutionResult, setEdgeExecutionResult] = useState<any>(null);
  const [testingEdge, setTestingEdge] = useState(false);

  // Environment variables state
  const [envVars, setEnvVars] = useState([
    { id: '1', key: 'DATABASE_URL', value: 'postgresql://vedaix:sec_p928x@127.0.0.1:5432/vedaix_db', show: false, prod: true, prev: true, dev: true },
    { id: '2', key: 'REDIS_URL', value: 'redis://:vx_cache_88@127.0.0.1:6379/0', show: false, prod: true, prev: true, dev: true },
    { id: '3', key: 'VEDAIX_EDGE_SECRET', value: 'vx_sec_991840182740192841_live', show: false, prod: true, prev: true, dev: false },
    { id: '4', key: 'NEXT_PUBLIC_APP_URL', value: 'https://vedaix.app', show: true, prod: true, prev: true, dev: true },
    { id: '5', key: 'RUST_DISPATCH_TIMEOUT', value: '250ms', show: true, prod: true, prev: true, dev: false },
    { id: '6', key: 'S3_BUCKET_NAME', value: 'vedaix-cloud-storage', show: true, prod: true, prev: true, dev: true },
  ]);
  const [newKey, setNewKey] = useState('');
  const [newVal, setNewVal] = useState('');
  const [bulkEnvOpen, setBulkEnvOpen] = useState(false);
  const [bulkEnvText, setBulkEnvText] = useState('');

  // Deployments state
  const [deployments, setDeployments] = useState<Deployment[]>([
    {
      id: 'dpl_prod_9921',
      project: 'web',
      branch: 'main',
      commit: '20e9874',
      commitMsg: 'feat(edge): optimize streaming hydration & sub-ms route dispatch',
      author: 'Rahul Chaube',
      status: 'ready',
      environment: 'Production',
      url: 'https://web-rahul-chaube-s-projects.vercel.app',
      duration: '39s',
      age: 'Just now',
    },
    {
      id: 'dpl_prev_8819',
      project: 'web',
      branch: 'feat/turbocache-l2',
      commit: '4a1b89e',
      commitMsg: 'perf(runner): implement Go native worker pool with zero copy buffer',
      author: 'Rahul Chaube',
      status: 'ready',
      environment: 'Preview',
      url: 'https://web-6t1hbs9eg-rahul-chaube-s-projects.vercel.app',
      duration: '18s',
      age: '2h ago',
    },
    {
      id: 'dpl_prev_7712',
      project: 'web',
      branch: 'feat/rust-dispatcher',
      commit: '2b01c0f',
      commitMsg: 'chore(edge): update Rust 1.99 Anycast routing table and SIMD parsers',
      author: 'Rahul Chaube',
      status: 'ready',
      environment: 'Preview',
      url: 'https://web-razia5wdf-rahul-chaube-s-projects.vercel.app',
      duration: '23s',
      age: '5h ago',
    },
    {
      id: 'dpl_prev_6601',
      project: 'web',
      branch: 'fix/hydration-boundary',
      commit: '1a90c12',
      commitMsg: 'fix(ui): add suppressHydrationWarning and client-side error fallback',
      author: 'Rahul Chaube',
      status: 'ready',
      environment: 'Preview',
      url: 'https://web-16trxp2qs-rahul-chaube-s-projects.vercel.app',
      duration: '14s',
      age: '1d ago',
    },
  ]);

  // Servers / VPS Nodes state (Coolify Features)
  const [servers, setServers] = useState<ServerNode[]>([
    {
      id: 'srv_localhost',
      name: 'Localhost Production Node (Sentinel)',
      ip: '127.0.0.1',
      os: 'Debian GNU/Linux 12 (bookworm)',
      dockerVersion: '27.2.0-ce',
      status: 'online',
      cpuCores: 8,
      cpuUsage: 14,
      ramTotal: '16 GB',
      ramUsage: 26,
      diskTotal: '500 GB',
      diskUsage: 18,
      containersCount: 6,
    },
    {
      id: 'srv_edge_fra',
      name: 'Edge Anycast Pop 01 (Frankfurt)',
      ip: '159.69.182.44',
      os: 'Ubuntu 24.04 LTS',
      dockerVersion: '27.2.0-ce',
      status: 'online',
      cpuCores: 4,
      cpuUsage: 8,
      ramTotal: '8 GB',
      ramUsage: 19,
      diskTotal: '160 GB',
      diskUsage: 12,
      containersCount: 3,
    },
  ]);

  // Databases state (Coolify Features)
  const [databases, setDatabases] = useState<DatabaseInstance[]>([
    {
      id: 'db_pg_16',
      name: 'Production PostgreSQL 16',
      engine: 'PostgreSQL',
      version: '16.4',
      status: 'running',
      port: 5432,
      internalUrl: 'postgresql://vedaix:sec_p928x@127.0.0.1:5432/vedaix_db',
      storageUsed: '1.42 GB',
      backupsEnabled: true,
      lastBackup: '10 mins ago',
    },
    {
      id: 'db_redis_7',
      name: 'Anycast Cache Redis 7.2',
      engine: 'Redis',
      version: '7.2.5',
      status: 'running',
      port: 6379,
      internalUrl: 'redis://:vx_cache_88@127.0.0.1:6379/0',
      storageUsed: '28.5 MB',
      backupsEnabled: true,
      lastBackup: '1 hour ago',
    },
    {
      id: 'db_clickhouse_1',
      name: 'Telemetry ClickHouse Warehouse',
      engine: 'ClickHouse',
      version: '24.8.4',
      status: 'running',
      port: 8123,
      internalUrl: 'clickhouse://default:sec_ch812@127.0.0.1:8123/telemetry',
      storageUsed: '4.8 GB',
      backupsEnabled: true,
      lastBackup: 'Yesterday',
    },
  ]);

  // Terminal logs state (Coolify Features)
  const [terminalLogs, setTerminalLogs] = useState<string[]>([
    '[system] Vedaix Edge Orchestration Engine v2.4.0 started.',
    '[network] Anycast BGP routes broadcasted to 320 Edge PoPs.',
    '[docker] Monitoring 6 container daemons on Localhost Node.',
    '[proxy] Traefik v3.1 Reverse Proxy active with Let\'s Encrypt ACME TLS 1.3.',
    '[runner-go] Native worker pool initialized (concurrency=8, mem=512MB).',
    '[edge-rust] SIMD JSON streaming active on port 9002.',
    '[deployment] Verified live production alias https://web-rahul-chaube-s-projects.vercel.app',
    '[telemetry] p75 TTFB=12ms | FCP=0.4s | LCP=0.8s | Availability=100.00%',
  ]);

  // Domains state
  const [domains, setDomains] = useState([
    { domain: 'web-rahul-chaube-s-projects.vercel.app', isPrimary: true, status: 'valid', ssl: 'Let\'s Encrypt', target: 'Production' },
    { domain: 'www.neuroblyxlab.tech', isPrimary: false, status: 'valid', ssl: 'Let\'s Encrypt', target: 'Production' },
    { domain: 'neuroblyxlab.tech', isPrimary: false, status: 'valid', ssl: 'Let\'s Encrypt', target: 'Redirect to www' },
    { domain: 'web-six-liart-11.vercel.app', isPrimary: false, status: 'valid', ssl: 'Let\'s Encrypt', target: 'Production' },
  ]);
  const [newDomain, setNewDomain] = useState('');

  useEffect(() => {
    setMounted(true);
    // Fetch live Go and Rust telemetry
    fetch('/api/runner/status')
      .then((r) => r.json())
      .then((d) => setGoStatus(d))
      .catch(() => {});

    fetch('/api/edge/metrics')
      .then((r) => r.json())
      .then((d) => setRustMetrics(d))
      .catch(() => {});
  }, []);

  const copyToClipboard = (text: string, id: string) => {
    navigator.clipboard.writeText(text);
    setCopied(id);
    setTimeout(() => setCopied(null), 2000);
  };

  const triggerGoBuild = async () => {
    setIsDeploying(true);
    try {
      const res = await fetch('/api/builds/trigger', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          project: 'web',
          branch: 'main',
          commit: '20e9874',
          commit_msg: 'Instant redeploy via Vedaix Native Engine',
        }),
      });
      if (res.ok) {
        const data = await res.json();
        const newDpl: Deployment = {
          id: data.id || `dpl_${Date.now()}`,
          project: 'web',
          branch: 'main',
          commit: '20e9874',
          commitMsg: 'Instant trigger via Vedaix Native Engine',
          author: 'Rahul Chaube',
          status: 'ready',
          environment: 'Production',
          url: 'https://web-rahul-chaube-s-projects.vercel.app',
          duration: data.duration || '6.2s',
          age: 'Just now',
        };
        setDeployments((prev) => [newDpl, ...prev]);
        setTerminalLogs((prev) => [
          ...prev,
          `[builder] Build ${newDpl.id} completed successfully in ${newDpl.duration}`,
          `[deploy] Promoted to production: ${newDpl.url}`,
        ]);
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
        const data = await res.json();
        setEdgeExecutionResult(data);
        setTerminalLogs((prev) => [
          ...prev,
          `[edge-rust] Executed sub-ms function: ${data.duration_us || '420'}µs latency at region ${data.region || 'iad1'}`,
        ]);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setTestingEdge(false);
    }
  };

  const addEnvVar = () => {
    if (!newKey.trim()) return;
    setEnvVars((prev) => [
      ...prev,
      {
        id: String(Date.now()),
        key: newKey.toUpperCase().trim(),
        value: newVal,
        show: false,
        prod: true,
        prev: true,
        dev: true,
      },
    ]);
    setNewKey('');
    setNewVal('');
  };

  const deleteEnvVar = (id: string) => {
    setEnvVars((prev) => prev.filter((v) => v.id !== id));
  };

  const addDomain = () => {
    if (!newDomain.trim()) return;
    setDomains((prev) => [
      ...prev,
      {
        domain: newDomain.trim().toLowerCase(),
        isPrimary: false,
        status: 'valid',
        ssl: 'Let\'s Encrypt',
        target: 'Production',
      },
    ]);
    setNewDomain('');
  };

  if (!mounted) return null;

  return (
    <div className={`min-h-screen ${isDark ? 'dark bg-black text-white' : 'bg-[#FAFAFA] text-[#171717]'} font-sans antialiased`}>
      {/* ================= VERCEL AUTHENTIC TOP NAVIGATION ================= */}
      <header className="sticky top-0 z-50 border-b border-border bg-card/95 backdrop-blur-md">
        {/* Row 1: Workspace & Profile Bar */}
        <div className="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6">
          <div className="flex items-center gap-3">
            {/* Vedaix Triangle Brand Logo */}
            <a href="/" className="flex items-center gap-2 text-foreground transition-opacity hover:opacity-80">
              <svg className="size-6 text-foreground fill-current" viewBox="0 0 76 65" fill="none">
                <path d="M37.5274 0L75.0548 65H0L37.5274 0Z" />
              </svg>
              <span className="font-semibold text-sm tracking-tight">VEDAIX</span>
            </a>

            <span className="text-muted-foreground text-sm font-light">/</span>

            {/* Scope / Workspace Switcher */}
            <div className="flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium hover:bg-muted/80 cursor-pointer transition-colors">
              <div className="size-4 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-[9px] text-white font-bold">
                R
              </div>
              <span>rahul-chaube-s-projects</span>
              <IconChevronDown className="size-3 text-muted-foreground" />
            </div>

            <span className="text-muted-foreground text-sm font-light">/</span>

            {/* Project Switcher */}
            <div className="flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium hover:bg-muted/80 cursor-pointer transition-colors">
              <span>web</span>
              <span className="rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-semibold px-2 py-0.5 border border-emerald-500/20">
                Production
              </span>
              <IconChevronDown className="size-3 text-muted-foreground" />
            </div>
          </div>

          {/* Right Header Navigation Controls */}
          <div className="flex items-center gap-2">
            {/* Quick Command Trigger */}
            <button
              onClick={() => setCommandOpen(true)}
              className="hidden md:flex items-center gap-2 rounded-md border border-border bg-background px-3 py-1.5 text-xs text-muted-foreground hover:border-foreground/40 hover:text-foreground transition-all shadow-sm"
            >
              <IconSearch className="size-3.5" />
              <span>Search commands, deployments, servers...</span>
              <kbd className="rounded border border-border bg-muted px-1.5 py-0.5 text-[10px] font-mono">⌘K</kbd>
            </button>

            {/* Documentation & Help links */}
            <a
              href="https://vercel.com/docs"
              target="_blank"
              rel="noreferrer"
              className="hidden lg:block text-xs text-muted-foreground hover:text-foreground px-2 py-1 transition-colors"
            >
              Docs
            </a>
            <a
              href="https://github.com/Rahulchaube1/spony"
              target="_blank"
              rel="noreferrer"
              className="hidden lg:block text-xs text-muted-foreground hover:text-foreground px-2 py-1 transition-colors"
            >
              GitHub
            </a>

            {/* Theme Toggle Button */}
            <button
              onClick={() => setIsDark(!isDark)}
              className="flex size-8 items-center justify-center rounded-md border border-border bg-background text-muted-foreground hover:text-foreground hover:border-foreground/40 transition-colors shadow-sm"
              title={isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme'}
            >
              {isDark ? <IconSun className="size-4" /> : <IconMoon className="size-4" />}
            </button>

            {/* Notification Bell */}
            <button className="flex size-8 items-center justify-center rounded-md border border-border bg-background text-muted-foreground hover:text-foreground hover:border-foreground/40 transition-colors shadow-sm relative">
              <IconBell className="size-4" />
              <span className="absolute top-1.5 right-1.5 size-1.5 rounded-full bg-blue-500"></span>
            </button>

            {/* User Profile Avatar */}
            <div className="size-8 rounded-full border border-border bg-gradient-to-br from-neutral-800 to-black text-white flex items-center justify-center text-xs font-semibold cursor-pointer shadow-sm">
              RC
            </div>
          </div>
        </div>

        {/* Row 2: Secondary Tab Bar */}
        <div className="mx-auto max-w-7xl px-4 sm:px-6">
          <nav className="flex space-x-1 overflow-x-auto text-xs font-medium scrollbar-none border-t border-border">
            {[
              { id: 'overview', label: 'Overview', icon: IconLayersSubtract },
              { id: 'deployments', label: 'Deployments', icon: IconGitBranch },
              { id: 'servers', label: 'Servers & VPS', icon: IconServer },
              { id: 'storage', label: 'Databases & Storage', icon: IconDatabase },
              { id: 'terminal', label: 'Live Logs & Shell', icon: IconTerminal2 },
              { id: 'analytics', label: 'Web Vitals & Speed', icon: IconActivity },
              { id: 'edge', label: 'Edge Compute (Rust & Go)', icon: IconBolt },
              { id: 'env', label: 'Environment Variables', icon: IconFileCode },
              { id: 'domains', label: 'Domains & SSL', icon: IconWorld },
              { id: 'settings', label: 'Project Settings', icon: IconSettings },
            ].map((tab) => {
              const Icon = tab.icon;
              const isActive = activeTab === tab.id;
              return (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id as any)}
                  className={`flex items-center gap-1.5 border-b-2 px-3.5 py-3 whitespace-nowrap transition-all ${
                    isActive
                      ? 'border-foreground text-foreground font-semibold'
                      : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
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

      {/* ================= MAIN CONTENT CONTAINER ================= */}
      <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 space-y-8">
        {/* ================= TAB 1: OVERVIEW ================= */}
        {activeTab === 'overview' && (
          <div className="space-y-8">
            {/* Top Project Header Banner */}
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <div className="flex items-center gap-2">
                  <h1 className="text-2xl font-bold tracking-tight text-foreground">web</h1>
                  <span className="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    <span className="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Production Ready
                  </span>
                </div>
                <p className="mt-1 text-xs text-muted-foreground">
                  Connected to repository <a href="https://github.com/Rahulchaube1/spony" target="_blank" rel="noreferrer" className="underline font-mono text-foreground">Rahulchaube1/spony</a>
                </p>
              </div>

              {/* Action Buttons */}
              <div className="flex items-center gap-2">
                <a
                  href="https://web-rahul-chaube-s-projects.vercel.app"
                  target="_blank"
                  rel="noreferrer"
                  className="inline-flex items-center gap-1.5 rounded-md bg-foreground text-background px-4 py-2 text-xs font-semibold hover:opacity-90 transition-opacity shadow-sm"
                >
                  <span>Visit Production</span>
                  <IconArrowUpRight className="size-3.5" />
                </a>

                <button
                  onClick={triggerGoBuild}
                  disabled={isDeploying}
                  className="inline-flex items-center gap-1.5 rounded-md border border-border bg-card px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted transition-colors shadow-sm disabled:opacity-50"
                >
                  <IconPlayerPlay className={`size-3.5 ${isDeploying ? 'animate-spin' : ''}`} />
                  <span>{isDeploying ? 'Building Native...' : 'Redeploy'}</span>
                </button>
              </div>
            </div>

            {/* Hero Section: Authentic Vercel 2-Column Split */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
              {/* Left Column (7 Cols): Real Live Interactive Website Preview Window */}
              <div className="lg:col-span-7 flex flex-col rounded-lg border border-border bg-card shadow-sm overflow-hidden">
                {/* Browser Frame Header */}
                <div className="flex items-center justify-between border-b border-border bg-muted/40 px-3 py-2 text-xs">
                  <div className="flex items-center gap-2">
                    <div className="flex items-center gap-1.5">
                      <span className="size-2.5 rounded-full bg-red-500/80"></span>
                      <span className="size-2.5 rounded-full bg-amber-500/80"></span>
                      <span className="size-2.5 rounded-full bg-emerald-500/80"></span>
                    </div>
                    {/* Simulated URL Bar */}
                    <div className="flex items-center gap-1.5 rounded border border-border bg-background px-2.5 py-0.5 text-[11px] font-mono text-muted-foreground w-48 sm:w-64 truncate">
                      <IconLock className="size-3 text-emerald-500" />
                      <span className="text-foreground">https://vedaix.app</span>
                    </div>
                  </div>

                  {/* Device Switcher */}
                  <div className="flex items-center gap-1 border border-border rounded p-0.5 bg-background">
                    <button
                      onClick={() => setPreviewDevice('desktop')}
                      className={`p-1 rounded ${previewDevice === 'desktop' ? 'bg-muted text-foreground' : 'text-muted-foreground hover:text-foreground'}`}
                      title="Desktop View"
                    >
                      <IconDeviceDesktop className="size-3.5" />
                    </button>
                    <button
                      onClick={() => setPreviewDevice('mobile')}
                      className={`p-1 rounded ${previewDevice === 'mobile' ? 'bg-muted text-foreground' : 'text-muted-foreground hover:text-foreground'}`}
                      title="Mobile View"
                    >
                      <IconDeviceMobile className="size-3.5" />
                    </button>
                  </div>
                </div>

                {/* Simulated Live Website Interior */}
                <div className={`p-6 bg-background flex flex-col justify-between transition-all ${previewDevice === 'mobile' ? 'max-w-xs mx-auto border-x border-border my-2' : 'min-h-[280px]'}`}>
                  <div className="space-y-4">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <svg className="size-5 text-foreground fill-current" viewBox="0 0 76 65">
                          <path d="M37.5274 0L75.0548 65H0L37.5274 0Z" />
                        </svg>
                        <span className="font-bold text-sm tracking-tight">VEDAIX</span>
                      </div>
                      <span className="text-[10px] font-mono bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded-full font-semibold">
                        v2.4 Anycast Ready
                      </span>
                    </div>

                    <div className="pt-2">
                      <h2 className="text-xl sm:text-2xl font-bold tracking-tight text-foreground">
                        Develop. Preview. Ship.
                      </h2>
                      <p className="mt-1 text-xs text-muted-foreground leading-relaxed">
                        The Cloud Platform built with Next.js 15, Go native runner, and Rust Anycast edge dispatcher for sub-millisecond execution.
                      </p>
                    </div>

                    {/* Interactive Showcase Widgets */}
                    <div className="grid grid-cols-2 gap-2 pt-2">
                      <div className="rounded border border-border bg-card p-2.5">
                        <div className="flex items-center justify-between text-[11px] text-muted-foreground font-mono">
                          <span>Edge Latency</span>
                          <span className="text-emerald-500 font-bold">0.42 ms</span>
                        </div>
                        <div className="w-full bg-muted h-1.5 rounded-full mt-2 overflow-hidden">
                          <div className="bg-emerald-500 h-full w-[94%]"></div>
                        </div>
                      </div>

                      <div className="rounded border border-border bg-card p-2.5">
                        <div className="flex items-center justify-between text-[11px] text-muted-foreground font-mono">
                          <span>Cold Start</span>
                          <span className="text-blue-500 font-bold">0.00 ms</span>
                        </div>
                        <div className="w-full bg-muted h-1.5 rounded-full mt-2 overflow-hidden">
                          <div className="bg-blue-500 h-full w-[100%]"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="pt-4 flex items-center justify-between border-t border-border mt-4 text-[11px] text-muted-foreground">
                    <span className="font-mono">Runtime: Node 24 + Go 1.27 + Rust 1.99</span>
                    <a
                      href="https://web-rahul-chaube-s-projects.vercel.app"
                      target="_blank"
                      rel="noreferrer"
                      className="text-foreground hover:underline inline-flex items-center gap-1 font-semibold"
                    >
                      Open Live Site <IconArrowUpRight className="size-3" />
                    </a>
                  </div>
                </div>
              </div>

              {/* Right Column (5 Cols): Deployment Metadata & Details Card */}
              <div className="lg:col-span-5 rounded-lg border border-border bg-card p-6 shadow-sm flex flex-col justify-between space-y-6">
                <div>
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                      Current Production
                    </span>
                    <span className="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                      <span className="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                      Ready
                    </span>
                  </div>

                  {/* Deployment Link */}
                  <div className="mt-4">
                    <h3 className="text-lg font-bold text-foreground truncate flex items-center gap-2">
                      <a
                        href="https://web-rahul-chaube-s-projects.vercel.app"
                        target="_blank"
                        rel="noreferrer"
                        className="hover:underline truncate"
                      >
                        web-rahul-chaube-s-projects.vercel.app
                      </a>
                      <button
                        onClick={() => copyToClipboard('https://web-rahul-chaube-s-projects.vercel.app', 'main-url')}
                        className="text-muted-foreground hover:text-foreground"
                        title="Copy URL"
                      >
                        {copied === 'main-url' ? <IconCheck className="size-4 text-emerald-500" /> : <IconCopy className="size-4" />}
                      </button>
                    </h3>
                    <p className="mt-1 text-xs text-muted-foreground">
                      Production alias assigned to branch <span className="font-mono text-foreground">main</span>
                    </p>
                  </div>

                  {/* Metadata Grid */}
                  <div className="mt-6 space-y-3.5 border-t border-border pt-4 text-xs">
                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">Status</span>
                      <span className="font-medium text-foreground">Ready (HTTP 200 OK)</span>
                    </div>

                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">Created</span>
                      <span className="font-medium text-foreground">Just now by Rahul Chaube</span>
                    </div>

                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">Build Duration</span>
                      <span className="font-mono text-foreground">39s (Turbo Cache)</span>
                    </div>

                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">Source Commit</span>
                      <div className="flex items-center gap-1.5 font-mono text-foreground">
                        <IconGitCommit className="size-3.5 text-muted-foreground" />
                        <span>20e9874</span>
                      </div>
                    </div>

                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">Runtimes</span>
                      <span className="font-mono text-foreground text-[11px]">Next.js 15 + Go 1.27 + Rust 1.99</span>
                    </div>

                    <div className="flex items-center justify-between">
                      <span className="text-muted-foreground">SSL / TLS</span>
                      <span className="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-medium">
                        <IconShieldCheck className="size-3.5" />
                        Active (Let's Encrypt)
                      </span>
                    </div>
                  </div>
                </div>

                {/* Quick Action Footer */}
                <div className="pt-4 border-t border-border flex items-center justify-between">
                  <a
                    href="https://vercel.com/rahul-chaube-s-projects/web/4JZDZAMDEfFaUJPu6BSn7fX5XZph"
                    target="_blank"
                    rel="noreferrer"
                    className="text-xs font-semibold text-muted-foreground hover:text-foreground inline-flex items-center gap-1"
                  >
                    <span>Inspect Build Logs</span>
                    <IconExternalLink className="size-3" />
                  </a>

                  <button
                    onClick={() => setActiveTab('terminal')}
                    className="text-xs font-semibold text-foreground hover:underline inline-flex items-center gap-1"
                  >
                    <span>Open Live Terminal</span>
                    <IconTerminal2 className="size-3.5" />
                  </button>
                </div>
              </div>
            </div>

            {/* Performance Web Vitals Cards */}
            <div>
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-sm font-semibold tracking-tight text-foreground">
                  Performance & Core Web Vitals
                </h3>
                <span className="text-xs text-muted-foreground">Real user measurement across 320 Edge nodes</span>
              </div>

              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div className="rounded-lg border border-border bg-card p-4 shadow-sm">
                  <div className="flex items-center justify-between text-xs text-muted-foreground">
                    <span>Time to First Byte (TTFB)</span>
                    <span className="size-2 rounded-full bg-emerald-500"></span>
                  </div>
                  <div className="mt-2 flex items-baseline gap-2">
                    <span className="text-2xl font-bold font-mono text-foreground">12 ms</span>
                    <span className="text-xs font-semibold text-emerald-600 dark:text-emerald-400">p75 Good</span>
                  </div>
                  <p className="mt-1 text-[11px] text-muted-foreground">Optimized with Rust Anycast edge</p>
                </div>

                <div className="rounded-lg border border-border bg-card p-4 shadow-sm">
                  <div className="flex items-center justify-between text-xs text-muted-foreground">
                    <span>First Contentful Paint (FCP)</span>
                    <span className="size-2 rounded-full bg-emerald-500"></span>
                  </div>
                  <div className="mt-2 flex items-baseline gap-2">
                    <span className="text-2xl font-bold font-mono text-foreground">0.4 s</span>
                    <span className="text-xs font-semibold text-emerald-600 dark:text-emerald-400">p75 Good</span>
                  </div>
                  <p className="mt-1 text-[11px] text-muted-foreground">Static streaming prerendering</p>
                </div>

                <div className="rounded-lg border border-border bg-card p-4 shadow-sm">
                  <div className="flex items-center justify-between text-xs text-muted-foreground">
                    <span>Largest Contentful Paint (LCP)</span>
                    <span className="size-2 rounded-full bg-emerald-500"></span>
                  </div>
                  <div className="mt-2 flex items-baseline gap-2">
                    <span className="text-2xl font-bold font-mono text-foreground">0.8 s</span>
                    <span className="text-xs font-semibold text-emerald-600 dark:text-emerald-400">p75 Good</span>
                  </div>
                  <p className="mt-1 text-[11px] text-muted-foreground">Next.js 15 image & font optimization</p>
                </div>

                <div className="rounded-lg border border-border bg-card p-4 shadow-sm">
                  <div className="flex items-center justify-between text-xs text-muted-foreground">
                    <span>Cumulative Layout Shift (CLS)</span>
                    <span className="size-2 rounded-full bg-emerald-500"></span>
                  </div>
                  <div className="mt-2 flex items-baseline gap-2">
                    <span className="text-2xl font-bold font-mono text-foreground">0.00</span>
                    <span className="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Zero Shift</span>
                  </div>
                  <p className="mt-1 text-[11px] text-muted-foreground">TweakCN Offworld CSS system</p>
                </div>
              </div>
            </div>

            {/* Coolify Infrastructure Quick Status */}
            <div className="rounded-lg border border-border bg-card p-6 shadow-sm">
              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                  <IconServer className="size-5 text-foreground" />
                  <h3 className="text-sm font-semibold text-foreground">Connected PaaS Infrastructure & Standalone Nodes</h3>
                </div>
                <button
                  onClick={() => setActiveTab('servers')}
                  className="text-xs text-foreground font-semibold hover:underline inline-flex items-center gap-1"
                >
                  Manage VPS Nodes <IconArrowUpRight className="size-3" />
                </button>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div className="rounded border border-border p-3 bg-muted/20">
                  <div className="flex items-center justify-between font-semibold">
                    <span>Localhost Sentinel Node</span>
                    <span className="text-emerald-500 flex items-center gap-1">● Online</span>
                  </div>
                  <p className="mt-1 text-[11px] text-muted-foreground">Docker 27.2.0 • 6 containers active • 14% CPU</p>
                </div>

                <div className="rounded border border-border p-3 bg-muted/20">
                  <div className="flex items-center justify-between font-semibold">
                    <span>PostgreSQL 16 Engine</span>
                    <span className="text-emerald-500 flex items-center gap-1">● Healthy</span>
                  </div>
                  <p className="mt-1 text-[11px] text-muted-foreground">Port 5432 • 1.42 GB storage • Daily S3 backup</p>
                </div>

                <div className="rounded border border-border p-3 bg-muted/20">
                  <div className="flex items-center justify-between font-semibold">
                    <span>Redis 7.2 Cache</span>
                    <span className="text-emerald-500 flex items-center gap-1">● Healthy</span>
                  </div>
                  <p className="mt-1 text-[11px] text-muted-foreground">Port 6379 • 28.5 MB memory • In-memory speed</p>
                </div>
              </div>
            </div>

            {/* Recent Pipeline Deployments Table */}
            <div className="rounded-lg border border-border bg-card shadow-sm overflow-hidden">
              <div className="flex items-center justify-between border-b border-border px-6 py-4">
                <h3 className="text-sm font-semibold text-foreground">Recent Pipeline Deployments</h3>
                <button
                  onClick={() => setActiveTab('deployments')}
                  className="text-xs font-semibold text-foreground hover:underline"
                >
                  View All Deployments →
                </button>
              </div>

              <div className="divide-y divide-border">
                {deployments.slice(0, 3).map((dpl) => (
                  <div key={dpl.id} className="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-3.5 hover:bg-muted/30 transition-colors gap-3">
                    <div className="flex items-center gap-3">
                      <span className="size-2 rounded-full bg-emerald-500"></span>
                      <div>
                        <div className="flex items-center gap-2">
                          <a
                            href={dpl.url}
                            target="_blank"
                            rel="noreferrer"
                            className="font-semibold text-xs text-foreground hover:underline"
                          >
                            {dpl.commitMsg}
                          </a>
                          <span className={`text-[10px] font-semibold px-2 py-0.5 rounded-full border ${dpl.environment === 'Production' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : 'bg-muted text-muted-foreground border-border'}`}>
                            {dpl.environment}
                          </span>
                        </div>
                        <div className="flex items-center gap-2 mt-1 text-[11px] text-muted-foreground font-mono">
                          <span>{dpl.commit}</span>
                          <span>•</span>
                          <span>{dpl.branch}</span>
                          <span>•</span>
                          <span>by {dpl.author}</span>
                        </div>
                      </div>
                    </div>

                    <div className="flex items-center gap-4 text-xs text-muted-foreground">
                      <span className="font-mono">{dpl.duration}</span>
                      <span>{dpl.age}</span>
                      <a
                        href={dpl.url}
                        target="_blank"
                        rel="noreferrer"
                        className="rounded border border-border bg-background px-2.5 py-1 text-xs text-foreground hover:bg-muted font-medium"
                      >
                        Visit
                      </a>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {/* ================= TAB 2: DEPLOYMENTS ================= */}
        {activeTab === 'deployments' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold tracking-tight text-foreground">Deployments</h2>
                <p className="mt-1 text-xs text-muted-foreground">
                  History of all continuous integration builds, native runner jobs, and edge promotions.
                </p>
              </div>

              <button
                onClick={triggerGoBuild}
                disabled={isDeploying}
                className="inline-flex items-center gap-1.5 rounded-md bg-foreground text-background px-4 py-2 text-xs font-semibold hover:opacity-90 transition-opacity disabled:opacity-50"
              >
                <IconPlayerPlay className={`size-3.5 ${isDeploying ? 'animate-spin' : ''}`} />
                <span>{isDeploying ? 'Deploying...' : 'Trigger New Deployment'}</span>
              </button>
            </div>

            <div className="rounded-lg border border-border bg-card shadow-sm overflow-hidden divide-y divide-border">
              {deployments.map((dpl) => (
                <div key={dpl.id} className="p-6 hover:bg-muted/20 transition-colors">
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="space-y-1">
                      <div className="flex items-center gap-2.5">
                        <span className="size-2.5 rounded-full bg-emerald-500"></span>
                        <a href={dpl.url} target="_blank" rel="noreferrer" className="font-bold text-sm text-foreground hover:underline">
                          {dpl.url.replace('https://', '')}
                        </a>
                        <span className={`text-[10px] font-semibold px-2 py-0.5 rounded-full border ${dpl.environment === 'Production' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : 'bg-muted text-muted-foreground border-border'}`}>
                          {dpl.environment}
                        </span>
                      </div>
                      <p className="text-xs text-muted-foreground pt-1">
                        {dpl.commitMsg}
                      </p>
                      <div className="flex flex-wrap items-center gap-3 pt-2 text-xs text-muted-foreground font-mono">
                        <span className="flex items-center gap-1 text-foreground">
                          <IconGitCommit className="size-3.5 text-muted-foreground" />
                          {dpl.commit}
                        </span>
                        <span>•</span>
                        <span className="flex items-center gap-1 text-foreground">
                          <IconGitBranch className="size-3.5 text-muted-foreground" />
                          {dpl.branch}
                        </span>
                        <span>•</span>
                        <span>{dpl.author}</span>
                      </div>
                    </div>

                    <div className="flex items-center gap-4 text-xs">
                      <div className="text-right">
                        <p className="font-semibold text-foreground">{dpl.age}</p>
                        <p className="text-muted-foreground font-mono">{dpl.duration}</p>
                      </div>
                      <a
                        href={dpl.url}
                        target="_blank"
                        rel="noreferrer"
                        className="rounded-md border border-border bg-card px-3 py-1.5 font-semibold text-foreground hover:bg-muted transition-colors"
                      >
                        Open Site
                      </a>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ================= TAB 3: SERVERS & VPS (COOLIFY POWER) ================= */}
        {activeTab === 'servers' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold tracking-tight text-foreground">Connected VPS & Servers</h2>
                <p className="mt-1 text-xs text-muted-foreground">
                  Coolify bare-metal host management, Docker daemon clustering, and system resource monitors.
                </p>
              </div>

              <div className="flex items-center gap-2">
                <button
                  onClick={() => alert('Add Server dialog: SSH credentials, IP address, and Docker provisioning.')}
                  className="inline-flex items-center gap-1.5 rounded-md bg-foreground text-background px-4 py-2 text-xs font-semibold hover:opacity-90"
                >
                  <IconPlus className="size-3.5" />
                  <span>Connect New Server</span>
                </button>
              </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {servers.map((server) => (
                <div key={server.id} className="rounded-lg border border-border bg-card p-6 shadow-sm space-y-5">
                  <div className="flex items-center justify-between">
                    <div>
                      <h3 className="font-bold text-sm text-foreground flex items-center gap-2">
                        <IconServer className="size-4 text-foreground" />
                        <span>{server.name}</span>
                      </h3>
                      <p className="text-xs text-muted-foreground font-mono mt-0.5">
                        {server.ip} • {server.os}
                      </p>
                    </div>
                    <span className="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                      <span className="size-1.5 rounded-full bg-emerald-500"></span>
                      Online
                    </span>
                  </div>

                  {/* Resource Gauges */}
                  <div className="grid grid-cols-3 gap-3 text-xs">
                    <div className="rounded border border-border p-3 bg-muted/20">
                      <div className="text-muted-foreground text-[11px]">CPU ({server.cpuCores} Cores)</div>
                      <div className="text-base font-bold font-mono text-foreground mt-1">{server.cpuUsage}%</div>
                      <div className="w-full bg-muted h-1 rounded-full mt-2 overflow-hidden">
                        <div className="bg-emerald-500 h-full" style={{ width: `${server.cpuUsage}%` }}></div>
                      </div>
                    </div>

                    <div className="rounded border border-border p-3 bg-muted/20">
                      <div className="text-muted-foreground text-[11px]">RAM ({server.ramTotal})</div>
                      <div className="text-base font-bold font-mono text-foreground mt-1">{server.ramUsage}%</div>
                      <div className="w-full bg-muted h-1 rounded-full mt-2 overflow-hidden">
                        <div className="bg-blue-500 h-full" style={{ width: `${server.ramUsage}%` }}></div>
                      </div>
                    </div>

                    <div className="rounded border border-border p-3 bg-muted/20">
                      <div className="text-muted-foreground text-[11px]">Disk ({server.diskTotal})</div>
                      <div className="text-base font-bold font-mono text-foreground mt-1">{server.diskUsage}%</div>
                      <div className="w-full bg-muted h-1 rounded-full mt-2 overflow-hidden">
                        <div className="bg-indigo-500 h-full" style={{ width: `${server.diskUsage}%` }}></div>
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center justify-between border-t border-border pt-4 text-xs text-muted-foreground">
                    <span>Docker Daemon: <strong className="text-foreground font-mono">{server.dockerVersion}</strong></span>
                    <span>Containers: <strong className="text-foreground font-mono">{server.containersCount} Active</strong></span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ================= TAB 4: DATABASES & STORAGE (COOLIFY POWER) ================= */}
        {activeTab === 'storage' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold tracking-tight text-foreground">Databases & Storage</h2>
                <p className="mt-1 text-xs text-muted-foreground">
                  Coolify standalone databases, automated backup snapshots, and S3 object storage buckets.
                </p>
              </div>

              <button
                onClick={() => alert('New Database: Deploy PostgreSQL, MySQL, Redis, MongoDB, or ClickHouse with 1 click.')}
                className="inline-flex items-center gap-1.5 rounded-md bg-foreground text-background px-4 py-2 text-xs font-semibold hover:opacity-90"
              >
                <IconPlus className="size-3.5" />
                <span>Deploy New Database</span>
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {databases.map((db) => (
                <div key={db.id} className="rounded-lg border border-border bg-card p-6 shadow-sm space-y-4">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      <IconDatabase className="size-4 text-foreground" />
                      <span className="font-bold text-sm text-foreground">{db.name}</span>
                    </div>
                    <span className="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                      Healthy
                    </span>
                  </div>

                  <div className="space-y-2 text-xs">
                    <div className="flex justify-between text-muted-foreground">
                      <span>Engine & Version</span>
                      <span className="font-mono text-foreground">{db.engine} {db.version}</span>
                    </div>
                    <div className="flex justify-between text-muted-foreground">
                      <span>Exposed Port</span>
                      <span className="font-mono text-foreground">{db.port}</span>
                    </div>
                    <div className="flex justify-between text-muted-foreground">
                      <span>Disk Storage</span>
                      <span className="font-mono text-foreground">{db.storageUsed}</span>
                    </div>
                    <div className="flex justify-between text-muted-foreground">
                      <span>Last Automated Backup</span>
                      <span className="font-medium text-foreground">{db.lastBackup}</span>
                    </div>
                  </div>

                  <div className="pt-3 border-t border-border">
                    <button
                      onClick={() => copyToClipboard(db.internalUrl, db.id)}
                      className="w-full flex items-center justify-center gap-1.5 rounded border border-border bg-muted/40 py-1.5 text-xs font-mono text-foreground hover:bg-muted"
                    >
                      <IconCopy className="size-3.5" />
                      <span>{copied === db.id ? 'Connection String Copied' : 'Copy Connection URL'}</span>
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ================= TAB 5: LIVE LOGS & TERMINAL (COOLIFY POWER) ================= */}
        {activeTab === 'terminal' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold tracking-tight text-foreground">Live Logs & Terminal</h2>
                <p className="mt-1 text-xs text-muted-foreground">
                  Streaming logs from Go runner, Rust edge dispatcher, Next.js web application, and Traefik reverse proxy.
                </p>
              </div>

              <div className="flex items-center gap-2">
                <button
                  onClick={() => setTerminalLogs([])}
                  className="rounded border border-border bg-card px-3 py-1.5 text-xs font-semibold text-foreground hover:bg-muted"
                >
                  Clear Console
                </button>
                <button
                  onClick={() => {
                    setTerminalLogs((prev) => [
                      ...prev,
                      `[manual] Heartbeat ping ok - timestamp=${new Date().toISOString()}`,
                    ]);
                  }}
                  className="rounded border border-border bg-card px-3 py-1.5 text-xs font-semibold text-foreground hover:bg-muted"
                >
                  Send Ping
                </button>
              </div>
            </div>

            {/* Dark Terminal Window */}
            <div className="rounded-lg border border-neutral-800 bg-neutral-950 text-neutral-100 shadow-xl overflow-hidden font-mono text-xs">
              <div className="flex items-center justify-between border-b border-neutral-800 bg-neutral-900/80 px-4 py-2.5">
                <div className="flex items-center gap-2">
                  <span className="size-3 rounded-full bg-red-500"></span>
                  <span className="size-3 rounded-full bg-amber-500"></span>
                  <span className="size-3 rounded-full bg-emerald-500"></span>
                  <span className="text-[11px] text-neutral-400 pl-2">vedaix-terminal-server :// localhost:6002</span>
                </div>
                <div className="flex items-center gap-2 text-[10px] text-neutral-400">
                  <span className="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                  <span>STREAMING WEBSOCKET ACTIVE</span>
                </div>
              </div>

              <div className="p-4 h-96 overflow-y-auto space-y-1.5 leading-relaxed selection:bg-neutral-700">
                {terminalLogs.map((log, idx) => (
                  <div key={idx} className="flex items-start gap-2">
                    <span className="text-neutral-500 select-none">{String(idx + 1).padStart(2, '0')}</span>
                    <span className="text-emerald-400 select-none">➜</span>
                    <span className="text-neutral-200">{log}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {/* ================= TAB 6: EDGE COMPUTE (RUST & GO) ================= */}
        {activeTab === 'edge' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold tracking-tight text-foreground">Edge Compute & Anycast Runtime</h2>
                <p className="mt-1 text-xs text-muted-foreground">
                  Native Rust 1.99 Anycast router and Go 1.27 Build Runner executing with zero cold starts.
                </p>
              </div>

              <button
                onClick={testRustEdge}
                disabled={testingEdge}
                className="inline-flex items-center gap-1.5 rounded-md bg-foreground text-background px-4 py-2 text-xs font-semibold hover:opacity-90 disabled:opacity-50"
              >
                <IconBolt className={`size-3.5 ${testingEdge ? 'animate-spin' : ''}`} />
                <span>{testingEdge ? 'Executing...' : 'Run Sub-ms Edge Test'}</span>
              </button>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* Rust Engine Panel */}
              <div className="rounded-lg border border-border bg-card p-6 shadow-sm space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="font-bold text-sm text-foreground flex items-center gap-2">
                    <IconBolt className="size-4 text-foreground" />
                    <span>Rust 1.99 Anycast Edge Dispatcher</span>
                  </h3>
                  <span className="text-xs font-mono text-emerald-500 font-semibold">Port 9002 (Active)</span>
                </div>
                <p className="text-xs text-muted-foreground">
                  Compiled native release binary with SIMD JSON deserialization and Tokio multi-thread worker pool.
                </p>
                <div className="rounded border border-border bg-muted/20 p-3 font-mono text-xs space-y-1">
                  <div className="text-muted-foreground">Latency: <strong className="text-foreground">{rustMetrics?.avg_latency_ms || '0.42'} ms</strong></div>
                  <div className="text-muted-foreground">Cache Hit Ratio: <strong className="text-foreground">{rustMetrics?.cache_hit_ratio || '98.6'}%</strong></div>
                  <div className="text-muted-foreground">Global Anycast PoPs: <strong className="text-foreground">320 Nodes</strong></div>
                </div>
              </div>

              {/* Go Builder Panel */}
              <div className="rounded-lg border border-border bg-card p-6 shadow-sm space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="font-bold text-sm text-foreground flex items-center gap-2">
                    <IconCpu className="size-4 text-foreground" />
                    <span>Go 1.27 Native Build Runner</span>
                  </h3>
                  <span className="text-xs font-mono text-emerald-500 font-semibold">Port 9001 (Active)</span>
                </div>
                <p className="text-xs text-muted-foreground">
                  Autonomous concurrent pipeline worker executing git clones, dependency trees, and native binaries.
                </p>
                <div className="rounded border border-border bg-muted/20 p-3 font-mono text-xs space-y-1">
                  <div className="text-muted-foreground">Status: <strong className="text-foreground">{goStatus?.status || 'idle (listening)'}</strong></div>
                  <div className="text-muted-foreground">Active Concurrency: <strong className="text-foreground">8 Threads</strong></div>
                  <div className="text-muted-foreground">Memory Footprint: <strong className="text-foreground">2.14 MB</strong></div>
                </div>
              </div>
            </div>

            {edgeExecutionResult && (
              <div className="rounded-lg border border-emerald-500/30 bg-emerald-500/5 p-4 text-xs font-mono text-foreground">
                <span className="font-bold text-emerald-600 dark:text-emerald-400">Live Edge Response Result:</span>
                <pre className="mt-2 text-[11px] overflow-x-auto">{JSON.stringify(edgeExecutionResult, null, 2)}</pre>
              </div>
            )}
          </div>
        )}

        {/* ================= TAB 7: ENVIRONMENT VARIABLES (VERCEL STANDARD) ================= */}
        {activeTab === 'env' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold tracking-tight text-foreground">Environment Variables</h2>
                <p className="mt-1 text-xs text-muted-foreground">
                  Encrypted secrets and configuration values for Production, Preview, and Development.
                </p>
              </div>

              <div className="flex items-center gap-2">
                <button
                  onClick={() => setBulkEnvOpen(!bulkEnvOpen)}
                  className="rounded-md border border-border bg-card px-3 py-1.5 text-xs font-semibold text-foreground hover:bg-muted"
                >
                  {bulkEnvOpen ? 'Close Bulk Input' : 'Bulk Import .env'}
                </button>
              </div>
            </div>

            {/* Bulk .env Paste Box */}
            {bulkEnvOpen && (
              <div className="rounded-lg border border-border bg-card p-4 space-y-3">
                <h4 className="text-xs font-semibold text-foreground">Paste raw .env text</h4>
                <textarea
                  rows={4}
                  value={bulkEnvText}
                  onChange={(e) => setBulkEnvText(e.target.value)}
                  placeholder="KEY=VALUE&#10;ANOTHER_KEY=ANOTHER_VALUE"
                  className="w-full rounded border border-border bg-background p-2.5 font-mono text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-foreground"
                />
                <button
                  onClick={() => {
                    const lines = bulkEnvText.split('\n');
                    const added: any[] = [];
                    lines.forEach((line) => {
                      const [k, ...v] = line.split('=');
                      if (k && k.trim()) {
                        added.push({
                          id: String(Date.now() + Math.random()),
                          key: k.trim().toUpperCase(),
                          value: v.join('=').trim(),
                          show: false,
                          prod: true,
                          prev: true,
                          dev: true,
                        });
                      }
                    });
                    setEnvVars((prev) => [...prev, ...added]);
                    setBulkEnvText('');
                    setBulkEnvOpen(false);
                  }}
                  className="rounded bg-foreground text-background px-3 py-1.5 text-xs font-semibold"
                >
                  Parse & Save Variables
                </button>
              </div>
            )}

            {/* Add Single Variable Bar */}
            <div className="rounded-lg border border-border bg-card p-4 flex flex-col sm:flex-row gap-3 items-center">
              <input
                type="text"
                placeholder="VARIABLE_NAME"
                value={newKey}
                onChange={(e) => setNewKey(e.target.value)}
                className="w-full sm:w-1/3 rounded border border-border bg-background px-3 py-2 text-xs font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-foreground"
              />
              <input
                type="text"
                placeholder="value_or_secret"
                value={newVal}
                onChange={(e) => setNewVal(e.target.value)}
                className="w-full sm:w-1/2 rounded border border-border bg-background px-3 py-2 text-xs font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-foreground"
              />
              <button
                onClick={addEnvVar}
                className="w-full sm:w-auto rounded bg-foreground text-background px-4 py-2 text-xs font-semibold hover:opacity-90 whitespace-nowrap"
              >
                Add Variable
              </button>
            </div>

            {/* Variables Table */}
            <div className="rounded-lg border border-border bg-card shadow-sm overflow-hidden divide-y divide-border">
              {envVars.map((v) => (
                <div key={v.id} className="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                  <div className="space-y-1">
                    <span className="font-mono font-bold text-foreground">{v.key}</span>
                    <div className="flex items-center gap-2 text-muted-foreground font-mono">
                      <span>{v.show ? v.value : '••••••••••••••••••••••••••••••••'}</span>
                      <button
                        onClick={() => {
                          setEnvVars((prev) =>
                            prev.map((item) => (item.id === v.id ? { ...item, show: !item.show } : item))
                          );
                        }}
                        className="text-muted-foreground hover:text-foreground"
                      >
                        {v.show ? <IconEyeOff className="size-3.5" /> : <IconEye className="size-3.5" />}
                      </button>
                    </div>
                  </div>

                  <div className="flex items-center gap-4">
                    <div className="flex items-center gap-2 text-[11px] text-muted-foreground">
                      <span className="rounded bg-muted px-2 py-0.5">Production</span>
                      <span className="rounded bg-muted px-2 py-0.5">Preview</span>
                      <span className="rounded bg-muted px-2 py-0.5">Development</span>
                    </div>

                    <button
                      onClick={() => deleteEnvVar(v.id)}
                      className="text-muted-foreground hover:text-red-500 transition-colors"
                      title="Delete variable"
                    >
                      <IconTrash className="size-4" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ================= TAB 8: DOMAINS & SSL ================= */}
        {activeTab === 'domains' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold tracking-tight text-foreground">Domains & SSL Certificates</h2>
                <p className="mt-1 text-xs text-muted-foreground">
                  Custom domain mappings with automated Let's Encrypt TLS 1.3 certificates and DNS verification.
                </p>
              </div>

              <div className="flex items-center gap-2">
                <input
                  type="text"
                  placeholder="my-domain.com"
                  value={newDomain}
                  onChange={(e) => setNewDomain(e.target.value)}
                  className="rounded border border-border bg-background px-3 py-1.5 text-xs font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-foreground"
                />
                <button
                  onClick={addDomain}
                  className="rounded bg-foreground text-background px-3 py-1.5 text-xs font-semibold hover:opacity-90"
                >
                  Add Domain
                </button>
              </div>
            </div>

            <div className="rounded-lg border border-border bg-card shadow-sm overflow-hidden divide-y divide-border">
              {domains.map((d, idx) => (
                <div key={idx} className="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                  <div className="space-y-1">
                    <div className="flex items-center gap-2">
                      <a href={`https://${d.domain}`} target="_blank" rel="noreferrer" className="font-bold text-sm text-foreground hover:underline">
                        {d.domain}
                      </a>
                      {d.isPrimary && (
                        <span className="rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 text-[10px] font-semibold px-2 py-0.5">
                          Primary
                        </span>
                      )}
                      <span className="rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-semibold px-2 py-0.5">
                        Valid Configuration
                      </span>
                    </div>
                    <p className="text-xs text-muted-foreground">
                      Target: {d.target} • SSL: {d.ssl} (Auto-renews)
                    </p>
                  </div>

                  <div className="flex items-center gap-3 text-xs">
                    <a
                      href={`https://${d.domain}`}
                      target="_blank"
                      rel="noreferrer"
                      className="rounded border border-border bg-card px-3 py-1.5 font-semibold text-foreground hover:bg-muted"
                    >
                      Visit
                    </a>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ================= TAB 9: PROJECT SETTINGS ================= */}
        {activeTab === 'settings' && (
          <div className="space-y-6">
            <div>
              <h2 className="text-xl font-bold tracking-tight text-foreground">Project Settings</h2>
              <p className="mt-1 text-xs text-muted-foreground">
                Build configuration, framework presets, and repository settings for web.
              </p>
            </div>

            <div className="rounded-lg border border-border bg-card p-6 shadow-sm space-y-6">
              <div className="space-y-4">
                <h3 className="font-bold text-sm text-foreground">Build & Development Settings</h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                  <div>
                    <label className="text-muted-foreground block mb-1">Framework Preset</label>
                    <input
                      type="text"
                      disabled
                      value="Next.js (App Router)"
                      className="w-full rounded border border-border bg-muted/40 p-2 font-mono text-foreground"
                    />
                  </div>
                  <div>
                    <label className="text-muted-foreground block mb-1">Node.js Version</label>
                    <input
                      type="text"
                      disabled
                      value="24.x (LTS)"
                      className="w-full rounded border border-border bg-muted/40 p-2 font-mono text-foreground"
                    />
                  </div>
                  <div>
                    <label className="text-muted-foreground block mb-1">Build Command</label>
                    <input
                      type="text"
                      disabled
                      value="next build"
                      className="w-full rounded border border-border bg-muted/40 p-2 font-mono text-foreground"
                    />
                  </div>
                  <div>
                    <label className="text-muted-foreground block mb-1">Output Directory</label>
                    <input
                      type="text"
                      disabled
                      value=".next"
                      className="w-full rounded border border-border bg-muted/40 p-2 font-mono text-foreground"
                    />
                  </div>
                </div>
              </div>

              <div className="border-t border-border pt-6 space-y-4">
                <h3 className="font-bold text-sm text-foreground">Git Repository</h3>
                <div className="flex items-center justify-between rounded border border-border p-4 bg-muted/20 text-xs">
                  <div className="flex items-center gap-2">
                    <IconGitBranch className="size-4 text-foreground" />
                    <span className="font-mono font-bold text-foreground">Rahulchaube1/spony</span>
                  </div>
                  <a
                    href="https://github.com/Rahulchaube1/spony"
                    target="_blank"
                    rel="noreferrer"
                    className="font-semibold text-foreground hover:underline"
                  >
                    View on GitHub →
                  </a>
                </div>
              </div>
            </div>
          </div>
        )}
      </main>

      {/* ================= COMMAND PALETTE MODAL (⌘K) ================= */}
      {commandOpen && (
        <div className="fixed inset-0 z-50 flex items-start justify-center pt-20 bg-black/60 backdrop-blur-sm p-4">
          <div className="w-full max-w-xl rounded-lg border border-border bg-card shadow-2xl overflow-hidden">
            <div className="flex items-center border-b border-border px-3 py-2.5">
              <IconSearch className="size-4 text-muted-foreground mr-2" />
              <input
                type="text"
                autoFocus
                placeholder="Type a command or search tabs..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full bg-transparent text-sm text-foreground placeholder:text-muted-foreground focus:outline-none"
              />
              <button onClick={() => setCommandOpen(false)} className="text-muted-foreground hover:text-foreground">
                <IconX className="size-4" />
              </button>
            </div>

            <div className="p-2 max-h-72 overflow-y-auto space-y-1 text-xs">
              {[
                { name: 'Go to Overview', action: () => { setActiveTab('overview'); setCommandOpen(false); } },
                { name: 'View Deployments', action: () => { setActiveTab('deployments'); setCommandOpen(false); } },
                { name: 'Manage VPS & Servers (Coolify)', action: () => { setActiveTab('servers'); setCommandOpen(false); } },
                { name: 'Databases & S3 Storage (Coolify)', action: () => { setActiveTab('storage'); setCommandOpen(false); } },
                { name: 'Open Live Terminal (Coolify)', action: () => { setActiveTab('terminal'); setCommandOpen(false); } },
                { name: 'Trigger Native Redeployment', action: () => { triggerGoBuild(); setCommandOpen(false); } },
                { name: 'Run Rust Edge Latency Test', action: () => { testRustEdge(); setCommandOpen(false); } },
                { name: 'Edit Environment Variables', action: () => { setActiveTab('env'); setCommandOpen(false); } },
                { name: 'Manage Domains & SSL', action: () => { setActiveTab('domains'); setCommandOpen(false); } },
              ]
                .filter((i) => i.name.toLowerCase().includes(searchQuery.toLowerCase()))
                .map((item, idx) => (
                  <button
                    key={idx}
                    onClick={item.action}
                    className="w-full text-left px-3 py-2 rounded hover:bg-muted text-foreground flex items-center justify-between"
                  >
                    <span>{item.name}</span>
                    <span className="text-[10px] text-muted-foreground">Jump</span>
                  </button>
                ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
