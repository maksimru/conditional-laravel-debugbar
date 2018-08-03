<?php

namespace MaksimM\ConditionalDebugBar\DebugModeBootValidators;

use MaksimM\ConditionalDebugBar\Interfaces\DebugModeChecker;

class SampleDebugBarBootValidator implements DebugModeChecker
{

    public function isInDebugMode()
    {
        return (env('APP_DEBUG') || app()->environment() == 'staging' || app()->environment() == 'local');
    }

}