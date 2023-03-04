<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Overrides;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Exceptions\BaseException;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Register;

/**
 * Overrides WooCommerce setting defaults.
 */
class DefaultSettings extends AbstractInterceptor
{
    /**
     * Filters WooCommerce settings.
     *
     * @return void
     * @throws Exception
     */
    public function addHooks() : void
    {
        $this->filterSettingGroups();
        $this->filterDefaultSettingOptions();
    }

    /**
     * Registers filters for all WooCommerce settings "groups" that have custom defaults configured.
     *
     * @return void
     * @throws Exception
     */
    protected function filterSettingGroups() : void
    {
        foreach (TypeHelper::array(Configuration::get('woocommerce.defaultSettings'), []) as $groupFilterName => $customDefaultSettings) {
            Register::filter()
                ->setGroup($groupFilterName)
                ->setHandler(fn ($settings) => $this->filterSettingGroup($settings, $customDefaultSettings))
                ->execute();
        }
    }

    /**
     * Filters a setting group to override the default values.
     *
     * @param array<string, mixed>|mixed $settings existing WooCommerce settings
     * @param array<string, mixed>|mixed $customDefaultSettings our default overrides
     * @return array<string, mixed>|mixed
     */
    protected function filterSettingGroup($settings, $customDefaultSettings)
    {
        if (! ArrayHelper::accessible($settings) || ! ArrayHelper::accessible($customDefaultSettings)) {
            return $settings;
        }

        foreach ($settings as $index => $settingGroup) {
            $settingId = TypeHelper::string(ArrayHelper::get($settingGroup, 'id'), '');

            // bail if we don't have a custom default for this setting
            if ($settingId && ArrayHelper::exists($customDefaultSettings, $settingId)) {
                $settings[$index]['default'] = ArrayHelper::get($customDefaultSettings, $settingId);
            }
        }

        return $settings;
    }

    /**
     * Filters the default value of all settings.
     * This ensures {@see get_option()} will return our custom default value when it's not yet set in the database.
     *
     * @return void
     * @throws Exception
     */
    protected function filterDefaultSettingOptions() : void
    {
        foreach ($this->getSettingIdsAndDefaultValues() as $optionName => $defaultValue) {
            Register::filter()
                ->setGroup("default_option_{$optionName}")
                ->setHandler(fn () => $defaultValue)
                ->execute();
        }
    }

    /**
     * Gets an array of setting IDs and their custom default values.
     *
     * @return array<string, mixed>
     * @throws BaseException
     */
    protected function getSettingIdsAndDefaultValues() : array
    {
        $settings = [];

        // we cannot use ArrayHelper::flatten() here because we want to preserve the array keys
        foreach (TypeHelper::array(Configuration::get('woocommerce.defaultSettings'), []) as $customDefaultSettings) {
            $settings = ArrayHelper::combine($settings, $customDefaultSettings);
        }

        return $settings;
    }
}
