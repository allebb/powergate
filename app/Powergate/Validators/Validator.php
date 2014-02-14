<?php

namespace Powergate\Validators;

use Input;
use Validator as LaravelValidator;

abstract class Validator
{

    protected $input;
    protected $createRules;
    protected $updateRules;
    protected $errors;
    protected $validation;

    public function __construct($input = null, $isNew = true)
    {
        // If this is not a new record, apply the $updateRules set instead.
        if ($isNew == false) {
            $this->createRules = $this->updateRules;
        }

        if ($input == null) {
            $this->input = Input::all();
        } else {
            $this->input = $input;
        }

        $this->validation = LaravelValidator::make($this->input, $this->createRules);
        if ($this->validation->fails()) {
            $this->errors = $this->validation->messages();
        }
    }

    /**
     * Checks if the validation has passed.
     * @return boolean
     */
    public function passes()
    {
        return $this->validation->passes();
    }

    /**
     * Checks if the validation has failed.
     * @return boolean
     */
    public function fails()
    {
        return $this->validation->fails();
    }

    /**
     * If the validation fails, the list of validation error messages are avaliable.
     * @return array List of validation errors.
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Returns the core Laravel validation instance (incase later we wish to extend this validation object.)
     * @return Illuminate\Validation\Validator Instance of the current base validation object.
     */
    public function validatorInstance()
    {
        return $this->validation;
    }

    /**
     * Returns an Exception if validation fails instead of boolean.
     * @throws ValidationException
     */
    public function checkValidation()
    {
        if ($this->fails()) {
            throw new ValidationException($this->validation->errors());
        }
    }

}
