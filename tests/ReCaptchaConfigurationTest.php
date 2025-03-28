<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 - present
 * LaravelGoogleRecaptcha - ReCaptchaConfigurationTest.php
 * author: Roberto Belotti - roby.belotti@gmail.com
 * web : robertobelotti.com, github.com/biscolab
 * Initial version created on: 13/2/2019
 * MIT license: https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 */

namespace Biscolab\ReCaptcha\Tests;

use Biscolab\ReCaptcha\ReCaptchaBuilder;

class ReCaptchaConfigurationTest extends TestCase
{
    /**
     * @var ReCaptchaBuilder
     */
    protected $recaptcha;

    /**
     * @test
     */
    public function testGetApiSiteKey(): void
    {
        $this->assertEquals("api_site_key", $this->recaptcha->getApiSiteKey());
    }

    /**
     * @test
     */
    public function testGetApiSecretKey(): void
    {
        $this->assertEquals("api_secret_key", $this->recaptcha->getApiSecretKey());
    }

    /**
     * @test
     */
    public function testSkipIpWhiteListIsArray(): void
    {
        $ip_whitelist = $this->recaptcha->getIpWhitelist();
        $this->assertTrue(is_array($ip_whitelist));
        $this->assertCount(2, $ip_whitelist);

        $this->assertEquals('10.0.0.1', $ip_whitelist[0]);
        $this->assertEquals('10.0.0.2', $ip_whitelist[1]);
    }

    /**
     * @test
     */
    public function testCurlTimeoutIsSet(): void
    {
        $this->assertEquals(3, $this->recaptcha->getCurlTimeout());
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('recaptcha.api_site_key', 'api_site_key');
        $app['config']->set('recaptcha.api_secret_key', 'api_secret_key');
        $app['config']->set('recaptcha.skip_ip', '10.0.0.1,10.0.0.2');
        $app['config']->set('recaptcha.curl_timeout', 3);
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->recaptcha = recaptcha();
    }
}
