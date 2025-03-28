<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 - present
 * LaravelGoogleRecaptcha - ReCaptchaV3Test.php
 * author: Roberto Belotti - roby.belotti@gmail.com
 * web : robertobelotti.com, github.com/biscolab
 * Initial version created on: 22/1/2019
 * MIT license: https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 */

namespace Biscolab\ReCaptcha\Tests;

use Biscolab\ReCaptcha\Controllers\ReCaptchaController;
use Biscolab\ReCaptcha\Facades\ReCaptcha;
use Biscolab\ReCaptcha\ReCaptchaBuilderV3;
use Illuminate\Support\Facades\App;

class ReCaptchaV3Test extends TestCase
{
    protected $recaptcha_v3;

    /**
     * @test
     */
    public function testGetApiVersion(): void
    {
        $this->assertEquals($this->recaptcha_v3->getVersion(), 'v3');
    }

    /**
     * @test
     */
    public function testAction(): void
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi([
            'action' => 'someAction',
        ]);
        $this->assertMatchesRegularExpression('/someAction/', $r);
    }

    /**
     * @test
     */
    public function testFetchCallbackFunction(): void
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi([
            'callback_then' => 'functionCallbackThen',
        ]);
        $this->assertMatchesRegularExpression('/functionCallbackThen\(response\)/', $r);
    }

    /**
     * @test
     */
    public function testcCatchCallbackFunction(): void
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi([
            'callback_catch' => 'functionCallbackCatch',
        ]);
        $this->assertMatchesRegularExpression('/functionCallbackCatch\(err\)/', $r);
    }

    /**
     * @test
     */
    public function testCustomValidationFunction(): void
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi([
            'custom_validation' => 'functionCustomValidation',
        ]);
        $this->assertMatchesRegularExpression('/functionCustomValidation\(token\)/', $r);
    }

    /**
     * @test
     */
    public function testCustomValidationUrl(): void
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi();
        $this->assertMatchesRegularExpression('/http:\/\/localhost\/my-custom-url\?my-custom-token-name/', $r);
    }

    /**
     * @test
     */
    public function testValidateController(): void
    {
        $controller = App::make(ReCaptchaController::class);
        $return = $controller->validateV3();

        $this->assertArrayHasKey("success", $return);
        $this->assertArrayHasKey("error-codes", $return);
    }

    /**
     * @test
     */
    public function testCurlTimeoutIsSet(): void
    {
        $this->assertEquals($this->recaptcha_v3->getCurlTimeout(), 3);
    }

    /**
     * @test
     */
    public function testHtmlScriptTagJsApiCalledByFacade(): void
    {
        ReCaptcha::shouldReceive('htmlScriptTagJsApi')
            ->once()
            ->with([]);

        htmlScriptTagJsApi([]);
    }

    /**
     * @test
     */
    public function testValidationUrlShouldBeMyCustomUrl(): void
    {
        $this->assertEquals($this->recaptcha_v3->getValidationUrl(), "http://localhost/my-custom-url");
    }

    /**
     * @test
     */
    public function testTokenParamNameShouldBeMyCustomTokenParamName(): void
    {
        $this->assertEquals($this->recaptcha_v3->getTokenParameterName(), "my-custom-token-name");
    }

    /**
     * @test
     */
    public function testValidationUrlShouldBeMyCustomValidationUrl(): void
    {
        $this->assertEquals(
            $this->recaptcha_v3->getValidationUrlWithToken(),
            "http://localhost/my-custom-url?my-custom-token-name"
        );
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('recaptcha.version', 'v3');
        $app['config']->set('recaptcha.curl_timeout', 3);

        $app['config']->set('recaptcha.default_validation_route', "my-custom-url");
        $app['config']->set('recaptcha.default_token_parameter_name', "my-custom-token-name");
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->recaptcha_v3 = new ReCaptchaBuilderV3('api_site_key', 'api_secret_key');
    }
}
