<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\OptimizeResumePrompt;
use App\Mcp\Resources\UserProfileResource;
use App\Mcp\Tools\AddUserProject;
use App\Mcp\Tools\GetUserProjects;
use App\Mcp\Tools\GetUserSkills;
use App\Mcp\Tools\GetUserStats;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('LvlUp Portfolio Server')]
#[Version('1.0.0')]
#[Instructions('This server provides information about LvlUp developers, their project history, gamification levels, stats, and skills.')]
class PortfolioServer extends Server
{
    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        GetUserStats::class,
        GetUserProjects::class,
        AddUserProject::class,
        GetUserSkills::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        UserProfileResource::class,
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        OptimizeResumePrompt::class,
    ];
}
