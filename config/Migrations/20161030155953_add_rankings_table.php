<?php

use Phinx\Migration\AbstractMigration;

class AddRankingsTable extends AbstractMigration
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
        $table = $this->table('rankings');
        $table->addColumn('user_id', 'integer', [
            'null' => false,
            'signed' => false,
        ]);
        $table->addColumn('puesto', 'integer', [
            'null' => false,
            'signed' => false,
        ]);
        $table->addColumn('puesto_anterior', 'integer', [
            'null' => true,
            'signed' => false,
        ]);
        $table->addColumn('performance', 'decimal', [
            'precision' => 10,
            'scale' => 4,
            'after' => 'puesto_anterior',
            'null' => false,
            'default' => 0.0
        ]);
        $table->addColumn('rentabilidad', 'decimal', [
            'precision' => 10,
            'scale' => 4,
            'after' => 'performance',
            'null' => false,
            'default' => 0.0
        ]);
        $table->addColumn('consistencia', 'decimal', [
            'precision' => 10,
            'scale' => 4,
            'after' => 'rentabilidad',
            'null' => false,
            'default' => 0.0
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('deleted', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addForeignKey('user_id', 'users', 'id');

        $table->save();
    }

    public function down() {
        $this->dropTable('rankings');
    }
}
