<?php

namespace LunarisForge\Pipelines;

use LunarisForge\Http\Request;
use LunarisForge\Http\Response;

class Pipeline
{
    /**
     * @var array<callable>
     */
    protected array $stages;

    /**
     * @param  array<callable>  $stages
     */
    public function __construct(array $stages = [])
    {
        $this->stages = $stages;
    }

    /**
     * @param  Request  $request
     * @param  callable  $handler
     *
     * @return Response
     */
    public function handle(Request $request, callable $handler): Response
    {
        $pipeline = array_reduce(
            array_reverse($this->stages),
            function ($nextStage, $currentStage) {
                return function (Request $request) use ($currentStage, $nextStage) {
                    return $currentStage($request, $nextStage);
                };
            },
            $handler
        );

        return $pipeline($request);
    }
}
