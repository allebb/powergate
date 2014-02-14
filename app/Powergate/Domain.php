<?php

namespace Powergate;

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

}
