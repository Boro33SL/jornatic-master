<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\MastersTable;
use Authorization\IdentityInterface;

/**
 * Masters table policy
 */
class MastersTablePolicy
{
    /**
     * Check if $user can create masters
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Table\MastersTable $masters
     * @return bool
     */
    public function canCreate(IdentityInterface $user, MastersTable $masters): bool
    {
        // Only authenticated masters can create new masters
        return true;
    }

    /**
     * Check if $user can access index
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Table\MastersTable $masters
     * @return bool
     */
    public function canIndex(IdentityInterface $user, MastersTable $masters): bool
    {
        // Any authenticated master can list masters
        return true;
    }
}
