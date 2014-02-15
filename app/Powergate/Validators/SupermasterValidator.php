<?php

namespace Powergate\Validators;

use Powergate\Validators\Validator;

class SupermasterValidator extends Validator
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
        'ip' => 'ip|required|unique:supermasters',
        'nameserver' => 'required',
        'account' => 'required',
    );

    /**
     * Rules applied to records that are being updated!
     * @var array
     */
    protected $updateRules = array(
        'ip' => 'ip|required',
        'nameserver' => 'required',
        'account' => 'required',
    );

}
