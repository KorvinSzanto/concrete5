<?php

namespace Concrete\Core\Validator\Site;

class SiteError
{

    /** @var string The error message */
    protected $message;

    /** @var string The severity as matched with ErrorLevel */
    protected $severity;

    /** @var \Concrete\Core\Validator\ValidatorInterface The validator object */
    protected $validator;

    /** @var int The original code that matches up with $validators E_* constants */
    protected $code;

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return \Validator\Site\SiteError;
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return SiteError
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     * @return SiteError
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
        return $this;
    }

    /**
     * @return \Concrete\Core\Attribute\ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param \Concrete\Core\Attribute\ValidatorInterface $validator
     * @return SiteError
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }


}
