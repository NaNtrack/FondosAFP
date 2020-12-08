<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ChangesFixture
 *
 */
class ChangesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'afp_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'from_fondo_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'to_fondo_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'from_value' => ['type' => 'decimal', 'length' => 10, 'precision' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'to_value' => ['type' => 'decimal', 'length' => 10, 'precision' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'monto' => ['type' => 'float', 'length' => null, 'precision' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'profits_loss' => ['type' => 'decimal', 'length' => 10, 'precision' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'change_dt' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'cuota_dt' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'fk_cha_user_id_idx' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
            'fk_cha_ffid_idx' => ['type' => 'index', 'columns' => ['from_fondo_id'], 'length' => []],
            'fk_cha_tfid_idx' => ['type' => 'index', 'columns' => ['to_fondo_id'], 'length' => []],
            'fk_cha_afp_id_idx' => ['type' => 'index', 'columns' => ['afp_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_cha_afp_id' => ['type' => 'foreign', 'columns' => ['afp_id'], 'references' => ['afps', 'id'], 'update' => 'noAction', 'delete' => 'restrict', 'length' => []],
            'fk_cha_ffid' => ['type' => 'foreign', 'columns' => ['from_fondo_id'], 'references' => ['fondos', 'id'], 'update' => 'noAction', 'delete' => 'restrict', 'length' => []],
            'fk_cha_tfid' => ['type' => 'foreign', 'columns' => ['to_fondo_id'], 'references' => ['fondos', 'id'], 'update' => 'noAction', 'delete' => 'restrict', 'length' => []],
            'fk_cha_user_id' => ['type' => 'foreign', 'columns' => ['user_id'], 'references' => ['users', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
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
            'user_id' => 1,
            'afp_id' => 1,
            'from_fondo_id' => 1,
            'to_fondo_id' => 2,
            'from_value' => 1.5,
            'to_value' => 1.8,
            'monto' => 1332,
            'profits_loss' => 4.3,
            'change_dt' => '2016-08-27',
            'cuota_dt' => '2016-08-27',
            'created' => '2016-08-27 01:51:05',
            'modified' => '2016-08-27 01:51:05'
        ],
    ];
}
