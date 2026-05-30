<?php

use App\Mcp\Servers\PortfolioServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('portfolio', PortfolioServer::class);
Mcp::web('/mcp/portfolio', PortfolioServer::class);
