<?php

namespace Powergate;

class Supermaster extends \Eloquent
{

    public $timestamps = false;

    protected $hidden = [];

    protected $fillable = [];

    protected $primaryKey = 'ip';

}
