<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 - present
 * LaravelGoogleRecaptcha - ReCaptchaServiceProvider.php
 * author: Roberto Belotti - roby.belotti@gmail.com
 * web : robertobelotti.com, github.com/biscolab
 * Initial version created on: 12/9/2018
 * MIT license: https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 */

namespace Biscolab\ReCaptcha;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ReCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot(): void
    {
        $this->addValidationRule();
        $this->registerRoutes();
        $this->publishes([
            __DIR__ . '/../config/recaptcha.php' => config_path('recaptcha.php'),
        ], 'config');
    }

    /**
     * Extends Validator to include a recaptcha type
     */
    public function addValidationRule(): void
    {
        $message = null;

        if (! config('recaptcha.empty_message')) {
            $message = trans(config('recaptcha.error_message_key'));
        }

        Validator::extendImplicit(recaptchaRuleName(), function ($attribute, $value) {

            return app('recaptcha')->validate($value);
        }, $message);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/recaptcha.php', 'recaptcha');

        $this->registerReCaptchaBuilder();
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['recaptcha'];
    }

    /**
     * @since v3.4.1
     */
    protected function registerRoutes(): ReCaptchaServiceProvider
    {
        Route::get(
            config('recaptcha.default_validation_route', 'biscolab-recaptcha/validate'),
            ['uses' => 'Biscolab\ReCaptcha\Controllers\ReCaptchaController@validateV3']
        )->middleware('web');

        return $this;
    }

    /**
     * Register the HTML builder instance.
     */
    protected function registerReCaptchaBuilder()
    {
        $this->app->singleton('recaptcha', function ($app) {

            $recaptcha_class = '';

            switch (config('recaptcha.version')) {
                case 'v3':
                    $recaptcha_class = ReCaptchaBuilderV3::class;
                    break;
                case 'v2':
                    $recaptcha_class = ReCaptchaBuilderV2::class;
                    break;
                case 'invisible':
                    $recaptcha_class = ReCaptchaBuilderInvisible::class;
                    break;
            }

            return new $recaptcha_class(config('recaptcha.api_site_key'), config('recaptcha.api_secret_key'));
        });
    }
}
