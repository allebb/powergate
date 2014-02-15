<?php

namespace Powergate\Validators\CustomRules;

class RecordTypeRule extends \Illuminate\Validation\Validator
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

    /**
     * The validation rule for 'record_type'.
     */
    public function validateRecordType($attribute, $value, $parameters)
    {
        if (in_array($value, $this->types)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Replaces attributes for custom validation rule messages.
     * Replaces the error message attribuite ':type' with the item value being validated on.
     * A customer error message should be added to 'lang/en/validation.php' by replacing the Pascal case with snakecase!
     */
    protected function replaceRecordType($message, $attribute, $rule, $parameters)
    {
        return str_replace(':type', $attribute, $message);
    }

}
