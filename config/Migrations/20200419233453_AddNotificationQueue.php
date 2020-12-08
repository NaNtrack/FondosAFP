<?php
use Migrations\AbstractMigration;

class AddNotificationQueue extends AbstractMigration
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
        $table = $this->table('notification_queue');
        $table->addColumn('user_id', 'integer', [
            'null' => false,
            'signed' => false,
        ]);
        $table->addColumn('device_id', 'string', [
            'limit' => '255',
            'null' => false,
            'default' => ''
        ]);
        $table->addColumn('notification_type', 'string', [
            'limit' => '20',
            'null' => false,
            'default' => ''
        ]);
        $table->addColumn('status', 'string', [
            'limit' => '20',
            'null' => false,
            'default' => 'none'
        ]);
        $table->addColumn('payload', 'string', [
            'limit' => null,
            'null' => false,
            'default' => ''
        ]);
        $table->addColumn('sent', 'timestamp', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('created', 'timestamp', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => false,
        ]);
        $table->addColumn('modified', 'timestamp', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => false,
        ]);

        $table->addForeignKey('user_id', 'users', 'id');

        $table->create();
    }

    public function down() {
        $this->dropTable('notification_queue');
    }
}
