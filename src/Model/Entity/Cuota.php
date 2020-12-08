<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cuota Entity
 *
 * @property int $id
 * @property \Cake\I18n\Time $fecha
 * @property int $afp_id
 * @property int $fondo_id
 * @property float $valor
 * @property float $patrimonio
 * @property float $variacion_val
 * @property float $varacion_por
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\Afp $afp
 * @property \App\Model\Entity\Fondo $fondo
 */
class Cuota extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
