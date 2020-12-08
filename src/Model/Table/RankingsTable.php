<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * Rankings Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Ranking get($primaryKey, $options = [])
 * @method \App\Model\Entity\Ranking newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Ranking[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Ranking|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Ranking patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Ranking[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Ranking findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RankingsTable extends Table
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

        $this->table('rankings');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->integer('puesto')
            ->requirePresence('puesto', 'create')
            ->notEmpty('puesto');

        $validator
            ->integer('puesto_anterior')
            ->allowEmpty('puesto_anterior');

        $validator
            ->decimal('performance')
            ->requirePresence('performance', 'create')
            ->notEmpty('performance');

        $validator
            ->decimal('rentabilidad')
            ->requirePresence('rentabilidad', 'create')
            ->notEmpty('rentabilidad');

        $validator
            ->decimal('consistencia')
            ->requirePresence('consistencia', 'create')
            ->notEmpty('consistencia');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * Calcula el ranking de los usuarios y almacena los datos en la tabla Ranking
     */
    public function calculateRanking()
    {
        $Users = TableRegistry::get('Users');
        $users = $Users->find()
            ->distinct()
            ->select(['Users.id', 'preferences.afp_id'])
            ->innerJoin('preferences', ['preferences.user_id = Users.id'])
            ->innerJoin('changes', ['changes.user_id = Users.id'])
            ->all()
            ->toArray();

        $ranking = [];
        $Changes = TableRegistry::get('Changes');
        if (empty($users)) return;
        foreach ($users as $user) {

            $ranking[] = [
                'user_id' => $user['id'],
                'performance' => $Changes->getPerformance($user['id'], $user['preferences']['afp_id']),
                'rentabilidad' => $Changes->getRentability($user['id'], $user['preferences']['afp_id']),
                'consistencia' => $Changes->getConsistency($user['id'], $user['preferences']['afp_id']),
            ];
        }

        usort($ranking, function($a, $b){
            if ($b['rentabilidad'] == $a['rentabilidad']) {
                return 0;
            }

            return ($a['rentabilidad'] < $b['rentabilidad']) ? 1 : -1;
        });

        $this->removeUsersWithoutChanges();

        $puesto = 0;
        foreach ($ranking as $rank) {
            $rank['puesto'] = ++$puesto;

            $previous = $this->find()
                ->where(['user_id' => $rank['user_id']])
                ->first();
            if (empty($previous)) {
                $previous = $this->newEntity($rank);
                $previous->puesto = $rank['puesto'];
            } else {
                $previous->puesto_anterior = $previous->puesto;
                $previous->puesto = $rank['puesto'];
            }

            $previous->performance = $rank['performance'];
            $previous->rentabilidad = $rank['rentabilidad'];
            $previous->consistencia = $rank['consistencia'];

            $this->save($previous);
        }

        $this->sendRankingChangesEmails();
    }

    /**
     * Returns an array with the users order by their
     */
    public function getRanking()
    {
        return $this->find()
            ->contain(['Users'])
            ->limit(10)
            ->order(['rentabilidad' => 'DESC'])
            ->all();
    }

    /**
     * Remueve del ranking a todos los usuarios que no registren cambios de fondos
     */
    private function removeUsersWithoutChanges()
    {
        $Changes = TableRegistry::get('Changes');
        $users = $Changes->find()
            ->distinct()
            ->select(['user_id'])
            ->all()
            ->toArray();

        $userIds = [];
        foreach ($users as $user) {
            $userIds[] = (int) $user['user_id'];
        }

        $this->deleteAll(['NOT' => ['user_id IN' => $userIds]]);
    }

    /**
     * Envia correos a los usuarios cuyo ranking a cambiado
     */
    private function sendRankingChangesEmails()
    {
        return null;
    }
}
