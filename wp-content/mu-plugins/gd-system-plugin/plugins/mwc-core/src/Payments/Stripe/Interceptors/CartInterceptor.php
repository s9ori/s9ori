<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Stripe\Interceptors;

use Exception;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\CartRepository;
use GoDaddy\WordPress\MWC\Core\Features\Stripe\Stripe as StripeFeature;
use GoDaddy\WordPress\MWC\Core\Payments\Stripe;
use GoDaddy\WordPress\MWC\Core\Payments\Stripe\Adapters\CartPaymentIntentAdapter;
use GoDaddy\WordPress\MWC\Core\Payments\Stripe\DataStores\WooCommerce\SessionPaymentIntentDataStore;
use GoDaddy\WordPress\MWC\Core\Payments\Stripe\Gateways\PaymentIntentGateway;

class CartInterceptor extends AbstractInterceptor
{
    /**
     * {@inheritDoc}
     *
     * @return void
     * @throws Exception
     */
    public function addHooks()
    {
        Register::action()
            ->setGroup('woocommerce_cart_updated')
            ->setHandler([$this, 'updateCart'])
            ->execute();
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public static function shouldLoad() : bool
    {
        return StripeFeature::shouldLoad() && Stripe::isConnected();
    }

    /**
     * Update Cart.
     *
     * @return void
     */
    public function updateCart()
    {
        try {
            $cartPaymentIntentAdapter = CartPaymentIntentAdapter::getNewInstance(CartRepository::getInstance());
            $paymentIntent = ($dataStore = SessionPaymentIntentDataStore::getNewInstance())->read();
            $gateway = PaymentIntentGateway::getNewInstance();

            if (! CartRepository::isEmpty() && $paymentIntent === null) {
                $dataStore->save($gateway->create($cartPaymentIntentAdapter->convertFromSource()));

                return;
            }

            if (! CartRepository::isEmpty()) {
                $gateway->update($cartPaymentIntentAdapter->convertFromSource($paymentIntent));

                return;
            }

            if (CartRepository::isEmpty() && $paymentIntent !== null) {
                $gateway->cancel($paymentIntent->getId());
                $dataStore->delete($paymentIntent);

                return;
            }
        } catch (Exception $exception) {
        }
    }
}
