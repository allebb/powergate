<?php

namespace Powergate\Validators\CustomRules;

class CustomRules extends \Illuminate\Validation\Validator
{

    protected $domain_server_types = [
        'MASTER',
        'SLAVE',
        'NATIVE',
    ];
    
    protected $record_types = [
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
     * The validation rule for 'domain_server_type'.
     */
    public function validateDomainServerType($attribute, $value, $parameters)
    {
        if (in_array($value, $this->domain_server_types)) {
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
    protected function replaceDomainServerType($message, $attribute, $rule, $parameters)
    {
        return str_replace(':type', $attribute, $message);
    }

    /**
     * The validation rule for 'record_type'.
     */
    public function validateRecordType($attribute, $value, $parameters)
    {
        if (in_array($value, $this->record_types)) {
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
