<?php

namespace MaksimM\ConditionalDebugBar\Tests;

use Barryvdh\Debugbar\ServiceProvider;
use MaksimM\ConditionalDebugBar\ConditionalDebugBarServiceProvider;
use MaksimM\ConditionalDebugBar\DebugModeBootValidators\TestingDebugBarBootValidator;
use MaksimM\ConditionalDebugBar\Http\Middleware\OptionalDebugBar;
use Orchestra\Testbench\BrowserKit\TestCase;

class BootedDebugBarRouteTest extends TestCase
{
    private $blank_page = '<html xmlns="http://www.w3.org/1999/html"><head></head><body</body</html>';

    /** @test */
    public function validateCleanPage()
    {
        $crawler = $this->call('GET', 'sample-page');
        $this->assertEquals($this->blank_page, $crawler->getContent());
    }

    /** @test */
    public function validateAssetsAccessMiddleware()
    {
        $this->validatePageWithMiddleware();
        $crawler = $this->call('GET', route('debugbar.assets.js'));
        $this->assertEquals(200, $crawler->getStatusCode());
        $crawler = $this->call('GET', route('debugbar.assets.css'));
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    /** @test */
    public function validatePageWithMiddleware()
    {
        $crawler = $this->call('GET', 'page-with-middleware');
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
        $app['router']->get('sample-page', ['uses' => function () {
            return $this->blank_page;
        }]);
        $app['router']->get('page-with-middleware', ['uses' => function () {
            return $this->blank_page;
        }])->middleware(OptionalDebugBar::class);
    }
}
