<?php

namespace MaksimM\ConditionalDebugBar\DebugModeBootValidators;

use MaksimM\ConditionalDebugBar\Interfaces\DebugModeChecker;

class TestingDebugBarBootValidator implements DebugModeChecker
{
    public function isInDebugMode()
    {
        return app()->environment() == 'testing';
    }
}
