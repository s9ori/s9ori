<?php

namespace GoDaddy\WordPress\MWC\Common\Repositories;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;

/**
 * A repository for GoDaddy store configurations.
 *
 * @NOTE in the future this class could be changed from a repository structure or moved to different namespace {unfulvio 2022-09-21}
 */
class StoreRepository
{
    /**
     * Gets the store ID.
     *
     * @NOTE for now, we are using a Poynt configuration setting, but this should change in the future {unfulvio 2022-09-21}
     *
     * @return string
     */
    public static function getStoreId() : string
    {
        return (string) Configuration::get('payments.poynt.siteStoreId', '');
    }
}
