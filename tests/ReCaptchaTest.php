<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 - present
 * LaravelGoogleRecaptcha - ReCaptchaTest.php
 * author: Roberto Belotti - roby.belotti@gmail.com
 * web : robertobelotti.com, github.com/biscolab
 * Initial version created on: 12/9/2018
 * MIT license: https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 */

namespace Biscolab\ReCaptcha\Tests;

use Biscolab\ReCaptcha\Facades\ReCaptcha;
use Biscolab\ReCaptcha\ReCaptchaBuilder;
use Biscolab\ReCaptcha\ReCaptchaBuilderInvisible;
use Biscolab\ReCaptcha\ReCaptchaBuilderV2;
use Biscolab\ReCaptcha\ReCaptchaBuilderV3;

class ReCaptchaTest extends TestCase
{
    /**
     * @var ReCaptchaBuilderInvisible
     */
    protected $recaptcha_invisible;

    /**
     * @var ReCaptchaBuilderV2
     */
    protected $recaptcha_v2;

    /**
     * @var ReCaptchaBuilderV3
     */
    protected $recaptcha_v3;

    /**
     * @tests
     */
    public function testHtmlScriptTagJsApiGetHtmlScriptTag(): void
    {
        $r = ReCaptcha::htmlScriptTagJsApi();
        $this->assertEquals('<script src="https://www.google.com/recaptcha/api.js" async defer></script>', $r);
    }

    /**
     * @test
     */
    public function testReCaptchaInvisibleHtmlFormButtonDefault(): void
    {
        $recaptcha = $this->recaptcha_invisible;
        $html_button = $recaptcha->htmlFormButton();
        $this->assertEquals(
            '<button class="g-recaptcha" data-callback="biscolabLaravelReCaptcha" data-sitekey="api_site_key">Submit</button>',
            $html_button
        );
    }

    /**
     * @test
     */
    public function testReCaptchaInvisibleHtmlFormButtonCustom(): void
    {
        $recaptcha = $this->recaptcha_invisible;
        $html_button = $recaptcha->htmlFormButton('Custom Text');
        $this->assertEquals(
            '<button class="g-recaptcha" data-callback="biscolabLaravelReCaptcha" data-sitekey="api_site_key">Custom Text</button>',
            $html_button
        );
    }

    /**
     * @test
     */
    public function testReCaptchaV2HtmlFormSnippet(): void
    {
        $recaptcha = $this->recaptcha_v2;
        $html_snippet = $recaptcha->htmlFormSnippet();
        $this->assertEquals(
            '<div class="g-recaptcha" data-sitekey="api_site_key" data-size="normal" data-theme="light" id="recaptcha-element"></div>',
            $html_snippet
        );
    }

    /**
     * @test
     * @expectedException     \Error
     */
    public function testReCaptchaInvisibleHtmlFormSnippetShouldThrowError(): void
    {
        $this->expectException('\Error');
        $this->recaptcha_invisible->htmlFormSnippet();
    }

    /**
     * @test
     */
    public function testSkipByIpAndReturnArrayReturnsDefaultArray(): void
    {
        $mock = $this->getMockBuilder(ReCaptchaBuilder::class)
            ->setConstructorArgs(["api_site_key", "api_secret_key"])
            ->onlyMethods(['returnArray'])
            ->getMock();

        $mock->method('returnArray')
            ->willReturn(true);

        $this->setSkipByIp($this->recaptcha_v3, true);

        $validate = $this->recaptcha_v3->validate("");

        $this->assertEquals([
            "skip_by_ip" => true,
            "score" => 0.9,
            "success" => true,
        ], $validate);
    }

    /**
     * @test
     */
    public function testSkipByIpReturnsValidResponse(): void
    {
        $this->setSkipByIp($this->recaptcha_invisible, true);
        $validate = $this->recaptcha_invisible->validate("");

        $this->assertTrue($validate);
    }

    /**
     * @test
     */
    public function testDefaultCurlTimeout(): void
    {
        $this->assertEquals($this->recaptcha_invisible->getCurlTimeout(), ReCaptchaBuilder::DEFAULT_CURL_TIMEOUT);
        $this->assertEquals($this->recaptcha_v2->getCurlTimeout(), ReCaptchaBuilder::DEFAULT_CURL_TIMEOUT);
        $this->assertEquals($this->recaptcha_v3->getCurlTimeout(), ReCaptchaBuilder::DEFAULT_CURL_TIMEOUT);
    }

    /**
     * @test
     * @expectedException     \Error
     */
    public function testReCaptchaV2htmlFormButtonShouldThrowError(): void
    {
        $this->expectException('\Error');
        $this->recaptcha_v2->htmlFormButton();
    }

    /**
     * @test
     */
    public function testRecaptchaFieldNameHelperReturnsReCaptchaBuilderDefaultFieldName(): void
    {
        $this->assertEquals(ReCaptchaBuilder::DEFAULT_RECAPTCHA_FIELD_NAME, recaptchaFieldName());
    }

    /**
     * @test
     */
    public function testRecaptchaRuleNameHelperReturnsReCaptchaBuilderDefaultRuleName(): void
    {
        $this->assertEquals(ReCaptchaBuilder::DEFAULT_RECAPTCHA_RULE_NAME, recaptchaRuleName());
    }

    /**
     * @test
     */
    public function testDefaultRecaptchaApiDomainIsGoogleDotCom(): void
    {
        $this->assertEquals("www.google.com", $this->recaptcha_v2->getApiDomain());
        $this->assertEquals("www.google.com", $this->recaptcha_invisible->getApiDomain());
        $this->assertEquals("www.google.com", $this->recaptcha_v3->getApiDomain());
    }

    protected function setSkipByIp(ReCaptchaBuilder $builder, bool $value)
    {
        $reflection = new \ReflectionClass($builder);
        $reflection_property = $reflection->getProperty('skip_by_ip');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($builder, $value);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->recaptcha_invisible = new ReCaptchaBuilderInvisible('api_site_key', 'api_secret_key');
        $this->recaptcha_v2 = new ReCaptchaBuilderV2('api_site_key', 'api_secret_key');
        $this->recaptcha_v3 = new ReCaptchaBuilderV3('api_site_key', 'api_secret_key');
    }
}
