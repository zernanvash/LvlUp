@extends('layouts.app')
@section('title', 'Skill Tree')
@section('page_title', 'Skill Tree')
@section('page_subtitle', 'Unlock skills through projects and progression')
@section('content')
@php
$tierColors = [
    'core'      => ['ring' => 'var(--lvl-skill-core)',      'glow' => 'var(--lvl-skill-core-glow)'],
    'basic'     => ['ring' => 'var(--lvl-skill-basic)',     'glow' => 'var(--lvl-skill-basic-glow)'],
    'advanced'  => ['ring' => 'var(--lvl-skill-advanced)',  'glow' => 'var(--lvl-skill-advanced-glow)'],
    'master'    => ['ring' => 'var(--lvl-skill-master)',    'glow' => 'var(--lvl-skill-master-glow)'],
    'legendary' => ['ring' => 'var(--lvl-skill-legendary)', 'glow' => 'var(--lvl-skill-legendary-glow)'],
];
@endphp

<div class="skill-tree-root relative -mx-4 -my-6 h-[calc(100vh-5rem)] overflow-hidden sm:-mx-6 lg:-mx-8" x-data="skillTreeApp()" x-init="init()">

    <canvas id="bgCanvas" class="absolute inset-0 pointer-events-none z-0"></canvas>

    <!-- HUD top bar -->
    <div class="absolute top-4 left-1/2 -translate-x-1/2 z-30 w-[calc(100%-1.5rem)] max-w-5xl">
        <div class="skill-hud lvl-panel flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex items-center gap-3">
                <div class="skill-hud-mark">
                    <i class="fas fa-sitemap"></i>
                </div>
                <div>
                    <p class="lvl-label">Progress map</p>
                    <h2 class="text-base font-black text-[var(--lvl-text)]">Skill Tree</h2>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3 sm:flex sm:items-center sm:gap-5">
                <div class="text-center">
                    <p class="lvl-label">Unlocked</p>
                    <p class="text-base font-black text-[var(--lvl-text)] leading-none">{{ count($unlockedNodeIds) }}<span class="text-sm text-[var(--lvl-faint)]">/{{ $nodes->count() }}</span></p>
                </div>
                <div class="hidden h-7 w-px bg-[var(--lvl-border-soft)] sm:block"></div>
                <div class="text-center">
                    <p class="lvl-label">Level</p>
                    <p class="text-base font-black text-[var(--lvl-text)] leading-none">{{ auth()->user()->level }}</p>
                </div>
                <div class="hidden h-7 w-px bg-[var(--lvl-border-soft)] sm:block"></div>
                <div class="text-center">
                    <p class="lvl-label">Rank</p>
                    <p class="text-base font-black text-[var(--lvl-text)] leading-none">{{ auth()->user()->rank }}</p>
                </div>
            </div>
            <div class="hidden items-center gap-2 text-xs lg:flex">
                <span class="skill-legend"><span style="background: var(--lvl-gold);"></span>Core</span>
                <span class="skill-legend"><span style="background: var(--lvl-green);"></span>Ready</span>
                <span class="skill-legend"><span style="background: var(--lvl-p600);"></span>Unlocked</span>
                <span class="skill-legend"><span style="background: var(--lvl-border);"></span>Locked</span>
            </div>
        </div>
    </div>

    <!-- Zoom controls -->
    <div class="absolute bottom-6 right-6 z-30 flex flex-col gap-2">
        <button @click="zoomIn()" class="skill-tool-button" title="Zoom in" aria-label="Zoom in">
            <i class="fas fa-plus"></i>
        </button>
        <button @click="zoomOut()" class="skill-tool-button" title="Zoom out" aria-label="Zoom out">
            <i class="fas fa-minus"></i>
        </button>
        <button @click="resetView()" class="skill-tool-button" title="Reset view" aria-label="Reset view">
            <i class="fas fa-compress-arrows-alt"></i>
        </button>
    </div>

    <div class="absolute bottom-6 left-6 z-20 hidden max-w-xs rounded-xl border border-[var(--lvl-border-soft)] bg-[var(--lvl-panel-bg)] px-4 py-3 text-xs text-[var(--lvl-muted)] shadow-lg backdrop-blur md:block">
        <div class="mb-2 flex items-center gap-2 font-bold text-[var(--lvl-text)]">
            <i class="fas fa-hand-pointer text-[var(--lvl-p600)]"></i>
            Explore
        </div>
        Drag the canvas, zoom with the wheel, and click nodes to inspect unlock requirements.
    </div>

    <!-- Skill tree stage -->
    <div id="skillTreeStage" class="w-full h-full cursor-grab active:cursor-grabbing select-none">
        <div id="skillTreeViewport" class="absolute origin-top-left" style="width:1400px; height:1200px;">
            <canvas id="skillTreeCanvas" width="1400" height="1200" class="absolute top-0 left-0 pointer-events-none"></canvas>
            <div class="relative w-full h-full">
                @foreach($nodes as $node)
                @php
                    $isUnlocked = in_array($node->id, $unlockedNodeIds);
                    $canUnlock  = $node->canBeUnlockedBy(auth()->user());
                    $state      = $isUnlocked ? 'unlocked' : ($canUnlock ? 'available' : 'locked');
                    $tc         = $tierColors[$node->tier] ?? $tierColors['basic'];
                @endphp
                <div id="node-{{ $node->id }}"
                    class="skill-node absolute z-10 cursor-pointer"
                    data-node-id="{{ $node->id }}"
                    data-parent-id="{{ $node->parent_node_id ?? '' }}"
                    data-state="{{ $state }}"
                    data-tier="{{ $node->tier }}"
                    style="left:{{ $node->x_position * 12 }}px; top:{{ $node->y_position * 11 }}px; transform:translate(-50%,-50%); --node-ring: {{ $tc['ring'] }}; --node-glow: {{ $tc['glow'] }};"
                    @click="openNodeModal({{ $node->id }})">

                    <div class="node-outer relative w-24 h-24 flex items-center justify-center">

                        @if($state === 'available')
                        <div class="node-ready-pulse absolute inset-1 rounded-full"></div>
                        @endif

                        <div class="node-hex relative flex h-20 w-20 items-center justify-center">
                            <div class="node-inner flex h-14 w-14 items-center justify-center">
                                <i class="{{ $node->skill->icon ?? 'fas fa-code' }} text-xl"></i>
                            </div>
                        </div>

                        @if($state === 'locked')
                        <div class="node-state-badge">
                            <i class="fas fa-lock"></i>
                        </div>
                        @elseif($state === 'unlocked')
                        <div class="node-state-badge is-unlocked">
                            <i class="fas fa-check"></i>
                        </div>
                        @endif
                    </div>

                    <div class="absolute -bottom-7 left-1/2 -translate-x-1/2 whitespace-nowrap pointer-events-none text-center">
                        <span class="node-label">
                            {{ Str::limit($node->title, 18) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Node Detail Modal -->
    <div x-show="modal.open"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        x-cloak class="fixed inset-0 z-[100] flex items-center justify-center backdrop-blur-md"
        style="background: var(--lvl-overlay);"
        @click.self="closeModal()">

        <div class="skill-modal lvl-panel relative w-full max-w-md mx-4 overflow-hidden">

            <!-- Decorative top accent -->
            <div class="h-1 w-full" x-show="modal.data"
                :style="modal.data ? `background: linear-gradient(90deg, transparent, ${stateColor(modal.data.state)}, transparent)` : ''"></div>

            <button @click="closeModal()" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-lg border border-[var(--lvl-border-soft)] bg-[var(--lvl-surface-raised)] text-[var(--lvl-muted)] hover:text-[var(--lvl-text)] transition z-10">
                <i class="fas fa-times text-xs"></i>
            </button>

            <!-- Loading Skeleton -->
            <div x-show="modal.loading" class="p-6 space-y-6 animate-skeleton-pulse" aria-hidden="true">
                <!-- Header skeleton -->
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-white/5 rounded-xl flex-shrink-0"></div>
                    <div class="flex-1 space-y-2 mt-1">
                        <div class="h-5 bg-white/5 rounded w-3/4"></div>
                        <div class="h-4 bg-white/5 rounded w-1/4"></div>
                    </div>
                </div>
                <!-- Description skeleton -->
                <div class="space-y-2 pt-2">
                    <div class="h-3.5 bg-white/5 rounded w-full"></div>
                    <div class="h-3.5 bg-white/5 rounded w-5/6"></div>
                    <div class="h-3.5 bg-white/5 rounded w-4/5"></div>
                </div>
                <!-- Requirements skeleton -->
                <div class="space-y-3 pt-4 border-t border-[var(--lvl-border-soft)]">
                    <div class="h-4 bg-white/5 rounded w-1/4 mb-1"></div>
                    <div class="h-12 bg-white/5 rounded-xl w-full"></div>
                    <div class="h-12 bg-white/5 rounded-xl w-full"></div>
                </div>
            </div>

            <template x-if="modal.data && !modal.loading">
                <div>
                    <!-- Header -->
                    <div class="p-5 pb-4">
                        <div class="flex items-start gap-4">
                            <!-- Node icon hex -->
                            <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center rounded-xl border"
                                :style="`background: ${stateColor(modal.data.state)}22; border-color: ${stateColor(modal.data.state)}55`">
                                <i :class="modal.data.node.skill ? modal.data.node.skill.icon : 'fas fa-code'"
                                   class="text-2xl" :style="`color: ${stateColor(modal.data.state)}`"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-display text-lg font-bold text-[var(--lvl-text)] leading-tight" x-text="modal.data.node.title"></h3>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border"
                                        :style="`color: ${stateColor(modal.data.state)}; border-color: ${stateColor(modal.data.state)}55; background: ${stateColor(modal.data.state)}11`"
                                        x-text="modal.data.state === 'unlocked' ? 'Unlocked' : modal.data.state === 'available' ? 'Ready' : 'Locked'"></span>
                                </div>
                                <p class="text-xs text-[var(--lvl-muted)]" x-text="modal.data.node.skill ? modal.data.node.skill.name : ''"></p>
                            </div>
                        </div>
                        <p class="text-[var(--lvl-muted)] text-sm mt-3 leading-relaxed" x-text="modal.data.node.description"></p>
                    </div>

                    <!-- Divider -->
                    <div class="mx-5 border-t border-[var(--lvl-border-soft)]"></div>

                    <!-- Requirements -->
                    <div class="p-5 space-y-2">
                        <p class="lvl-label mb-3">Requirements</p>

                        <!-- Level -->
                        <div class="requirement-row">
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-star-half-alt text-xs w-4 text-center" :style="`color: ${stateColor(modal.data.state)}`"></i>
                                <span class="text-[var(--lvl-muted)]">Level <span class="font-bold text-[var(--lvl-text)]" x-text="modal.data.requirements.level.required"></span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold tabular-nums"
                                    :style="`color: ${modal.data.requirements.level.met ? 'var(--lvl-green)' : 'var(--lvl-gold)'}`"
                                    x-text="`${modal.data.requirements.level.current} / ${modal.data.requirements.level.required}`"></span>
                                <i class="text-xs" :class="modal.data.requirements.level.met ? 'fas fa-check-circle' : 'fas fa-hourglass-half'"
                                   :style="`color: ${modal.data.requirements.level.met ? 'var(--lvl-green)' : 'var(--lvl-gold)'}`"></i>
                            </div>
                        </div>

                        <!-- Parent -->
                        <template x-if="modal.data.requirements.parent">
                            <div class="requirement-row">
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-project-diagram text-xs w-4 text-center text-[var(--lvl-p600)]"></i>
                                    <span class="text-[var(--lvl-muted)]">Unlock <span class="font-bold text-[var(--lvl-text)]" x-text="modal.data.requirements.parent.title"></span></span>
                                </div>
                                <i class="text-xs" :class="modal.data.requirements.parent.met ? 'fas fa-check-circle' : 'fas fa-times-circle'"
                                   :style="`color: ${modal.data.requirements.parent.met ? 'var(--lvl-green)' : 'var(--lvl-red)'}`"></i>
                            </div>
                        </template>

                        <!-- Tasks -->
                        <template x-for="task in modal.data.requirements.tasks" :key="task.description">
                            <div class="requirement-task">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs text-[var(--lvl-muted)] leading-snug flex-1 pr-3" x-text="task.description"></span>
                                    <span class="text-xs font-bold tabular-nums flex-shrink-0"
                                        :style="`color: ${task.completed ? 'var(--lvl-green)' : 'var(--lvl-gold)'}`"
                                        x-text="`${task.current}/${task.required}`"></span>
                                </div>
                                <div class="w-full bg-[var(--lvl-surface-soft)] rounded-full h-1">
                                    <div class="h-1 rounded-full transition-all duration-700"
                                        :style="`width: ${Math.min(100, (task.current / Math.max(1, task.required)) * 100)}%; background: ${task.completed ? 'var(--lvl-green)' : stateColor(modal.data.state)}`"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Action -->
                    <div class="px-5 pb-5">
                        <template x-if="modal.data.state === 'available'">
                            <button @click="unlockNode(modal.data.node.id)"
                                :disabled="unlocking"
                                class="lvl-action w-full py-3 rounded-xl font-display font-bold text-sm transition shadow-lg text-white disabled:opacity-60"
                                style="background: linear-gradient(135deg, var(--lvl-green), var(--lvl-p400));">
                                <template x-if="!unlocking">
                                    <span><i class="fas fa-unlock mr-2"></i> Unlock Skill Node</span>
                                </template>
                                <template x-if="unlocking">
                                    <span><i class="fas fa-spinner fa-spin mr-2"></i> Unlocking...</span>
                                </template>
                            </button>
                        </template>
                        <template x-if="modal.data.state === 'unlocked'">
                            <div class="text-center py-2 text-sm font-bold" style="color: var(--lvl-green);">
                                <i class="fas fa-check-circle mr-1"></i> Skill Unlocked
                            </div>
                        </template>
                        <template x-if="modal.data.state === 'locked'">
                            <a href="{{ route('projects.create') }}" class="btn-secondary block w-full py-2.5 rounded-lg text-center text-sm font-bold">
                                <i class="fas fa-plus mr-2"></i> Add Project to Progress
                            </a>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
    function skillTreeApp() {
        return {
            scale: 0.85,
            offsetX: 0,
            offsetY: 80,
            isDragging: false,
            startX: 0, startY: 0,
            modal: { open: false, loading: false, data: null },
            unlocking: false,

            cssVar(name) {
                return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
            },

            tierColor(tier) {
                const map = {
                    core: '--lvl-skill-core',
                    basic: '--lvl-skill-basic',
                    advanced: '--lvl-skill-advanced',
                    master: '--lvl-skill-master',
                    legendary: '--lvl-skill-legendary',
                };
                return this.cssVar(map[tier] || '--lvl-p600') || '#a855f7';
            },

            stateColor(state) {
                if (state === 'unlocked') return this.cssVar('--lvl-p600') || '#a855f7';
                if (state === 'available') return this.cssVar('--lvl-green') || '#22c55e';
                return this.cssVar('--lvl-faint') || '#6b7280';
            },

            init() {
                this.initBackground();
                this.initStage();
                this.$nextTick(() => {
                    setTimeout(() => { this.drawConnections(); this.centerView(); }, 200);
                });
                window.addEventListener('resize', () => this.resizeBackground());
            },

            resizeBackground() {
                const bg = document.getElementById('bgCanvas');
                if (!bg) return;
                bg.width = window.innerWidth;
                bg.height = window.innerHeight;
            },

            initBackground() {
                const bg = document.getElementById('bgCanvas');
                this.resizeBackground();
                const ctx = bg.getContext('2d');
                const stars = Array.from({length: 90}, () => ({
                    x: Math.random() * bg.width, y: Math.random() * bg.height,
                    r: Math.random() * 1.2 + 0.25, a: Math.random(), speed: Math.random() * 0.003 + 0.001
                }));
                const draw = () => {
                    const text = this.cssVar('--lvl-text') || '#ffffff';
                    ctx.clearRect(0, 0, bg.width, bg.height);
                    ctx.fillStyle = 'transparent';
                    ctx.fillRect(0, 0, bg.width, bg.height);
                    stars.forEach(s => {
                        s.a += s.speed;
                        const alpha = (Math.sin(s.a) + 1) / 2 * 0.28 + 0.06;
                        ctx.beginPath();
                        ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                        ctx.fillStyle = this.hexToRgba(text, alpha);
                        ctx.fill();
                    });
                    requestAnimationFrame(draw);
                };
                draw();
            },

            hexToRgba(value, alpha) {
                if (!value || !value.startsWith('#')) return `rgba(200,200,255,${alpha})`;
                const hex = value.replace('#', '');
                const full = hex.length === 3 ? hex.split('').map(c => c + c).join('') : hex;
                const num = parseInt(full, 16);
                const r = (num >> 16) & 255;
                const g = (num >> 8) & 255;
                const b = num & 255;
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            },

            drawConnections() {
                const canvas = document.getElementById('skillTreeCanvas');
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                document.querySelectorAll('.skill-node').forEach(node => {
                    const parentId = node.getAttribute('data-parent-id');
                    if (!parentId) return;
                    const parentEl = document.getElementById(`node-${parentId}`);
                    if (!parentEl) return;

                    const x1 = parentEl.offsetLeft, y1 = parentEl.offsetTop;
                    const x2 = node.offsetLeft,    y2 = node.offsetTop;
                    const state = node.getAttribute('data-state');
                    const parentState = parentEl.getAttribute('data-state');

                    // Bezier curve for organic feel
                    const mx = (x1 + x2) / 2;
                    const my = (y1 + y2) / 2;

                    ctx.beginPath();
                    ctx.moveTo(x1, y1);
                    ctx.bezierCurveTo(x1, my, x2, my, x2, y2);

                    const unlockedColor = this.stateColor('unlocked');
                    const readyColor = this.stateColor('available');
                    const lockedColor = this.cssVar('--lvl-border') || '#334155';

                    if (parentState === 'unlocked' && state === 'unlocked') {
                        ctx.strokeStyle = unlockedColor;
                        ctx.shadowBlur = 8;
                        ctx.shadowColor = unlockedColor;
                        ctx.lineWidth = 3;
                        ctx.globalAlpha = 0.74;
                    } else if (state === 'available') {
                        ctx.strokeStyle = readyColor;
                        ctx.shadowBlur = 7;
                        ctx.shadowColor = readyColor;
                        ctx.lineWidth = 2.5;
                        ctx.globalAlpha = 0.72;
                    } else {
                        ctx.strokeStyle = lockedColor;
                        ctx.shadowBlur = 0;
                        ctx.lineWidth = 1.5;
                        ctx.globalAlpha = 0.34;
                    }
                    ctx.stroke();
                    ctx.shadowBlur = 0;
                    ctx.globalAlpha = 1;

                    // Draw energy orb on unlocked connections
                    if (parentState === 'unlocked' && state === 'unlocked') {
                        const t = (Date.now() % 2000) / 2000;
                        const bx = Math.pow(1-t,3)*x1 + 3*Math.pow(1-t,2)*t*x1 + 3*(1-t)*t*t*x2 + Math.pow(t,3)*x2;
                        const by = Math.pow(1-t,3)*y1 + 3*Math.pow(1-t,2)*t*my + 3*(1-t)*t*t*my + Math.pow(t,3)*y2;
                        ctx.beginPath();
                        ctx.arc(bx, by, 4, 0, Math.PI * 2);
                        ctx.fillStyle = unlockedColor;
                        ctx.shadowBlur = 8;
                        ctx.shadowColor = unlockedColor;
                        ctx.fill();
                        ctx.shadowBlur = 0;
                    }
                });

                requestAnimationFrame(() => this.drawConnections());
            },

            initStage() {
                const stage = document.getElementById('skillTreeStage');
                const viewport = document.getElementById('skillTreeViewport');

                const applyTransform = () => {
                    viewport.style.transform = `translate(${this.offsetX}px, ${this.offsetY}px) scale(${this.scale})`;
                };

                stage.addEventListener('mousedown', e => {
                    if (e.target.closest('.skill-node')) return;
                    this.isDragging = true;
                    this.startX = e.clientX - this.offsetX;
                    this.startY = e.clientY - this.offsetY;
                    stage.style.cursor = 'grabbing';
                });
                window.addEventListener('mousemove', e => {
                    if (!this.isDragging) return;
                    this.offsetX = e.clientX - this.startX;
                    this.offsetY = e.clientY - this.startY;
                    applyTransform();
                });
                window.addEventListener('mouseup', () => { this.isDragging = false; stage.style.cursor = 'grab'; });

                stage.addEventListener('wheel', e => {
                    e.preventDefault();
                    const rect = stage.getBoundingClientRect();
                    const mx = e.clientX - rect.left, my = e.clientY - rect.top;
                    const factor = e.deltaY > 0 ? 0.92 : 1.08;
                    const ns = Math.min(Math.max(this.scale * factor, 0.3), 2.5);
                    this.offsetX = mx - (mx - this.offsetX) * (ns / this.scale);
                    this.offsetY = my - (my - this.offsetY) * (ns / this.scale);
                    this.scale = ns;
                    applyTransform();
                }, { passive: false });

                this._applyTransform = applyTransform;
            },

            centerView() {
                const target = document.querySelector('[data-state="available"]')
                    || Array.from(document.querySelectorAll('[data-state="unlocked"]')).pop();
                if (target) {
                    this.offsetX = window.innerWidth / 2 - target.offsetLeft * this.scale;
                    this.offsetY = window.innerHeight / 2 - target.offsetTop * this.scale;
                } else {
                    this.offsetX = window.innerWidth / 2 - 300;
                    this.offsetY = 120;
                }
                if (this._applyTransform) this._applyTransform();
            },

            zoomIn()  { this.scale = Math.min(this.scale * 1.2, 2.5); if (this._applyTransform) this._applyTransform(); },
            zoomOut() { this.scale = Math.max(this.scale * 0.8, 0.3); if (this._applyTransform) this._applyTransform(); },
            resetView() { this.scale = 0.85; this.centerView(); },

            async openNodeModal(nodeId) {
                window.dispatchEvent(new CustomEvent('lvlup-feature-hint', {
                    detail: {
                        key: 'feature-skill-node-detail',
                        label: 'Feature hint',
                        title: 'Skill node details',
                        body: 'This panel shows the exact requirements for a skill node. Ready nodes can be unlocked immediately; locked nodes tell you what evidence or level you still need.',
                        steps: [
                            'Check the level, parent, and project requirements.',
                            'Use Add Project to Progress when a node needs more work.',
                            'Unlocking nodes can award XP, badges, and new available nodes.',
                        ],
                    },
                }));
                this.modal.open = true;
                this.modal.loading = true;
                this.modal.data = null;
                try {
                    const res = await fetch(`/skill-tree/${nodeId}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    this.modal.data = data;
                } catch(e) {
                    this.modal.open = false;
                } finally {
                    this.modal.loading = false;
                }
            },

            closeModal() { this.modal.open = false; this.modal.data = null; },

            async unlockNode(nodeId) {
                if (this.unlocking) return;
                this.unlocking = true;
                try {
                    const res = await fetch(`/skill-tree/${nodeId}/unlock`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    });
                    const data = await res.json();

                    if (data.success) {
                        // Update modal state
                        if (this.modal.data) this.modal.data.state = 'unlocked';

                        // Update the node element visuals on the canvas
                        const nodeEl = document.getElementById(`node-${nodeId}`);
                        if (nodeEl) {
                            // Confetti explosion from node center
                            const rect = nodeEl.getBoundingClientRect();
                            if (typeof window.triggerConfetti === 'function') {
                                window.triggerConfetti(rect.left + rect.width / 2, rect.top + rect.height / 2);
                            }

                            nodeEl.setAttribute('data-state', 'unlocked');
                            const tier = nodeEl.getAttribute('data-tier') || 'basic';
                            const ring = this.tierColor(tier);

                            // Update outer hex background + glow
                            const outerHex = nodeEl.querySelector('.node-hex');
                            if (outerHex) {
                                outerHex.style.background = '';
                                outerHex.style.boxShadow = '';

                                // Update inner hex (first direct child div of outerHex)
                                const innerHex = outerHex.querySelector('div');
                                if (innerHex) {
                                    innerHex.style.background = '';

                                    // Update icon color
                                    const icon = innerHex.querySelector('i');
                                    if (icon) {
                                        // Remove all text-color classes and set inline color
                                        icon.className = icon.className.replace(/\btext-(?:green|gray|blue|purple|pink|amber|orange|red|violet)-\d+\b/g, '').trim();
                                        icon.style.color = ring;
                                    }
                                }
                            }

                            // Remove the ping animation (was for available state)
                            const ping = nodeEl.querySelector('.node-ready-pulse, .animate-ping');
                            if (ping) ping.remove();

                            // Remove lock overlay if present
                            const lockOverlay = nodeEl.querySelector('.fa-lock')?.closest('div');
                            if (lockOverlay) lockOverlay.remove();

                            // Add check badge if not already there
                            const nodeOuter = nodeEl.querySelector('.node-outer');
                            if (nodeOuter && !nodeOuter.querySelector('.unlock-check')) {
                                const check = document.createElement('div');
                                check.className = 'unlock-check node-state-badge is-unlocked';
                                check.innerHTML = `<i class="fas fa-check"></i>`;
                                nodeOuter.appendChild(check);
                            }

                            // Update label color
                            const label = nodeEl.querySelector('.node-label');
                            if (label) {
                                label.className = 'node-label';
                            }
                        }

                        // Fire unlock toast
                        window.pushToast({
                            label: (data.tier || 'skill').toUpperCase() + ' NODE UNLOCKED',
                            title: data.title,
                            sub: null,
                            icon: data.icon || 'fas fa-code',
                            color: this.tierColor(data.tier),
                            duration: 4000,
                        });

                        // Fire badge toasts if any were earned
                        if (data.new_badges && data.new_badges.length) {
                            data.new_badges.forEach((badge, idx) => {
                                setTimeout(() => {
                                    window.pushToast({
                                        label: badge.rarity.toUpperCase() + ' BADGE UNLOCKED',
                                        title: badge.title,
                                        sub: '+' + badge.xp_reward + ' XP',
                                        icon: badge.icon,
                                        color: badge.rarity_color,
                                        duration: 5000,
                                    });
                                    if (typeof window.triggerXpGain === 'function') {
                                        window.triggerXpGain(badge.xp_reward);
                                    }
                                    if (typeof window.pulseXpBar === 'function') {
                                        window.pulseXpBar();
                                    }
                                }, (idx + 1) * 600);
                            });
                        }

                        // Level-up overlay if leveled up
                        if (data.level_up) {
                            setTimeout(() => {
                                // Dispatch to appShell via a custom event on the body
                                document.body.dispatchEvent(new CustomEvent('trigger-level-up', { detail: data.level_up }));
                            }, 300);
                        }
                    } else {
                        window.pushToast({
                            label: 'UNLOCK FAILED',
                            title: data.message || 'Could not unlock node',
                            sub: null,
                            icon: 'fas fa-times-circle',
                            color: '#ef4444',
                            duration: 4000,
                        });
                    }
                } catch(e) {
                    window.pushToast({
                        label: 'ERROR',
                        title: 'Something went wrong',
                        sub: null,
                        icon: 'fas fa-exclamation-triangle',
                        color: '#f59e0b',
                        duration: 3000,
                    });
                } finally {
                    this.unlocking = false;
                }
            },
        }
    }
    </script>

    <style>
    [x-cloak] { display: none !important; }
    .skill-tree-root {
        --lvl-skill-core: var(--lvl-gold);
        --lvl-skill-basic: var(--lvl-p400);
        --lvl-skill-advanced: var(--lvl-p600);
        --lvl-skill-master: #d978b7;
        --lvl-skill-legendary: #f07f45;
        --lvl-skill-core-glow: rgba(239, 159, 39, .28);
        --lvl-skill-basic-glow: rgba(127, 119, 221, .24);
        --lvl-skill-advanced-glow: rgba(175, 169, 236, .24);
        --lvl-skill-master-glow: rgba(217, 120, 183, .22);
        --lvl-skill-legendary-glow: rgba(240, 127, 69, .24);
        background:
            radial-gradient(circle at 28% 18%, color-mix(in srgb, var(--lvl-p600) 16%, transparent), transparent 22rem),
            linear-gradient(color-mix(in srgb, var(--lvl-border-soft) 42%, transparent) 1px, transparent 1px),
            linear-gradient(90deg, color-mix(in srgb, var(--lvl-border-soft) 42%, transparent) 1px, transparent 1px),
            var(--lvl-body-bg);
        background-size: auto, 42px 42px, 42px 42px, auto;
    }

    .skill-tree-root::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at 72% 34%, color-mix(in srgb, var(--lvl-gold) 10%, transparent), transparent 18rem),
            radial-gradient(circle at 50% 80%, color-mix(in srgb, var(--lvl-green) 10%, transparent), transparent 20rem);
        opacity: .9;
    }

    .skill-hud-mark,
    .skill-tool-button {
        align-items: center;
        background: var(--lvl-surface-raised);
        border: 1px solid var(--lvl-border-soft);
        color: var(--lvl-p600);
        display: flex;
        justify-content: center;
    }

    .skill-hud-mark {
        border-radius: .7rem;
        height: 2.5rem;
        width: 2.5rem;
    }

    .skill-tool-button {
        backdrop-filter: blur(14px);
        border-radius: .7rem;
        box-shadow: var(--lvl-shadow);
        height: 2.5rem;
        transition: transform .16s ease, border-color .16s ease, color .16s ease, background-color .16s ease;
        width: 2.5rem;
    }

    .skill-tool-button:hover {
        border-color: var(--lvl-p100);
        color: var(--lvl-p800);
        transform: translateY(-1px);
    }

    .skill-legend {
        align-items: center;
        color: var(--lvl-muted);
        display: inline-flex;
        font-weight: 700;
        gap: .4rem;
    }

    .skill-legend span {
        border-radius: 999px;
        box-shadow: 0 0 0 3px color-mix(in srgb, currentColor 10%, transparent);
        height: .55rem;
        width: .55rem;
    }

    .node-outer {
        transition: transform .18s ease;
    }

    .skill-node:hover .node-outer {
        transform: translateY(-3px) scale(1.04);
    }

    .node-hex,
    .node-inner {
        clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
    }

    .node-hex {
        background: linear-gradient(135deg, color-mix(in srgb, var(--node-ring) 62%, var(--lvl-surface-raised)), color-mix(in srgb, var(--node-ring) 22%, var(--lvl-surface)));
        border: 1px solid color-mix(in srgb, var(--node-ring) 72%, var(--lvl-border));
        box-shadow: 0 12px 26px rgba(0, 0, 0, .22), 0 0 24px var(--node-glow);
        transition: filter .18s ease, transform .18s ease, box-shadow .18s ease;
    }

    .node-inner {
        background: color-mix(in srgb, var(--lvl-surface) 82%, var(--node-ring));
        color: var(--node-ring);
        text-shadow: 0 0 14px color-mix(in srgb, var(--node-ring) 55%, transparent);
    }

    .skill-node[data-state="available"] .node-hex {
        background: linear-gradient(135deg, color-mix(in srgb, var(--lvl-green) 72%, var(--lvl-surface)), color-mix(in srgb, var(--lvl-green) 24%, var(--lvl-surface)));
        border-color: color-mix(in srgb, var(--lvl-green) 70%, var(--lvl-border));
        box-shadow: 0 12px 28px rgba(0, 0, 0, .2), 0 0 24px color-mix(in srgb, var(--lvl-green) 28%, transparent);
    }

    .skill-node[data-state="available"] .node-inner {
        color: var(--lvl-green);
    }

    .skill-node[data-state="locked"] .node-hex {
        background: linear-gradient(135deg, var(--lvl-surface-soft), color-mix(in srgb, var(--lvl-surface-soft) 65%, #000));
        border-color: var(--lvl-border-soft);
        box-shadow: 0 10px 22px rgba(0, 0, 0, .14);
        opacity: .68;
    }

    .skill-node[data-state="locked"] .node-inner {
        background: color-mix(in srgb, var(--lvl-surface) 82%, var(--lvl-border));
        color: var(--lvl-faint);
        text-shadow: none;
    }

    .node-ready-pulse {
        animation: nodeReadyPulse 2.8s ease-in-out infinite;
        background: radial-gradient(circle, color-mix(in srgb, var(--lvl-green) 20%, transparent), transparent 68%);
        border: 1px solid color-mix(in srgb, var(--lvl-green) 36%, transparent);
    }

    @keyframes nodeReadyPulse {
        0%, 100% { opacity: .45; transform: scale(.9); }
        50% { opacity: .95; transform: scale(1.12); }
    }

    .node-state-badge {
        align-items: center;
        background: var(--lvl-surface-raised);
        border: 1px solid var(--lvl-border-soft);
        border-radius: 999px;
        bottom: .25rem;
        color: var(--lvl-faint);
        display: flex;
        font-size: .55rem;
        height: 1.25rem;
        justify-content: center;
        position: absolute;
        right: .25rem;
        width: 1.25rem;
    }

    .node-state-badge.is-unlocked {
        background: color-mix(in srgb, var(--node-ring) 18%, var(--lvl-surface-raised));
        border-color: color-mix(in srgb, var(--node-ring) 58%, var(--lvl-border-soft));
        color: var(--node-ring);
    }

    .node-label {
        background: color-mix(in srgb, var(--lvl-panel-bg) 88%, transparent);
        border: 1px solid var(--lvl-border-soft);
        border-radius: .45rem;
        color: var(--lvl-muted);
        display: inline-block;
        font-size: .65rem;
        font-weight: 800;
        padding: .16rem .5rem;
        box-shadow: 0 8px 18px rgba(0, 0, 0, .14);
    }

    .skill-node[data-state="unlocked"] .node-label,
    .skill-node[data-state="available"] .node-label {
        color: var(--node-ring);
    }

    .skill-node[data-state="available"] .node-label {
        color: var(--lvl-green);
    }

    .skill-modal {
        border-radius: .9rem !important;
    }

    .requirement-row,
    .requirement-task {
        background: var(--lvl-surface-raised);
        border: 1px solid var(--lvl-border-soft);
        border-radius: .75rem;
        padding: .7rem .8rem;
    }

    .requirement-row {
        align-items: center;
        display: flex;
        justify-content: space-between;
    }

    @media (prefers-reduced-motion: reduce) {
        .node-ready-pulse {
            animation: none;
        }
    }

    #skillTreeStage { cursor: grab; }
    </style>
@endsection
