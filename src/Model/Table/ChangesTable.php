<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use \Cake\ORM\TableRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverterCommand;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverter;

/**
 * Changes Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Afps
 * @property \Cake\ORM\Association\BelongsTo $FromFondos
 * @property \Cake\ORM\Association\BelongsTo $ToFondos
 *
 * @method \App\Model\Entity\Change get($primaryKey, $options = [])
 * @method \App\Model\Entity\Change newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Change[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Change|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Change patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Change[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Change findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ChangesTable extends Table
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

        $this->table('changes');
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
        $this->belongsTo('FromFondos', [
            'className' => 'Fondos',
            'foreignKey' => 'from_fondo_id',
            'joinType' => 'INNER',
            'propertyName' => 'from_fondo',
        ]);
        $this->belongsTo('ToFondos', [
            'className' => 'Fondos',
            'foreignKey' => 'to_fondo_id',
            'joinType' => 'INNER',
            'propertyName' => 'to_fondo',
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
            ->decimal('from_value')
            ->allowEmpty('from_value');

        $validator
            ->decimal('to_value')
            ->allowEmpty('to_value');

        $validator
            ->decimal('profits_loss')
            ->allowEmpty('profits_loss');

        $validator
            ->date('request_dt')
            ->allowEmpty('request_dt');

        $validator
            ->date('change_dt')
            ->allowEmpty('change_dt');

        $validator
            ->date('cuota_dt')
            ->allowEmpty('cuota_dt');

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
        $rules->add($rules->existsIn(['from_fondo_id'], 'FromFondos'));
        $rules->add($rules->existsIn(['to_fondo_id'], 'ToFondos'));

        return $rules;
    }

    /**
     *
     * @param string $filename
     * @param string $afp
     * @return array
     */
    public function cargarMovimientos($userId, $filename, $afp = 'HABITAT')
    {
        switch ($afp) {
            case 'CUPRUM':
                return $this->cargarMovimientosCuprum($userId, $filename);
            case 'HABITAT':
                return $this->cargarMovimientosHabitat($userId, $filename);
            case 'PLANVITAL':
                return $this->cargarMovimientosPlanvital($userId, $filename);
            case 'PROVIDA':
                return $this->cargarMovimientosProvida($userId, $filename);
            case 'CAPITAL':
                return $this->cargarMovimientosCapital($userId, $filename);
            case 'MODELO':
                return $this->cargarMovimientosModelo($userId, $filename);
            default:
                throw new \Exception('No se implementado la carga para la AFP ' . $afp);
        }
    }

    private function cargarMovimientosCuprum($userId, $filename) {
       throw new \Exception('La carga de certificados aun no se ha habilitado para AFP Cuprum');
    }

    private function cargarMovimientosHabitat($userId, $filename)
    {
        $command = new GhostscriptConverterCommand();
        $command->run($filename, $filename.$userId, '1.3');

        $reader = new \Asika\Pdf2text();
        $output = $reader->decode($filename.$userId);

        $lines = explode(PHP_EOL, utf8_encode($output));
        $valores0 = [
          'Cartola de Movimientos',
          'Certificado de Movimientos',
          'Últimos Movimientos - Cuenta Obligatoria'
        ];

        if (!in_array($lines[0], $valores0)) {
            throw new \Exception(__('El certificado no es válido'));
        }

        $certData = [
            'date' => $lines[1],
            'fullname' => $lines[3],
            'rut' => str_replace(' ', '', $lines[5]),
        ];

        for($i = 0 ; $i < 7 ; $i++) {
            unset($lines[$i]);
        }

        $valid = [];

        foreach($lines as $i => $line) {
            switch ($line) {
                case 'Fecha de Pago':
                case 'Perodo':
                case 'Cotizado':
                case 'Tipo Movimiento':
                case 'Cuotas':
                case 'Fondo':
                case 'Monto':
                case 'Valor':
                case 'Cuota':
                case 'Abonos':
                case 'Cargos':
                    break;
                default:
                  $valid[] = $line;
                    break;
            }
        }

        $movements = [];
        $mov = [];

        foreach($valid as $i => $line) {
            if (strpos($line, '* Le recordamos') !== FALSE) {
                $movements[] = $mov;
                break;
            }
            if ($this->validateDate($line)) {
                $movements[] = $mov;
                $mov = [];
            }
            $mov[] = $line;
        }

        return [
            'movimientos' => $movements,
            'data' => $certData
        ];
    }

    private function cargarMovimientosPlanvital($userId, $filename)
    {
        throw new \Exception('La carga de certificados aun no se ha habilitado para AFP Planvital');
    }

    private function cargarMovimientosProvida($userId, $filename)
    {
        throw new \Exception('La carga de certificados aun no se ha habilitado para AFP Provida');
    }

    public function cargarMovimientosCapital($userId, $filename)
    {
        throw new \Exception('La carga de certificados aun no se ha habilitado para AFP Capital');
    }

    public function cargarMovimientosModelo($userId, $filename)
    {
        throw new \Exception('La carga de certificados aun no se ha habilitado para AFP Modelo');
    }

    public function validateDate($date)
    {
        $d = \DateTime::createFromFormat('d/m/Y', $date);
        return $d && $d->format('d/m/Y') === $date;
    }

    /**
     *
     * @param int $userId
     * @param string $afp
     * @param array $movimientos
     * @return int
     * @throws \Exception
     */
    public function guardarCambios($userId, $afp, $movimientos)
    {
        $Afps = TableRegistry::get('Afps');
        $afpId = $Afps->findByName($afp)->first()->id;
        switch ($afp) {
            case 'CUPRUM':
                throw new \Exception('No se implementado la carga para la AFP ' . $afp);
            case 'HABITAT':
                return $this->guardarCambiosHabitat($userId, $afpId, $movimientos);
            case 'PLANVITAL':
                throw new \Exception('No se implementado la carga para la AFP ' . $afp);
            case 'PROVIDA':
                throw new \Exception('No se implementado la carga para la AFP ' . $afp);
            case 'CAPITAL':
                throw new \Exception('No se implementado la carga para la AFP ' . $afp);
            case 'MODELO':
                throw new \Exception('No se implementado la carga para la AFP ' . $afp);
            default:
                throw new \Exception('No se implementado la carga para la AFP ' . $afp);
        }
    }

    /**
     * Guarda los cambios de fondos a partir de los movimientos generados
     *
     * @param int $userId
     * @param array $movimientos
     */
    public function guardarCambiosHabitat($userId, $afpId, $movimientos)
    {
        $changes = [];

        foreach ($movimientos as $movNumber => $mov) {
            if (empty($mov) || empty($mov[2])) {
                continue;
            }

            $isChange = false;
            $totalMov = count($movimientos);
            if (strpos(strtolower($mov[2]), 'fondo desde fondo') !== FALSE ||
            strpos(strtolower($mov[1]), 'fondo desde fondo') !== FALSE ||
            $movNumber == $totalMov -1) {
                $isChange = true;
            }

            if ($isChange) {
                $changes[] = $mov;
            }
        }



        $data = [];
        //echo "<pre>".print_r($changes, true)."</pre>";exit;
        $totalChanges = count($changes);
        foreach ($changes as $changeNumber => $change) {
            $fondo = TableRegistry::get('Fondos');
            $date = \DateTime::createFromFormat("d/m/Y", $change[0]);
            if ($changeNumber == $totalChanges - 1) {
              $fromFondoName = $change[4];
              $toFondoName = $change[4];
              $toValue = str_replace(',', '.', str_replace('.', '', str_replace('$', '', $change[7])));
              $monto = str_replace(',', '.', str_replace('.', '', str_replace('$', '', $change[5])));
            } else if (count($change) >= 8) {
                $fromFondoName = strtoupper(substr($change[2], -1));
                $toFondoName = $change[4];
                $toValue = str_replace(',', '.', str_replace('.', '', str_replace('$', '', $change[7])));
                $monto = str_replace(',', '.', str_replace('.', '', str_replace('$', '', $change[5])));
            } else if (count($change) == 7) {
              $fromFondoName = strtoupper(substr($change[1], -1));
              $toFondoName = $change[3];
              $toValue = str_replace(',', '.', str_replace('.', '', str_replace('$', '', $change[6])));
              $monto = str_replace(',', '.', str_replace('.', '', str_replace('$', '', $change[4])));
            }


            $fromFondoId = $fondo->query()->where(['name' => $fromFondoName])->first()->id;
            $toFondoId = $fondo->query()->where(['name' => $toFondoName])->first()->id;
            $cuotaDt = $this->findCuotaDt($afpId, $toFondoId, $toValue, $date->format("Y-m-d"));
            $fromValue = $this->findCuotaValue($afpId, $fromFondoId, $cuotaDt);
            $data[] = [
                'user_id' => $userId,
                'afp_id' => $afpId,
                'from_fondo' => $fromFondoName,
                'to_fondo' => $toFondoName,
                'from_fondo_id' => $fromFondoId,
                'to_fondo_id' => $toFondoId,
                'monto' => $monto,
                'change_dt' => $date->format("Y-m-d"),
                'cuota_dt' => $cuotaDt,
                'from_value' => $fromValue,
                'to_value' => $toValue,
            ];
        }

        $changes = array_reverse($data);
        $totalChanges = count($changes);

        $this->saveChanges($changes);

        return $changes;
    }

    /**
     * Encuentra la fecha de la cuota
     *
     * @param int $afpId
     * @param int $fondoId
     * @param float $value
     * @param string $changeDate
     * @return string
     */
    public function findCuotaDt($afpId, $fondoId, $value, $changeDate)
    {
        $cuotas = TableRegistry::get('Cuotas');
        $cuota = $cuotas->find()
            ->where([
                'afp_id' => $afpId,
                'fondo_id' => $fondoId,
                'valor' => $value,
                'fecha <= ' => $changeDate
            ])
            ->orderDesc('fecha')
            ->first();

        if (!empty($cuota)) {
            return $cuota->fecha->i18nFormat("yyyy-MM-dd");
        }

        return '';
    }

    /**
     * Encuentra el valor de la cuota
     *
     * @param int $afpId
     * @param int $fondoId
     * @param int $date
     * @return float
     */
    public function findCuotaValue($afpId, $fondoId, $date)
    {
        $cuotas = TableRegistry::get('Cuotas');
        $cuota = $cuotas->find()
            ->where([
                'afp_id' => $afpId,
                'fondo_id' => $fondoId,
                'fecha' => $date
            ])
            ->first();
        if (!empty($cuota)) {
            return $cuota->valor;
        }

        return 0;
    }

    private function saveChanges($changes)
    {
      $totalChanges = count($changes);
      if ($totalChanges > 0 ) {
        $this->deleteAll(['user_id' => $changes[0]['user_id']]);
      }
      foreach ($changes as $changeIndex => $data) {
        $change = $this->find()
            ->where([
                'user_id' => $data['user_id'],
                'afp_id' => $data['afp_id'],
                'from_fondo_id' => $data['from_fondo_id'],
                'to_fondo_id' => $data['to_fondo_id'],
                'change_dt' => $data['change_dt'],
            ])
            ->first();
        if (empty($change)) {
            if ($totalChanges == 1) { // Nunca se ha cambiado de fondo
              $data['profits_loss'] = $this->calculateProfitOrLoss($data);
              $data['performance'] = $this->calculatePerformance($data);
              $entity = $this->newEntity($data);
              $this->save($entity);
            } else if ($totalChanges > 1) { // Al menos se ha cambiado una vez
              if ($changeIndex == 0) {
                // No se guarda el cambio
              } else if ($changeIndex == 1) {
                $data['profits_loss'] = floatval($changes[1]['from_value']) - floatval($changes[0]['from_value']);
                $data['performance'] = (floatval($changes[1]['from_value']) - floatval($changes[0]['from_value']))/floatval($changes[0]['from_value']) * 100;
                $entity = $this->newEntity($data);
                $this->save($entity);
              } else {
                $data['profits_loss'] = $this->calculateProfitOrLoss($data);
                $data['performance'] = $this->calculatePerformance($data);
                $entity = $this->newEntity($data);
                $this->save($entity);
              }
            }
        }
      }
    }

    public function calculateProfitOrLoss($data)
    {
        if ($data['from_fondo_id'] == $data['to_fondo_id']) {
          $cuotas = TableRegistry::get('Cuotas');
          $last = $cuotas->find()
              ->where([
                  'afp_id' => $data['afp_id'],
                  'fondo_id' => $data['from_fondo_id'],
              ])
              ->order(['fecha' => 'DESC'])
              ->first();

          if (!empty($last)) {
              return floatval($last->valor) - floatval($data['from_value']);
          }
          return 0;
        }

        $previous = $this->find()
            ->where([
                'user_id' => $data['user_id'],
                'afp_id' => $data['afp_id'],
                'to_fondo_id' => $data['from_fondo_id'],
                'cuota_dt <' => $data['cuota_dt'],
                'Changes.from_fondo_id != Changes.to_fondo_id'
            ])
            ->order(['cuota_dt' => 'DESC'])
            ->first();

        if (!empty($previous)) {
            return (floatval($data['from_value']) - floatval($previous->to_value));
        }

        return 0;
    }

    public function calculatePerformance($data)
    {
        if ($data['from_fondo_id'] == $data['to_fondo_id']) {
          $cuotas = TableRegistry::get('Cuotas');
          $last = $cuotas->find()
              ->where([
                  'afp_id' => $data['afp_id'],
                  'fondo_id' => $data['from_fondo_id'],
              ])
              ->order(['fecha' => 'DESC'])
              ->first();

          if (!empty($last)) {
              return (floatval($last->valor) - floatval($data['from_value']))/floatval($data['from_value'])*100;
          }
            return 0;
        }

        $previous = $this->find()
            ->where([
                'user_id' => $data['user_id'],
                'afp_id' => $data['afp_id'],
                'to_fondo_id' => $data['from_fondo_id'],
                'cuota_dt <' => $data['cuota_dt'],
                'Changes.from_fondo_id != Changes.to_fondo_id'
            ])
            ->order(['cuota_dt' => 'DESC'])
            ->first();

        if (!empty($previous) && floatval($previous->to_value) > 0) {
            return ((floatval($data['from_value']) - floatval($previous->to_value)) / floatval($previous->to_value)) * 100;
        }

        return 0;
    }

    /**
     * Returns the latest change made by the user
     *
     * @param int $userId
     * @param int $afpId
     * @return array
     */
    public function getLatestChange($userId, $afpId)
    {
        $cuotaDtQuery = $this->find()
            ->where([
                'user_id' => $userId,
                'afp_id' => $afpId,
                'Changes.from_fondo_id != Changes.to_fondo_id'
            ])
            ->order(['cuota_dt' => 'DESC'])
            ->contain(['FromFondos', 'ToFondos'])
            ->first();

        $cuotaDt = '';

        if (!empty($cuotaDtQuery)) {
            $cuotaDt = $cuotaDtQuery->cuota_dt->format('Y-m-d');
        }

        $rows = $this->find()
            ->where([
                'user_id' => $userId,
                'afp_id' => $afpId,
            ])
            ->order(['cuota_dt' => 'DESC', 'ToFondos.name' => 'ASC'])
            ->contain(['FromFondos', 'ToFondos'])
            ->limit(2)
            ->all();

        $changes = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if ($row->cuota_dt->format('Y-m-d') === $cuotaDt) {
                    $changes[] = $row->toArray();
                }
            }
        }

        return $changes;
    }

    public function changesUploadErrorEmail($userId, $filename)
    {
        $Users = TableRegistry::get('Users');
        $user = $Users->findById($userId)->first();
        $exists = file_exists($filename) && is_readable($filename);
        if (!empty($user) && $exists) {
            $email = new \Cake\Mailer\Email('default');
            try {
                $email->to('your-email@domain.com')
                    ->subject('[FondosAFP] Error al subir el archivo')
                    ->addAttachments($filename)
                    ->send('Ha ocurrido un error al cargar el archivo para el usuario ' . $user->first_name . ' ' . $user->last_name . ' ('.$user->email.')');
            } catch (\Exception $ex) {
                $email->to('your-email@domain.com')
                    ->subject('[FondosAFP] Exception')
                    ->send($ex->getMessage());
            }
        }
    }

    public function getPerformance($userId, $afpId)
    {
        $query = $this->find();

        $sum = 0;
        $query
            ->select(['performance'])
            ->where([
                'user_id' => $userId,
                'Changes.from_fondo_id != Changes.to_fondo_id'
            ])
            ->group(['afp_id', 'change_dt', 'from_fondo_id', 'performance']);

        $count = $query->count();

        $lastChanges = $this->getLatestChange($userId, $afpId);
        if (!empty($lastChanges)) {
            $Cuotas = TableRegistry::get('Cuotas');
            foreach ($lastChanges as $lastChange) {
                $cuota = $Cuotas->getLatestValue($afpId, $lastChange['to_fondo_id']);
                $ganancia = $cuota->valor - $lastChange['to_value'];
                $count++;
                $sum += (float)100*(($ganancia)/$lastChange['to_value']);
            }
        }

        if ($count > 0) {
            $rows = $query->all();

            foreach ($rows as $row) {
                $sum += (float)$row->performance;
            }
            return ($sum/$count);
        }

        return 0;
    }

    public function getRentability($userId, $afpId)
    {
        $query = $this->find();

        $sum = 0;
        $query->select(['performance'])
            ->where([
                'user_id' => $userId,
                'Changes.from_fondo_id != Changes.to_fondo_id'
            ]);

        $lastChanges = $this->getLatestChange($userId, $afpId);
        if (!empty($lastChanges)) {
            $Cuotas = TableRegistry::get('Cuotas');
            foreach ($lastChanges as $lastChange) {
                $cuota = $Cuotas->getLatestValue($afpId, $lastChange['to_fondo_id']);
                $ganancia = $cuota->valor - $lastChange['to_value'];
                $sum += (float)100*($ganancia/$lastChange['to_value']);
            }
        }

        $rows = $query->all();

        foreach ($rows as $row) {
            $sum += (float)$row->performance;
        }

        return $sum;
    }

    public function getConsistency($userId, $afpId)
    {
        $ganados = $this->find()
            ->where([
                'user_id' => $userId,
                'profits_loss >=' => 0,
                'Changes.from_fondo_id != Changes.to_fondo_id'
            ])
            ->count();

        $total = $this->find()
            ->where([
                'user_id' => $userId,
                'Changes.from_fondo_id != Changes.to_fondo_id'
            ])
            ->count();

        $lastChanges = $this->getLatestChange($userId, $afpId);
        if (!empty($lastChanges)) {
            $Cuotas = TableRegistry::get('Cuotas');
            foreach ($lastChanges as $lastChange) {
                $cuota = $Cuotas->getLatestValue($afpId, $lastChange['to_fondo_id']);
                $ganancia = $cuota->valor - $lastChange['to_value'];
                if ($ganancia >= 0) {
                    $ganados++;
                }
                $total++;
            }
        }

        if ($total > 0) {
            return ($ganados/$total)*100;
        }

        return 0;
    }

    public function addChange($userId, $afpId, $changeDt, $fromFondo, $toFondo) {
        $Fondos = TableRegistry::get('Fondos');
        $fromFondoId = $Fondos->findByName($fromFondo)->first()->id;
        $toFondoId = $Fondos->findByName($toFondo)->first()->id;

        $fromValue = $this->findCuotaValue($afpId, $fromFondoId, $changeDt);
        if (empty($fromValue)) {
            throw new \Exception(__('No se ha podido encontrar el valor de los fondos para la fecha indicada'));
        }
        $cuotaDt = $this->findCuotaDt($afpId, $fromFondoId, $fromValue, $changeDt);
        $toValue = $this->findCuotaValue($afpId, $toFondoId, $changeDt);

        $data = [
            'user_id' => $userId,
            'afp_id' => $afpId,
            'change_dt' => $changeDt,
            'cuota_dt' => $cuotaDt,
            'from_fondo_id' => $fromFondoId,
            'to_fondo_id' => $toFondoId,
            'from_value' => $fromValue,
            'to_value' => $toValue,
            'monto' => 0,
            'source' => 'history'
        ];

        $this->saveChange($data);
        $this->recalculateMetrics($userId);
    }

    private function saveChange($data)
        {
            $change = $this->find()
                ->where([
                    'user_id' => $data['user_id'],
                    'afp_id' => $data['afp_id'],
                    'from_fondo_id' => $data['from_fondo_id'],
                    'to_fondo_id' => $data['to_fondo_id'],
                    'change_dt' => $data['change_dt'],
                ])
                ->first();
            if (empty($change)) {
                $data['profits_loss'] = $this->calculateProfitOrLoss($data);
                $data['performance'] = $this->calculatePerformance($data);
                $entity = $this->newEntity($data);
                $this->save($entity);
            }
        }

    public function recalculateMetrics($userId)
    {
        $rows = $this->find()->where(['user_id' => $userId])->all();
        foreach ($rows as $row) {
            $data = $row->toArray();
            $data['profits_loss'] = $this->calculateProfitOrLoss($data);
            $data['performance'] = $this->calculatePerformance($data);
            $entity = $this->patchEntity($row, $data);
            $this->save($entity);
        }
        $Rankings = TableRegistry::get('Rankings');
        $Rankings->calculateRanking();
    }

    public function getReporteMensualData($userId)
    {
        $Preferences = TableRegistry::get('preferences');
        $userPreferences = $Preferences->getByUserId($userId);



        $data = [
            'A' => [
                'valor' => 0,
                'porcentaje' => 0
            ],
            'B' => [
                'valor' => 0,
                'porcentaje' => 0
            ],
            'C' => [
                'valor' => 0,
                'porcentaje' => 0
            ],
            'D' => [
                'valor' => 0,
                'porcentaje' => 0
            ],
            'E' => [
                'valor' => 0,
                'porcentaje' => 0
            ],
        ];



        return $data;
    }
}
