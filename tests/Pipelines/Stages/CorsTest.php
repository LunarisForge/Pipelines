<?php

namespace Tests\Pipeline\Stages;

use LunarisForge\Http\Enums\HttpStatus;
use LunarisForge\Http\Request;
use LunarisForge\Http\Response;
use LunarisForge\Pipelines\Stages\Cors;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CorsTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testCorsAddsHeaders(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('GET');

        $next = function (Request $request) {
            return new Response('Content');
        };

        $cors = new Cors();
        $response = $cors($request, $next);

        $this->assertEquals('*', $response->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals('GET, POST, PATCH, DELETE, OPTIONS', $response->getHeader('Access-Control-Allow-Methods'));
        $this->assertEquals('Content-Type, Authorization', $response->getHeader('Access-Control-Allow-Headers'));
        $this->assertEquals('Content', $response->getContents());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCorsHandlesPreflightRequest(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('OPTIONS');

        $next = function (Request $request) {
            return new Response('Should not be used');
        };

        $cors = new Cors();
        $response = $cors($request, $next);

        $this->assertEquals(HttpStatus::NO_CONTENT->code(), $response->getStatusCode());
        $this->assertEmpty($response->getContents());
    }
}
