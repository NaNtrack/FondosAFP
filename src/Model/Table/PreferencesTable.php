<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Preferences Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Afps
 * @property \Cake\ORM\Association\BelongsTo $Fondos
 *
 * @method \App\Model\Entity\Preference get($primaryKey, $options = [])
 * @method \App\Model\Entity\Preference newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Preference[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Preference|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Preference patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Preference[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Preference findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PreferencesTable extends Table
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

        $this->table('preferences');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Afps', [
            'foreignKey' => 'afp_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Fondos', [
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
        $rules->add($rules->existsIn(['afp_id'], 'Afps'));
        $rules->add($rules->existsIn(['fondo_id'], 'Fondos'));

        return $rules;
    }

    /**
     * Save user preferences
     *
     * @param int $userId
     * @param int $afpId
     * @param int $fondoId
     * @return bool
     */
    public function savePreferences($userId, $afpId, $fondoId)
    {
        $preference = $this->findByUserId($userId)->first();
        if (!empty($preference)) {
            $preference->afp_id = $afpId;
            $preference->fondo_id = $fondoId;

        } else {
            $data = [
                'user_id' => $userId,
                'afp_id' => $afpId,
                'fondo_id' => $fondoId
            ];
            $preference = $this->newEntity($data);
        }
        $this->save($preference);

        return true;
    }

    /**
     *
     * @param int $userId
     * @param array $preferences
     */
    public function updatePreferences($userId, $preferences) {
        $preference = $this->findByUserId($userId)->first();

        $data = [
            'homepage' => $preferences['homepage'],
            'mostrar_otras_afps' => $this->getElementFromArray($preferences, 'mostrar_otras_afps') == 'on' ? 1 :0,
            'event_fondos_up' => $this->getElementFromArray($preferences, 'event_fondos_up') == 'on' ? 1 :0,
            'event_fondos_down' => $this->getElementFromArray($preferences, 'event_fondos_down') == 'on' ? 1 :0,
            'resumen_semanal' => $this->getElementFromArray($preferences, 'resumen_semanal') == 'on' ? 1 :0,
            'event_ranking_up' => $this->getElementFromArray($preferences, 'event_ranking_up') == 'on' ? 1 :0,
            'event_ranking_down' => $this->getElementFromArray($preferences, 'event_ranking_down') == 'on' ? 1 :0,
            'event_new_follow' => $this->getElementFromArray($preferences, 'event_new_follow') == 'on' ? 1 :0,
        ];

        if (empty($preference)) {
           $preference = $this->newEntity($data);
        } else {
            $preference = $this->patchEntity($preference, $data);
        }

        $this->save($preference);
    }

    private function getElementFromArray($array, $element, $defaultValue = null)
    {
        if (!isset($array[$element])) {
            return $defaultValue;
        }

        return $array[$element];
    }

    /**
     *
     * @return \Cake\Mailer\Email
     */
    public function getMailer()
    {
        return new \Cake\Mailer\Email('default');
    }

    public function sendEmailsFromImport()
    {
        $usersEventUp = $this->find()
            ->where(['event_fondos_up' => 1])
            ->all();

        if (!empty($usersEventUp)) {
            $this->sendEventFondosUpEmail($usersEventUp);
        }

        $usersEventDown = $this->find()
            ->where(['event_fondos_down' => 1])
            ->all();

        if (!empty($usersEventDown)) {
            $this->sendEventFondosDownEmail($usersEventDown);
        }
    }

    private function sendEventFondosUpEmail($userPreferences)
    {
        $Users = TableRegistry::get('Users');
        $Cuotas = TableRegistry::get('Cuotas');
        $Changes = TableRegistry::get('Changes');

        foreach ($userPreferences as $up) {
            $latestChanges = $Changes->getLatestChange($up->user_id, $up->afp_id);
            foreach ($latestChanges as $latestChange) {
                $lastCuota = $Cuotas->getLatestValue($latestChange['afp_id'], $latestChange['to_fondo_id']);
                if (empty($lastCuota)) {
                    continue;
                }

                if (!empty($latestChange) && $latestChange['cuota_dt'] == $lastCuota->fecha) {
                    continue;
                }
                if ($lastCuota->fecha == $up->event_fondos_up_date) {
                    continue;
                }

                $fecha = '';
                $ganancia = 0;
                $gananciaTotal = 0;
                $fondoName = '';
                $fromFecha = '';
                $fromValue = '';
                if (empty($up->event_fondos_up_date)) {
                    if (!empty($latestChange)) {
                        $previousCuota = $latestChange;
                        $fecha = $latestChange['change_dt'];
                        $ganancia = $lastCuota->valor - $latestChange['to_value'];
                        $fondoName = $latestChange['to_fondo']['name'];
                        $fromFecha = $latestChange['cuota_dt'];
                        $fromValue = $latestChange['to_value'];
                        $gananciaTotal = $lastCuota->valor - $fromValue;
                    } else {
                        $latestValue = $Cuotas->getLatestValue($up->afp_id, $up->fondo_id);
                        $previousCuota = $Cuotas->getPreviousCuota($up->afp_id, $up->fondo_id, $latestValue->fecha);
                        $fecha = $previousCuota->fecha;
                        $fondoName = $previousCuota->fondo->name;
                        $fromFecha = $previousCuota->fecha;
                        $fromValue = $previousCuota->valor;
                        $ganancia = $latestValue->valor - $previousCuota->valor;
                        $gananciaTotal = $lastCuota->valor - $fromValue;
                    }
                } else {
                    if (!empty($latestChange)) {
                        $previousCuota = $Cuotas->getCuotaFromFecha($latestChange['afp_id'], $latestChange['to_fondo_id'], $up->event_fondos_up_date);
                        $fecha = $previousCuota->fecha;
                        $ganancia = $lastCuota->valor - $previousCuota->valor;
                        $fondoName = $latestChange['to_fondo']['name'];
                        $fromFecha = $latestChange['cuota_dt'];
                        $fromValue = $latestChange['to_value'];
                        $gananciaTotal = $lastCuota->valor - $fromValue;
                    } else {
                        $previousCuota = $Cuotas->getCuotaFromFecha($up->afp_id, $up->fondo_id, $up->event_fondos_up_date);
                        $fecha = $previousCuota->fecha;
                        $ganancia = $lastCuota->valor - $previousCuota->valor;
                        $fondoName = $previousCuota->fondo->name;
                        $fromFecha = $previousCuota->fecha;
                        $fromValue = $previousCuota->valor;
                        $gananciaTotal = $lastCuota->valor - $fromValue;
                    }
                }

                if ($ganancia < 0) {
                    $this->query()
                        ->update()
                        ->set([
                            'event_fondos_up_date' => $lastCuota->fecha->format('Y-m-d H:i:s'),
                        ])
                        ->where(['id' => $up->id])
                        ->execute();
                    continue;
                }

                $user = $Users->findById($up->user_id)->first();
                if (empty($user->email) ||
                    strpos($user->email, '@facebook.com') > 0 ||
                    strpos($user->email, '+test') > 0){
                    continue;
                }
                $this->query()
                    ->update()
                    ->set([
                        'event_fondos_up_date' => $lastCuota->fecha->format('Y-m-d H:i:s'),
                    ])
                    ->where(['id' => $up->id])
                    ->execute();

                $email = $this->getMailer();
                $email->template('event_fondos_up')
                    ->emailFormat('html')
                    ->to($user->email)
                    ->subject('Tus fondos han subido')
                    ->viewVars([
                        'title' => 'Tus fondos han subido',
                        'fondo' => $fondoName,
                        'ganancia' => $ganancia,
                        'fecha' => $this->getFechaAsString($fecha),
                        'from_fecha' => $this->getFechaAsString($fromFecha),
                        'ganancia_total' => $gananciaTotal,
                    ])
                    ->send();
                usleep(72000);
            }
        }
    }

    private function sendEventFondosDownEmail($userPreferences)
    {
        $Users = TableRegistry::get('Users');
        $Cuotas = TableRegistry::get('Cuotas');
        $Changes = TableRegistry::get('Changes');

        foreach ($userPreferences as $up) {
            $latestChanges = $Changes->getLatestChange($up->user_id, $up->afp_id);
            foreach ($latestChanges as $latestChange) {
                $lastCuota = $Cuotas->getLatestValue($latestChange['afp_id'], $latestChange['to_fondo_id']);
                if (empty($lastCuota)) {
                    continue;
                }

                if (!empty($latestChange) && $latestChange['cuota_dt'] == $lastCuota->fecha) {
                    continue;
                }
                if ($lastCuota->fecha == $up->event_fondos_down_date) {
                    continue;
                }

                $fecha = '';
                $ganancia = 0;
                $gananciaTotal = 0;
                $fondoName = '';
                $fromFecha = '';
                $fromValue = '';
                if (empty($up->event_fondos_down_date)) {
                    if (!empty($latestChange)) {
                        $previousCuota = $latestChange;
                        $fecha = $latestChange['change_dt'];
                        $ganancia = $lastCuota->valor - $latestChange['to_value'];
                        $fondoName = $latestChange['to_fondo']['name'];
                        $fromFecha = $latestChange['change_dt'];
                        $fromValue = $latestChange['to_value'];
                        $gananciaTotal = $lastCuota->valor - $fromValue;
                    } else {
                        $latestValue = $Cuotas->getLatestValue($up->afp_id, $up->fondo_id);
                        $previousCuota = $Cuotas->getPreviousCuota($up->afp_id, $up->fondo_id, $latestValue->fecha);
                        $fecha = $previousCuota->fecha;
                        $fondoName = $previousCuota->fondo->name;
                        $fromFecha = $previousCuota->fecha;
                        $fromValue = $previousCuota->valor;
                        $ganancia = $latestValue->valor - $previousCuota->valor;
                        $gananciaTotal = $lastCuota->valor - $fromValue;
                    }
                } else {
                    if (!empty($latestChange)) {
                        $previousCuota = $Cuotas->getCuotaFromFecha($up->afp_id, $latestChange['to_fondo_id'], $up->event_fondos_down_date);
                        $fecha = $previousCuota->fecha;
                        $ganancia = $lastCuota->valor - $previousCuota->valor;
                        $fondoName = $latestChange['to_fondo']['name'];
                        $fromFecha = $latestChange['cuota_dt'];
                        $fromValue = $latestChange['to_value'];
                    } else {
                        $previousCuota = $Cuotas->getCuotaFromFecha($up->afp_id, $up->fondo_id, $up->event_fondos_down_date);
                        $fecha = $previousCuota->fecha;
                        $ganancia = $lastCuota->valor - $previousCuota->valor;
                        $fondoName = $previousCuota->fondo->name;
                        $fromFecha = $previousCuota->fecha;
                        $fromValue = $previousCuota->valor;
                    }
                    $gananciaTotal = $lastCuota->valor - $fromValue;
                }

                if ($ganancia >= 0) {
                    $this->query()
                        ->update()
                        ->set([
                            'event_fondos_down_date' => $lastCuota->fecha->format('Y-m-d H:i:s'),
                        ])
                        ->where(['id' => $up->id])
                        ->execute();
                    continue;
                }

                $user = $Users->findById($up->user_id)->first();

                $this->query()
                    ->update()
                    ->set([
                        'event_fondos_down_date' => $lastCuota->fecha->format('Y-m-d H:i:s'),
                    ])
                    ->where(['id' => $up->id])
                    ->execute();

                $email = $this->getMailer();
                $email->template('event_fondos_down')
                    ->emailFormat('html')
                    ->to($user->email)
                    ->subject('Tus fondos han bajado')
                    ->viewVars([
                        'title' => 'Tus fondos han bajado',
                        'fondo' => $fondoName,
                        'ganancia' => $ganancia,
                        'fecha' => $this->getFechaAsString($fecha),
                        'from_fecha' => $this->getFechaAsString($fromFecha),
                        'ganancia_total' => $gananciaTotal,
                    ])
                    ->send();
                usleep(72000);
            }
        }
    }

    private function getFechaAsString($fecha)
    {
        $dias = [
            1 => __('Lunes'),
            2 => __('Martes'),
            3 => __('Miércoles'),
            4 => __('Jueves'),
            5 => __('Viernes'),
            6 => __('Sábado'),
            7 => __('Domingo'),
        ];
        $meses = [
            1 => __('Enero'),
            2 => __('Febrero'),
            3 => __('Marzo'),
            4 => __('Abril'),
            5 => __('Mayo'),
            6 => __('Junio'),
            7 => __('Julio'),
            8 => __('Agosto'),
            9 => __('Septiembre'),
            10 => __('Octubre'),
            11 => __('Noviembre'),
            12 => __('Diciembre'),
        ];

        return sprintf("%s %s de %s del %s",
            $dias[$fecha->format('N')],
            $fecha->format('d'),
            $meses[$fecha->format('n')],
            $fecha->format('Y'));
    }

    public function updateAfp($userId, $afpId)
    {
        $user = $this->findByUserId($userId)->first();
        if (!empty($user)) {
            $user->afp_id = $afpId;
            $this->save($user);
        }

        return true;
    }

    public function getUserAfpId($userId) {
        $user = $this->findByUserId($userId)->first();
        if (!empty($user)) {
            return $user->afp_id;
        }

        return null;
    }

    public function getUserFondoId($userId) {
        $user = $this->findByUserId($userId)->first();
        if (!empty($user)) {
            return $user->fondo_id;
        }

        return null;
    }

    public function sendEmailReporteMensual()
    {
        $Users = TableRegistry::get('users');
        $AFPs = TableRegistry::get('afps');
        $Changes = TableRegistry::get('changes');
        $usersForReport = $Users->find()
            ->innerJoin('preferences', ['preferences.user_id = users.id'])
            ->where(['resumen_mensual' => 1])
            ->all();

        foreach ($usersForReport as $user) {
            $afp = $AFPs->getById();
            $data = $Changes->getReporteMensualData($user->id, date("Y-m"));
            $email = $this->getMailer();
            $email->template('resumen_mensual')
                ->emailFormat('html')
                ->to($user->email)
                ->subject('Reporte mensual de tus fondos')
                ->viewVars([
                    'title' => 'Reporte mensual de tus fondos',
                    'afp' => 'AFP' . ucwords($afp->name),
                    'fechaReporte' => date("Y-m"),
                    'data' => $data,
                    'otrasAFPs' => []
                ])
                ->send();
            usleep(72000);
        }
    }
}
