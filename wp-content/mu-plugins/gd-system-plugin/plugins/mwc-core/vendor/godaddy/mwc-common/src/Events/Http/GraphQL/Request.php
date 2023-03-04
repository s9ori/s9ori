<?php

namespace GoDaddy\WordPress\MWC\Common\Events\Http\GraphQL;

use Exception;
use GoDaddy\WordPress\MWC\Common\Auth\AuthProviderFactory;
use GoDaddy\WordPress\MWC\Common\Auth\Contracts\AuthMethodContract;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Contracts\GraphQLOperationContract;
use GoDaddy\WordPress\MWC\Common\Events\Http\GraphQL\Contracts\RequestContract;
use GoDaddy\WordPress\MWC\Common\Http\GraphQL\Request as GraphQLRequest;
use GoDaddy\WordPress\MWC\Common\Http\Traits\CanSetAuthMethodTrait;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;

/**
 * A request to interact with the Events API.
 *
 * @method static static getNewInstance(GraphQLOperationContract $operation)
 */
class Request extends GraphQLRequest implements RequestContract
{
    use CanGetNewInstanceTrait;
    use CanSetAuthMethodTrait;

    /** @var string|null */
    protected $siteId;

    /**
     * {@inheritdoc}
     */
    public static function withAuth(GraphQLOperationContract $operation)
    {
        return static::getNewInstance($operation)->tryToSetAuthMethod();
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(GraphQLOperationContract $operation)
    {
        parent::__construct($operation);

        $this->setUrl(Configuration::get('mwc.events.api.url'));
    }

    /**
     * Gets site ID.
     *
     * @return string|null
     */
    public function getSiteId() : ?string
    {
        return $this->siteId;
    }

    /**
     * Sets site ID.
     *
     * @param string $value The site ID to set.
     * @return $this
     */
    public function setSiteId(string $value) : Request
    {
        $this->siteId = $value;

        try {
            $this->addHeaders(['X-Site-Id' => $value]);
        } catch (Exception $exception) {
            // ignore exception that only occurs if the parameter to addHeaders() or the headers property are not arrays
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function getAuthMethodFromAuthProvider() : AuthMethodContract
    {
        return AuthProviderFactory::getNewInstance()->getEventsAuthProvider()->getMethod();
    }
}
