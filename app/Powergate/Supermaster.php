<?php

namespace Powergate;

/**
 * An Eloquent Model: 'Powergate\Supermaster'
 *
 * @property string $ip
 * @property string $nameserver
 * @property string $account
 */
class Supermaster extends \Eloquent
{

    public $timestamps = false;
    
    protected $hidden = [];
    
    protected $fillable = [];
    
    protected $primaryKey = 'ip';
    
    public $incrementing = false;

}
