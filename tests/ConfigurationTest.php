<?php

namespace MaksimM\ConditionalDebugBar\Tests;

use MaksimM\ConditionalDebugBar\ConditionalDebugBarServiceProvider;
use MaksimM\ConditionalDebugBar\DebugModeBootValidators\SampleDebugBarBootValidator;
use Orchestra\Testbench\TestCase;

class ConfigurationTest extends TestCase
{

    /** @test */
    public function validate_config_file()
    {
        $this->assertArrayHasKey('conditional-debugbar', $this->app['config']);
        $this->assertEquals(SampleDebugBarBootValidator::class, $this->app['config']['conditional-debugbar']['debugbar-boot-validator']);
    }

    protected function getPackageProviders($app)
    {
        return [ConditionalDebugBarServiceProvider::class];
    }

}