<?php

namespace GoDaddy\WordPress\MWC\Core\Auth\JWT\ManagedWooCommerce\Http\Requests;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;

/**
 * The request to get JWK from the MWC API.
 */
class KeySetRequest extends Request
{
    use CanGetNewInstanceTrait;

    /**
     * Request constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $apiUrl = Configuration::get('mwc.extensions.api.url');

        // removes version from the route (this endpoint is not versioned)
        if (false !== strpos($apiUrl, '/v')) {
            $apiUrl = StringHelper::beforeLast($apiUrl, 'v');
        }

        parent::__construct(StringHelper::trailingSlash($apiUrl).'.well-known/jwks.json');
    }
}
