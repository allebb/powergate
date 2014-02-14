<?php

namespace Powergate\Validators;

use Powergate\Validators\Validator;

class RecordValidator extends Validator
{

    protected $types = [
        'A',
        'AAAA',
        'CNAME',
        'HINFO',
        'MX',
        'NAPTR',
        'NS',
        'PTR',
        'SOA',
        'SPF',
        'SRV',
        'SSHFP',
        'TXT',
        'RP'
    ];

    public function __construct(array $input, $isNew = true)
    {
        parent::__construct($input, $isNew);
    }

    /**
     * Rules applied to new records!
     * @var array
     */
    protected $createRules = array(
        'name' => 'required|unique:records', // Domain must be unique!
        'type' => 'record_type|required',
        'content' => 'required',
        'ttl' => 'required|numeric',
        'prio' => 'required|numeric',
        'change_date' => 'numeric',
    );

    /**
     * Rules applied to records that are being updated!
     * @var array
     */
    protected $updateRules = array(
        'name' => 'required',
        'type' => 'record_type|required',
        'content' => 'required',
        'ttl' => 'required|numeric',
        'prio' => 'required|numeric',
        'change_date' => 'numeric',
    );

}
