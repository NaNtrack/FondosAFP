<?php
use Migrations\AbstractMigration;

class AddDevicesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $table = $this->table('devices');
        $table->addColumn('user_id', 'integer', [
            'null' => false,
            'signed' => false,
        ]);
        $table->addColumn('device_id', 'string', [
            'limit' => '255',
            'null' => false,
            'default' => ''
        ]);
        $table->addColumn('os', 'string', [
            'limit' => '10',
            'null' => false,
            'default' => ''
        ]);
        $table->addColumn('enable_notifications', 'integer', [
            'limit' => 1,
            'null' => false,
            'default' => 1
        ]);
        $table->addColumn('notify_changes', 'integer', [
            'limit' => 1,
            'null' => false,
            'default' => 1
        ]);
        $table->addColumn('notify_news', 'integer', [
            'limit' => 1,
            'null' => false,
            'default' => 1
        ]);
        $table->addColumn('notify_app_updates', 'integer', [
            'limit' => 1,
            'null' => false,
            'default' => 1
        ]);
        $table->addColumn('notify_other', 'integer', [
            'limit' => 1,
            'null' => false,
            'default' => 1
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);

        $table->addForeignKey('user_id', 'users', 'id');

        $table->create();
    }

    public function down() {
        $this->dropTable('devices');
    }
}
