<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\DB;

class ApplicationStatusService
{
    /**
     * Transition application status with validation.
     */
    public function transition(Application $application, string $to, array $meta = []): Application
    {
        $valid = [
            'draft', 'intake_complete', 'under_review', 'rfe_needed', 'waiting_applicant', 'ready_to_file', 'filed', 'shipped', 'closed'
        ];
        if(!in_array($to,$valid,true)){
            throw new \InvalidArgumentException('Invalid status: '.$to);
        }
        $from = $application->status;
        if($from === $to) return $application; // no-op
        // Basic guard rules
        $allowed = [
            'draft' => ['intake_complete'],
            'intake_complete' => ['under_review'],
            'under_review' => ['rfe_needed','ready_to_file'],
            'rfe_needed' => ['waiting_applicant'],
            'waiting_applicant' => ['under_review','ready_to_file'],
            'ready_to_file' => ['filed'],
            'filed' => ['shipped','closed'],
            'shipped' => ['closed'],
        ];
        $nexts = $allowed[$from] ?? [];
        if(!in_array($to,$nexts,true)){
            throw new \RuntimeException("Illegal transition $from -> $to");
        }
        DB::transaction(function() use ($application,$to,$meta){
            $application->update([
                'status' => $to,
                'progress_pct' => $this->progressFor($to, $application->progress_pct)
            ]);
        });
        return $application->refresh();
    }

    protected function progressFor(string $status, int $current): int
    {
        $map = [
            'draft' => 5,
            'intake_complete' => 15,
            'under_review' => 30,
            'rfe_needed' => 35,
            'waiting_applicant' => 40,
            'ready_to_file' => 60,
            'filed' => 80,
            'shipped' => 90,
            'closed' => 100,
        ];
        return max($current, $map[$status] ?? $current);
    }
}
