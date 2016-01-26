<?php

namespace P3in\Modules\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use P3in\Models\Website;

Class WebsitesServiceProvider extends ServiceProvider {

    public function boot()
    {
        // Register Middleware
        $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');
        $kernel->prependMiddleware('P3in\Modules\Middleware\ValidateAndSetWebsite');

        // Register Website Validation
        Validator::extend('site_connection', function($attribute, $value, $parameters, $validator) {

            return Website::testConnection($value, true);

        });

        Validator::replacer('site_connection', function($message, $attribute, $rule, $parameters) {

            return 'Unable to establish a connection to the server with the provided information.';

        });

    }

    public function register()
    {
        // Our system expects there to serve websites via revers proxy.
        // So we need to set the trusted proxies here.
        $request = $this->app['request'];
        $proxies = $this->app['config']->get('app.trusted_proxies');

        if (empty($proxies) || $proxies === '*') {
            $proxies = [$request->getClientIp()];
        }

        $request->setTrustedProxies($proxies);

        $this->app->register(\Collective\Remote\RemoteServiceProvider::class);
        $this->app->register(\Langemike\Laravel5Less\LessServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('SSH', \Collective\Remote\RemoteFacade::class);
        $loader->alias('Less', \Langemike\Laravel5Less\LessFacade::class);

    }

    public function provides()
    {
        //
    }
}