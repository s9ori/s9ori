<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\GoDaddyPayments\Frontend\Admin;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Models\User;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Core\Admin\Notices\Notices as AdminNotices;
use GoDaddy\WordPress\MWC\Core\Features\Worldpay\Worldpay;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Interceptors\AutoConnectInterceptor;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Models\Business;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Onboarding;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\Events\Producers\OnboardingEventsProducer;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\GoDaddyPayments\ApplePayGateway;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\GoDaddyPayments\GooglePayGateway;
use WC_Shipping_Zones;

/**
 * Class Notices.
 *
 * TODO: consider converting this class into a general notice handler (rendering and Ajax) for core notices {@wvega 2021-05-28}
 */
class Notices
{
    /** @var string action used to dismiss a notice */
    const ACTION_DISMISS_NOTICE = 'mwc_dismiss_notice';

    /** @var string path for the GoDaddy Payments plugin */
    const GODADDY_PAYMENTS_PLUGIN_PATH = 'godaddy-payments/godaddy-payments.php';

    /** @var array sections to display GoDaddy Payment Recommendation */
    const GDP_RECOMMENDATION_SECTIONS = ['local_pickup_plus', 'cod'];

    /** @var array sections to display GoDaddy Payment Recommendation for Sell in Person */
    const GDP_SIP_RECOMMENDATION_SECTIONS = ['local_pickup_plus', 'cod'];

    /** @var array tabs to display GoDaddy Payment Recommendation */
    const GDP_RECOMMENDATION_TABS = ['shipping'];

    /** @var array tabs to display GoDaddy Payment SIP Recommendation */
    const GDP_SIP_RECOMMENDATION_TABS = ['shipping'];

    /** @var string WC Local Pickup Shipping Method id */
    const WC_LOCAL_PICKUP = 'local_pickup';

    /** @var array registered admin notices */
    protected $notices = [];

    /**
     * Notices constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->registerHooks();
    }

    /**
     * Registers the hooks.
     *
     * @throws Exception
     */
    protected function registerHooks()
    {
        Register::action()
            ->setGroup('admin_init')
            ->setHandler([$this, 'registerNotices'])
            ->execute();

        Register::action()
            ->setGroup('admin_notices')
            ->setHandler([$this, 'renderNotices'])
            ->execute();
    }

    /**
     * Renders the notices.
     *
     * @throws Exception
     */
    public function renderNotices()
    {
        if (! $user = User::getCurrent()) {
            return;
        }

        foreach ($this->notices as $data) {
            if (! $this->shouldRenderNotice($user, $data)) {
                continue;
            }

            $this->renderNotice($data);
        }
    }

    /**
     * Determines whether a notice should be rendered for the given user.
     *
     * @param User $user a user object
     * @param array $data notice data
     * @return bool
     */
    public function shouldRenderNotice(User $user, array $data) : bool
    {
        // bail if notice is not dismissible or if the notice was not dismissed by the user
        return ! ArrayHelper::get($data, 'dismissible', true)
            || ! AdminNotices::isNoticeDismissed($user, ArrayHelper::get($data, 'id', ''));
    }

    /**
     * Renders a notice.
     *
     * @param array $data
     * @throws Exception
     */
    protected function renderNotice(array $data)
    {
        if (empty($data['message'])) {
            return;
        }

        $classes = ArrayHelper::combine([
            'notice',
            'notice-'.ArrayHelper::get($data, 'type', 'info'),
        ], ArrayHelper::wrap(ArrayHelper::get($data, 'classes', [])));

        if (! empty($data['dismissible'])) {
            $classes[] = 'is-dismissible';
        } ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-message-id="<?php echo esc_attr(ArrayHelper::get($data, 'id', '')); ?>"><p><?php echo wp_kses_post($data['message']); ?></p></div>
        <?php
    }

    /**
     * Adds a notice for display.
     *
     * @param array $data
     */
    protected function registerNotice(array $data)
    {
        if (empty($data['id'])) {
            return;
        }

        $this->notices[$data['id']] = $data;
    }

    /**
     * Registers the notices that should be displayed.
     *
     * TODO: this method definitely needs to be broken up, and hopefully removed if we reactify these notices {@cwiseman 2021-05-24}
     *
     * @throws Exception
     */
    public function registerNotices() : void
    {
        if (Worldpay::shouldLoad()) {
            return;
        }

        // only an error notice if beginning onboarding fails
        if (ArrayHelper::get($_GET, 'onboardingError')) {
            $this->registerNotice([
                'dismissible' => true,
                'id'          => 'mwc-payments-godaddy-onboarding-error',
                'message'     => __('There was an error connecting to GoDaddy Payments. Please try again.', 'mwc-core'),
                'type'        => 'error',
            ]);

            return;
        }

        $status = Onboarding::getStatus();
        switch ($status) {
            case Onboarding::STATUS_CONNECTED:
                if (AutoConnectInterceptor::wasConnected()) {
                    $message = ''; // do not display a connected notice here if PBD was connected
                } elseif ($this->isGatewayEnabled()) {
                    $message = sprintf(
                        __('%1$sGoDaddy Payments successfully enabled!%2$s GoDaddy Payments is now available to your customers at checkout.', 'mwc-core'),
                        '<strong>',
                        '</strong>'
                    );
                } else {
                    $message = sprintf(
                        __('%1$sGoDaddy Payments is now connected to your store!%2$s Enable the payment method to add it to your checkout. %3$sEnable GoDaddy Payments%4$s', 'mwc-core'),
                        '<strong>',
                        '</strong>',
                        '<a href="'.esc_url(OnboardingEventsProducer::getEnablePaymentMethodUrl()).'">',
                        '</a>'
                    );
                }
                $id = 'connected';
                $type = 'success';
                break;

            case Onboarding::STATUS_DISCONNECTED:
                $message = sprintf(
                    __('%1$sYour GoDaddy Payments account has been closed.%2$s The payment method has been disabled so it will not appear on your checkout. Please set up your account to resume processing payments.', 'mwc-core'),
                    '<strong>',
                    '</strong>'
                );
                $id = 'disconnected';
                $type = 'success';
                break;

            case Onboarding::STATUS_INCOMPLETE:
                $message = sprintf(
                    __('%1$sIt looks like you didn\'t finish your GoDaddy Payments application. You\'re just a few minutes from processing payments.%2$s %3$sResume%4$s', 'mwc-core'),
                    '<strong>',
                    '</strong>',
                    '<a href="'.esc_url(OnboardingEventsProducer::getOnboardingStartUrl('admin_notice_resume_link')).'">',
                    '</a>'
                );
                $id = 'incomplete';
                $type = 'success';
                break;

            case Onboarding::STATUS_SUSPENDED:
                $message = sprintf(
                    __('%1$sYour GoDaddy Payments account needs attention.%2$s The payment method has been disabled so it will not appear on your checkout. Please check your email for next steps.', 'mwc-core'),
                    '<strong>',
                    '</strong>'
                );
                $id = 'suspended';
                $type = 'warning';
                break;

            case Onboarding::STATUS_TERMINATED:
                $message = sprintf(
                    __('%1$sYour GoDaddy Payments account has been terminated.%2$s The payment method has been disabled so it will not appear on your checkout. Please check your email for more information.', 'mwc-core'),
                    '<strong>',
                    '</strong>'
                );
                $id = 'terminated';
                $type = 'error';
                break;
        }

        if (! empty($message) && ! empty($id) && ! empty($type)) {
            $this->registerNotice([
                'dismissible' => true,
                'id'          => "mwc-payments-godaddy-{$id}",
                'message'     => $message,
                'type'        => $type,
            ]);
        }

        $this->registerPoyntPluginNotices();
        $this->registerApplePayNotices();
        $this->registerGooglePayNotices();

        $this->registerGdpRecommendationNotices();
        $this->registerGdpSipRecommendationNotices();

        $this->maybeRegisterConnectedAccountNotice();
        $this->maybeRegisterCompleteProfileNotice();

        // remaining notices only display if the gateway is connected & enabled
        if (! $this->isGatewayEnabled() || ! Onboarding::canEnablePaymentGateway(Onboarding::getStatus())) {
            return;
        }

        if (WooCommerceRepository::isWooCommerceActive() && 'US' !== WC()->countries->get_base_country()) {
            $this->registerNotice([
                'dismissible' => false,
                'id'          => 'mwc-payments-godaddy-non-us',
                'message'     => sprintf(
                    __('GoDaddy Payments is available for United States-based businesses. Please %1$supdate your Store Address%2$s if you are in the U.S.', 'mwc-core'),
                    '<a href="'.esc_url(admin_url('admin.php?page=wc-settings')).'">',
                    '</a>'
                ),
                'type' => 'warning',
            ]);
        }

        if ('USD' !== get_woocommerce_currency()) {
            $this->registerNotice([
                'dismissible' => false,
                'id'          => 'mwc-payments-godaddy-non-usd',
                'message'     => sprintf(
                    __('GoDaddy Payments requires U.S. dollar transactions. Please %1$schange your Currency%2$s in order to use the payment method.', 'mwc-core'),
                    '<a href="'.esc_url(admin_url('admin.php?page=wc-settings')).'">',
                    '</a>'
                ),
                'type' => 'warning',
            ]);
        }

        if (ManagedWooCommerceRepository::isStagingEnvironment()) {
            $this->registerNotice([
                'dismissible' => true,
                'id'          => 'mwc-payments-godaddy-staging',
                'message'     => __('WooCommerce charges or authorizations/captures as well as refunds and voids made in your Staging site will process normally in your GoDaddy Payments account.', 'mwc-core'),
                'type'        => 'warning',
            ]);
        }
    }

    /**
     * Determines whether the GoDaddy Payments gateway is enabled.
     *
     * We need to check the configuration value when the notices are being registered to make sure we catch the new settings values after the form in the settings page is saved.
     *
     * @return bool
     * @throws Exception
     */
    protected function isGatewayEnabled() : bool
    {
        // TODO: update the provider name if we rename poynt to godaddy-payments or something else {@wvega 2021-05-29}
        return Configuration::get('payments.poynt.enabled', false);
    }

    /**
     * Determines whether the GoDaddy Payments Sell in Person gateway is enabled.
     *
     * @return bool
     * @throws Exception
     */
    protected function isSiPGatewayEnabled() : bool
    {
        return (bool) Configuration::get('payments.godaddy-payments-payinperson.enabled', false);
    }

    /**
     * Determines whether the BOPIT feature is active.
     *
     * @return bool
     * @throws Exception
     */
    public static function isBOPITFeatureEnabled() : bool
    {
        return Configuration::get('features.bopit', false);
    }

    /**
     * Registers admin notices to display GoDaddy Payments Recommendation.
     *
     * @throws Exception
     */
    protected function registerGdpRecommendationNotices()
    {
        if (! $this->shouldRegisterGdpRecommendationNotices()) {
            return;
        }

        $this->registerNotice([
            'dismissible' => true,
            'classes'     => 'mwc-godaddy-payments-recommendation',
            'id'          => 'mwc-godaddy-payments-recommendation',
            'message'     => sprintf(
                '<img src="%1$s" alt="'.esc_attr__('Provided by GoDaddy', 'mwc-core').'"/>
                <h3>'.esc_html__('GoDaddy Payments', 'mwc-core').'</h3>
                <p>'.esc_html__('Sell online and in person with GoDaddy Payments. Sync local pickup and delivery orders right to your Smart Terminal, then get paid fast with next-day deposits.', 'mwc-core').'</p>
                <a href="%2$s" class="mwc-button">'.esc_html__('Get Started', 'mwc-core').'</a>',
                esc_url(WordPressRepository::getAssetsUrl('images/branding/gd-icon.svg')),
                esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&gdpsetup=true'))
            ),
            'type' => 'info',
        ]);
    }

    /**
     * Registers admin notices to display GoDaddy Sell in Person Recommendation.
     *
     * @throws Exception
     */
    protected function registerGdpSipRecommendationNotices()
    {
        if (! $this->shouldRegisterGdpSipRecommendationNotices()) {
            return;
        }

        $this->registerNotice([
            'dismissible' => true,
            'classes'     => 'mwc-godaddy-payments-recommendation',
            'id'          => 'mwc-godaddy-payments-sip-recommendation',
            'message'     => sprintf(
                '<img src="%1$s" alt="'.esc_attr__('Provided by GoDaddy', 'mwc-core').'"/>
                <h3>'.esc_html__('GoDaddy Selling in Person', 'mwc-core').'</h3>
                <p>'.esc_html__('Use GoDaddy Payments Selling in Person to sync local pickup and delivery orders to your Smart Terminal. Sell anything, anywhere and get paid fast with next-day deposits.', 'mwc-core').'</p>
                <a class="mwc-button" href="%2$s">'.esc_html__('Get Started', 'mwc-core').'</a>',
                esc_url(WordPressRepository::getAssetsUrl('images/branding/gd-icon.svg')),
                esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=godaddy-payments-payinperson'))
            ),
            'type' => 'info',
        ]);
    }

    /**
     * Determines whether GoDaddy Payments Recommendation Notice should be registered.
     *
     * @return bool
     * @throws Exception
     */
    protected function shouldRegisterGdpRecommendationNotices() : bool
    {
        /* @NOTE we are not past `admin_init` context, and we can't use {@see WordPressRepository::isCurrentScreen()} here {unfulvio 2022-02-10} */
        if (! self::isBOPITFeatureEnabled()
            || '' !== Onboarding::getStatus()
            || 'wc-settings' !== ArrayHelper::get($_GET, 'page')
        ) {
            return false;
        }

        return ArrayHelper::contains(static::GDP_RECOMMENDATION_SECTIONS, ArrayHelper::get($_GET, 'section'))
            || (ArrayHelper::contains(static::GDP_RECOMMENDATION_TABS, ArrayHelper::get($_GET, 'tab')) && ($this->isLocalPickupEnabled() || $this->isLocalDeliveryEnabled()));
    }

    /**
     * Determines whether GoDaddy Payments Recommendation Notice should be registered for the Sell in Person gateway.
     *
     * @return bool
     * @throws Exception
     */
    protected function shouldRegisterGdpSipRecommendationNotices() : bool
    {
        /* @NOTE we are not past `admin_init` context, and we can't use {@see WordPressRepository::isCurrentScreen()} here {unfulvio 2022-02-10} */
        if (! self::isBOPITFeatureEnabled()
            || $this->isSiPGatewayEnabled()
            || ! Onboarding::canEnablePaymentGateway(Onboarding::getStatus())
            || 'wc-settings' !== ArrayHelper::get($_GET, 'page')
        ) {
            return false;
        }

        return ArrayHelper::contains(static::GDP_SIP_RECOMMENDATION_SECTIONS, ArrayHelper::get($_GET, 'section'))
            || (ArrayHelper::contains(static::GDP_SIP_RECOMMENDATION_TABS, ArrayHelper::get($_GET, 'tab')) && ($this->isLocalPickupEnabled() || $this->isLocalDeliveryEnabled()));
    }

    /**
     * Registers admin notices that should be rendered if the Poynt plugin is active.
     */
    protected function registerPoyntPluginNotices()
    {
        if (! $this->isPluginActive(static::GODADDY_PAYMENTS_PLUGIN_PATH)) {
            return;
        }

        $this->registerNotice([
            'dismissible' => true,
            'id'          => 'mwc-payments-godaddy-payments-already-included',
            'message'     => sprintf(
                __('GoDaddy Payments (Poynt) is included for Managed WordPress customers without a separate plugin! Go to %1$sPayments settings%2$s to enable it.', 'mwc-core'),
                '<a href="'.esc_url(admin_url('admin.php?page=wc-settings&tab=checkout')).'">',
                '</a>'
            ),
            'type' => 'info',
        ]);
    }

    /**
     * Registers Apple Pay notices.
     *
     * @throws Exception
     */
    protected function registerApplePayNotices()
    {
        if (true !== Configuration::get('payments.applePay.enabled', false) || ! ApplePayGateway::isActive()) {
            return;
        }

        if (ApplePayGateway::isDomainRegisteredWithApple()) {
            $this->registerNotice([
                'dismissible' => true,
                'id'          => 'mwc-payments-godaddy-payments-apple-pay-enabled',
                'message'     => sprintf(
                    __('GoDaddy Payments - Apple Pay has been enabled on your selected pages and shows %1$sin Safari on supported devices%2$s.', 'mwc-core'),
                    '<a href="https://support.apple.com/en-us/HT208531" target="_blank">',
                    ' <span class="dashicons dashicons-external"></span></a>'
                ),
                'type' => 'success',
            ]);
        } else {
            $this->registerNotice([
                'dismissible' => false,
                'id'          => 'mwc-payments-godaddy-payments-apple-pay-registration-failed',
                'message'     => sprintf(
                    __('There was a problem registering your site with Apple Pay. Please disable Apple Pay and try re-enabling, or %1$scontact support%2$s.', 'mwc-core'),
                    '<a href="'.esc_url(admin_url('admin.php?page=godaddy-get-help')).'">',
                    '</a>'
                ),
                'type' => 'error',
            ]);
        }

        $page = ArrayHelper::get($_GET, 'page');
        $section = ArrayHelper::get($_GET, 'section');
        $hasEnabledPages = ! empty(Configuration::get('payments.applePay.enabledPages'));

        // only display this notice on the Apple Pay settings page
        if ($hasEnabledPages || 'wc-settings' !== $page || 'godaddy-payments-apple-pay' !== $section) {
            return;
        }

        $this->registerNotice([
            'dismissible' => false,
            'id'          => 'mwc-payments-godaddy-payments-apple-pay-no-enabled-pages',
            'message'     => __('Please select the pages where Apple Pay should show.', 'mwc-core'),
            'type'        => 'error',
        ]);
    }

    /**
     * Registers Google Pay notices.
     *
     * @throws Exception
     */
    protected function registerGooglePayNotices() : void
    {
        if (true !== Configuration::get('payments.googlePay.enabled', false) || ! GooglePayGateway::isActive()) {
            return;
        }

        $this->registerGooglePayEnabledNotice();

        $page = ArrayHelper::get($_GET, 'page');
        $section = ArrayHelper::get($_GET, 'section');

        $isGooglePaySettingsPage = 'wc-settings' === $page && 'godaddy-payments-google-pay' === $section;

        // only display this notice on the Google Pay settings page
        if (! GooglePayGateway::hasEnabledPages() && $isGooglePaySettingsPage) {
            $this->registerGooglePaySelectPagesNotice();
        }
    }

    /**
     * Registers the Google Pay enabled notice.
     */
    protected function registerGooglePayEnabledNotice() : void
    {
        $this->registerNotice([
            'dismissible' => true,
            'id'          => 'mwc-payments-godaddy-payments-google-pay-enabled',
            'message'     => sprintf(
                /* translators: Placeholders: %1$s - <a> tag for the Google Pay docs link, %2$s - </a> tag */
                __('GoDaddy Payments - Google Pay has been enabled on your selected pages and shows %1$sin supported browsers and devices%2$s.', 'mwc-core'),
                '<a href="https://developers.google.com/pay/api/web/guides/test-and-deploy/integration-checklist#test-using-browser-developer-console" target="_blank">',
                ' <span class="dashicons dashicons-external"></span></a>'
            ),
            'type' => 'success',
        ]);
    }

    /**
     * Registers the Google Pay notice to indicate that the user must select an enabled page.
     */
    protected function registerGooglePaySelectPagesNotice() : void
    {
        $this->registerNotice([
            'dismissible' => false,
            'id'          => 'mwc-payments-godaddy-payments-google-pay-no-enabled-pages',
            'message'     => __('Please select the pages where Google Pay should show.', 'mwc-core'),
            'type'        => 'error',
        ]);
    }

    /**
     * Registers the notice for a connected GDP account.
     *
     * @throws Exception
     */
    protected function maybeRegisterConnectedAccountNotice()
    {
        if (true !== Configuration::get('features.gdp_by_default.enabled')) {
            return;
        }

        if (! $this->shouldShowGDPConnectionNotices()) {
            return;
        }

        if (! $business = $this->getConnectedBusiness()) {
            return;
        }

        $this->registerNotice([
            'dismissible' => true,
            'id'          => Onboarding::hasSwitchedAccounts() ? 'mwc-payments-godaddy-payments-connection-switched' : 'mwc-payments-godaddy-payments-connection',
            'message'     => $this->getConnectionNoticeMessage($business),
            'type'        => 'success',
        ]);
    }

    /**
     * Determines if the GDP connection notices should be displayed for the current page load.
     *
     * This is currently limited to:
     * - WooCommerce -> Settings -> General
     * - WooCommerce -> Settings -> Payments
     * - WooCommerce -> Settings -> Payments -> GoDaddy Payments
     *
     * @return bool
     * @throws Exception
     */
    protected function shouldShowGDPConnectionNotices() : bool
    {
        if ('wc-settings' !== ArrayHelper::get($_GET, 'page')) {
            return false;
        }

        $tab = ArrayHelper::get($_GET, 'tab');

        if ($tab && ! ArrayHelper::contains(['general', 'checkout'], $tab)) {
            return false;
        }

        $section = ArrayHelper::get($_GET, 'section');

        if ($section && 'poynt' !== $section) {
            return false;
        }

        return Poynt::isConnected() && AutoConnectInterceptor::wasConnected();
    }

    /**
     * Gets the connected business.
     *
     * @return Business|null
     */
    protected function getConnectedBusiness() : ?Business
    {
        try {
            return Poynt::getBusiness();
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Gets the connection notice message.
     *
     * @param Business $business
     *
     * @return string
     * @throws Exception
     */
    protected function getConnectionNoticeMessage(Business $business) : string
    {
        // display a special message after switch
        if (Onboarding::hasSwitchedAccounts()) {
            return sprintf(
                /* translators: Placeholders: %1$s - a connected account's legal name, %2$s - a connected account's email address, %3$s - <a> tag, %4$s - </a> */
                __('GoDaddy Payments is now connected with the account %1$s %2$s. (Not right? %3$sSwitch account%4$s.)', 'mwc-core'),
                $business->getDoingBusinessAs(),
                $business->getEmailAddress(),
                '<a href="'.esc_url(OnboardingEventsProducer::getSwitchStartUrl()).'">', '</a>'
            );
        }

        // otherwise, display the newly connected message
        $message = '<strong>'.__('You\'re all set to take payments with GoDaddy Payments!', 'mwc-core').'</strong>';

        if (Onboarding::getRequiredActions()) {
            $message .= ' '.sprintf(
                /* translators: Placeholders: %1$s - <a> tag, %2$s - </a> */
                __('To get your funds deposited to your bank account, verify your identity and add your banking info. %1$sSet up payouts%2$s', 'mwc-core'),
                '<a href="'.esc_url(Onboarding::getApplicationUrl()).'" target="_blank">', '</a>'
            );
        }

        $message .= '<br /><br />'.sprintf(
            /* translators: Placeholders: %1$s - a connected account's legal name, %2$s - a connected account's email address, %3$s - <a> tag, %4$s - </a> */
            __('The connected GoDaddy Payments account is %1$s %2$s (Not the business owner\'s account? %3$sSwitch account%4$s).', 'mwc-core'),
            $business->getDoingBusinessAs(),
            $business->getEmailAddress(),
            '<a href="'.esc_url(OnboardingEventsProducer::getSwitchStartUrl()).'">', '</a>'
        );

        return $message;
    }

    /**
     * Registers the notice for completing a GDP profile.
     *
     * @throws Exception
     */
    protected function maybeRegisterCompleteProfileNotice() : void
    {
        if (true !== Configuration::get('features.gdp_by_default.enabled')) {
            return;
        }

        if (Onboarding::STATUS_TERMINATED === Onboarding::getStatus() || ! Configuration::get('payments.poynt.onboarding.hasFirstPayment', false)) {
            return;
        }

        if (! $noticeProperties = $this->getCompleteProfileNoticeProperties()) {
            return;
        }

        $this->registerNotice($noticeProperties);
    }

    /**
     * Gets the notice properties based on the site's required actions.
     *
     * Returns null if no notice is needed.
     *
     * NOTE: this could be converted to using an adapter once we have a Notice object and a proper notice system {@cwiseman 2022-04-01}
     *
     * @return array<string, mixed>|null
     * @throws Exception
     */
    protected function getCompleteProfileNoticeProperties() : ?array
    {
        $requiredActions = Onboarding::getRequiredActions();
        $ctaUrl = Onboarding::getApplicationUrl();
        $ctaMessage = __('Set up payouts', 'mwc-core');

        if (ArrayHelper::contains($requiredActions, 'COMPLETE_VERIFICATION_PAST_DUE')) {
            $message = __('You haven\'t completed the necessary steps to verify your identity with GoDaddy Payments. Until you do that, you won\'t be able to take any payments or deposit your funds.', 'mwc-core');

            $ctaMessage = __('Complete verification', 'mwc-core');
            $isDismissible = false;
            $type = 'error';
        } elseif (ArrayHelper::contains($requiredActions, 'COMPLETE_VERIFICATION_WARNING')) {
            $message = __('The deadline to complete your payouts setup is coming up! To keep transacting with GoDaddy Payments and to get your money, verify your identity and add banking info.', 'mwc-core');

            $isDismissible = false;
            $type = 'warning';
        } elseif (ArrayHelper::contains($requiredActions, 'COMPLETE_VERIFICATION_REMINDER')) {
            $message = __('You\'ve still got money waiting with GoDaddy Payments! Verify your identity and add your banking info as soon as you can to get your funds and keep taking payments.', 'mwc-core');

            $isDismissible = true;
            $type = 'warning';
        } elseif (ArrayHelper::contains($requiredActions, 'COMPLETE_VERIFICATION')) {
            $message = __('You\'ve got money waiting with GoDaddy Payments! Verify your identity and add your banking info to get your payouts deposited.', 'mwc-core');

            $isDismissible = true;
            $type = 'success';
        } elseif (ArrayHelper::contains($requiredActions, 'ADD_BANK')) {
            $message = __('You\'ve got money waiting with GoDaddy Payments! Add your banking info to get your payouts deposited.', 'mwc-core');

            $isDismissible = false;
            $type = 'success';

            $ctaUrl = add_query_arg([
                'businessId' => Poynt::getBusinessId(),
                'storeId'    => Poynt::getSiteStoreId(),
            ], Poynt::getHubUrl());

            $ctaMessage = __('Link bank account', 'mwc-core');
        } else {
            return null;
        }

        $message .= $this->getCompleteProfileNoticeCallToAction($ctaMessage, $ctaUrl);

        return [
            'dismissible' => $isDismissible,
            'id'          => 'mwc-payments-godaddy-payments-complete-profile',
            'message'     => $message,
            'type'        => $type,
        ];
    }

    /**
     * Gets the call-to-action HTML for the complete profile notice.
     *
     * @param string $message
     * @param string $url
     *
     * @return string
     */
    protected function getCompleteProfileNoticeCallToAction(string $message, string $url) : string
    {
        return '<br /><br /><a href="'.esc_url($url).'" target="_blank" class="mwc-payments-godaddy-cta">'.esc_html($message).'<span class="dashicons dashicons-external"></span></a>';
    }

    /**
     * Determines whether the zones have Local Pickup Method enabled.
     *
     * @return bool
     * @throws Exception
     */
    protected function isLocalPickupEnabled() : bool
    {
        $shippingZones = WooCommerceRepository::isWooCommerceActive() ? WC_Shipping_Zones::get_zones() : [];

        foreach (ArrayHelper::wrap($shippingZones) as $zone) {
            $localPickupShippingMethods = ArrayHelper::where(ArrayHelper::get($zone, 'shipping_methods', []), static function ($method) {
                return static::WC_LOCAL_PICKUP === $method->id;
            });

            return ! empty($localPickupShippingMethods);
        }

        return false;
    }

    /**
     * Determines whether the zones have Local Delivery Method enabled.
     *
     * @return bool
     * @throws Exception
     */
    protected function isLocalDeliveryEnabled() : bool
    {
        $shippingZones = WooCommerceRepository::isWooCommerceActive() ? WC_Shipping_Zones::get_zones() : [];

        foreach (ArrayHelper::wrap($shippingZones) as $zone) {
            $localDeliveryShippingMethods = ArrayHelper::where(ArrayHelper::get($zone, 'shipping_methods', []), static function ($method) {
                return 'mwc_local_delivery' === $method->id;
            });

            return ! empty($localDeliveryShippingMethods);
        }

        return false;
    }

    /**
     * Determines whether the given plugin is active.
     *
     * TODO: add this method to the WordPressRepository or make it possible to create PluginExtension objects from installed (non-managed) plugins {@wvega 2021-06-03}
     *
     * @param string path to the plugin file relative to the plugins directory
     * @return bool
     */
    protected function isPluginActive(string $path) : bool
    {
        return is_plugin_active($path);
    }
}
