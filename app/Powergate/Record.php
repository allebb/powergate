<?php

namespace Powergate;

/**
 * An Eloquent Model: 'Powergate\Record'
 *
 * @property integer $id
 * @property integer $domain_id
 * @property string $name
 * @property string $type
 * @property string $contents
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

    public function saveNew()
    {
        $this->domain_id = Input::get('domain_id');
        $this->name = Input::get('name');
        $this->type = strtoupper(Input::get('type'));
        $this->content = strtolower(Input::get('content'));
        $this->ttl = Input::get('ttl');
        $this->prio = Input::get('prio');
        $this->change_date = time();
        $this->save();
    }

    public function saveUpdate()
    {
        $this->domain_id = Input::get('domain_id');
        $this->name = Input::get('name');
        $this->type = strtoupper(Input::get('type'));
        $this->content = strtolower(Input::get('content'));
        $this->ttl = Input::get('ttl');
        $this->prio = Input::get('prio');
        $this->change_date = time();
        $this->save();
    }

}
