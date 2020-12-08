<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Fondos Model
 *
 * @property \Cake\ORM\Association\HasMany $Alerts
 * @property \Cake\ORM\Association\HasMany $Cuotas
 *
 * @method \App\Model\Entity\Fondo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Fondo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Fondo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Fondo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Fondo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Fondo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Fondo findOrCreate($search, callable $callback = null)
 */
class FondosTable extends Table
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

        $this->table('fondos');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->hasMany('Alerts', [
            'foreignKey' => 'fondo_id'
        ]);
        $this->hasMany('Cuotas', [
            'foreignKey' => 'fondo_id'
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
            ->allowEmpty('api_name');

        $validator
            ->allowEmpty('country');

        $validator
            ->integer('status')
            ->allowEmpty('status');

        return $validator;
    }
    
    public function findActive(Query $query)
    {
        return $query->orderAsc('name');
    }
}
