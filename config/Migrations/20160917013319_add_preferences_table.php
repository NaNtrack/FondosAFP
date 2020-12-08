<?php

use Phinx\Migration\AbstractMigration;

class AddPreferencesTable extends AbstractMigration
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
        $table = $this->table('preferences');
        $table->addColumn('user_id', 'integer', [
            'null' => false,
            'signed' => false,
        ]);
        $table->addColumn('afp_id', 'integer', [
            'null' => false,
            'signed' => false,
        ]);
        $table->addColumn('fondo_id', 'integer', [
            'null' => true,
            'signed' => false,
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
        $table->addForeignKey('afp_id', 'afps', 'id');
        $table->addForeignKey('fondo_id', 'fondos', 'id');

        $table->save();
    }
    
    public function down() {
        $this->dropTable('preferences');
    }
}
