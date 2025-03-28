<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 - present
 * LaravelGoogleRecaptcha - ReCaptcha.php
 * author: Roberto Belotti - roby.belotti@gmail.com
 * web : robertobelotti.com, github.com/biscolab
 * Initial version created on: 12/9/2018
 * MIT license: https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 */

namespace Biscolab\ReCaptcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string htmlScriptTagJsApi(?array $config = [])
 * @method static string htmlFormButton(?string $button_label = '', ?array $properties = [])
 * @method static string htmlFormSnippet()
 * @method static string getFormId()
 */
class ReCaptcha extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'recaptcha';
    }
}
