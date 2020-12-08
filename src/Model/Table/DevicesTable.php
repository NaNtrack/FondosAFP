<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

use JsonSchema\Uri\Retrievers\AbstractRetriever;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

/**
 * Devices Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Device get($primaryKey, $options = [])
 * @method \App\Model\Entity\Device newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Device[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Device|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Device|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Device patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Device[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Device findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DevicesTable extends Table
{
    private $factory = null;
    public function getNotificationsFactory() {
        if ($this->factory == null) {
            $this->factory = (new Factory)
                ->withServiceAccount(ROOT . DS . 'config/website-configuration.json')
                ->withDatabaseUri('https://fondosafp-140601.firebaseio.com');
        }

        return $this->factory;
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('devices');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

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
            ->scalar('os')
            ->maxLength('os', 10)
            ->requirePresence('os', 'create')
            ->notEmpty('os');

        $validator
            ->integer('enable_notifications')
            ->requirePresence('enable_notifications', 'create')
            ->notEmpty('enable_notifications');

        $validator
            ->integer('notify_changes')
            ->requirePresence('notify_changes', 'create')
            ->notEmpty('notify_changes');

        $validator
            ->integer('notify_news')
            ->requirePresence('notify_news', 'create')
            ->notEmpty('notify_news');

        $validator
            ->integer('notify_app_updates')
            ->requirePresence('notify_app_updates', 'create')
            ->notEmpty('notify_app_updates');

        $validator
            ->integer('notify_other')
            ->requirePresence('notify_other', 'create')
            ->notEmpty('notify_other');

        $validator
            ->scalar('token')
            ->maxLength('token', 255)
            ->requirePresence('token', 'create')
            ->notEmpty('token');

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

    public function sendNotifications()
    {
        $devicesNotified = 0;
        $devicesToBeNotified = $this->find()
            ->where([
                'enable_notifications' => 1,
                'notify_changes' => 1,
            ])
            ->all();

        if (empty($devicesToBeNotified)) {
            return 0;
        }

        $Changes = TableRegistry::get('Changes');
        $Preferences = TableRegistry::get('Preferences');
        foreach ($devicesToBeNotified as $device) {
            $afpId = $Preferences->getUserAfpId($device->user_id);
            $fondoId = $Preferences->getUserFondoId($device->user_id);
            if (!($afpId && $fondoId)) {
                continue;
            }

            $latestChanges = $Changes->getLatestChange($device->user_id, $afpId);
            if (count($latestChanges) > 0) {
                $devicesNotified += $this->sendNotificationsByLatestChange($device, $latestChanges, $afpId, $fondoId);
            } else {
                $devicesNotified += $this->sendNotificationsByDeviceCreatedDate($device, $afpId, $fondoId);
            }
        }

        return $devicesNotified;
    }

    private function sendNotificationsByLatestChange($device, $latestChanges, $afpId, $fondoId) {
        $Cuotas = TableRegistry::get('Cuotas');
        $NotificationQueue = TableRegistry::get('NotificationQueue');
        $notifiedCount = 0;
        foreach ($latestChanges as $latestChange) {
            if (empty($latestChange)) {
                continue;
            }
            $lastCuota = $Cuotas->getLatestValue($latestChange['afp_id'], $latestChange['to_fondo_id']);
            if (empty($lastCuota)) {
                continue;
            }

            if ($latestChange['cuota_dt'] == $lastCuota->fecha) {
                continue;
            }
            if ($lastCuota->fecha == $device->notify_changes_date) {
                echo "lastCuota->fecha == device->notify_changes_date: {$lastCuota->fecha}=={$device->notify_changes_date}\n";
                continue;
            }

            $ganancia = 0;
            $gananciaTotal = 0;
            $fromFecha = '';
            $fondoName = '';
            if (empty($device->notify_changes_date)) {
                $ganancia = round($lastCuota->valor - $latestChange['to_value'], 2);
                $gananciaTotal = $ganancia;
                $fromFecha = $latestChange['change_dt'];
                $fondoName = $latestChange['to_fondo']['name'];
            } else {
                $previousCuota = $Cuotas->getCuotaFromFecha($afpId, $latestChange['to_fondo_id'], $device->notify_changes_date);
                $ganancia = round($lastCuota->valor - $previousCuota->valor, 2);
                $gananciaTotal = round($lastCuota->valor - $latestChange['to_value'], 2);
                $fromFecha = $latestChange['cuota_dt'];
                $fondoName = $latestChange['to_fondo']['name'];
            }


            $this->query()
                ->update()
                ->set(['notify_changes_date' => $lastCuota->fecha->format('Y-m-d H:i:s')])
                ->where(['id' => $device->id])
                ->execute();

            $messaging = $this->getNotificationsFactory()->createMessaging();
            $title = "Tus fondos han ".($ganancia>= 0 ? 'subido $' : 'bajado -$')."$ganancia";
            $body = "Desde el ".$this->getFechaAsString($fromFecha)." llevas \$$gananciaTotal en tu fondo $fondoName";
            $messageData = [
                'token' => $device->token,
                'notification' => ['title' => $title, 'body' => $body],
                'data' => [
                    'type' => 'notify_change',
                    'title' => $title,
                    'body' => $body,
                    'ganancia' => $ganancia,
                    'gananciaTotal' => $gananciaTotal,
                    'fechaDesde' => $this->getFechaAsString($fromFecha),
                    'fondo' => $fondoName,
                    'afp' => 'AFP NAME',
                ],
            ];

            $notificationData = $NotificationQueue->newEntity([
                'user_id' => $device->user_id,
                'device_id' => $device->id,
                'payload' => json_encode($messageData['data']),
                'notification_type' => $messageData['data']['type'],
                'status' => 'unread',
                'sent' => date("Y-m-d H:i:s"),
            ]);
            $NotificationQueue->save($notificationData);
            $message = CloudMessage::fromArray($messageData);
            $messaging->send($message);
            $notifiedCount++;
            usleep(250000); //250ms
        }

        return $notifiedCount;
    }

    private function sendNotificationsByDeviceCreatedDate($device, $afpId, $fondoId) {
        $ganancia = 0;
        $gananciaTotal = 0;
        $fromFecha = '';
        $fondoName = '';
        $notifiedCount = 0;
        $Cuotas = TableRegistry::get('Cuotas');
        $NotificationQueue = TableRegistry::get('NotificationQueue');
        $lastCuota = $Cuotas->getLatestValue($afpId, $fondoId);
        $initialCuota = $Cuotas->getCuotaFromFecha($afpId, $fondoId, $device->created);

        if ($initialCuota == null) {
            echo "SIN CUOTA PARA FECHA DE REGISTRO DEL DEVICE userId: {$device->user_id}, $afpId, $fondoId\n";
            return 0;
        }

        if ($device->notify_changes_date != null &&
            $lastCuota->fecha != null &&
            $device->notify_changes_date->format('Y-m-d') == $lastCuota->fecha->format('Y-m-d')) {
            echo "FECHA NOTIFICACION == FECHA ULTIMA CUOTA: userId: {$device->user_id}, $afpId, $fondoId\n";
            return 0;
        }

        if (empty($device->notify_changes_date)) {
            $ganancia = round($lastCuota->valor - $initialCuota->valor, 2);
            $gananciaTotal = $ganancia;
            $fromFecha = $device->created;
            $fondoName = $initialCuota['fondo']['name'];
        } else {
            $previousCuota = $Cuotas->getCuotaFromFecha($afpId, $fondoId, $device->notify_changes_date);
            $ganancia = round($lastCuota->valor - $previousCuota->valor, 2);
            $gananciaTotal = round($lastCuota->valor - $initialCuota->valor, 2);
            $fromFecha = $device->notify_changes_date;
            $fondoName =  $initialCuota['fondo']['name'];
        }

        if($ganancia == 0) {
            echo "Ganancia 0 para userId: {$device->user_id}, $afpId, $fondoId\n";
            return 0;
        }

        $this->query()
            ->update()
            ->set(['notify_changes_date' => $lastCuota->fecha->format('Y-m-d H:i:s')])
            ->where(['id' => $device->id])
            ->execute();

        $title = "Tus fondos han ".($ganancia>= 0 ? 'subido $' : 'bajado -$')."$ganancia";
        $body = "Desde el ".$this->getFechaAsString($fromFecha)." llevas \$$gananciaTotal en tu fondo $fondoName";
        $messageData =[
            'token' => $device->token,
            'notification' => ['title' => $title, 'body' => $body],
            'data' => [
                'type' => 'notify_change',
                'title' => $title,
                'body' => $body,
                'ganancia' => $ganancia,
                'gananciaTotal' => $gananciaTotal,
                'fechaDesde' => $this->getFechaAsString($fromFecha),
                'fondo' => $fondoName,
                'afp' => 'AFP NAME',
            ],
        ] ;

        $notificationData = $NotificationQueue->newEntity([
            'user_id' => $device->user_id,
            'device_id' => $device->id,
            'payload' => json_encode($messageData['data']),
            'notification_type' => $messageData['data']['type'],
            'status' => 'unread',
            'sent' => date("Y-m-d H:i:s"),
        ]);
        $NotificationQueue->save($notificationData);

        $messaging = $this->getNotificationsFactory()->createMessaging();
        $message = CloudMessage::fromArray($messageData);
        $messaging->send($message);
        $notifiedCount++;
        usleep(250000); //250ms
        return 1;
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
}
