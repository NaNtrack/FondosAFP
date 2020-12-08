<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FondosFixture
 *
 */
class FondosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'fixed' => true, 'length' => 1, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'description' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'api_name' => ['type' => 'string', 'fixed' => true, 'length' => 1, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'country' => ['type' => 'string', 'length' => 3, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'status' => ['type' => 'integer', 'length' => 1, 'unsigned' => true, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
            'name' => 'A',
            'description' => 'Fondo A',
            'api_name' => 'a',
            'country' => 'CL',
            'status' => 1
        ],
        [
            'id' => 2,
            'name' => 'B',
            'description' => 'Fondo B',
            'api_name' => 'b',
            'country' => 'CL',
            'status' => 1
        ],
        [
            'id' => 3,
            'name' => 'C',
            'description' => 'Fondo C',
            'api_name' => 'c',
            'country' => 'CL',
            'status' => 1
        ],
        [
            'id' => 4,
            'name' => 'D',
            'description' => 'Fondo D',
            'api_name' => 'd',
            'country' => 'CL',
            'status' => 1
        ],
        [
            'id' => 5,
            'name' => 'E',
            'description' => 'Fondo E',
            'api_name' => 'e',
            'country' => 'CL',
            'status' => 1
        ],
    ];
}
