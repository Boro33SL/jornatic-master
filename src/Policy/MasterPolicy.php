<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Master;
use Authorization\IdentityInterface;

/**
 * Master policy
 */
class MasterPolicy
{
    /**
     * Check if $user can view Master
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Master $master
     * @return bool
     */
    public function canView(IdentityInterface $user, Master $master): bool
    {
        // Masters can only view their own profile
        return $user->getIdentifier() === $master->id;
    }

    /**
     * Check if $user can edit Master
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Master $master
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Master $master): bool
    {
        // Masters can only edit their own profile
        return $user->getIdentifier() === $master->id;
    }

    /**
     * Check if $user can delete Master
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Master $master
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Master $master): bool
    {
        // Prevent deletion of own account for safety
        return false;
    }

    /**
     * Check if $user can access dashboard
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Master $master
     * @return bool
     */
    public function canDashboard(IdentityInterface $user, Master $master): bool
    {
        // Any authenticated master can access dashboard
        if ($user->getIdentifier() === $master->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if $user can logout
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Master $master
     * @return bool
     */
    public function canLogout(IdentityInterface $user, Master $master): bool
    {
        // Any authenticated master can logout
        return true;
    }
}
