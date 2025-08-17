<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Masters Model
 *
 * @property \App\Model\Table\MasterRolesTable&\Cake\ORM\Association\BelongsTo $MasterRoles
 * @property \App\Model\Table\MasterAccessLogsTable&\Cake\ORM\Association\HasMany $MasterAccessLogs
 *
 * @method \App\Model\Entity\Master newEmptyEntity()
 * @method \App\Model\Entity\Master newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Master> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Master get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Master findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Master patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Master> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Master|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Master saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Master>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Master>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Master>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Master> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Master>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Master>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Master>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Master> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MastersTable extends Table
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

        $this->setTable('masters');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Role', [
            'className' => 'MasterRoles',
            'foreignKey' => 'master_role_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('MasterAccessLogs', [
            'foreignKey' => 'master_id',
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
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->nonNegativeInteger('master_role_id')
            ->notEmptyString('master_role_id');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->scalar('allowed_ips')
            ->allowEmptyString('allowed_ips');

        $validator
            ->scalar('two_factor_secret')
            ->maxLength('two_factor_secret', 255)
            ->allowEmptyString('two_factor_secret');

        $validator
            ->boolean('two_factor_enabled')
            ->notEmptyString('two_factor_enabled');

        $validator
            ->dateTime('last_login')
            ->allowEmptyDateTime('last_login');

        $validator
            ->integer('login_attempts')
            ->notEmptyString('login_attempts');

        $validator
            ->dateTime('locked_until')
            ->allowEmptyDateTime('locked_until');

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
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);
        $rules->add($rules->existsIn(['master_role_id'], 'MasterRoles'), ['errorField' => 'master_role_id']);

        return $rules;
    }

    /**
     * Finder for authentication - returns active masters only
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object
     * @param array $options Options array
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findAuth(SelectQuery $query, array $options): SelectQuery
    {
        return $query
            ->contain(['Role'])
            ->where([
            'is_active' => true,
            'OR' => [
                'locked_until IS' => null,
                'locked_until <' => new DateTime(),
            ],
        ]);
    }
}
