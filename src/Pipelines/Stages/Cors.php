<?php

namespace LunarisForge\Pipelines\Stages;

use LunarisForge\Http\Enums\HttpStatus;
use LunarisForge\Http\Request;
use LunarisForge\Http\Response;

class Cors
{
    /**
     * Handle the request and add CORS headers to the response
     *
     * @param  Request  $request
     * @param  callable  $handler
     * @return Response
     */
    public function __invoke(Request $request, callable $handler): Response
    {
        $response = $handler($request);

        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        // Handle preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            return new Response('', HttpStatus::NO_CONTENT);
        }

        return $response;
    }
}
