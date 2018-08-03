<?php

namespace MaksimM\ConditionalDebugBar\Interfaces;

interface DebugModeChecker
{
    /**
     * @return bool
     */
    public function isInDebugMode();
}
