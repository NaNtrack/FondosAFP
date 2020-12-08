<?php

use Phinx\Migration\AbstractMigration;

class AddLoginDatesToUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('login_dt', 'datetime', [
            'after' => 'verified',
            'null' => true,
            'default' => NULL
        ]);
        $table->addColumn('previous_login_dt', 'datetime', [
            'after' => 'login_dt',
            'null' => true,
            'default' => NULL
        ]);
        $table->addColumn('first_login_dt', 'datetime', [
            'after' => 'previous_login_dt',
            'null' => true,
            'default' => NULL
        ]);
        $table->save();
    }
    
    public function down()
    {
        $table = $this->table('users');
        $table->removeColumn('login_dt');
        $table->removeColumn('previous_login_dt');
        $table->removeColumn('first_login_dt');
        $table->save();
    }
}
