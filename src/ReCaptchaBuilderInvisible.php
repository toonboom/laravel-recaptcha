<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 - present
 * LaravelGoogleRecaptcha - ReCaptchaBuilderInvisible.php
 * author: Roberto Belotti - roby.belotti@gmail.com
 * web : robertobelotti.com, github.com/biscolab
 * Initial version created on: 12/9/2018
 * MIT license: https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 */

namespace Biscolab\ReCaptcha;

use Biscolab\ReCaptcha\Exceptions\InvalidConfigurationException;
use Illuminate\Support\Arr;

class ReCaptchaBuilderInvisible extends ReCaptchaBuilder
{
    protected ?string $form_id = null;

    public function __construct(string $api_site_key, string $api_secret_key)
    {
        parent::__construct($api_site_key, $api_secret_key, 'invisible');
    }

    /**
     * Write HTML <button> tag in your HTML code
     * Insert before </form> tag
     */
    public function htmlFormButton(string $button_label = 'Submit', ?array $properties = []): string
    {
        $tag_properties = '';

        $properties = array_merge(
            ['data-callback' => 'biscolabLaravelReCaptcha'],
            $properties,
            ['data-sitekey' => $this->api_site_key]
        );

        if (empty($properties['class'])) {
            $properties['class'] = 'g-recaptcha';
        } else {
            $properties['class'] .= ' g-recaptcha';
        }

        ksort($properties);

        if ($properties) {
            //            $tag_properties = str_replace("=", '="',
            //                    http_build_query($properties, null, '" ', PHP_QUERY_RFC3986)) . '"';
            $temp_properties = [];
            foreach ($properties as $k => $v) {
                $temp_properties[] = $k . '="' . $v . '"';
            }

            $tag_properties = implode(" ", $temp_properties);
        }

        return ($this->version === 'invisible') ? '<button ' . $tag_properties . '>' . $button_label . '</button>' : '';
    }

    /**
     * Write script HTML tag in you HTML code
     * Insert before </head> tag
     */
    public function htmlScriptTagJsApi(?array $configuration = []): string
    {
        $html = parent::htmlScriptTagJsApi();

        $form_id = Arr::get($configuration, 'form_id');
        if (!$form_id) {
            $form_id = $this->getFormId();
        } else {
            $this->form_id = $form_id;
        }

        return $html . ('<script>
		       function biscolabLaravelReCaptcha(token) {
		         document.getElementById("' . $form_id . '").submit();
		       }
		     </script>');
    }

    public function getFormId(): string
    {
        $form_id = $this->form_id ? $this->form_id : config('recaptcha.default_form_id');

        if (!$form_id) {
            throw new InvalidConfigurationException("formId required");
        }

        return $form_id;
    }
}
