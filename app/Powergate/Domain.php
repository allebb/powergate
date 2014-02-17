<?php

namespace Powergate;

use Powergate\Validators\DomainValidator;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\Powergate\\Record[] $records
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

    public function serviceNew(array $input)
    {
        // Lets validate the input first!
        $validator = new DomainValidator($input);
        $validator->checkValidation();

        // Assign values
        $this->name = strtolower($input['name']);
        $this->master = (isset($input['master']) ? strtolower($input['master']) : null);
        $this->type = strtoupper($input['type']);
        $this->account = (isset($input['account']) ? strtolower($input['account']) : null);
        $this->last_check = null;
        $this->notified_serial = null;

        // If succssful we should be able to save the result
        $this->save();
    }

    public function serviceUpdate(array $input)
    {

        // Lets validate the input first!
        $validator = new DomainValidator($input, false);
        $validator->checkValidation();

        // Assign values
        $this->name = strtolower($input['name']);
        $this->master = (isset($input['master']) ? strtolower($input['master']) : null);
        $this->type = strtoupper($input['type']);
        $this->account = (isset($input['account']) ? strtolower($input['account']) : null);

        // If succssful we should be able to save the result
        $this->save();
    }

}
