<?php
namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        return $document->application && $document->application->user_id === $user->id;
    }

    public function update(User $user, Document $document): bool
    {
        return $this->view($user,$document);
    }

    public function translate(User $user, Document $document): bool
    {
        return $this->view($user,$document) && $document->needs_translation;
    }
}
