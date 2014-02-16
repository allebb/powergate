<?php

namespace Powergate;

use Input;

/**
 * An Eloquent Model: 'Powergate\Domain'
 *
 * @property integer $id
 * @property string $name
 * @property string $master
 * @property integer $last_check
 * @property string $type
 * @property integer $notified_serial
 * @property string $account
 * @property integer $user_id
 */
class Domain extends \Eloquent
{

    public $timestamps = false;
    
    protected $hidden = [];
    
    protected $fillable = [
        'name',
        'master',
        'type',
        'account',
    ];
    
    public function records()
    {
        return $this->hasMany('Powergate\\Record');
    }

    public function saveNew()
    {
        $this->name = strtolower(Input::get('name'));
        $this->master = Input::get('master');
        $this->type = strtoupper(Input::get('type'));
        $this->account = strtolower(Input::get('account'));
        $this->save();
    }

    public function saveUpdate()
    {
        $this->name = strtolower(Input::get('name'));
        $this->master = Input::get('master');
        $this->type = strtoupper(Input::get('type'));
        $this->account = strtolower(Input::get('account'));
        $this->save();
    }

}
