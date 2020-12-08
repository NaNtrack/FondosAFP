<?php
use Migrations\AbstractMigration;

class AddDeviceToken extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('devices');
        $table->addColumn('token', 'string', [
            'null' => false,
            'default' => '',
        ]);
        $table->update();
    }
}
