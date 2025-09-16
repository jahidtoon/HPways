<?php

namespace App\Services;

use App\Models\QuizNode;

class QuizGraphService
{
    /**
     * Build a normalized quiz_spec used by both public UI and any admin tools.
     * Single source of truth: reads from DB (quiz_nodes) and terminal metadata.
     */
    public function buildSpec(): array
    {
        $nodes = QuizNode::all()->map(function ($node) {
            return [
                'id' => $node->node_id,
                'title' => $node->title,
                'question' => $node->question,
                'type' => $node->type,
                'options' => $node->options,
                'x' => $node->x,
                'y' => $node->y,
            ];
        })->toArray();

        // Terminal metadata
        $terminals = collect($this->terminals());
        $terminalDetails = $terminals->keyBy('code')->map(function ($t) {
            return [
                'title' => $t['title'] ?? $t['code'],
                'message' => $t['message'] ?? '',
                'link' => $t['link'] ?? null,
            ];
        })->toArray();

        return [
            'meta' => [
                'version' => '1.0',
                'brand' => 'Horizon Pathways',
                'root' => 'Q1',
                'routes' => [
                    'pricingUrl' => '/pricing',
                    'loginUrl' => '/login',
                ],
                'terminals' => config('quiz.terminals', []),
                'actionable' => config('quiz.actionable_terminals', []),
                'terminalToVisa' => config('quiz.terminal_to_visa_type', []),
                'terminalDetails' => $terminalDetails,
            ],
            'nodes' => $nodes,
        ];
    }

    /**
     * Centralized terminal registry. For now, delegate to existing QuizController-style method
     * by replicating its data here to avoid coupling on a controller class.
     * TODO: Move detailed terminal data into config/quiz.php if desired.
     */
    protected function terminals(): array
    {
        // Keep using VisaTypeMapper map for consistency; details can be kept minimal here.
        // If detailed titles/messages exist elsewhere, inject them as needed.
        $codes = config('quiz.terminals', []);
        $actionable = config('quiz.actionable_terminals', []);
        return array_map(function ($code) use ($actionable) {
            return [
                'code' => $code,
                'title' => $code,
                'message' => in_array($code, $actionable, true)
                    ? 'You may be eligible to proceed.'
                    : 'You are not eligible or no action is available.',
            ];
        }, $codes);
    }
}
