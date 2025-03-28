<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 - present
 * LaravelGoogleRecaptcha - ReCaptchaHelpersInvisibleTest.phpp
 * author: Roberto Belotti - roby.belotti@gmail.com
 * web : robertobelotti.com, github.com/biscolab
 * Initial version created on: 8/8/2019
 * MIT license: https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 */

namespace Biscolab\ReCaptcha\Tests;

use Biscolab\ReCaptcha\Facades\ReCaptcha;

class ReCaptchaHelpersInvisibleTest extends TestCase
{
    /**
     * @test
     */
    public function testHtmlScriptTagJsApiCalledByFacade(): void
    {
        ReCaptcha::shouldReceive('htmlScriptTagJsApi')
            ->once()
            ->with([
                "form_id" => "test-form",
            ]);

        htmlScriptTagJsApi([
            "form_id" => "test-form",
        ]);
    }

    /**
     * @test
     */
    public function testHtmlFormButtonCalledByFacade(): void
    {
        ReCaptcha::shouldReceive('htmlFormButton')
            ->once()
            ->with("Inner text", [
                'id' => 'button_id',
            ]);

        htmlFormButton("Inner text", [
            'id' => 'button_id',
        ]);
    }

    /**
     * @test
     */
    public function testGetFormIdCalledByFacade(): void
    {
        ReCaptcha::shouldReceive('getFormId')
            ->once();

        getFormId();
    }

    public function testHtmlFormButtonConfiguration(): void
    {
        $button_html = htmlFormButton("Inner text", [
            'id' => 'button_id',
            'class' => 'button_class',
            'data-sitekey' => 'custom-data-sitekey',
            'data-callback' => 'myCallback',
        ]);

        $this->assertEquals(
            '<button class="button_class g-recaptcha" data-callback="myCallback" data-sitekey="api_site_key" id="button_id">Inner text</button>',
            $button_html
        );
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function testHtmlFormSnippetCalledByFacade(): void
    {
        $this->expectException('\TypeError');
        ReCaptcha::shouldReceive('htmlFormSnippet')
            ->once();

        htmlFormSnippet();
    }

    public function testGetFormIdReturnDefaultFormIdValue(): void
    {
        $this->assertEquals('biscolab-recaptcha-invisible-form', getFormId());
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('recaptcha.api_site_key', 'api_site_key');
        $app['config']->set('recaptcha.api_site_key', 'api_site_key');
        $app['config']->set('recaptcha.version', 'invisible');
    }
}
