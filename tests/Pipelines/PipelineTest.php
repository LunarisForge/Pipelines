<?php

namespace Tests\Pipelines;

use LunarisForge\Http\Request;
use LunarisForge\Http\Response;
use LunarisForge\Pipelines\Pipeline;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class PipelineTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testPipelineExecutesStagesInOrder(): void
    {
        $request = $this->createMock(Request::class);

        $stage1 = function (Request $request, callable $next) {
            $response = $next($request);
            $response->setHeader('X-Stage-1', 'Stage 1 Executed');
            return $response;
        };

        $stage2 = function (Request $request, callable $next) {
            $response = $next($request);
            $response->setHeader('X-Stage-2', 'Stage 2 Executed');
            return $response;
        };

        $pipeline = new Pipeline([$stage1, $stage2]);

        $finalHandler = function (Request $request) {
            return new Response('Final Response');
        };

        $response = $pipeline->handle($request, $finalHandler);

        $this->assertEquals('Final Response', $response->getContents());
        $this->assertEquals('Stage 1 Executed', $response->getHeader('X-Stage-1'));
        $this->assertEquals('Stage 2 Executed', $response->getHeader('X-Stage-2'));
    }
}
