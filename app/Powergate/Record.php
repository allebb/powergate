<?php

namespace Powergate;

/**
 * An Eloquent Model: 'Powergate\Record'
 *
 * @property integer $id
 * @property integer $domain_id
 * @property string $name
 * @property string $type
 * @property string $content
 * @property integer $ttl
 * @property integer $prio
 * @property integer $change_date
 * @property integer $user_id
 */
class Record extends \Eloquent
{

    public $timestamps = false;

    protected $hidden = [];

    protected $fillable = [];

}
