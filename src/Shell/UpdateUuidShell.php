<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Log\Log;

/**
 * Importar shell command.
 */
class UpdateUuidShell extends Shell
{

    public function main() 
    {
        ini_set('max_execution_time', 0);
        $this->loadModel('Users');
        $this->out("Starting update");
        
        $users = $this->Users->find()
            ->where(['uuid IS' => null])
            ->all();
        
        $updated = count($users);
        
        foreach ($users as $user) {
            $this->Users->query()
                ->update()
                ->set(['uuid' => \Cake\Utility\Text::uuid()])
                ->where(['id' => $user->id])
                ->execute();
        }
        
        $this->out("Job finished, $updated users updated");

        return $updated;
    }
}

