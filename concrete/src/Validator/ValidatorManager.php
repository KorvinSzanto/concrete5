<?php
namespace Concrete\Core\Validator;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;

class ValidatorManager implements ValidatorManagerInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @var ValidatorInterface[] */
    protected $validators = array();

    protected $inflator;

    /**
     * Get the validator requirements in the form of an array keyed by it's respective error code.
     *
     * Example:
     *    [ self::E_TOO_SHORT => 'Must be at least 10 characters' ]
     *
     * @return string[]
     */
    public function getRequirementStrings()
    {
        $strings = array();
        foreach ($this->getValidators() as $validator) {
            $validator_strings = $validator->getRequirementStrings();
            $strings = array_merge($strings, $validator_strings);
        }

        return $strings;
    }

    /**
     * Get a list of all validators.
     *
     * @return ValidatorInterface[]|array|\Iterator Iterator of validators keyed by their handles
     */
    public function getValidators()
    {
        $inflate = $this->getInflator();
        foreach ($this->validators as $key => $validator) {
            yield $key => $inflate($validator);
        }
    }

    /**
     * Does a validator with this handle exist.
     *
     * @param string $handle
     *
     * @return bool
     */
    public function hasValidator($handle)
    {
        return isset($this->validators[$handle]);
    }

    /**
     * Get the inflator to use
     * @return callable
     */
    protected function getInflator()
    {
        return $this->inflator ?: [$this, 'inflate'];
    }

    protected function setInflator(callable $inflator)
    {
        $this->inflator = $inflator;
        return $this;
    }

    /**
     * Return a full validator object
     *
     * @param $validator
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function inflate($validator)
    {
        if (is_string($validator)) {
            $validator = $this->app->make($validator);
        } elseif (is_callable($validator)) {
            $validator = $this->app->call($validator);
        }

        if (!$validator instanceof ValidatorInterface) {
            throw new \InvalidArgumentException('Invalid Validator Binding.');
        }

        return $validator;
    }

    /**
     * Add a validator to the stack.
     * Validators are unique by handle, so adding a validator with the same handle as a validator in the stack
     * replaces the old validator with the new one.
     *
     * @param string $handle
     * @param \Concrete\Core\Validator\ValidatorInterface $validator
     */
    public function setValidator($handle, ValidatorInterface $validator = null)
    {
        $this->validators[$handle] = $validator;
    }

    /**
     * Is this mixed value valid based on the added validators.
     *
     * @param mixed             $mixed Can be any value
     * @param \ArrayAccess|null $error The error object that will contain the error strings
     *
     * @return bool
     *
     * @throws \InvalidArgumentException Invalid mixed value type passed.
     */
    public function isValid($mixed, \ArrayAccess $error = null)
    {
        $valid = true;
        foreach ($this->getValidators() as $validator) {
            if (!$validator->isValid($mixed, $error)) {
                $valid = false;
            }
        }

        return $valid;
    }
}
