<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Device Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $os
 * @property int $enable_notifications
 * @property int $notify_changes
 * @property int $notify_news
 * @property int $notify_app_updates
 * @property int $notify_other
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string $token
 *
 * @property \App\Model\Entity\User $user
 */
class Device extends Entity
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
        'user_id' => true,
        'os' => true,
        'enable_notifications' => true,
        'notify_changes' => true,
        'notify_news' => true,
        'notify_app_updates' => true,
        'notify_other' => true,
        'created' => true,
        'modified' => true,
        'token' => true,
        'user' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token'
    ];
}
