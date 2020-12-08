<?php

use Phinx\Migration\AbstractMigration;

class AddPreferences extends AbstractMigration
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
        $table->addColumn('homepage', 'string', [
            'limit' => '50',
            'after' => 'fondo_id',
            'null' => false,
            'default' => 'dashboard'
        ]);
        $table->addColumn('mostrar_otras_afps', 'integer', [
            'limit' => 1,
            'after' => 'homepage',
            'null' => false,
            'default' => 1
        ]);
        $table->addColumn('event_fondos_up', 'integer', [
            'limit' => 1,
            'after' => 'mostrar_otras_afps',
            'null' => false,
            'default' => 0
        ]);
        $table->addColumn('event_fondos_down', 'integer', [
            'limit' => 1,
            'after' => 'event_fondos_up',
            'null' => false,
            'default' => 0
        ]);
        $table->addColumn('resumen_semanal', 'integer', [
            'limit' => 1,
            'after' => 'event_fondos_down',
            'null' => false,
            'default' => 0
        ]);
        $table->addColumn('event_ranking_up', 'integer', [
            'limit' => 1,
            'after' => 'resumen_semanal',
            'null' => false,
            'default' => 0
        ]);
        $table->addColumn('event_ranking_down', 'integer', [
            'limit' => 1,
            'after' => 'event_ranking_up',
            'null' => false,
            'default' => 0
        ]);
        $table->addColumn('event_new_follow', 'integer', [
            'limit' => 1,
            'after' => 'event_ranking_down',
            'null' => false,
            'default' => 0
        ]);
        
        $table->save();
    }
    
    public function down()
    {
        $table = $this->table('preferences');
        $table->removeColumn('homepage');
        $table->removeColumn('mostrar_otras_afps');
        $table->removeColumn('event_fondos_up');
        $table->removeColumn('event_fondos_down');
        $table->removeColumn('resumen_semanal');
        $table->removeColumn('event_ranking_up');
        $table->removeColumn('event_ranking_down');
        $table->removeColumn('event_new_follow');
        $table->save();
    }
}
