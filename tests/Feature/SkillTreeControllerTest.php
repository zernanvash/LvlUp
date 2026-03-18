<?php

use App\Models\User;
use App\Models\SkillNode;
use App\Models\Skill;

beforeEach(function () {
    $this->user = User::factory()->create([
        'level' => 5,
        'xp' => 100,
        'total_xp' => 500,
    ]);
});

it('displays skill tree index with user progress', function () {
    $this->actingAs($this->user);
    
    // Create skills first
    $skill1 = Skill::factory()->create(['name' => 'PHP', 'icon' => 'fab fa-php']);
    $skill2 = Skill::factory()->create(['name' => 'Laravel', 'icon' => 'fab fa-laravel']);
    
    // Create some skill nodes
    $rootNode = SkillNode::factory()->create([
        'title' => 'Root Node',
        'required_level' => 1,
        'parent_node_id' => null,
        'skill_id' => $skill1->id,
        'task_requirements' => null,
    ]);
    
    $childNode = SkillNode::factory()->create([
        'title' => 'Child Node',
        'required_level' => 5,
        'parent_node_id' => $rootNode->id,
        'skill_id' => $skill2->id,
        'task_requirements' => null,
    ]);
    
    // Test controller returns correct data (without rendering view)
    $controller = new \App\Http\Controllers\SkillTreeController();
    $response = $controller->index();
    
    expect($response)->toBeInstanceOf(\Illuminate\View\View::class);
    expect($response->getData())->toHaveKeys(['nodes', 'unlockedNodeIds']);
    expect($response->getData()['nodes'])->toHaveCount(2);
});

it('shows node details with requirements and progress', function () {
    $this->actingAs($this->user);
    
    $node = SkillNode::factory()->create([
        'title' => 'Test Node',
        'description' => 'Test Description',
        'required_level' => 3,
        'task_requirements' => [
            [
                'type' => 'project_count',
                'description' => 'Upload 2 projects',
                'required' => 2,
            ],
        ],
    ]);
    
    // Test controller returns correct data (without rendering view)
    $controller = new \App\Http\Controllers\SkillTreeController();
    $response = $controller->show($node);
    
    expect($response)->toBeInstanceOf(\Illuminate\View\View::class);
    expect($response->getData())->toHaveKeys(['node', 'state', 'requirements']);
    expect($response->getData()['node']->id)->toBe($node->id);
});

it('unlocks node when all requirements are met', function () {
    $this->actingAs($this->user);
    
    // Create a node that can be unlocked
    $node = SkillNode::factory()->create([
        'title' => 'Unlockable Node',
        'required_level' => 1,
        'parent_node_id' => null,
        'task_requirements' => null,
    ]);
    
    $response = $this->post(route('skill-tree.unlock', $node));
    
    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    // Verify node is unlocked
    expect($this->user->fresh()->unlockedNodes->contains($node->id))->toBeTrue();
});

it('prevents unlocking node when level requirement not met', function () {
    $this->actingAs($this->user);
    
    // Create a node with high level requirement
    $node = SkillNode::factory()->create([
        'title' => 'High Level Node',
        'required_level' => 10,
        'parent_node_id' => null,
        'task_requirements' => null,
    ]);
    
    $response = $this->post(route('skill-tree.unlock', $node));
    
    $response->assertRedirect();
    $response->assertSessionHas('error');
    
    // Verify node is not unlocked
    expect($this->user->fresh()->unlockedNodes->contains($node->id))->toBeFalse();
});

it('prevents unlocking node when parent is not unlocked', function () {
    $this->actingAs($this->user);
    
    // Create parent and child nodes
    $parentNode = SkillNode::factory()->create([
        'title' => 'Parent Node',
        'required_level' => 1,
        'parent_node_id' => null,
    ]);
    
    $childNode = SkillNode::factory()->create([
        'title' => 'Child Node',
        'required_level' => 1,
        'parent_node_id' => $parentNode->id,
    ]);
    
    $response = $this->post(route('skill-tree.unlock', $childNode));
    
    $response->assertRedirect();
    $response->assertSessionHas('error');
    
    // Verify node is not unlocked
    expect($this->user->fresh()->unlockedNodes->contains($childNode->id))->toBeFalse();
});

it('prevents unlocking node when task requirements not met', function () {
    $this->actingAs($this->user);
    
    // Create a node with task requirements
    $node = SkillNode::factory()->create([
        'title' => 'Task Node',
        'required_level' => 1,
        'parent_node_id' => null,
        'task_requirements' => [
            [
                'type' => 'project_count',
                'description' => 'Upload 5 projects',
                'required' => 5,
            ],
        ],
    ]);
    
    $response = $this->post(route('skill-tree.unlock', $node));
    
    $response->assertRedirect();
    $response->assertSessionHas('error');
    
    // Verify node is not unlocked
    expect($this->user->fresh()->unlockedNodes->contains($node->id))->toBeFalse();
});

it('returns JSON progress with all nodes and user stats', function () {
    $this->actingAs($this->user);
    
    // Create some nodes
    $node1 = SkillNode::factory()->create([
        'title' => 'Node 1',
        'required_level' => 1,
    ]);
    
    $node2 = SkillNode::factory()->create([
        'title' => 'Node 2',
        'required_level' => 10,
    ]);
    
    // Unlock one node
    $this->user->unlockedNodes()->attach($node1->id, ['unlocked_at' => now()]);
    
    $response = $this->get(route('skill-tree.progress'));
    
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);
    
    $data = $response->json();
    
    expect($data)->toHaveKeys(['success', 'user', 'nodes', 'total_nodes', 'unlocked_count', 'available_count']);
    expect($data['user'])->toHaveKeys(['level', 'xp', 'total_xp', 'rank']);
    expect($data['nodes'])->toBeArray();
    expect($data['total_nodes'])->toBe(2);
    expect($data['unlocked_count'])->toBe(1);
});

it('prevents unlocking already unlocked node', function () {
    $this->actingAs($this->user);
    
    $node = SkillNode::factory()->create([
        'title' => 'Already Unlocked',
        'required_level' => 1,
    ]);
    
    // Unlock the node first
    $this->user->unlockedNodes()->attach($node->id, ['unlocked_at' => now()]);
    
    // Try to unlock again
    $response = $this->post(route('skill-tree.unlock', $node));
    
    $response->assertRedirect();
    $response->assertSessionHas('error');
});
