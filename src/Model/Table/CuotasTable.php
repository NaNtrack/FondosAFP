<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * Cuotas Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Afps
 * @property \Cake\ORM\Association\BelongsTo $Fondos
 *
 * @method \App\Model\Entity\Cuota get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cuota newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cuota[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cuota|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cuota patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cuota[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cuota findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CuotasTable extends Table
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

        $this->table('cuotas');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Afps', [
            'foreignKey' => 'afp_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Fondos', [
            'foreignKey' => 'fondo_id',
            'joinType' => 'INNER'
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
            ->date('fecha')
            ->requirePresence('fecha', 'create')
            ->notEmpty('fecha');

        $validator
            ->decimal('valor')
            ->requirePresence('valor', 'create')
            ->notEmpty('valor');

        $validator
            ->decimal('patrimonio')
            ->allowEmpty('patrimonio');

        $validator
            ->decimal('variacion_val')
            ->allowEmpty('variacion_val');

        $validator
            ->decimal('varacion_por')
            ->allowEmpty('varacion_por');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['afp_id'], 'Afps'));
        $rules->add($rules->existsIn(['fondo_id'], 'Fondos'));

        return $rules;
    }

    public function getLatestValue($afpId, $fondoId)
    {
        $cuotas = TableRegistry::get('Cuotas');
        return $cuotas->find()
            ->where([
                'fondo_id' => $fondoId,
                'afp_id' => $afpId,
            ])
            ->order(['fecha' => 'DESC'])
            ->first();
    }

    /**
     *
     * @param int $afpId
     * @param array $fondoIds
     * @param string $from
     * @param string $to
     * @param string $type
     * @return array
     */
    public function getCuotas($afpId, $fondoIds, $from, $to, $type)
    {
        $results = [];
        foreach ($fondoIds as $fondoId) {
            if ( $type == 'porcentaje' ) {
                $firstValue = $this->find('all', [
                    'fields' => [
                        'afp_id',
                        'fondo_id',
                        'fecha',
                        'valor'
                    ]
                ])->where([
                    'afp_id' => $afpId,
                    'fondo_id' => $fondoId,
                    'fecha >=' => $from,
                    'fecha <=' => $to
                ])
                ->contain([])
                ->first();

                $query = $this->find('all', [
                    'fields' => [
                        'afp_id',
                        'fondo_id',
                        'fecha',
                        "valor"
                    ]
                ])->where([
                    'afp_id' => $afpId,
                    'fondo_id' => $fondoId,
                    'fecha >=' => $from,
                    'fecha <=' => $to
                ])
                ->contain([])
                ->order([
                    'fondo_id' => 'ASC',
                    'fecha' => 'ASC'
                ]);
                foreach ($query as $row) {
                    $r = $row->toArray();
                    $r['valor'] = round(($r['valor'] * 100 / $firstValue->valor) - 100, 2);
                    $results[] = $r;
                }
                array_shift($results);
            } elseif ($type == 'valor') {
                $query = $this->find('all', [
                    'fields' => [
                        'afp_id',
                        'fondo_id',
                        'fecha',
                        'valor'
                    ]
                ])
                ->where([
                    'afp_id' => $afpId,
                    'fondo_id' => $fondoId,
                    'fecha >=' => $from,
                    'fecha <=' => $to
                ])
                ->contain([])
                ->order([
                    'fondo_id' => 'ASC',
                    'fecha' => 'ASC'
                ]);
                //debug($query);
                foreach ($query as $row) {
                    $results[] = $row->toArray();
                }
            } elseif ($type == 'patrimonio') {
                $query = $this->find('all', [
                    'fields' => [
                        'afp_id',
                        'fondo_id',
                        'fecha',
                        'patrimonio'
                    ]
                ])
                ->where([
                    'afp_id' => $afpId,
                    'fondo_id' => $fondoId,
                    'fecha >=' => $from,
                    'fecha <=' => $to
                ])
                ->contain([])
                ->order([
                    'fondo_id' => 'ASC',
                    'fecha' => 'ASC'
                ]);
                foreach ($query as $row) {
                    $results[] = $row->toArray();
                }
            }
        }

        return $results;
    }

    /**
     *
     * @param int $afpId
     * @param int $fondoId
     * @param string $fecha
     */
    public function getCuotaFromFecha($afpId, $fondoId, $fecha)
    {
        $cuotas = TableRegistry::get('Cuotas');
        return $cuotas->find()
            ->where([
                'fondo_id' => $fondoId,
                'afp_id' => $afpId,
                'fecha' => $fecha
            ])
            ->order(['fecha' => 'DESC'])
            ->contain(['Fondos'])
            ->first();
    }

    public function getPreviousCuota($afpId, $fondoId, $fecha)
    {
        $cuotas = TableRegistry::get('Cuotas');
        return $cuotas->find()
            ->where([
                'fondo_id' => $fondoId,
                'afp_id' => $afpId,
                'fecha <' => $fecha
            ])
            ->order(['fecha' => 'DESC'])
            ->contain(['Fondos'])
            ->first();
    }
}
