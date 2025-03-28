<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 - present
 * LaravelGoogleRecaptcha - ReCaptchaBuilderV3.php
 * author: Roberto Belotti - roby.belotti@gmail.com
 * web : robertobelotti.com, github.com/biscolab
 * Initial version created on: 22/1/2019
 * MIT license: https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 */

namespace Biscolab\ReCaptcha;

use Illuminate\Support\Arr;

class ReCaptchaBuilderV3 extends ReCaptchaBuilder
{
    public function __construct(string $api_site_key, string $api_secret_key)
    {
        parent::__construct($api_site_key, $api_secret_key, 'v3');
    }

    public function getTokenParameterName(): string
    {
        return config('recaptcha.default_token_parameter_name', 'token');
    }

    public function getValidationUrl(): string
    {
        return url(config('recaptcha.default_validation_route', 'biscolab-recaptcha/validate'));
    }

    public function getValidationUrlWithToken(): string
    {
        return implode("?", [$this->getValidationUrl(), $this->getTokenParameterName()]);
    }

    /**
     * Write script HTML tag in you HTML code
     * Insert before </head> tag
     */
    public function htmlScriptTagJsApi(?array $configuration = []): string
    {
        if ($this->skip_by_ip) {
            return '';
        }

        $html = '<script src="' . $this->api_js_url . sprintf('?render=%s"></script>', $this->api_site_key);

        $action = Arr::get($configuration, 'action', 'homepage');

        $js_custom_validation = Arr::get($configuration, 'custom_validation', '');

        // Check if set custom_validation. That function will override default fetch validation function
        if ($js_custom_validation) {
            $validate_function = ($js_custom_validation) ? $js_custom_validation . '(token);' : '';
        } else {

            $js_then_callback = Arr::get($configuration, 'callback_then', '');
            $js_callback_catch = Arr::get($configuration, 'callback_catch', '');

            $js_then_callback = ($js_then_callback) ? $js_then_callback . '(response)' : '';
            $js_callback_catch = ($js_callback_catch) ? $js_callback_catch . '(err)' : '';

            $validate_function = "
                fetch('" . $this->getValidationUrlWithToken() . "=' + token, {
                    headers: {
                        \"X-Requested-With\": \"XMLHttpRequest\",
                        \"X-CSRF-TOKEN\": csrfToken.content
                    }
                })
                .then(function(response) {
                   	{$js_then_callback}
                })
                .catch(function(err) {
                    {$js_callback_catch}
                });";
        }

        return $html . "<script>
                    var csrfToken = document.head.querySelector('meta[name=\"csrf-token\"]');
                  grecaptcha.ready(function() {
                      grecaptcha.execute('{$this->api_site_key}', {action: '{$action}'}).then(function(token) {
                        {$validate_function}
                      });
                  });
		     </script>";
    }
}
