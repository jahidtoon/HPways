<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function view(User $user, Application $application): bool
    {
        return $application->user_id === $user->id;
    }

    public function update(User $user, Application $application): bool
    {
        return $application->user_id === $user->id && $application->status === 'draft';
    }

    public function uploadDocuments(User $user, Application $application): bool
    {
        return $application->user_id === $user->id;
    }
}
