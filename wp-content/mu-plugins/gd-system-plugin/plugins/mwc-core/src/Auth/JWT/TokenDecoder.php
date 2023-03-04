<?php

namespace GoDaddy\WordPress\MWC\Core\Auth\JWT;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use GoDaddy\WordPress\MWC\Common\Auth\JWT\Contracts\JwtDecoderContract;
use stdClass;

/**
 * JWT Token Decoder.
 */
class TokenDecoder implements JwtDecoderContract
{
    /** @var array */
    protected $keySet;

    /** @var string */
    protected $defaultAlgorithm = 'RS256';

    /**
     * Sets the keyset.
     *
     * @param array $value
     * @return JwtDecoderContract
     */
    public function setKeySet(array $value) : JwtDecoderContract
    {
        $this->keySet = $value;

        return $this;
    }

    /**
     * Sets the default algorithm that'll be used to decode the JWT if the key (JWK) doesn't specify an alg value.
     *
     * @param string $value
     * @return JwtDecoderContract
     */
    public function setDefaultAlgorithm(string $value) : JwtDecoderContract
    {
        $this->defaultAlgorithm = $value;

        return $this;
    }

    /**
     * Decodes the token.
     *
     * @param string $token
     * @return stdClass
     */
    public function decode(string $token) : stdclass
    {
        return JWT::decode($token, JWK::parseKeySet($this->keySet, $this->defaultAlgorithm));
    }
}
