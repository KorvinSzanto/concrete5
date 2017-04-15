<?php

namespace Concrete\Core\Validator;

use Psr\Log\LogLevel;

class ErrorLevel extends LogLevel
{

    const RECOMMENDATION = self::INFO;
    const MUSTFIX = self::CRITICAL;

}
