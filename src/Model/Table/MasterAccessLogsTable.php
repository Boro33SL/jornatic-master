<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterAccessLogs Model
 *
 * @property \App\Model\Table\MastersTable&\Cake\ORM\Association\BelongsTo $Masters
 * @method \App\Model\Entity\MasterAccessLog newEmptyEntity()
 * @method \App\Model\Entity\MasterAccessLog newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\MasterAccessLog> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterAccessLog get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\MasterAccessLog findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\MasterAccessLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\MasterAccessLog> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterAccessLog|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\MasterAccessLog saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\MasterAccessLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MasterAccessLog>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MasterAccessLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MasterAccessLog> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MasterAccessLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MasterAccessLog>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MasterAccessLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MasterAccessLog> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MasterAccessLogsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('master_access_logs');
        $this->setDisplayField('ip_address');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Masters', [
            'foreignKey' => 'master_id',
            'joinType' => 'LEFT', // Cambiar a LEFT JOIN para permitir logs sin master
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('master_id')
            ->allowEmptyString('master_id'); // Permitir null para logins fallidos

        $validator
            ->scalar('ip_address')
            ->maxLength('ip_address', 45)
            ->requirePresence('ip_address', 'create')
            ->notEmptyString('ip_address');

        $validator
            ->scalar('user_agent')
            ->allowEmptyString('user_agent');

        $validator
            ->scalar('action')
            ->maxLength('action', 100)
            ->requirePresence('action', 'create')
            ->notEmptyString('action');

        $validator
            ->scalar('resource')
            ->maxLength('resource', 100)
            ->allowEmptyString('resource');

        $validator
            ->integer('resource_id')
            ->allowEmptyString('resource_id');

        $validator
            ->scalar('details')
            ->maxLength('details', 4294967295)
            ->allowEmptyString('details');

        $validator
            ->boolean('success')
            ->notEmptyString('success');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // Permitir master_id null para logs anónimos (logins fallidos)
        $rules->add($rules->existsIn(['master_id'], 'Masters'), [
            'errorField' => 'master_id',
            'allowNullableNulls' => true,
        ]);

        return $rules;
    }
}
