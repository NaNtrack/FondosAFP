<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Afps Model
 *
 * @property \Cake\ORM\Association\HasMany $Alerts
 * @property \Cake\ORM\Association\HasMany $Changes
 * @property \Cake\ORM\Association\HasMany $Cuotas
 *
 * @method \App\Model\Entity\Afp get($primaryKey, $options = [])
 * @method \App\Model\Entity\Afp newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Afp[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Afp|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Afp patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Afp[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Afp findOrCreate($search, callable $callback = null)
 */
class AfpsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('afps');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->hasMany('Alerts', [
            'foreignKey' => 'afp_id'
        ]);
        $this->hasMany('Changes', [
            'foreignKey' => 'afp_id'
        ]);
        $this->hasMany('Cuotas', [
            'foreignKey' => 'afp_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->allowEmpty('description');

        $validator
            ->requirePresence('api_name', 'create')
            ->notEmpty('api_name');

        $validator
            ->requirePresence('country', 'create')
            ->notEmpty('country');

        $validator
            ->integer('status')
            ->allowEmpty('status');

        return $validator;
    }
    
    public function findActive(Query $query)
    {
        return $query->where(['status' => 1])->orderAsc('name');
    }
}
