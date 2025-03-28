<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 - present
 * LaravelGoogleRecaptcha - ReCaptchaController.php
 * author: Roberto Belotti - roby.belotti@gmail.com
 * web : robertobelotti.com, github.com/biscolab
 * Initial version created on: 4/2/2019
 * MIT license: https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 */

namespace Biscolab\ReCaptcha\Controllers;

use Illuminate\Routing\Controller;

class ReCaptchaController extends Controller
{
    public function validateV3(): bool|array
    {
        $token = request()
            ->input(config('recaptcha.default_token_parameter_name', 'token'), '');

        return recaptcha()->validate($token);
    }
}
