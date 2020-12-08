<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $Changes
 * @property \Cake\ORM\Association\HasOne $Preferences
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->table('users');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Changes', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasOne('Preferences', [
            'foreignKey' => 'user_id'
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
            ->notEmpty('email', 'An email is required')
            ->notEmpty('role', 'A role is required')
            ->add('role', 'inList', [
                'rule' => ['inList', ['admin', 'usuario']],
                'message' => 'Please enter a valid role'
            ]);

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
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }

    /**
     * Updates the user login information.
     *
     * @param array $data An array with a key 'id' for the user id
     * @throws \Exception If the user is not found
     */
    public function setLoginDates($data) {
        $loginDt = gmdate("Y-m-d H:i:s");
        $previousLoginDt = null;
        $firstLoginDt = null;

        $user = $this->findById($data['id'])->first();

        if (empty($user)) {
            throw new \Exception(__('No se ha encontrado el usuario'));
        }

        if ($user->uuid === null) {
            $this->query()->update()->set([
                'uuid' => \Cake\Utility\Text::uuid()
            ])->where(['id' => $user->id])->execute();
        }

        if (empty($user->first_login_dt)) {
            $firstLoginDt = gmdate("Y-m-d H:i:s");
            $this->sendNewUserEmail($user);
            //$this->sendWelcomeEmail($user);
        } else {
            $firstLoginDt = $user->first_login_dt->format('Y-m-d H:i:s');
        }
        if (!empty($user->login_dt)) {
            $previousLoginDt = $user->login_dt;
        }

        $this->query()->update()->set([
            'login_dt' => $loginDt,
            'previous_login_dt' => $previousLoginDt,
            'first_login_dt' => $firstLoginDt
        ])->where(['id' => $user->id])->execute();
    }

    /**
     * Send a welcome email to the new registered user.
     *
     * @param object $user
     */
    private function sendWelcomeEmail($user)
    {
        $email = $this->getMailer();
        $email->to($user->email)
            ->subject('Bienvenido')
            ->send('Bienvenido a Fondos AFP');
    }

    /**
     * Sends an email to the admin letting him know that there is a new user.
     *
     * @param object $user
     */
    private function sendNewUserEmail($user)
    {
        $email = $this->getMailer();
        $email->to('your-email@domain.com')
            ->subject('[FondosAFP] Nuevo usuario')
            ->send(print_r($user, true));
    }

    /**
     *
     * @return \Cake\Mailer\Email
     */
    public function getMailer()
    {
        return new \Cake\Mailer\Email('default');
    }

    /**
     * Returns a array with the neccesary information for the users report
     */
    public function getReport()
    {
        return $this->find()
            ->select(['Users.id', 'first_name', 'last_name', 'email', 'social_source', 'login_dt', 'Users.created', 'afp_name' => 'afps.name' , 'fondo_name' => 'fondos.name', 'changes_count' => 'count(changes.id)'])
            ->leftJoin('preferences', ['user_id = Users.id'])
            ->leftJoin('afps', ['preferences.afp_id = afps.id'])
            ->leftJoin('fondos', ['preferences.fondo_id = fondos.id'])
            ->leftJoin('changes', ['changes.user_id = Users.id'])
            ->group(['Users.id', 'first_name', 'last_name', 'email', 'social_source', 'login_dt', 'Users.created', 'afps.name', 'fondos.name'])
            ->order(['Users.created' => 'DESC'])
            ->all()
            ->toArray();
    }

    public function saveEmail($userId, $email)
    {
        $existingUserEmail = $this->findByEmail($email)->first();
        if (!empty($existingUserEmail) && $existingUserEmail->id !== $userId) {
            throw new \Exception('Otro usuario ya tienen registrado ese correo electrónico');
        }

        $user = $this->findById($userId)->first();
        if (!empty($user)) {
            $user->email = $email;
            $user->verified = 0;
            $this->save($user);
        }

        //send confirmation email
        $confirmUrl = 'https://www.fondosafp.com/confirm_email?token='.base64_encode("{$user->id}:{$user->email}");
        $emailer = $this->getMailer();
        $emailer->to($user->email)
            ->subject('Por favor confirma tu email')
            ->send('Presiona el siguiente link para poder confirmar tu dirección de correo electrónico: ' . $confirmUrl);

        return true;
    }

    public function confirmEmail($userId, $email)
    {
        $user = $this->findById($userId)->first();
        if (!empty($user) && $email == $user->email) {
            $user->verified = 1;
            $this->save($user);
        }

        return true;
    }

    public function removeUser($userId)
    {
        $Preferences = \Cake\ORM\TableRegistry::get('Preferences');
        $Changes = \Cake\ORM\TableRegistry::get('Changes');
        $Preferences->deleteAll(['user_id' => $userId]);
        $Changes->deleteAll(['user_id' => $userId]);
        $this->deleteAll(['id' => $userId]);
    }
}
