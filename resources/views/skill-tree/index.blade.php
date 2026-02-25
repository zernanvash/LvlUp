@extends('layouts.app')
@section('title', 'Skill Tree')
@section('content')

<div class="w-full h-[calc(100vh-80px)] relative overflow-hidden bg-[#0a0e27]" x-data="skillTreeApp()" x-init="init()">

    <div class="absolute top-6 left-1/2 -translate-x-1/2 z-30 w-[95%] max-w-6xl">
        <div
            class="glow-border rounded-2xl p-4 bg-gradient-to-br from-purple-900/60 to-[#0a0e27]/80 backdrop-blur-md shadow-2xl border border-purple-500/30">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center border border-blue-500/50">
                            <i class="fas fa-network-wired text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-blue-300 opacity-70">Skills</p>
                            <p class="font-display text-lg font-bold text-white">
                                {{ count($unlockedNodeIds) }}/{{ $nodes->count() }}</p>
                        </div>
                    </div>

                    <div class="w-px h-8 bg-purple-500/20"></div>

                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-pink-600/20 rounded-lg flex items-center justify-center border border-pink-500/50">
                            <i class="fas fa-level-up-alt text-pink-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-pink-300 opacity-70">Level</p>
                            <p class="font-display text-lg font-bold text-white">{{ auth()->user()->level }}</p>
                        </div>
                    </div>

                    <div class="w-px h-8 bg-purple-500/20"></div>

                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-amber-600/20 rounded-lg flex items-center justify-center border border-amber-500/50">
                            <i class="fas fa-trophy text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-amber-300 opacity-70">Rank</p>
                            <p class="font-display text-lg font-bold text-white">{{ auth()->user()->rank }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-black/20 px-4 py-2 rounded-xl border border-white/5">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                        <span class="text-gray-300">Available</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                        <span class="text-gray-300">Locked</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-2 h-2 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.6)]"></span>
                        <span class="text-gray-300">Unlocked</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="skillTreeStage" class="w-full h-full cursor-grab active:cursor-grabbing">
        <div id="skillTreeViewport" class="absolute origin-top-left transition-transform duration-700 ease-out"
            style="width:2000px; height:2000px;">
            <canvas id="skillTreeCanvas" width="2000" height="2000"
                class="absolute top-0 left-0 pointer-events-none"></canvas>

            <div class="relative w-full h-full">
                @foreach($nodes as $node)
                @php
                $isUnlocked = in_array($node->id, $unlockedNodeIds);
                $canUnlock = $node->canBeUnlockedBy(auth()->user());
                $state = $isUnlocked ? 'unlocked' : ($canUnlock ? 'available' : 'locked');
                @endphp

                <div id="node-{{ $node->id }}"
                    class="skill-node absolute transition-transform duration-300 hover:z-20"
                    data-node-id="{{ $node->id }}"
                    data-parent-id="{{ $node->parent_node_id }}"
                    data-state="{{ $state }}"
                    style="left: {{ $node->x_position }}%; top: {{ $node->y_position }}%; transform: translate(-50%, -50%);"
                    @click="openNodeModal({{ $node->id }})">

                    <div class="relative group">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-2xl border-2 transition-all duration-300 group-hover:scale-110
                                @if($state === 'unlocked') border-purple-400 bg-purple-900/80 shadow-purple-500/20 
                                @elseif($state === 'available') border-green-400 bg-green-900/40 animate-pulse shadow-green-500/40
                                @else border-gray-600 bg-gray-800/80 opacity-60 @endif">

                            <i class="{{ $node->skill->icon ?? 'fas fa-code' }} text-xl text-white"></i>
                        </div>

                        <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 whitespace-nowrap pointer-events-none">
                            <p class="text-[10px] font-bold text-center tracking-tight
                                    @if($state === 'unlocked') text-purple-300
                                    @elseif($state === 'available') text-green-300
                                    @else text-gray-500 @endif">
                                {{ Str::limit($node->title, 15) }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- <div x-show="tooltip.show" x-transition class="fixed z-50 pointer-events-none"
        :style="`left: ${tooltip.x}px; top: ${tooltip.y}px; transform: translate(-50%, -100%); margin-top: -10px;`">
        <div
            class="glow-border rounded-xl p-4 bg-gradient-to-br from-purple-900/95 to-purple-950/95 backdrop-blur-xl shadow-2xl max-w-xs border border-purple-500/30">
            <template x-if="tooltip.node">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <i :class="tooltip.node.skill.icon" class="text-xl text-purple-300"></i>
                        <h4 class="font-display font-bold text-white" x-text="tooltip.node.title"></h4>
                    </div>
                    <p class="text-xs text-purple-200 mb-3" x-text="tooltip.node.description"></p>
                    <div class="mt-2 pt-2 border-t border-purple-500/30 text-center">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-purple-400"
                            x-text="tooltip.state"></span>
                    </div>
                </div>
            </template>
        </div>
    </div> -->

    <div x-show="modal.open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-md">

        <div class="relative w-full max-w-lg bg-[#0f1229] border border-purple-500/30 rounded-2xl p-8 shadow-2xl">

            <!-- Dedicated Close Button -->
            <button @click="closeModal()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full 
                   bg-gray-800 hover:bg-red-600 text-gray-300 hover:text-white transition">
                ✕
            </button>

            <template x-if="modal.node">
                <div>
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 rounded-lg bg-purple-500/20 flex items-center justify-center border border-purple-500/40">
                            <i :class="modal.node.skill.icon" class="text-2xl text-purple-300"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" x-text="modal.node.title"></h3>
                            <p class="text-purple-400 text-sm" x-text="modal.node.skill.name"></p>
                        </div>
                    </div>

                    <p class="text-gray-300 mb-6 leading-relaxed" x-text="modal.node.description"></p>

                    <template x-if="modal.state === 'available'">
                        <form :action="`/skill-tree/${modal.node.id}/unlock`" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-2 rounded-lg bg-green-600 hover:bg-green-500 text-white font-bold transition">
                                Unlock Skill
                            </button>
                        </form>
                    </template>
                </div>
            </template>

        </div>
    </div>

    <script>
    function skillTreeApp() {
        return {
            scale: 1,
            offsetX: window.innerWidth / 2,
            offsetY: 70,
            isDragging: false,
            startX: 0,
            startY: 0,
            nodes: @json($nodes),
            // tooltip: {
            //     show: false,
            //     x: 0,
            //     y: 0,
            //     node: null,
            //     state: ''
            // },
            modal: {
                open: false,
                node: null,
                state: null
            },

            init() {
                const stage = document.getElementById("skillTreeStage");
                const viewport = document.getElementById("skillTreeViewport");
                const canvas = document.getElementById("skillTreeCanvas");
                const ctx = canvas.getContext("2d");

                const updateTransform = () => {
                    viewport.style.transform =
                        `translate(${this.offsetX}px, ${this.offsetY}px) scale(${this.scale})`;
                };

                const centerOnProgress = () => {
                    const available = document.querySelector('[data-state="available"]');
                    const unlocked = Array.from(document.querySelectorAll('[data-state="unlocked"]')).pop();
                    const target = available || unlocked;

                    if (target) {
                        // Center the target node in the screen
                        this.offsetX = (window.innerWidth / 2) - target.offsetLeft;
                        this.offsetY = (window.innerHeight / 2) - target.offsetTop;
                    } else {
                        this.offsetX = window.innerWidth / 2 - 1000;
                        this.offsetY = 150;
                    }
                    updateTransform();
                };

                const drawLines = () => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.lineWidth = 5;
                    ctx.lineCap = "round";

                    document.querySelectorAll('.skill-node').forEach(node => {
                        const parentId = node.getAttribute('data-parent-id');
                        if (parentId && parentId !== "0") {
                            const parentEl = document.getElementById(`node-${parentId}`);
                            if (parentEl) {
                                // Using offsetTop/Left ensures we get the position relative to the viewport container
                                const x1 = parentEl.offsetLeft;
                                const y1 = parentEl.offsetTop;
                                const x2 = node.offsetLeft;
                                const y2 = node.offsetTop;

                                const state = node.getAttribute('data-state');

                                // Draw Line
                                ctx.beginPath();
                                ctx.moveTo(x1, y1);
                                ctx.lineTo(x2, y2);

                                // Style
                                ctx.strokeStyle = state === 'unlocked' ? '#a855f7' : '#334155';
                                ctx.globalAlpha = state === 'locked' ? 0.3 : 1;
                                ctx.stroke();
                            }
                        }
                    });
                };

                // Dragging Logic
                stage.addEventListener("mousedown", e => {
                    if (e.target.closest('.skill-node')) return;
                    this.isDragging = true;
                    this.startX = e.clientX - this.offsetX;
                    this.startY = e.clientY - this.offsetY;
                });

                window.addEventListener("mousemove", e => {
                    if (!this.isDragging) return;
                    this.offsetX = e.clientX - this.startX;
                    this.offsetY = e.clientY - this.startY;
                    updateTransform();
                });

                window.addEventListener("mouseup", () => this.isDragging = false);

                // Zoom Logic
                stage.addEventListener("wheel", e => {
                    e.preventDefault();

                    const rect = stage.getBoundingClientRect();

                    // Mouse position relative to stage
                    const mouseX = e.clientX - rect.left;
                    const mouseY = e.clientY - rect.top;

                    const zoomFactor = e.deltaY > 0 ? 0.9 : 1.1;
                    const newScale = Math.min(Math.max(this.scale * zoomFactor, 0.25), 2);

                    // Adjust offset so zoom focuses on cursor
                    this.offsetX = mouseX - (mouseX - this.offsetX) * (newScale / this.scale);
                    this.offsetY = mouseY - (mouseY - this.offsetY) * (newScale / this.scale);

                    this.scale = newScale;

                    updateTransform();
                }, {
                    passive: false
                });

                // Initial view setup
                setTimeout(drawLines, 150);
                window.addEventListener("resize", drawLines);
                centerOnProgress();
            },

            // showTooltip(nodeId, event) {
            //     const node = this.nodes.find(n => n.id === nodeId);
            //     if (!node) return;
            //     const rect = event.currentTarget.getBoundingClientRect();
            //     this.tooltip.x = rect.left + rect.width / 2;
            //     this.tooltip.y = rect.top;
            //     this.tooltip.node = node;
            //     this.tooltip.state = event.currentTarget.dataset.state;
            //     this.tooltip.show = true;
            // },

            // hideTooltip() {
            //     this.tooltip.show = false;
            // },

            openNodeModal(nodeId) {
                const node = this.nodes.find(n => n.id === nodeId);
                this.modal.node = node;
                this.modal.state = document.getElementById(`node-${nodeId}`).dataset.state;
                this.modal.open = true;
            },

            closeModal() {
                this.modal.open = false;
                this.modal.node = null;
                this.modal.state = null;
            }
        }
    }
    </script>

    <style>
    [x-cloak] {
        display: none !important;
    }

    .glow-border {
        box-shadow: 0 0 20px rgba(139, 92, 246, 0.15);
    }

    #skillTreeStage {
        background-image: radial-gradient(circle at 2px 2px, rgba(255, 255, 255, 0.03) 1px, transparent 0);
        background-size: 50px 50px;
    }
    </style>
    @endsection