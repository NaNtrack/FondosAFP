<?php
use Migrations\AbstractMigration;

class AddDeviceNotifyChangesDate extends AbstractMigration
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
        $table->addColumn('notify_changes_date', 'datetime', [
            'null' => true,
            'default' => null,
            'after' => 'notify_changes'
        ]);
        $table->update();
    }
}
