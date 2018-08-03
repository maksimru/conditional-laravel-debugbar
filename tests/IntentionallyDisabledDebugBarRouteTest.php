<?php

namespace MaksimM\ConditionalDebugBar\Tests;

use Barryvdh\Debugbar\ServiceProvider;
use MaksimM\ConditionalDebugBar\ConditionalDebugBarServiceProvider;
use MaksimM\ConditionalDebugBar\Http\Middleware\OptionalDebugBar;
use Orchestra\Testbench\BrowserKit\TestCase;

class IntentionallyDisabledDebugBarRouteTest extends TestCase
{
    private $blank_page = '<html xmlns="http://www.w3.org/1999/html"><head></head><body</body</html>';

    /** @test */
    public function validateAssets()
    {
        $this->validatePage();
        $crawler = $this->call('GET', route('debugbar.assets.js'));
        $this->assertEquals(404, $crawler->getStatusCode());
        $crawler = $this->call('GET', route('debugbar.assets.css'));
        $this->assertEquals(404, $crawler->getStatusCode());
    }

    /** @test */
    public function validatePage()
    {
        $crawler = $this->call('GET', 'page-with-middleware');
        $this->assertEquals($this->blank_page, $crawler->getContent());
        $this->assertNotContains('PhpDebugBar.DebugBar', $crawler->getContent());
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
        $app['config']->set('debugbar.enabled', true);
        $app['config']->set('app.debug', true);
        resolve(\Barryvdh\Debugbar\LaravelDebugbar::class)->enable();
        $app['router']->get('page-with-middleware', ['uses' => function () {
            return $this->blank_page;
        }])->middleware(OptionalDebugBar::class);
    }
}
