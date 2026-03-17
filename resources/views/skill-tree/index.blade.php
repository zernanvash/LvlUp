@extends('layouts.app')
@section('title', 'Skill Tree')
@section('content')
@php
$tierColors = [
    'core'      => ['ring' => '#f59e0b', 'glow' => 'rgba(245,158,11,0.7)',  'bg' => 'from-amber-900/80 to-amber-700/60',   'text' => 'text-amber-300'],
    'basic'     => ['ring' => '#3b82f6', 'glow' => 'rgba(59,130,246,0.7)',  'bg' => 'from-blue-900/80 to-blue-700/60',     'text' => 'text-blue-300'],
    'advanced'  => ['ring' => '#8b5cf6', 'glow' => 'rgba(139,92,246,0.7)',  'bg' => 'from-violet-900/80 to-violet-700/60', 'text' => 'text-violet-300'],
    'master'    => ['ring' => '#ec4899', 'glow' => 'rgba(236,72,153,0.7)',  'bg' => 'from-pink-900/80 to-pink-700/60',     'text' => 'text-pink-300'],
    'legendary' => ['ring' => '#f97316', 'glow' => 'rgba(249,115,22,0.9)',  'bg' => 'from-orange-900/80 to-red-700/60',   'text' => 'text-orange-300'],
];
@endphp

<div class="skill-tree-root w-full h-[calc(100vh-80px)] relative overflow-hidden" x-data="skillTreeApp()" x-init="init()">

    <!-- Starfield background -->
    <canvas id="bgCanvas" class="absolute inset-0 pointer-events-none z-0"></canvas>

    <!-- HUD top bar -->
    <div class="absolute top-4 left-1/2 -translate-x-1/2 z-30 w-[96%] max-w-5xl">
        <div class="flex items-center justify-between gap-3 px-5 py-3 rounded-2xl bg-black/60 backdrop-blur-xl border border-white/10 shadow-2xl">
            <div class="flex items-center gap-1 font-display text-lg font-bold text-white tracking-widest">
                <i class="fas fa-sitemap text-purple-400 mr-2"></i>SKILL TREE
            </div>
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <p class="text-[9px] uppercase tracking-widest text-blue-400 opacity-70">Unlocked</p>
                    <p class="font-display font-bold text-white text-base leading-none">{{ count($unlockedNodeIds) }}<span class="text-gray-500 text-sm">/{{ $nodes->count() }}</span></p>
                </div>
                <div class="w-px h-6 bg-white/10"></div>
                <div class="text-center">
                    <p class="text-[9px] uppercase tracking-widest text-pink-400 opacity-70">Level</p>
                    <p class="font-display font-bold text-white text-base leading-none">{{ auth()->user()->level }}</p>
                </div>
                <div class="w-px h-6 bg-white/10"></div>
                <div class="text-center">
                    <p class="text-[9px] uppercase tracking-widest text-amber-400 opacity-70">Rank</p>
                    <p class="font-display font-bold text-white text-base leading-none">{{ auth()->user()->rank }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-[0_0_8px_#f59e0b]"></span><span class="text-gray-400">Core</span></span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-400 shadow-[0_0_8px_#22c55e]"></span><span class="text-gray-400">Ready</span></span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-400 shadow-[0_0_8px_#a855f7]"></span><span class="text-gray-400">Unlocked</span></span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-600"></span><span class="text-gray-400">Locked</span></span>
            </div>
        </div>
    </div>

    <!-- Zoom controls -->
    <div class="absolute bottom-6 right-6 z-30 flex flex-col gap-2">
        <button @click="zoomIn()" class="w-10 h-10 rounded-xl bg-black/60 border border-white/10 text-white hover:bg-purple-600/40 transition flex items-center justify-center backdrop-blur">
            <i class="fas fa-plus"></i>
        </button>
        <button @click="zoomOut()" class="w-10 h-10 rounded-xl bg-black/60 border border-white/10 text-white hover:bg-purple-600/40 transition flex items-center justify-center backdrop-blur">
            <i class="fas fa-minus"></i>
        </button>
        <button @click="resetView()" class="w-10 h-10 rounded-xl bg-black/60 border border-white/10 text-white hover:bg-purple-600/40 transition flex items-center justify-center backdrop-blur">
            <i class="fas fa-compress-arrows-alt"></i>
        </button>
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
                    style="left:{{ $node->x_position * 12 }}px; top:{{ $node->y_position * 11 }}px; transform:translate(-50%,-50%);"
                    @click="openNodeModal({{ $node->id }})">

                    <!-- Outer glow ring -->
                    <div class="node-outer relative w-24 h-24 flex items-center justify-center transition-transform duration-200 hover:scale-110">

                        <!-- Animated ring for available -->
                        @if($state === 'available')
                        <div class="absolute inset-0 rounded-full animate-ping opacity-30" style="background: radial-gradient(circle, {{ $tc['ring'] }}44, transparent 70%);"></div>
                        @endif

                        <!-- Hexagon shape via clip-path -->
                        <div class="node-hex w-20 h-20 flex items-center justify-center relative transition-all duration-300"
                            style="clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
                            @if($state === 'unlocked')
                                background: linear-gradient(135deg, {{ $tc['ring'] }}99, {{ $tc['ring'] }}44);
                                box-shadow: 0 0 30px {{ $tc['glow'] }}, inset 0 0 20px {{ $tc['ring'] }}33;
                            @elseif($state === 'available')
                                background: linear-gradient(135deg, #22c55e99, #16a34a44);
                                box-shadow: 0 0 25px rgba(34,197,94,0.8);
                            @else
                                background: linear-gradient(135deg, #1e293b, #0f172a);
                            @endif">

                            <!-- Inner hex -->
                            <div class="w-14 h-14 flex items-center justify-center"
                                style="clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
                                @if($state === 'unlocked')
                                    background: linear-gradient(135deg, {{ $tc['ring'] }}66, {{ $tc['ring'] }}22);
                                @elseif($state === 'available')
                                    background: linear-gradient(135deg, #16a34a66, #15803d22);
                                @else
                                    background: #0f172a;
                                @endif">
                                <i class="{{ $node->skill->icon ?? 'fas fa-code' }} text-xl
                                    @if($state === 'unlocked') {{ $tc['text'] }}
                                    @elseif($state === 'available') text-green-300
                                    @else text-gray-600 @endif"></i>
                            </div>
                        </div>

                        <!-- Lock overlay for locked nodes -->
                        @if($state === 'locked')
                        <div class="absolute bottom-1 right-1 w-5 h-5 bg-gray-800 rounded-full flex items-center justify-center border border-gray-600">
                            <i class="fas fa-lock text-gray-500 text-[8px]"></i>
                        </div>
                        @elseif($state === 'unlocked')
                        <div class="absolute bottom-1 right-1 w-5 h-5 rounded-full flex items-center justify-center border" style="background: {{ $tc['ring'] }}33; border-color: {{ $tc['ring'] }}88;">
                            <i class="fas fa-check text-[8px]" style="color: {{ $tc['ring'] }}"></i>
                        </div>
                        @endif
                    </div>

                    <!-- Node label -->
                    <div class="absolute -bottom-7 left-1/2 -translate-x-1/2 whitespace-nowrap pointer-events-none text-center">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md
                            @if($state === 'unlocked') {{ $tc['text'] }} bg-black/40
                            @elseif($state === 'available') text-green-300 bg-black/40
                            @else text-gray-500 bg-black/20 @endif">
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
        x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md"
        @click.self="closeModal()">

        <div class="relative w-full max-w-md mx-4 rounded-2xl shadow-2xl overflow-hidden border border-white/10"
            style="background: linear-gradient(135deg, #0d1117 0%, #161b2e 100%);">

            <!-- Decorative top accent -->
            <div class="h-1 w-full" x-show="modal.data"
                :style="modal.data ? `background: linear-gradient(90deg, transparent, ${stateColor(modal.data.state)}, transparent)` : ''"></div>

            <button @click="closeModal()" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-lg bg-white/5 hover:bg-red-500/30 text-gray-400 hover:text-white transition z-10">
                <i class="fas fa-times text-xs"></i>
            </button>

            <!-- Loading -->
            <div x-show="modal.loading" class="p-12 text-center">
                <div class="w-12 h-12 border-2 border-purple-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-purple-300 text-sm">Loading node data...</p>
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
                                    <h3 class="font-display text-lg font-bold text-white leading-tight" x-text="modal.data.node.title"></h3>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border"
                                        :style="`color: ${stateColor(modal.data.state)}; border-color: ${stateColor(modal.data.state)}55; background: ${stateColor(modal.data.state)}11`"
                                        x-text="modal.data.state === 'unlocked' ? '✓ Unlocked' : modal.data.state === 'available' ? '⚡ Ready' : '🔒 Locked'"></span>
                                </div>
                                <p class="text-xs text-gray-400" x-text="modal.data.node.skill ? modal.data.node.skill.name : ''"></p>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm mt-3 leading-relaxed" x-text="modal.data.node.description"></p>
                    </div>

                    <!-- Divider -->
                    <div class="mx-5 border-t border-white/5"></div>

                    <!-- Requirements -->
                    <div class="p-5 space-y-2">
                        <p class="text-[10px] uppercase tracking-widest text-gray-500 mb-3">Requirements</p>

                        <!-- Level -->
                        <div class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-white/3 border border-white/5">
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-star-half-alt text-xs w-4 text-center" :style="`color: ${stateColor(modal.data.state)}`"></i>
                                <span class="text-gray-300">Level <span class="font-bold text-white" x-text="modal.data.requirements.level.required"></span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold tabular-nums"
                                    :class="modal.data.requirements.level.met ? 'text-green-400' : 'text-yellow-400'"
                                    x-text="`${modal.data.requirements.level.current} / ${modal.data.requirements.level.required}`"></span>
                                <i class="text-xs" :class="modal.data.requirements.level.met ? 'fas fa-check-circle text-green-400' : 'fas fa-hourglass-half text-yellow-400'"></i>
                            </div>
                        </div>

                        <!-- Parent -->
                        <template x-if="modal.data.requirements.parent">
                            <div class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-white/3 border border-white/5">
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-project-diagram text-xs w-4 text-center text-purple-400"></i>
                                    <span class="text-gray-300">Unlock <span class="font-bold text-white" x-text="modal.data.requirements.parent.title"></span></span>
                                </div>
                                <i class="text-xs" :class="modal.data.requirements.parent.met ? 'fas fa-check-circle text-green-400' : 'fas fa-times-circle text-red-400'"></i>
                            </div>
                        </template>

                        <!-- Tasks -->
                        <template x-for="task in modal.data.requirements.tasks" :key="task.description">
                            <div class="px-3 py-2.5 rounded-xl bg-white/3 border border-white/5">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs text-gray-300 leading-snug flex-1 pr-3" x-text="task.description"></span>
                                    <span class="text-xs font-bold tabular-nums flex-shrink-0"
                                        :class="task.completed ? 'text-green-400' : 'text-yellow-400'"
                                        x-text="`${task.current}/${task.required}`"></span>
                                </div>
                                <div class="w-full bg-white/5 rounded-full h-1">
                                    <div class="h-1 rounded-full transition-all duration-700"
                                        :style="`width: ${Math.min(100, (task.current / Math.max(1, task.required)) * 100)}%; background: ${task.completed ? '#22c55e' : stateColor(modal.data.state)}`"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Action -->
                    <div class="px-5 pb-5">
                        <template x-if="modal.data.state === 'available'">
                            <button @click="unlockNode(modal.data.node.id)"
                                :disabled="unlocking"
                                class="w-full py-3 rounded-xl font-display font-bold text-sm transition shadow-lg text-white disabled:opacity-60"
                                style="background: linear-gradient(135deg, #16a34a, #15803d);">
                                <template x-if="!unlocking">
                                    <span><i class="fas fa-unlock mr-2"></i> Unlock Skill Node</span>
                                </template>
                                <template x-if="unlocking">
                                    <span><i class="fas fa-spinner fa-spin mr-2"></i> Unlocking...</span>
                                </template>
                            </button>
                        </template>
                        <template x-if="modal.data.state === 'unlocked'">
                            <div class="text-center py-2 text-green-400 text-sm font-bold">
                                <i class="fas fa-check-circle mr-1"></i> Skill Unlocked
                            </div>
                        </template>
                        <template x-if="modal.data.state === 'locked'">
                            <a href="/projects/create" class="block w-full py-3 rounded-xl text-center text-sm font-bold text-purple-300 border border-purple-500/30 hover:bg-purple-500/10 transition">
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

            stateColor(state) {
                if (state === 'unlocked') return '#a855f7';
                if (state === 'available') return '#22c55e';
                return '#6b7280';
            },

            init() {
                this.initBackground();
                this.initStage();
                this.$nextTick(() => {
                    setTimeout(() => { this.drawConnections(); this.centerView(); }, 200);
                });
                window.addEventListener('resize', () => this.drawConnections());
            },

            initBackground() {
                const bg = document.getElementById('bgCanvas');
                bg.width = window.innerWidth;
                bg.height = window.innerHeight;
                const ctx = bg.getContext('2d');
                const stars = Array.from({length: 180}, () => ({
                    x: Math.random() * bg.width, y: Math.random() * bg.height,
                    r: Math.random() * 1.5 + 0.3, a: Math.random(), speed: Math.random() * 0.005 + 0.002
                }));
                const draw = () => {
                    ctx.clearRect(0, 0, bg.width, bg.height);
                    ctx.fillStyle = '#060914';
                    ctx.fillRect(0, 0, bg.width, bg.height);
                    stars.forEach(s => {
                        s.a += s.speed;
                        const alpha = (Math.sin(s.a) + 1) / 2 * 0.8 + 0.1;
                        ctx.beginPath();
                        ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                        ctx.fillStyle = `rgba(200,200,255,${alpha})`;
                        ctx.fill();
                    });
                    requestAnimationFrame(draw);
                };
                draw();
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

                    if (parentState === 'unlocked' && state === 'unlocked') {
                        ctx.strokeStyle = '#a855f7';
                        ctx.shadowBlur = 12;
                        ctx.shadowColor = '#a855f7';
                        ctx.lineWidth = 3;
                        ctx.globalAlpha = 0.9;
                    } else if (state === 'available') {
                        ctx.strokeStyle = '#22c55e';
                        ctx.shadowBlur = 10;
                        ctx.shadowColor = '#22c55e';
                        ctx.lineWidth = 2.5;
                        ctx.globalAlpha = 0.8;
                    } else {
                        ctx.strokeStyle = '#1e293b';
                        ctx.shadowBlur = 0;
                        ctx.lineWidth = 1.5;
                        ctx.globalAlpha = 0.4;
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
                        ctx.fillStyle = '#c084fc';
                        ctx.shadowBlur = 10;
                        ctx.shadowColor = '#a855f7';
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
                            nodeEl.setAttribute('data-state', 'unlocked');
                            const tier = nodeEl.getAttribute('data-tier') || 'basic';
                            const tierColors = {
                                core: '#f59e0b', basic: '#3b82f6', advanced: '#8b5cf6',
                                master: '#ec4899', legendary: '#f97316'
                            };
                            const ring = tierColors[tier] || '#a855f7';

                            // Update outer hex background + glow
                            const outerHex = nodeEl.querySelector('.node-hex');
                            if (outerHex) {
                                outerHex.style.background = `linear-gradient(135deg, ${ring}99, ${ring}44)`;
                                outerHex.style.boxShadow = `0 0 30px ${ring}b3, inset 0 0 20px ${ring}33`;

                                // Update inner hex (first direct child div of outerHex)
                                const innerHex = outerHex.querySelector('div');
                                if (innerHex) {
                                    innerHex.style.background = `linear-gradient(135deg, ${ring}66, ${ring}22)`;

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
                            const ping = nodeEl.querySelector('.animate-ping');
                            if (ping) ping.remove();

                            // Remove lock overlay if present
                            const lockOverlay = nodeEl.querySelector('.fa-lock')?.closest('div');
                            if (lockOverlay) lockOverlay.remove();

                            // Add check badge if not already there
                            const nodeOuter = nodeEl.querySelector('.node-outer');
                            if (nodeOuter && !nodeOuter.querySelector('.unlock-check')) {
                                const check = document.createElement('div');
                                check.className = 'unlock-check absolute bottom-1 right-1 w-5 h-5 rounded-full flex items-center justify-center border';
                                check.style.cssText = `background: ${ring}33; border-color: ${ring}88;`;
                                check.innerHTML = `<i class="fas fa-check text-[8px]" style="color: ${ring}"></i>`;
                                nodeOuter.appendChild(check);
                            }

                            // Update label color
                            const label = nodeEl.querySelector('.-bottom-7 span');
                            if (label) {
                                label.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md bg-black/40';
                                label.style.color = ring;
                            }
                        }

                        // Fire unlock toast
                        const tierColors = {
                            core: '#f59e0b', basic: '#3b82f6', advanced: '#8b5cf6',
                            master: '#ec4899', legendary: '#f97316'
                        };
                        window.pushToast({
                            label: (data.tier || 'skill').toUpperCase() + ' NODE UNLOCKED',
                            title: data.title,
                            sub: null,
                            icon: data.icon || 'fas fa-code',
                            color: tierColors[data.tier] || '#a855f7',
                            duration: 4000,
                        });

                        // Fire badge toasts if any were earned
                        if (data.new_badges && data.new_badges.length) {
                            data.new_badges.forEach((badge, idx) => {
                                setTimeout(() => window.pushToast({
                                    label: badge.rarity.toUpperCase() + ' BADGE UNLOCKED',
                                    title: badge.title,
                                    sub: '+' + badge.xp_reward + ' XP',
                                    icon: badge.icon,
                                    color: badge.rarity_color,
                                    duration: 5000,
                                }), (idx + 1) * 600);
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
    .skill-tree-root { background: #060914; }
    .node-hex { transition: filter 0.2s, transform 0.2s; }
    .skill-node:hover .node-hex { filter: brightness(1.3); }
    #skillTreeStage { cursor: grab; }
    </style>
@endsection
