<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Master;
use Authorization\IdentityInterface;

/**
 * Política de Master
 *
 * Define las reglas de autorización para las operaciones sobre la entidad Master
 */
class MasterPolicy
{
    /**
     * Función para verificar si el usuario puede ver Master
     *
     * @param \Authorization\IdentityInterface $user El usuario
     * @param \App\Model\Entity\Master $master La entidad master
     * @return bool
     */
    public function canView(IdentityInterface $user, Master $master): bool
    {
        // Los masters solo pueden ver su propio perfil
        return $user->getIdentifier() === $master->id;
    }

    /**
     * Función para verificar si el usuario puede editar Master
     *
     * @param \Authorization\IdentityInterface $user El usuario
     * @param \App\Model\Entity\Master $master La entidad master
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Master $master): bool
    {
        // Los masters solo pueden editar su propio perfil
        return $user->getIdentifier() === $master->id;
    }

    /**
     * Función para verificar si el usuario puede eliminar Master
     *
     * @param \Authorization\IdentityInterface $user El usuario
     * @param \App\Model\Entity\Master $master La entidad master
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Master $master): bool
    {
        // Prevenir eliminación de la propia cuenta por seguridad
        return false;
    }

    /**
     * Función para verificar si el usuario puede acceder al dashboard
     *
     * @param \Authorization\IdentityInterface $user El usuario
     * @param \App\Model\Entity\Master $master La entidad master
     * @return bool
     */
    public function canDashboard(IdentityInterface $user, Master $master): bool
    {
        // Cualquier master autenticado puede acceder al dashboard
        if ($user->getIdentifier() === $master->id) {
            return true;
        }

        return false;
    }

    /**
     * Función para verificar si el usuario puede cerrar sesión
     *
     * @param \Authorization\IdentityInterface $user El usuario
     * @param \App\Model\Entity\Master $master La entidad master
     * @return bool
     */
    public function canLogout(IdentityInterface $user, Master $master): bool
    {
        // Cualquier master autenticado puede cerrar sesión
        return true;
    }

    /**
     * Función para verificar si el usuario puede listar Masters
     *
     * @param \Authorization\IdentityInterface $user El usuario
     * @param \App\Model\Entity\Master $master La entidad master
     * @return bool
     */
    public function canIndex(IdentityInterface $user, Master $master): bool
    {
        // Solo masters autenticados pueden ver la lista
        return true;
    }

    /**
     * Función para verificar si el usuario puede añadir nuevos Masters
     *
     * @param \Authorization\IdentityInterface $user El usuario
     * @param \App\Model\Entity\Master $master La entidad master
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Master $master): bool
    {
        // Solo masters autenticados pueden añadir nuevos masters
        // Podrías restringir esto a un master específico o rol si lo necesitas
        return true;
    }
}
