<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\MastersTable;
use Authorization\IdentityInterface;

/**
 * Política de tabla Masters
 *
 * Define las reglas de autorización para las operaciones sobre la tabla Masters
 */
class MastersTablePolicy
{
    /**
     * Función para verificar si el usuario puede crear masters
     *
     * @param \Authorization\IdentityInterface $user El usuario
     * @param \App\Model\Table\MastersTable $masters La tabla masters
     * @return bool
     */
    public function canCreate(IdentityInterface $user, MastersTable $masters): bool
    {
        // Solo masters autenticados pueden crear nuevos masters
        return true;
    }

    /**
     * Función para verificar si el usuario puede acceder al índice
     *
     * @param \Authorization\IdentityInterface $user El usuario
     * @param \App\Model\Table\MastersTable $masters La tabla masters
     * @return bool
     */
    public function canIndex(IdentityInterface $user, MastersTable $masters): bool
    {
        // Cualquier master autenticado puede listar masters
        return true;
    }
}
