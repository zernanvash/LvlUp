@extends('layouts.app')

@section('title', 'Skill Tree')
@section('page_title', 'Skill Tree')
@section('page_subtitle', 'Unlock your path to mastery')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Stats Bar -->
    <div class="glow-border rounded-2xl p-6 mb-8 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-purple-300">Skill Points</p>
                        <p class="font-display text-2xl font-bold text-white">{{ auth()->user()->skill_points }}</p>
                    </div>
                </div>
                
                <div class="w-px h-12 bg-purple-500/30"></div>
                
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-network-wired text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-blue-300">Skills Unlocked</p>
                        <p class="font-display text-2xl font-bold text-white">{{ count($unlockedNodeIds) }}</p>
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
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-gray-300">Available</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                    <span class="text-gray-300">Locked</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                    <span class="text-gray-300">Unlocked</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Skill Tree Canvas -->
    <div class="glow-border rounded-3xl p-8 bg-gradient-to-br from-[#1a1d3e]/80 to-[#0a0e27]/80 backdrop-blur">
        <div class="relative overflow-x-auto" style="min-height: 800px;">
            <canvas id="skillTreeCanvas" width="1200" height="800" class="w-full"></canvas>
            
            <!-- Skill Nodes -->
            <div id="skillNodes" class="relative" style="width: 1200px; height: 800px;">
                @foreach($nodes as $node)
                <div 
                    class="skill-node absolute cursor-pointer transition-all duration-300 hover:scale-110"
                    data-node-id="{{ $node->id }}"
                    data-unlocked="{{ in_array($node->id, $unlockedNodeIds) ? 'true' : 'false' }}"
                    data-can-unlock="{{ $node->canBeUnlockedBy(auth()->user()) ? 'true' : 'false' }}"
                    style="left: {{ $node->x_position }}%; top: {{ $node->y_position }}%; transform: translate(-50%, -50%);"
                    @click="showNodeDetails({{ $node->id }})"
                >
                    <div class="relative">
                        <!-- Node Circle -->
                        <div class="w-20 h-20 rounded-full flex items-center justify-center shadow-lg transition-all
                            @if(in_array($node->id, $unlockedNodeIds))
                                bg-gradient-to-br from-purple-500 to-purple-600 rarity-epic
                            @elseif($node->canBeUnlockedBy(auth()->user()))
                                bg-gradient-to-br from-green-500 to-green-600 animate-pulse
                            @else
                                bg-gradient-to-br from-gray-600 to-gray-700
                            @endif
                        ">
                            <i class="{{ $node->skill->icon }} text-2xl text-white"></i>
                        </div>
                        
                        <!-- Node Title -->
                        <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 whitespace-nowrap">
                            <p class="text-xs font-bold text-center
                                @if(in_array($node->id, $unlockedNodeIds))
                                    text-purple-300
                                @elseif($node->canBeUnlockedBy(auth()->user()))
                                    text-green-300
                                @else
                                    text-gray-400
                                @endif
                            ">
                                {{ $node->skill->name }}
                            </p>
                        </div>
                        
                        <!-- Cost Badge -->
                        @if(!in_array($node->id, $unlockedNodeIds) && $node->skill_point_cost > 0)
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center border-2 border-amber-300 shadow-lg">
                            <span class="text-xs font-bold text-white">{{ $node->skill_point_cost }}</span>
                        </div>
                        @endif
                        
                        <!-- Unlocked Check -->
                        @if(in_array($node->id, $unlockedNodeIds))
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-green-300 shadow-lg">
                            <i class="fas fa-check text-xs text-white"></i>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Node Details Modal -->
<div x-data="{ open: false, nodeData: null }" x-cloak>
    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80" @click.self="open = false">
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/90 to-purple-950/90 backdrop-blur max-w-md w-full" @click.away="open = false">
            <template x-if="nodeData">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                                <i :class="nodeData.skill.icon" class="text-3xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-display text-xl font-bold text-white" x-text="nodeData.title"></h3>
                                <p class="text-sm text-purple-300" x-text="nodeData.skill.name"></p>
                            </div>
                        </div>
                        <button @click="open = false" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <p class="text-purple-100 mb-6" x-text="nodeData.description"></p>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-purple-300">Required Level:</span>
                            <span class="font-bold text-white" x-text="nodeData.required_level"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-purple-300">Skill Point Cost:</span>
                            <span class="font-bold text-amber-400" x-text="nodeData.skill_point_cost"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-purple-300">Tier:</span>
                            <span class="font-bold text-white uppercase" x-text="nodeData.tier"></span>
                        </div>
                    </div>
                    
                    <template x-if="nodeData.is_unlocked">
                        <div class="bg-green-500/20 border-2 border-green-500/40 rounded-xl p-4 text-center">
                            <i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i>
                            <p class="text-green-300 font-bold">Already Unlocked!</p>
                        </div>
                    </template>
                    
                    <template x-if="!nodeData.is_unlocked && nodeData.can_unlock">
                        <button 
                            @click="unlockNode(nodeData.id)"
                            class="w-full btn-glow bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 px-6 py-3 rounded-xl font-bold shadow-lg transition"
                        >
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <i class="fas fa-unlock"></i>
                                Unlock Now
                            </span>
                        </button>
                    </template>
                    
                    <template x-if="!nodeData.is_unlocked && !nodeData.can_unlock">
                        <div class="bg-red-500/20 border-2 border-red-500/40 rounded-xl p-4 text-center">
                            <i class="fas fa-lock text-red-400 text-2xl mb-2"></i>
                            <p class="text-red-300 font-bold">Requirements Not Met</p>
                            <p class="text-sm text-red-300/70 mt-1">
                                Level {{ auth()->user()->level }} / {{ nodeData.required_level }} required
                            </p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    const canvas = document.getElementById('skillTreeCanvas');
    const ctx = canvas.getContext('2d');
    
    // Draw connections between nodes
    const nodes = @json($nodes);
    
    function drawConnections() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = 'rgba(167, 139, 250, 0.3)';
        ctx.lineWidth = 2;
        
        nodes.forEach(node => {
            if (node.parent_node_id) {
                const parent = nodes.find(n => n.id === node.parent_node_id);
                if (parent) {
                    const x1 = (parent.x_position / 100) * canvas.width;
                    const y1 = (parent.y_position / 100) * canvas.height;
                    const x2 = (node.x_position / 100) * canvas.width;
                    const y2 = (node.y_position / 100) * canvas.height;
                    
                    ctx.beginPath();
                    ctx.moveTo(x1, y1);
                    ctx.lineTo(x2, y2);
                    ctx.stroke();
                }
            }
        });
    }
    
    drawConnections();
    
    // Node details modal
    function showNodeDetails(nodeId) {
        fetch(`/skill-tree/${nodeId}/details`)
            .then(res => res.json())
            .then(data => {
                Alpine.store('modal', {
                    open: true,
                    nodeData: data.node
                });
            });
    }
    
    function unlockNode(nodeId) {
        if (!confirm('Are you sure you want to unlock this skill?')) return;
        
        fetch(`/skill-tree/${nodeId}/unlock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
</script>
@endsection
