<?php

namespace App\Services;

use App\Models\User;
use App\Models\Application;

class StaffAssignmentService
{
    /**
     * Automatically assign a case manager to an application.
     * Uses load balancing - assigns to the case manager with the least active applications.
     */
    public static function assignCaseManager(Application $application): bool
    {
        // Skip if already assigned
        if ($application->case_manager_id) {
            return true;
        }

        // Find available case managers ordered by current load (least loaded first)
        $caseManager = User::role('case_manager')
            ->withCount(['managedCases' => function ($query) {
                $query->whereNotIn('status', ['completed', 'cancelled', 'rejected']);
            }])
            ->orderBy('managed_cases_count')
            ->first();

        if ($caseManager) {
            $application->case_manager_id = $caseManager->id;
            $application->save();
            return true;
        }

        return false; // No case managers available
    }

    /**
     * Get case manager assignment statistics
     */
    public static function getAssignmentStats(): array
    {
        $caseManagers = User::role('case_manager')
            ->withCount(['managedCases' => function ($query) {
                $query->whereNotIn('status', ['completed', 'cancelled', 'rejected']);
            }])
            ->get();

        return $caseManagers->map(function ($cm) {
            return [
                'id' => $cm->id,
                'name' => $cm->name,
                'active_cases' => $cm->managed_cases_count,
            ];
        })->toArray();
    }
}