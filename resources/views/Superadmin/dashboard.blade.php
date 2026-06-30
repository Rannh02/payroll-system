@extends('Superadmin.layouts.master')

@section('title', 'Control Deck')

@section('content')
    <div style="max-width: 1600px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem;">

        <!-- Header -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed rgba(99, 102, 241, 0.15); padding-bottom: 1.5rem;">
            <div>
                <h1
                    style="font-size: 1.75rem; font-weight: 800; tracking: -0.02em; color: var(--text-main); margin-bottom: 0.25rem;">
                    CORE OPERATIONS CONTROL
                </h1>
                <p style="color: var(--text-muted); font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span
                        style="display: inline-block; width: 6px; height: 6px; background-color: var(--accent); border-radius: 50%;"></span>
                    Active Terminal: root@via-core-engine
                </p>
            </div>
            <div
                style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.15); padding: 0.5rem 1rem; border-radius: 6px; color: var(--primary); font-weight: 600;">
                SYS_UPTIME: 14D 03H 22M
            </div>
        </div>

        <!-- System Stats Metrics Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">

            <div class="tech-panel">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; align-items: center;">
                    <span
                        style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.05em; text-transform: uppercase;">
                        Database Engine
                    </span>
                    <i data-lucide="database" style="color: var(--primary); width: 1.25rem; height: 1.25rem;"></i>
                </div>
                <div
                    style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); font-family: 'JetBrains Mono', monospace;">
                    ONLINE
                </div>
                <div
                    style="font-size: 0.75rem; color: var(--accent); font-family: 'JetBrains Mono', monospace; margin-top: 0.5rem; font-weight: 600;">
                    Ping: 1.2ms | Connections: Active
                </div>
            </div>

            <div class="tech-panel">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; align-items: center;">
                    <span
                        style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.05em; text-transform: uppercase;">
                        System Load
                    </span>
                    <i data-lucide="cpu" style="color: var(--accent); width: 1.25rem; height: 1.25rem;"></i>
                </div>
                <div
                    style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); font-family: 'JetBrains Mono', monospace;">
                    0.24%
                </div>
                <div
                    style="font-size: 0.75rem; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 0.5rem; font-weight: 500;">
                    Threads: 16 | Idle States: Nominal
                </div>
            </div>

            <div class="tech-panel">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; align-items: center;">
                    <span
                        style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.05em; text-transform: uppercase;">
                        Memory Pool
                    </span>
                    <i data-lucide="hard-drive" style="color: #a855f7; width: 1.25rem; height: 1.25rem;"></i>
                </div>
                <div
                    style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); font-family: 'JetBrains Mono', monospace;">
                    14.2 MB
                </div>
                <div
                    style="font-size: 0.75rem; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 0.5rem; font-weight: 500;">
                    Usage: 4% | Allocated: 256MB
                </div>
            </div>

        </div>

        <!-- Detailed Status Logs / Panel Console -->


    </div>
@endsection