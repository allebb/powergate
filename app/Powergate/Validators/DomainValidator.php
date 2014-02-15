<?php

namespace Powergate\Validators;

use Powergate\Validators\Validator;

class DomainValidator extends Validator
{

    public function __construct(array $input, $isNew = true)
    {
        parent::__construct($input, $isNew);
    }

    /**
     * Rules applied to new records!
     * @var array
     */
    protected $createRules = array(
        'name' => 'required|unique:domains', // Domain must be unique!
        'type' => 'domain_server_type|required',
    );

    /**
     * Rules applied to records that are being updated!
     * @var array
     */
    protected $updateRules = array(
        'name' => 'required',
        'type' => 'domain_server_type|required',
    );

}
