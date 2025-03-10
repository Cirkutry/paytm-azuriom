<?php

namespace Azuriom\Plugin\Paytm\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Plugin\Paytm\PaytmMethod;

class PaytmServiceProvider extends BasePluginServiceProvider
{
    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        $this->loadViews();
		
        $this->loadTranslations();

        payment_manager()->registerPaymentMethod('paytm', PaytmMethod::class);
    }
}
