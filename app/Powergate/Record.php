<?php

namespace Powergate;

use Powergate\Validators\RecordValidator;

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
 * @property string $content
 * @property-read \Powergate\\Domain $domain
 */
class Record extends \Eloquent
{

    public $timestamps = false;
    protected $hidden = [];
    protected $fillable = [];

    public function domain()
    {
        return $this->belongsTo('Powergate\\Domain');
    }

    /**
     * A 'service' to create and validate a new Recrod, this could be moved to a seperate service
     * class by the application is lightweight therefore would be overkill if I did!
     * @param array $input
     */
    public function serviceNew(array $input)
    {
        // Lets validate the input first!
        $validator = new RecordValidator($input);
        $validator->checkValidation();

        // Assign values
        $this->domain_id = (int) $input['domain_id'];
        $this->name = strtolower($input['name']);
        $this->type = strtoupper($input['type']);
        $this->content = strtolower($input['content']);
        $this->ttl = (int) $input['ttl'];
        $this->prio = (isset($input['prio']) ? (int) $input['prio'] : null);
        $this->change_date = (int) time();

        // If succssful we should be able to save the result"
        return $this->save();
    }

    /**
     * A 'service' to create and validate a new Recrod, this could be moved to a seperate service
     * class by the application is lightweight therefore would be overkill if I did!
     * @param array $input
     */
    public function serviceUpdate(array $input)
    {

        // Lets validate the input first!
        $validator = new RecordValidator($input);
        $validator->checkValidation();

        // Assign values
        $this->domain_id = (int) $input['domain_id'];
        $this->name = strtolower($input['name']);
        $this->type = strtoupper($input['type']);
        $this->content = strtolower($input['type']);
        $this->ttl = (int) $input['ttl'];
        $this->prio = (isset($input['prio']) ? (int) $input['prio'] : $this->prio);
        $this->change_date = (int) time();

        // If succssful we should be able to save the result"
        return $this->save();
    }

}
