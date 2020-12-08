<?php

use Phinx\Migration\AbstractMigration;

class UpdatePreferences extends AbstractMigration
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
    public function change()
    {
         $table = $this->table('preferences');
         $table->changeColumn('event_fondos_up', 'integer', [
            'null' => false,
            'default' => 1,
            'signed' => false,
         ]);
         $table->changeColumn('event_fondos_down', 'integer', [
            'null' => false,
            'default' => 1,
            'signed' => false,
         ]);
         $table->changeColumn('resumen_semanal', 'integer', [
            'null' => false,
            'default' => 1,
            'signed' => false,
         ]);
         $table->changeColumn('event_ranking_up', 'integer', [
            'null' => false,
            'default' => 1,
            'signed' => false,
         ]);
         $table->changeColumn('event_ranking_down', 'integer', [
            'null' => false,
            'default' => 1,
            'signed' => false,
         ]);
         $table->changeColumn('event_new_follow', 'integer', [
            'null' => false,
            'default' => 1,
            'signed' => false,
         ]);
         $table->update();
    }
}
