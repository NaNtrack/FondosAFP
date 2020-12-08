<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Change Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $afp_id
 * @property int $from_fondo_id
 * @property int $to_fondo_id
 * @property float $from_value
 * @property float $to_value
 * @property float $monto
 * @property float $profits_loss
 * @property \Cake\I18n\Time $change_dt
 * @property \Cake\I18n\Time $cuota_dt
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Afp $afp
 * @property \App\Model\Entity\Fondo $from_fondo
 * @property \App\Model\Entity\Fondo $to_fondo
 */
class Change extends Entity
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
