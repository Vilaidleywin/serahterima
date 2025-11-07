<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;

class DocumentPolicy
{
    // Admin dilarang akses semua dokumen
    public function before(User $user, $ability)
    {
        if ($user->role === 'admin') {
            return false;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->role === 'user';
    }

    public function view(User $user, Document $doc): bool
    {
        return $user->role === 'user' && $user->division === $doc->division;
    }

    public function create(User $user): bool
    {
        return $user->role === 'user';
    }

    public function update(User $user, Document $doc): bool
    {
        return $user->role === 'user' && $user->division === $doc->division;
    }

    public function delete(User $user, Document $doc): bool
    {
        return $user->role === 'user' && $user->division === $doc->division;
    }
}
