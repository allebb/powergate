<?php

namespace Powergate\Validators\CustomRules;

class DomainServerTypeRule extends \Illuminate\Validation\Validator
{

    protected $types = [
        'MASTER',
        'SLAVE',
        'NATIVE',
    ];

    /**
     * The validation rule for 'domain_server_type'.
     */
    public function validateDomainServerType($attribute, $value, $parameters)
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
    protected function replaceDomainServerType($message, $attribute, $rule, $parameters)
    {
        return str_replace(':type', $attribute, $message);
    }

}
