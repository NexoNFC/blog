<?php

namespace App\Policies;

use App\Models\NfcPoint;
use App\Models\User;

class NfcPointPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('nfc.view');
    }

    public function update(User $user, NfcPoint $nfcPoint): bool
    {
        return $user->can('nfc.update') || $user->can('nfc.manage-content');
    }
}
