<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Poynt\Interceptors;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Models\User;
use GoDaddy\WordPress\MWC\Common\Platforms\Exceptions\PlatformRepositoryException;
use GoDaddy\WordPress\MWC\Common\Platforms\PlatformRepositoryFactory;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\SentryRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;
use GoDaddy\WordPress\MWC\Core\Payments\Events\ProviderAccountAssociatedEvent;
use GoDaddy\WordPress\MWC\Core\Payments\Events\ProviderAccountNotAssociatedEvent;
use GoDaddy\WordPress\MWC\Core\Payments\Exceptions\MissingParameterException;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Onboarding;
use GoDaddy\WordPress\MWC\Core\Payments\Providers\MWC\Gateways\OnboardingAccountGateway;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\Events\Producers\OnboardingEventsProducer;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\GoDaddyPaymentsGateway;

/**
 * An interceptor to auto-connect GoDaddy Payments when the admin is loaded for the first time.
 */
class AutoConnectInterceptor extends AbstractInterceptor
{
    /** @var string option name for the attempted flag */
    protected static $attemptedFlagOptionName = 'mwc_payments_poynt_auto_connect_attempted';

    /** @var string option name for the connected flag */
    protected static $connectedFlagOptionName = 'mwc_payments_poynt_auto_connected';

    /**
     * Determines whether the interceptor should be loaded.
     *
     * Current conditions:
     * - WooCommerce is active
     * - The feature flag is not manually disabled
     * - Auto-connect has not been attempted yet
     * - The site was created on or before 2022-10-10
     *
     * @throws PlatformRepositoryException
     * @return bool
     */
    public static function shouldLoad() : bool
    {
        $shouldLoad = WooCommerceRepository::isWooCommerceActive() &&
            true === Configuration::get('features.gdp_by_default.enabled') && ! static::wasAttempted();

        $platformName = PlatformRepositoryFactory::getNewInstance()->getPlatformRepository()->getPlatformName();

        return ($platformName === 'mwp') ?
            (
                $shouldLoad &&
                Configuration::get('godaddy.site.token') &&
                // don't allow for sites created before 2022-10-10
                ((int) Configuration::get('godaddy.site.created')) >= 1665360000
            ) : $shouldLoad;
    }

    /**
     * Adds the action & filter hooks.
     *
     * @throws Exception
     */
    public function addHooks() : void
    {
        Register::action()
            ->setGroup('admin_init')
            ->setHandler([$this, 'attemptAutoConnect'])
            ->execute();
    }

    /**
     * Attempts to auto-connect the site to GoDaddy Payments.
     *
     * @internal
     */
    public function attemptAutoConnect() : void
    {
        if (! User::getCurrent()) {
            return;
        }

        static::setAttempted(true);

        try {
            // only proceed for sites that are eligible for GDP and haven't already attempted connection
            if (! GoDaddyPaymentsGateway::isActive() || Poynt::getServiceId()) {
                return;
            }

            Onboarding::generateIds();

            $account = OnboardingAccountGateway::getNewInstance()->findOrCreate(Configuration::get('godaddy.account.uid'), Onboarding::getWebhookSecret());

            $this->validateAccountData($account);

            Poynt::setAppId(ArrayHelper::get($account, 'cloudAppId'));
            Poynt::setApplicationId(ArrayHelper::get($account, 'applicationId'));
            Poynt::setBusinessId(ArrayHelper::get($account, 'businessId'));
            Poynt::setPrivateKey(ArrayHelper::get($account, 'privateKey'));
            Poynt::setPublicKey(ArrayHelper::get($account, 'publicKey'));
            Poynt::setServiceId(ArrayHelper::get($account, 'serviceId'));
            Poynt::setSiteStoreId(ArrayHelper::get($account, 'storeId'));

            static::setConnected(true);

            Events::broadcast(new ProviderAccountAssociatedEvent('godaddy-payments'));

            $this->updateAccount();
        } catch (Exception $exception) {
            Events::broadcast(new ProviderAccountNotAssociatedEvent('godaddy-payments'));

            if (SentryRepository::loadSDK()) {
                \Sentry\captureException(new SentryException($exception->getMessage(), $exception));
            }
        }
    }

    /**
     * Validates the Account Data array for required keys.
     *
     * @param array<string, string> $account
     *
     * @throws MissingParameterException
     */
    protected function validateAccountData(array $account) : void
    {
        foreach ([
            'cloudAppId',
            'applicationId',
            'businessId',
            'privateKey',
            'publicKey',
            'serviceId',
            'storeId',
        ] as $key) {
            if (empty(ArrayHelper::get($account, $key))) {
                throw new MissingParameterException("Could not validate account data, missing {$key}");
            }
        }
    }

    /**
     * Updates account by calling getNewInstance on OnboardingEventsProducer.
     *
     * @return void
     * @throws Exception
     */
    protected function updateAccount() : void
    {
        OnboardingEventsProducer::getNewInstance()->updateAccount();
    }

    /**
     * Determines if the auto-connect was already attempted.
     *
     * @return bool
     */
    public static function wasAttempted() : bool
    {
        return 'yes' === get_option(static::$attemptedFlagOptionName);
    }

    /**
     * Sets whether the auto-connect has been attempted.
     *
     * @param bool $wasAttempted
     */
    public static function setAttempted(bool $wasAttempted) : void
    {
        update_option(static::$attemptedFlagOptionName, wc_bool_to_string($wasAttempted));
    }

    /**
     * Determines if the auto-connect was already connected.
     *
     * @return bool
     */
    public static function wasConnected() : bool
    {
        return 'yes' === get_option(static::$connectedFlagOptionName);
    }

    /**
     * Sets whether the auto-connect has been connected.
     *
     * @param bool $wasConnected
     */
    public static function setConnected(bool $wasConnected) : void
    {
        update_option(static::$connectedFlagOptionName, wc_bool_to_string($wasConnected));
    }
}
