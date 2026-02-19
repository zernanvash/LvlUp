@extends('layouts.app')

@section('title', 'Skill Tree')
@section('page_title', 'Skill Tree')
@section('page_subtitle', 'Unlock your path to mastery')

@section('content')
<div class="max-w-7xl mx-auto" x-data="skillTreeApp()">
    <!-- Stats Bar -->
    <div class="glow-border rounded-2xl p-6 mb-8 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-network-wired text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-blue-300">Skills Unlocked</p>
                        <p class="font-display text-2xl font-bold text-white">{{ count($unlockedNodeIds) }} / {{ $nodes->count() }}</p>
                    </div>
                </div>
                
                <div class="w-px h-12 bg-purple-500/30"></div>
                
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-level-up-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-pink-300">Your Level</p>
                        <p class="font-display text-2xl font-bold text-white">{{ auth()->user()->level }}</p>
                    </div>
                </div>
                
                <div class="w-px h-12 bg-purple-500/30"></div>
                
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
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

    <!-- Skill Tree Canvas -->
    <div class="glow-border rounded-3xl p-8 bg-gradient-to-br from-[#1a1d3e]/80 to-[#0a0e27]/80 backdrop-blur overflow-hidden relative">
        <!-- Particle background effect -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute w-1 h-1 bg-purple-400 rounded-full animate-ping" style="top: 10%; left: 20%; animation-delay: 0s;"></div>
            <div class="absolute w-1 h-1 bg-pink-400 rounded-full animate-ping" style="top: 30%; left: 60%; animation-delay: 1s;"></div>
            <div class="absolute w-1 h-1 bg-blue-400 rounded-full animate-ping" style="top: 60%; left: 40%; animation-delay: 2s;"></div>
            <div class="absolute w-1 h-1 bg-amber-400 rounded-full animate-ping" style="top: 80%; left: 80%; animation-delay: 1.5s;"></div>
            <div class="absolute w-1 h-1 bg-green-400 rounded-full animate-ping" style="top: 50%; left: 10%; animation-delay: 0.5s;"></div>
        </div>
        
        <div class="relative overflow-auto rounded-xl" style="min-height: 800px;">
            <!-- Canvas for connection lines -->
            <canvas id="skillTreeCanvas" width="1200" height="800" class="absolute top-0 left-0 pointer-events-none"></canvas>
            
            <!-- Skill Nodes Container -->
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
                
                <div 
                    class="skill-node absolute cursor-pointer transition-all duration-300"
                    data-node-id="{{ $node->id }}"
                    data-state="{{ $state }}"
                    style="left: {{ $node->x_position }}%; top: {{ $node->y_position }}%; transform: translate(-50%, -50%);"
                    @mouseenter="showTooltip({{ $node->id }}, $event)"
                    @mouseleave="hideTooltip()"
                    @click="openNodeModal({{ $node->id }})"
                >
                    <div class="relative group">
                        <!-- Node Circle with rarity-based styling -->
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
                        
                        <!-- Glow effect for available nodes -->
                        @if($state === 'available')
                        <div class="absolute inset-0 rounded-full bg-green-400/30 blur-xl animate-pulse"></div>
                        @endif
                        
                        <!-- Node Title -->
                        <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 whitespace-nowrap pointer-events-none">
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
                        
                        <!-- Tier Badge -->
                        <div class="absolute -top-2 -left-2 w-6 h-6 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center border-2 border-indigo-300 shadow-lg">
                            <span class="text-xs font-bold text-white">{{ $node->tier }}</span>
                        </div>
                        
                        <!-- Unlocked Check -->
                        @if($state === 'unlocked')
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-green-300 shadow-lg">
                            <i class="fas fa-check text-xs text-white"></i>
                        </div>
                        @endif
                        
                        <!-- Level Requirement Badge -->
                        @if($state === 'locked' && $node->required_level > auth()->user()->level)
                        <div class="absolute -bottom-2 -right-2 w-7 h-7 bg-red-500 rounded-full flex items-center justify-center border-2 border-red-300 shadow-lg">
                            <span class="text-xs font-bold text-white">{{ $node->required_level }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tooltip -->
    <div 
        x-show="tooltip.show" 
        x-transition
        class="fixed z-50 pointer-events-none"
        :style="`left: ${tooltip.x}px; top: ${tooltip.y}px; transform: translate(-50%, -100%); margin-top: -10px;`"
    >
        <div class="glow-border rounded-xl p-4 bg-gradient-to-br from-purple-900/95 to-purple-950/95 backdrop-blur-xl shadow-2xl max-w-xs">
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
                            <span class="font-bold" :class="tooltip.node.required_level <= {{ auth()->user()->level }} ? 'text-green-400' : 'text-red-400'" x-text="tooltip.node.required_level"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-purple-300">Tier:</span>
                            <span class="font-bold text-white" x-text="tooltip.node.tier"></span>
                        </div>
                    </div>
                    
                    <!-- Task Requirements -->
                    <template x-if="tooltip.node.task_requirements && tooltip.node.task_requirements.length > 0">
                        <div class="mt-3 pt-3 border-t border-purple-500/30">
                            <p class="text-xs font-bold text-purple-300 mb-2">Task Requirements:</p>
                            <div class="space-y-1">
                                <template x-for="task in tooltip.taskProgress" :key="task.description">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-purple-200" x-text="task.description"></span>
                                        <span class="font-bold" :class="task.completed ? 'text-green-400' : 'text-amber-400'" x-text="`${task.current}/${task.required}`"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    
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

    <!-- Node Details Modal -->
    <div 
        x-show="modal.open" 
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80"
        @click.self="modal.open = false"
    >
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/95 to-purple-950/95 backdrop-blur-xl max-w-lg w-full" @click.away="modal.open = false">
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
                            <span class="font-bold" :class="modal.node.required_level <= {{ auth()->user()->level }} ? 'text-green-400' : 'text-red-400'" x-text="modal.node.required_level"></span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-purple-500/10">
                            <span class="text-sm text-purple-300">Tier:</span>
                            <span class="font-bold text-white" x-text="modal.node.tier"></span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-purple-500/10">
                            <span class="text-sm text-purple-300">Rarity:</span>
                            <span class="font-bold text-white capitalize" x-text="modal.node.skill.rarity || 'common'"></span>
                        </div>
                    </div>
                    
                    <!-- Parent Requirement -->
                    <template x-if="modal.node.parent_node_id">
                        <div class="mb-6 p-4 rounded-xl bg-indigo-500/10 border border-indigo-500/30">
                            <p class="text-sm font-bold text-indigo-300 mb-2">Parent Node Required:</p>
                            <div class="flex items-center justify-between">
                                <span class="text-indigo-200" x-text="modal.node.parent?.title || 'Unknown'"></span>
                                <span :class="modal.parentUnlocked ? 'text-green-400' : 'text-red-400'">
                                    <i :class="modal.parentUnlocked ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                </span>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Task Requirements -->
                    <template x-if="modal.taskProgress && modal.taskProgress.length > 0">
                        <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30">
                            <p class="text-sm font-bold text-amber-300 mb-3">Task Requirements:</p>
                            <div class="space-y-2">
                                <template x-for="task in modal.taskProgress" :key="task.description">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1">
                                            <p class="text-sm text-amber-200" x-text="task.description"></p>
                                            <div class="mt-1 h-2 bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-amber-500 to-amber-600 transition-all duration-300"
                                                    :style="`width: ${Math.min(100, (task.current / task.required) * 100)}%`"></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-bold" :class="task.completed ? 'text-green-400' : 'text-amber-400'" x-text="`${task.current}/${task.required}`"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Action Buttons -->
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
                            <button 
                                type="submit"
                                class="w-full btn-glow bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 px-6 py-4 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:scale-105"
                            >
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
                                Complete the requirements above to unlock this skill
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
        tooltip: {
            show: false,
            x: 0,
            y: 0,
            node: null,
            state: null,
            taskProgress: []
        },
        modal: {
            open: false,
            node: null,
            state: null,
            taskProgress: [],
            parentUnlocked: false
        },
        nodes: @json($nodes),
        unlockedNodeIds: @json($unlockedNodeIds),
        
        init() {
            this.$nextTick(() => {
                this.drawConnections();
            });
        },
        
        drawConnections() {
            const canvas = document.getElementById('skillTreeCanvas');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            this.nodes.forEach(node => {
                if (node.parent_node_id) {
                    const parent = this.nodes.find(n => n.id === node.parent_node_id);
                    if (parent) {
                        const x1 = (parent.x_position / 100) * canvas.width;
                        const y1 = (parent.y_position / 100) * canvas.height;
                        const x2 = (node.x_position / 100) * canvas.width;
                        const y2 = (node.y_position / 100) * canvas.height;
                        
                        // Determine line color based on unlock status
                        const isUnlocked = this.unlockedNodeIds.includes(node.id);
                        const parentUnlocked = this.unlockedNodeIds.includes(parent.id);
                        
                        if (isUnlocked && parentUnlocked) {
                            ctx.strokeStyle = 'rgba(168, 85, 247, 0.6)'; // Purple for unlocked path
                            ctx.lineWidth = 3;
                        } else if (parentUnlocked) {
                            ctx.strokeStyle = 'rgba(34, 197, 94, 0.4)'; // Green for available path
                            ctx.lineWidth = 2;
                        } else {
                            ctx.strokeStyle = 'rgba(107, 114, 128, 0.3)'; // Gray for locked path
                            ctx.lineWidth = 2;
                        }
                        
                        ctx.beginPath();
                        ctx.moveTo(x1, y1);
                        ctx.lineTo(x2, y2);
                        ctx.stroke();
                    }
                }
            });
        },
        
        showTooltip(nodeId, event) {
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;
            
            const element = event.currentTarget;
            const rect = element.getBoundingClientRect();
            
            this.tooltip.x = rect.left + rect.width / 2;
            this.tooltip.y = rect.top;
            this.tooltip.node = node;
            this.tooltip.state = element.dataset.state;
            
            // Fetch task progress
            fetch(`/skill-tree/${nodeId}`)
                .then(res => res.json())
                .then(data => {
                    this.tooltip.taskProgress = data.requirements?.tasks || [];
                })
                .catch(() => {
                    this.tooltip.taskProgress = [];
                });
            
            this.tooltip.show = true;
        },
        
        hideTooltip() {
            this.tooltip.show = false;
        },
        
        openNodeModal(nodeId) {
            fetch(`/skill-tree/${nodeId}`)
                .then(res => res.json())
                .then(data => {
                    this.modal.node = data.node;
                    this.modal.state = data.state;
                    this.modal.taskProgress = data.requirements?.tasks || [];
                    this.modal.parentUnlocked = data.requirements?.parent?.unlocked || false;
                    this.modal.open = true;
                })
                .catch(err => {
                    console.error('Failed to load node details:', err);
                });
        }
    }
}
</script>
@endsection
