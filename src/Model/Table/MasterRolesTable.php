<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Modelo MasterRoles
 *
 * Gestión de roles de usuarios master
 *
 * @property \App\Model\Table\MastersTable&\Cake\ORM\Association\HasMany $Masters
 * @method \App\Model\Entity\MasterRole newEmptyEntity()
 * @method \App\Model\Entity\MasterRole newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\MasterRole> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterRole get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\MasterRole findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\MasterRole patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\MasterRole> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterRole|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\MasterRole saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\MasterRole>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MasterRole>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MasterRole>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MasterRole> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MasterRole>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MasterRole>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MasterRole>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MasterRole> deleteManyOrFail(iterable $entities, array $options = [])
 */
class MasterRolesTable extends Table
{
    /**
     * Función de inicialización
     *
     * @param array<string, mixed> $config Configuración de la tabla
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('master_roles');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Masters', [
            'foreignKey' => 'master_role_id',
        ]);
    }

    /**
     * Reglas de validación por defecto
     *
     * @param \Cake\Validation\Validator $validator Instancia del validador
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 40)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
