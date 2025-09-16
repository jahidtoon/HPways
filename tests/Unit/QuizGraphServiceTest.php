<?php

namespace Tests\Unit;

use Tests\TestCase;

class QuizGraphServiceTest extends TestCase
{
    /**
     * Ensure the quiz spec service returns a well-formed structure.
     */
    public function test_build_spec_structure()
    {
        $service = app(\App\Services\QuizGraphService::class);
        $spec = $service->buildSpec();

        $this->assertIsArray($spec);
        $this->assertArrayHasKey('meta', $spec);
        $this->assertArrayHasKey('nodes', $spec);

        // Meta checks
        $meta = $spec['meta'];
        $this->assertIsArray($meta);
        $this->assertArrayHasKey('version', $meta);
        $this->assertArrayHasKey('root', $meta);
        $this->assertArrayHasKey('routes', $meta);
        $this->assertArrayHasKey('terminals', $meta);
        $this->assertArrayHasKey('actionable', $meta);
        $this->assertArrayHasKey('terminalToVisa', $meta);
        $this->assertArrayHasKey('terminalDetails', $meta);

        // Nodes list exists (may be empty if DB has no nodes in test env)
        $this->assertIsArray($spec['nodes']);

        // If nodes exist, validate shape of the first one
        if (!empty($spec['nodes'])) {
            $n = $spec['nodes'][0];
            $this->assertArrayHasKey('id', $n);
            $this->assertArrayHasKey('title', $n);
            $this->assertArrayHasKey('question', $n);
            $this->assertArrayHasKey('type', $n);
            $this->assertArrayHasKey('options', $n);
            $this->assertArrayHasKey('x', $n);
            $this->assertArrayHasKey('y', $n);
        }
    }
}
