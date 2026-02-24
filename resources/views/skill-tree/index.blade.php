@extends('layouts.app')

@section('title', 'Skill Tree')
@section('page_title', 'Skill Tree')
@section('page_subtitle', 'Unlock your path to mastery')

@section('content')
<div class="max-w-7xl mx-auto" x-data="skillTreeApp()">
    <div class="glow-border rounded-2xl p-6 mb-8 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-network-wired text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-blue-300">Skills Unlocked</p>
                        <p class="font-display text-2xl font-bold text-white">{{ count($unlockedNodeIds) }} /
                            {{ $nodes->count() }}</p>
                    </div>
                </div>

                <div class="w-px h-12 bg-purple-500/30"></div>

                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-level-up-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-pink-300">Your Level</p>
                        <p class="font-display text-2xl font-bold text-white">{{ auth()->user()->level }}</p>
                    </div>
                </div>

                <div class="w-px h-12 bg-purple-500/30"></div>

                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-trophy text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-amber-300">Rank</p>
                        <p class="font-display text-2xl font-bold text-white">{{ auth()->user()->rank }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-green-500 shadow-lg shadow-green-500/50"></span>
                    <span class="text-gray-300">Available</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                    <span class="text-gray-300">Locked</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-purple-500 shadow-lg shadow-purple-500/50"></span>
                    <span class="text-gray-300">Unlocked</span>
                </div>
            </div>
        </div>
    </div>

    <div
        class="relative glow-border rounded-3xl p-8 bg-gradient-to-br from-[#1a1d3e]/80 to-[#0a0e27]/80 backdrop-blur overflow-hidden relative">
        <div class="absolute inset-0 overflow-hidden pointer-events-none" @click="modal.open=false">
            <div class="absolute w-1 h-1 bg-purple-400 rounded-full animate-ping"
                style="top: 10%; left: 20%; animation-delay: 0s;"></div>
            <div class="absolute w-1 h-1 bg-pink-400 rounded-full animate-ping"
                style="top: 30%; left: 60%; animation-delay: 1s;"></div>
            <div class="absolute w-1 h-1 bg-blue-400 rounded-full animate-ping"
                style="top: 60%; left: 40%; animation-delay: 2s;"></div>
            <div class="absolute w-1 h-1 bg-amber-400 rounded-full animate-ping"
                style="top: 80%; left: 80%; animation-delay: 1.5s;"></div>
            <div class="absolute w-1 h-1 bg-green-400 rounded-full animate-ping"
                style="top: 50%; left: 10%; animation-delay: 0.5s;"></div>
        </div>

        <div id="skillTreeStage" class="relative overflow-hidden rounded-xl" style="min-height: 800px;">
            <div id="skillTreeViewport" class="relative" style="width:1200px;height:800px;">
                <canvas id="skillTreeCanvas" width="1200" height="800"
                    class="absolute top-0 left-0 pointer-events-none"></canvas>

                <div id="skillNodes" class="relative" style="width: 1200px; height: 800px;">
                    @foreach($nodes as $node)
                    @php
                    $isUnlocked = in_array($node->id, $unlockedNodeIds);
                    $canUnlock = $node->canBeUnlockedBy(auth()->user());
                    $taskProgress = $node->calculateTaskProgress(auth()->user());

                    // Determine node state
                    $state = 'locked';
                    if ($isUnlocked) {
                    $state = 'unlocked';
                    } elseif ($canUnlock) {
                    $state = 'available';
                    }

                    // Get rarity color from skill
                    $rarityColor = $node->skill->rarity_color ?? '#6b7280';
                    @endphp

                    <div id="node-{{ $node->id }}" class="skill-node absolute cursor-pointer transition-all duration-300"
                        data-node-id="{{ $node->id }}" data-parent-id="{{ $node->parent_node_id }}"
                        data-state="{{ $state }}"
                        style="left: {{ $node->x_position }}%; top: {{ $node->y_position }}%; transform: translate(-50%, -50%);"
                        @mouseenter="showTooltip({{ $node->id }}, $event)" @mouseleave="hideTooltip()"
                        @click="openNodeModal({{ $node->id }})">
                        <div class="relative group">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center shadow-xl transition-all duration-300 group-hover:scale-110
                            @if($state === 'unlocked')
                                rarity-{{ $node->skill->rarity ?? 'common' }}
                            @elseif($state === 'available')
                                bg-gradient-to-br from-green-500 to-green-600 animate-pulse shadow-green-500/50
                            @else
                                bg-gradient-to-br from-gray-600 to-gray-700 opacity-60
                            @endif
                        ">
                                <i class="{{ $node->skill->icon ?? 'fas fa-code' }} text-2xl text-white"></i>
                            </div>

                            @if($state === 'available')
                            <div class="absolute inset-0 rounded-full bg-green-400/30 blur-xl animate-pulse"></div>
                            @endif

                            <div
                                class="absolute -bottom-10 left-1/2 -translate-x-1/2 whitespace-nowrap pointer-events-none">
                                <p class="text-xs font-bold text-center transition-colors duration-300
                                @if($state === 'unlocked')
                                    text-purple-300
                                @elseif($state === 'available')
                                    text-green-300
                                @else
                                    text-gray-500
                                @endif
                            ">
                                    {{ Str::limit($node->title, 20) }}
                                </p>
                            </div>

                            <div
                                class="absolute -top-2 -left-2 w-6 h-6 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center border-2 border-indigo-300 shadow-lg">
                                <span class="text-xs font-bold text-white">{{ $node->tier }}</span>
                            </div>

                            @if($state === 'unlocked')
                            <div
                                class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-green-300 shadow-lg">
                                <i class="fas fa-check text-xs text-white"></i>
                            </div>
                            @endif

                            @if($state === 'locked' && $node->required_level > auth()->user()->level)
                            <div
                                class="absolute -bottom-2 -right-2 w-7 h-7 bg-red-500 rounded-full flex items-center justify-center border-2 border-red-300 shadow-lg">
                                <span class="text-xs font-bold text-white">{{ $node->required_level }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div x-show="tooltip.show" x-transition class="fixed z-50 pointer-events-none"
        :style="`left: ${tooltip.x}px; top: ${tooltip.y}px; transform: translate(-50%, -100%); margin-top: -10px;`">
        <div
            class="glow-border rounded-xl p-4 bg-gradient-to-br from-purple-900/95 to-purple-950/95 backdrop-blur-xl shadow-2xl max-w-xs">
            <template x-if="tooltip.node">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <i :class="tooltip.node.skill.icon" class="text-xl text-purple-300"></i>
                        <h4 class="font-display font-bold text-white" x-text="tooltip.node.title"></h4>
                    </div>
                    <p class="text-sm text-purple-200 mb-3" x-text="tooltip.node.description"></p>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-purple-300">Level Required:</span>
                            <span class="font-bold"
                                :class="tooltip.node.required_level <= {{ auth()->user()->level }} ? 'text-green-400' : 'text-red-400'"
                                x-text="tooltip.node.required_level"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-purple-300">Tier:</span>
                            <span class="font-bold text-white" x-text="tooltip.node.tier"></span>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-purple-500/30">
                        <p class="text-xs text-center" :class="{
                            'text-green-400 font-bold': tooltip.state === 'available',
                            'text-purple-400': tooltip.state === 'unlocked',
                            'text-gray-400': tooltip.state === 'locked'
                        }">
                            <span x-show="tooltip.state === 'available'">✨ Click to unlock!</span>
                            <span x-show="tooltip.state === 'unlocked'">✓ Unlocked</span>
                            <span x-show="tooltip.state === 'locked'">🔒 Locked</span>
                        </p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div x-show="modal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80">
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/95 to-purple-950/95 backdrop-blur-xl max-w-lg w-full"
            @click.away="modal.open = false">
            <template x-if="modal.node">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center"
                                :class="`rarity-${modal.node.skill.rarity || 'common'}`">
                                <i :class="modal.node.skill.icon" class="text-3xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-display text-2xl font-bold text-white" x-text="modal.node.title"></h3>
                                <p class="text-sm text-purple-300" x-text="modal.node.skill.name"></p>
                            </div>
                        </div>
                        <button @click="modal.open = false" class="text-gray-400 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <p class="text-purple-100 mb-6" x-text="modal.node.description"></p>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-purple-500/10">
                            <span class="text-sm text-purple-300">Required Level:</span>
                            <span class="font-bold"
                                :class="modal.node.required_level <= {{ auth()->user()->level }} ? 'text-green-400' : 'text-red-400'"
                                x-text="modal.node.required_level"></span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-purple-500/10">
                            <span class="text-sm text-purple-300">Tier:</span>
                            <span class="font-bold text-white" x-text="modal.node.tier"></span>
                        </div>
                    </div>

                    <template x-if="modal.state === 'unlocked'">
                        <div class="bg-green-500/20 border-2 border-green-500/40 rounded-xl p-4 text-center">
                            <i class="fas fa-check-circle text-green-400 text-3xl mb-2"></i>
                            <p class="text-green-300 font-bold text-lg">Already Unlocked!</p>
                            <p class="text-sm text-green-300/70 mt-1">You have mastered this skill</p>
                        </div>
                    </template>

                    <template x-if="modal.state === 'available'">
                        <form :action="`/skill-tree/${modal.node.id}/unlock`" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full btn-glow bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 px-6 py-4 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:scale-105">
                                <span class="relative z-10 flex items-center justify-center gap-2 text-lg">
                                    <i class="fas fa-unlock"></i>
                                    Unlock This Skill
                                </span>
                            </button>
                        </form>
                    </template>

                    <template x-if="modal.state === 'locked'">
                        <div class="bg-red-500/20 border-2 border-red-500/40 rounded-xl p-4 text-center">
                            <i class="fas fa-lock text-red-400 text-3xl mb-2"></i>
                            <p class="text-red-300 font-bold text-lg">Requirements Not Met</p>
                            <p class="text-sm text-red-300/70 mt-2">
                                Complete the requirements to unlock this skill
                            </p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function skillTreeApp() {
    return {
        scale: 1,
        offsetX: 0,
        offsetY: 0,
        isDragging: false,
        startX: 0,
        startY: 0,
        nodes: @json($nodes),
        tooltip: { show: false, x: 0, y: 0, node: null, state: null, taskProgress: [] },
        modal: { open: false, node: null, state: null, taskProgress: [], parentUnlocked: false },

        init() {
            const container = document.getElementById("skillTreeStage");
            const viewport = document.getElementById("skillTreeViewport");
            const canvas = document.getElementById("skillTreeCanvas");
            const ctx = canvas.getContext("2d");

            const updateTransform = () => {
                viewport.style.transform = `translate(${this.offsetX}px, ${this.offsetY}px) scale(${this.scale})`;
            };

            const drawLines = () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.strokeStyle = "#94a3b8";
                ctx.lineWidth = 3;

                document.querySelectorAll('.skill-node').forEach(node => {
                    const parentId = node.getAttribute('data-parent-id');
                    if (parentId && parentId !== "0") {
                        const parentEl = document.getElementById(`node-${parentId}`);
                        if (parentEl) {
                            ctx.beginPath();
                            // offsetLeft/Top are used because they are relative to the viewport container
                            ctx.moveTo(parentEl.offsetLeft, parentEl.offsetTop);
                            ctx.lineTo(node.offsetLeft, node.offsetTop);
                            ctx.stroke();
                        }
                    }
                });
            };

            // Panning
            container.addEventListener("mousedown", e => {
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

            // Zooming
            container.addEventListener("wheel", e => {
                e.preventDefault();
                const zoomSpeed = 0.0015;
                this.scale -= e.deltaY * zoomSpeed;
                this.scale = Math.min(Math.max(this.scale, 0.3), 2.5);
                updateTransform();
            }, { passive: false });

            // Initial Draw
            drawLines();
            window.addEventListener("resize", drawLines);
        },

        showTooltip(nodeId, event) {
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;
            const rect = event.currentTarget.getBoundingClientRect();
            this.tooltip.x = rect.left + rect.width / 2;
            this.tooltip.y = rect.top;
            this.tooltip.node = node;
            this.tooltip.state = event.currentTarget.dataset.state;
            this.tooltip.show = true;
        },

        hideTooltip() { this.tooltip.show = false; },

        openNodeModal(nodeId) {
            fetch(`/skill-tree/${nodeId}`)
                .then(res => res.json())
                .then(data => {
                    this.modal.node = data.node;
                    this.modal.state = data.state;
                    this.modal.open = true;
                });
        }
    }
}
</script>
@endsection