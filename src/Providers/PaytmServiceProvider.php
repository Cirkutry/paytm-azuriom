<?php

namespace Azuriom\Plugin\Paytm\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Plugin\Paytm\PaytmMethod;

class PaytmServiceProvider extends BasePluginServiceProvider
{
    /**
     * Register any plugin services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any plugin services.
     *
     * @return void
     */
    public function boot()
    {
        if (! plugins()->isEnabled('shop')) {
            logger()->warning('Paytm requires the Shop plugin to work!');
            return;
        }

        $this->loadViews();
        $this->loadTranslations();
        
        payment_manager()->registerPaymentMethod('paytm', PaytmMethod::class);
    }
}
