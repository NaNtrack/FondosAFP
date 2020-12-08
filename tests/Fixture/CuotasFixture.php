<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CuotasFixture
 *
 */
class CuotasFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'fecha' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'afp_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'fondo_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'valor' => ['type' => 'decimal', 'length' => 10, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => ''],
        'patrimonio' => ['type' => 'float', 'length' => null, 'precision' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'variacion_val' => ['type' => 'decimal', 'length' => 10, 'precision' => 2, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'varacion_por' => ['type' => 'decimal', 'length' => 5, 'precision' => 2, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'fk_afp_id_idx' => ['type' => 'index', 'columns' => ['afp_id'], 'length' => []],
            'fk_fondo_id_idx' => ['type' => 'index', 'columns' => ['fondo_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_afp_id' => ['type' => 'foreign', 'columns' => ['afp_id'], 'references' => ['afps', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_fondo_id' => ['type' => 'foreign', 'columns' => ['fondo_id'], 'references' => ['fondos', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'fecha' => '2016-08-29',
            'afp_id' => 1,
            'fondo_id' => 1,
            'valor' => 1.5,
            'patrimonio' => 1,
            'variacion_val' => 1.5,
            'varacion_por' => 1.5,
            'created' => '2016-08-27 01:50:46',
            'modified' => '2016-08-27 01:50:46'
        ],
        [
            'id' => 2,
            'fecha' => '2016-08-29',
            'afp_id' => 1,
            'fondo_id' => 2,
            'valor' => 1.7,
            'patrimonio' => 1,
            'variacion_val' => 1.5,
            'varacion_por' => 1.5,
            'created' => '2016-08-27 01:50:46',
            'modified' => '2016-08-27 01:50:46'
        ],
    ];
}
