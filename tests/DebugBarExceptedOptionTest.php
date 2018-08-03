<?php

namespace Orchestra\Testbench\BrowserKit\Tests;

use Barryvdh\Debugbar\ServiceProvider;
use MaksimM\ConditionalDebugBar\ConditionalDebugBarServiceProvider;
use MaksimM\ConditionalDebugBar\DebugModeBootValidators\TestingDebugBarBootValidator;
use MaksimM\ConditionalDebugBar\Http\Middleware\OptionalDebugBar;
use Orchestra\Testbench\BrowserKit\TestCase;

class DebugBarExceptedOptionTest extends TestCase
{
    private $blank_page = '<html xmlns="http://www.w3.org/1999/html"><head></head><body</body</html>';

    /** @test */
    public function validateExcludedPage()
    {
        $crawler = $this->call('GET', 'excluded/page');
        $this->assertEquals($this->blank_page, $crawler->getContent());
        $this->assertNotContains('PhpDebugBar.DebugBar', $crawler->getContent());
    }

    /** @test */
    public function validateExcludedPageWithSlash()
    {
        $crawler = $this->call('GET', '/excluded/another-page');
        $this->assertEquals($this->blank_page, $crawler->getContent());
        $this->assertNotContains('PhpDebugBar.DebugBar', $crawler->getContent());
    }

    /** @test */
    public function validateIncludedPage()
    {
        $crawler = $this->call('GET', 'included/page');
        $this->assertNotEquals($this->blank_page, $crawler->getContent());
        $this->assertContains('PhpDebugBar.DebugBar', $crawler->getContent());
    }

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class, ConditionalDebugBarServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('conditional-debugbar.debugbar-boot-validator', TestingDebugBarBootValidator::class);
        $app['config']->set('debugbar.except', ['excluded/*']);
        $app['router']->get('excluded/page', ['uses' => function () {
            return $this->blank_page;
        }])->middleware(OptionalDebugBar::class);
        $app['router']->get('/excluded/another-page', ['uses' => function () {
            return $this->blank_page;
        }])->middleware(OptionalDebugBar::class);
        $app['router']->get('included/page', ['uses' => function () {
            return $this->blank_page;
        }])->middleware(OptionalDebugBar::class);
    }
}
