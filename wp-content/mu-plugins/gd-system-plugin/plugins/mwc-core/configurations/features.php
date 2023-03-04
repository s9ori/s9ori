<?php

use GoDaddy\WordPress\MWC\Common\Features\AbstractFeature;
use GoDaddy\WordPress\MWC\Core\Configuration\CartRecoveryEmailsRuntimeConfiguration;
use GoDaddy\WordPress\MWC\Core\Features\CartRecoveryEmails\CartRecoveryEmails;
use GoDaddy\WordPress\MWC\Core\Features\CostOfGoods\CostOfGoods;
use GoDaddy\WordPress\MWC\Core\Features\EmailNotifications\EmailNotifications;
use GoDaddy\WordPress\MWC\Core\Features\GiftCertificates\GiftCertificates;
use GoDaddy\WordPress\MWC\Core\Features\GoogleAnalytics\GoogleAnalytics;
use GoDaddy\WordPress\MWC\Core\Features\Marketplaces\Marketplaces;
use GoDaddy\WordPress\MWC\Core\Features\Onboarding\Dashboard;
use GoDaddy\WordPress\MWC\Core\Features\Onboarding\Onboarding;
use GoDaddy\WordPress\MWC\Core\Features\SequentialOrderNumbers\SequentialOrderNumbers;
use GoDaddy\WordPress\MWC\Core\Features\ShipmentTracking\ShipmentTracking;
use GoDaddy\WordPress\MWC\Core\Features\Shipping\Shipping;
use GoDaddy\WordPress\MWC\Core\Features\Stripe\Stripe;
use GoDaddy\WordPress\MWC\Core\Features\UrlCoupons\UrlCoupons;

$mwcEcommerceAllowedHostingPlans = [
    'ecommerce',
    'flex',
    'expand',
    'premier',
];

return [
    /*
     *--------------------------------------------------------------------------
     * Features Settings
     *--------------------------------------------------------------------------
     *
     * The configurations below are used to determine whether a feature is available or not.
     * - If a feature flag exists for a given feature in AWS Evidently, it will override the enabled key.
     * - Order of precedence: overrides.disabled, overrides.enabled, feature flag (from AWS Evidently API), enabled.
     *
     * Descriptive information (name, urls, etc.) is used to display the native features on the WooCommerce > Extensions page.
     */
    'apple_pay'     => ! (defined('DISABLE_MWC_APPLE_PAY') && DISABLE_MWC_APPLE_PAY),
    'google_pay'    => ! (defined('DISABLE_MWC_GOOGLE_PAY') && DISABLE_MWC_GOOGLE_PAY),
    'bopit'         => ! (defined('DISABLE_MWC_BOPIT') && DISABLE_MWC_BOPIT),
    'cost_of_goods' => [
        'name'        => function_exists('__') ? __('Cost of goods', 'mwc-core') : 'Cost of goods',
        'description' => function_exists('__') ? sprintf(
            /* translators: Placeholders: %1$s - <a> tag for the plugin link, %2$s - </a> tag */
            __('Track profit and cost of goods for your store. Generate profit reports by date, product, category, and more. This feature replaces the %1$sCost of Goods%2$s plugin.', 'mwc-core'),
            '<a href="https://woocommerce.com/products/woocommerce-cost-of-goods/" target="_blank">', '</a>'
        ) : '',
        'documentation_url' => 'https://godaddy.com/help/40874',
        'settings_url'      => function_exists('admin_url') ? admin_url('admin.php?page=wc-settings&tab=orders') : '',
        'categories'        => [
            'Store Management',
        ],
        'enabled'                         => true,
        'allowedHostingPlans'             => $mwcEcommerceAllowedHostingPlans,
        'requiredPlugins'                 => ['woocommerce'],
        'shouldDisableAccountRestriction' => AbstractFeature::DISABLE_ACCOUNT_RESTRICTION_ALWAYS,
        'overrides'                       => [
            'enabled'  => defined('ENABLE_MWC_COST_OF_GOODS') && ENABLE_MWC_COST_OF_GOODS,
            'disabled' => defined('DISABLE_MWC_COST_OF_GOODS') && DISABLE_MWC_COST_OF_GOODS,
        ],
        'className' => CostOfGoods::class,
    ],
    'email_deliverability' => ! (defined('DISABLE_MWC_EMAIL_DELIVERABILITY') && DISABLE_MWC_EMAIL_DELIVERABILITY),
    'email_notifications'  => [
        'name'        => function_exists('__') ? __('Ecommerce emails', 'mwc-core') : 'Ecommerce emails',
        'description' => function_exists('__') ? sprintf(
            /* translators: Placeholders: %1$s - <a> tag for the plugin link, %2$s - </a> tag */
            __('Customize your emails to reflect your brand and increase customer loyalty. This feature replaces the %1$sWooCommerce Email Customizer%2$s plugin.', 'mwc-core'),
            '<a href="https://woocommerce.com/products/woocommerce-email-customizer/" target="_blank">', '</a>'
        ) : '',
        'documentation_url' => 'https://godaddy.com/help/40929',
        'settings_url'      => function_exists('admin_url') ? admin_url('admin.php?page=gd-email-notifications&tab=settings') : '',
        'categories'        => [
            'Marketing and Messaging',
        ],
        'enabled'                         => true,
        'allowedHostingPlans'             => $mwcEcommerceAllowedHostingPlans,
        'requiredPlugins'                 => ['woocommerce'],
        'shouldDisableAccountRestriction' => AbstractFeature::DISABLE_ACCOUNT_RESTRICTION_WITH_OVERRIDE,
        'overrides'                       => [
            'enabled'  => defined('ENABLE_MWC_EMAIL_NOTIFICATIONS') && ENABLE_MWC_EMAIL_NOTIFICATIONS,
            'disabled' => defined('DISABLE_MWC_EMAIL_NOTIFICATIONS') && DISABLE_MWC_EMAIL_NOTIFICATIONS,
        ],
        'className' => EmailNotifications::class,
    ],
    'google_analytics' => [
        'name'        => function_exists('__') ? __('Google analytics', 'mwc-core') : 'Google analytics',
        'description' => function_exists('__') ? sprintf(
            /* translators: Placeholders: %1$s and %3$s - <a> tag for the plugin link, %2$s and %4$s - </a> tag */
            __('Track advanced eCommerce events and more with Google Analytics. This feature replaces the %1$sGoogle Analytics%2$s and %3$sGoogle Analytics Pro%4$s plugins.', 'mwc-core'),
            '<a href="https://woocommerce.com/products/woocommerce-google-analytics/" target="_blank">', '</a>',
            '<a href="https://woocommerce.com/products/woocommerce-google-analytics-pro/" target="_blank">', '</a>'
        ) : '',
        'documentation_url' => 'https://godaddy.com/help/40882',
        'settings_url'      => function_exists('admin_url') ? admin_url('admin.php?page=wc-settings&tab=integration&section=google_analytics_pro') : '',
        'categories'        => [
            'Marketing and Messaging',
        ],
        'enabled'                         => true,
        'allowedHostingPlans'             => $mwcEcommerceAllowedHostingPlans,
        'requiredPlugins'                 => ['woocommerce'],
        'shouldDisableAccountRestriction' => AbstractFeature::DISABLE_ACCOUNT_RESTRICTION_ALWAYS,
        'overrides'                       => [
            'enabled'  => defined('ENABLE_MWC_GOOGLE_ANALYTICS') && ENABLE_MWC_GOOGLE_ANALYTICS,
            'disabled' => defined('DISABLE_MWC_GOOGLE_ANALYTICS') && DISABLE_MWC_GOOGLE_ANALYTICS,
        ],
        'className' => GoogleAnalytics::class,
    ],
    'onboarding' => [
        'enabled'                         => true,
        'allowedHostingPlans'             => [],
        'requiredPlugins'                 => ['woocommerce'],
        'shouldDisableAccountRestriction' => AbstractFeature::DISABLE_ACCOUNT_RESTRICTION_WITH_OVERRIDE,
        'overrides'                       => [
            'enabled'  => defined('ENABLE_MWC_ONBOARDING') && ENABLE_MWC_ONBOARDING,
            'disabled' => defined('DISABLE_MWC_ONBOARDING') && DISABLE_MWC_ONBOARDING,
        ],
        'className' => Onboarding::class,
    ],
    'sequential_order_numbers' => [
        'name'        => function_exists('__') ? __('Sequential order numbers', 'mwc-core') : 'Sequential order numbers',
        'description' => function_exists('__') ? sprintf(
            /* translators: Placeholders: %1$s - <a> tag for the plugin link, %2$s - </a> tag */
            __('Format order numbers, change your starting number, and differentiate free orders. This feature replaces the %1$sSequential Order Numbers Pro%2$s plugin.', 'mwc-core'),
            '<a href="https://woocommerce.com/products/sequential-order-numbers-pro/" target="_blank">', '</a>'
        ) : '',
        'documentation_url' => 'https://godaddy.com/help/40712',
        'settings_url'      => function_exists('admin_url') ? admin_url('admin.php?page=wc-settings&tab=orders') : '',
        'categories'        => [
            'Store Management',
        ],
        'enabled'                         => true,
        'allowedHostingPlans'             => $mwcEcommerceAllowedHostingPlans,
        'requiredPlugins'                 => ['woocommerce'],
        'shouldDisableAccountRestriction' => AbstractFeature::DISABLE_ACCOUNT_RESTRICTION_ALWAYS,
        'overrides'                       => [
            'enabled'  => defined('ENABLE_MWC_SEQUENTIAL_ORDER_NUMBERS') && ENABLE_MWC_SEQUENTIAL_ORDER_NUMBERS,
            'disabled' => defined('DISABLE_MWC_SEQUENTIAL_ORDER_NUMBERS') && DISABLE_MWC_SEQUENTIAL_ORDER_NUMBERS,
        ],
        'className' => SequentialOrderNumbers::class,
    ],
    'shipment_tracking' => [
        'name'        => function_exists('__') ? __('Shipment tracking', 'mwc-core') : 'Shipment tracking',
        'description' => function_exists('__') ? sprintf(
            /* translators: Placeholders: %1$s - <a> tag for the plugin link, %2$s - </a> tag */
            __('Share shipment tracking information with your customers. Open one of your Processing orders to get started. This feature replaces the %1$sShipment Tracking%2$s plugin.', 'mwc-core'),
            '<a href="https://woocommerce.com/products/shipment-tracking/" target="_blank">', '</a>'
        ) : '',
        'documentation_url' => 'https://godaddy.com/help/40631',
        'settings_url'      => '',
        'categories'        => [
            'Shipping',
        ],
        'enabled'   => get_option('mwc_shipment_tracking_active', 'yes') === 'yes',
        'className' => ShipmentTracking::class,
    ],
    'url_coupons' => [
        'name'        => function_exists('__') ? __('Discount links', 'mwc-core') : 'Discount links',
        'description' => function_exists('__') ? sprintf(
            /* translators: Placeholders: %1$s - <a> tag for the plugin link, %2$s - </a> tag */
            __('Share discount links with your customers and add coupons from ads, email campaigns, or social links. Create or edit a coupon to get started. This feature replaces the %1$sURL Coupons%2$s plugin.', 'mwc-core'),
            '<a href="https://woocommerce.com/products/url-coupons/" target="_blank">', '</a>'
        ) : '',
        'documentation_url' => 'https://godaddy.com/help/40840',
        'categories'        => [
            'Marketing and Messaging',
        ],
        'enabled'                         => true,
        'allowedHostingPlans'             => $mwcEcommerceAllowedHostingPlans,
        'requiredPlugins'                 => ['woocommerce'],
        'shouldDisableAccountRestriction' => AbstractFeature::DISABLE_ACCOUNT_RESTRICTION_ALWAYS,
        'overrides'                       => [
            'enabled'  => defined('ENABLE_MWC_URL_COUPONS') && ENABLE_MWC_URL_COUPONS,
            'disabled' => defined('DISABLE_MWC_URL_COUPONS') && DISABLE_MWC_URL_COUPONS,
        ],
        'className' => UrlCoupons::class,
    ],
    'gift_certificates' => [
        'name'        => function_exists('__') ? __('Gift certificates', 'mwc-core') : 'Gift certificates',
        'description' => function_exists('__') ? sprintf(
            /* translators: Placeholders: %1$s - <a> tag for the plugin link, %2$s - </a> tag */
            __('Create custom gift certificates that your customers can purchase and send to their friends and family. This feature replaces the %1$sPDF Product Vouchers%2$s plugin.', 'mwc-core'),
            '<a href="https://woocommerce.com/products/pdf-product-vouchers/" target="_blank">', '</a>'
        ) : '',
        'documentation_url' => 'https://www.godaddy.com/help/40294',
        'settings_url'      => function_exists('admin_url') ? admin_url('edit.php?post_type=wc_voucher') : '',
        'categories'        => [
            'Merchandising',
            'Product Type',
        ],
        'enabled'                         => true,
        'allowedHostingPlans'             => $mwcEcommerceAllowedHostingPlans,
        'requiredPlugins'                 => ['woocommerce'],
        'shouldDisableAccountRestriction' => AbstractFeature::DISABLE_ACCOUNT_RESTRICTION_NEVER,
        'overrides'                       => [
            'enabled'  => defined('ENABLE_MWC_GIFT_CERTIFICATES') && ENABLE_MWC_GIFT_CERTIFICATES,
            'disabled' => defined('DISABLE_MWC_GIFT_CERTIFICATES') && DISABLE_MWC_GIFT_CERTIFICATES,
        ],
        'className' => GiftCertificates::class,
    ],
    'onboarding_dashboard' => [
        'enabled'                         => true,
        'allowedHostingPlans'             => [],
        'requiredPlugins'                 => ['woocommerce'],
        'shouldDisableAccountRestriction' => AbstractFeature::DISABLE_ACCOUNT_RESTRICTION_WITH_OVERRIDE,
        'overrides'                       => [
            'enabled'  => defined('ENABLE_MWC_ONBOARDING_DASHBOARD') && ENABLE_MWC_ONBOARDING_DASHBOARD,
            'disabled' => defined('DISABLE_MWC_ONBOARDING_DASHBOARD') && DISABLE_MWC_ONBOARDING_DASHBOARD,
        ],
        'className' => Dashboard::class,
    ],
    'bopit_sync' => [
        'enabled' => ! (defined('DISABLE_MWC_BOPIT_SYNC') && DISABLE_MWC_BOPIT_SYNC),
    ],
    'cart_recovery_emails' => [
        'name'              => function_exists('__') ? __('Abandoned checkout reminders', 'mwc-core') : 'Abandoned checkout reminders',
        'description'       => function_exists('__') ? __('Automatically track cart activity and send an email reminder to recover lost revenue.', 'mwc-core') : '',
        'documentation_url' => 'https://godaddy.com/help/41079',
        'settings_url'      => function_exists('admin_url') ? admin_url('admin.php?page=gd-email-notifications&tab=emails&category=cart_recovery') : '',
        'categories'        => [
            'Cart and checkout',
            'Marketing and Messaging',
        ],
        'enabled'                  => true,
        'requiredFeatures'         => EmailNotifications::class,
        'expired_cart_in_seconds'  => 14 * 24 * 60 * 60, // 14 days
        'expiring_cart_in_seconds' => 13 * 24 * 60 * 60, // 13 days
        'send_wait_period'         => 5 * 24 * 60 * 60, // 5 days
        'overrides'                => [
            'enabled'  => defined('ENABLE_MWC_CART_RECOVERY_EMAILS') && ENABLE_MWC_CART_RECOVERY_EMAILS,
            'disabled' => defined('DISABLE_MWC_CART_RECOVERY_EMAILS') && DISABLE_MWC_CART_RECOVERY_EMAILS,
        ],
        'className'             => CartRecoveryEmails::class,
        'runtime_configuration' => CartRecoveryEmailsRuntimeConfiguration::class,
        'isDelayReadOnly'       => false,
    ],
    'gdp_by_default' => [
        'enabled' => ! (defined('DISABLE_GDP_BY_DEFAULT') && DISABLE_GDP_BY_DEFAULT),
    ],
    'stripe' => [
        'enabled'   => false,
        'overrides' => [
            'enabled'  => defined('ENABLE_MWC_STRIPE') && ENABLE_MWC_STRIPE,
            'disabled' => defined('DISABLE_MWC_STRIPE') && DISABLE_MWC_STRIPE,
        ],
        'requiredPlugins'   => ['woocommerce'],
        'name'              => function_exists('__') ? __('Stripe', 'mwc-core') : 'Stripe',
        'description'       => function_exists('__') ? __('Accept credit card payments using Stripe.', 'mwc-core') : '',
        'documentation_url' => 'https://stripe.com/pricing',
        'settings_url'      => function_exists('admin_url') ? admin_url('admin.php?page=wc-settings&tab=checkout&section=stripe') : '',
        'categories'        => [
            'Payments',
        ],
        'allowedHostingPlans' => [],
        'className'           => Stripe::class,
    ],
    'marketplaces' => [
        'name'              => function_exists('__') ? __('Marketplaces', 'mwc-core') : 'Marketplaces',
        'description'       => function_exists('__') ? __('Sell to millions of customers from one place. Offer your products everywhere, from Amazon to Instagram, all from your own online store.', 'mwc-core') : '',
        'documentation_url' => 'https://godaddy.com/help/a-41221',
        'settings_url'      => function_exists('admin_url') ? admin_url('admin.php?page=gd-marketplaces') : '',
        'categories'        => [
            'Marketing and Messaging',
            'Merchandising',
            'Store Management',
        ],
        'enabled'   => false,
        'overrides' => [
            'enabled'  => defined('ENABLE_MWC_MARKETPLACES') && ENABLE_MWC_MARKETPLACES,
            'disabled' => defined('DISABLE_MWC_MARKETPLACES') && DISABLE_MWC_MARKETPLACES,
        ],
        'className' => Marketplaces::class,
    ],
    'shipping' => [
        'enabled'           => false,
        'name'              => 'Shipping',
        'description'       => function_exists('__') ? __('Take order fulfillment to the next level! Get live shipping rates from your favourite providers, print labels in one click, and automate shipment tracking and customer email notifications.', 'mwc-core') : '',
        'documentation_url' => 'https://godaddy.com/help/a-41210',
        'settings_url'      => '',
        'categories'        => [
            'Shipping',
        ],
        'allowedHostingPlans' => [],
        'overrides'           => [
            'disabled' => true,
        ],
        'className' => Shipping::class,
    ],
    'worldpay' => [
        'enabled' => false,
        'hqUrl'   => 'https://poynt.godaddy.com/',
    ],
];
