<?php
/**
 * Copyright © Rhubarb Tech Inc. All Rights Reserved.
 *
 * All information contained herein is, and remains the property of Rhubarb Tech Incorporated.
 * The intellectual and technical concepts contained herein are proprietary to Rhubarb Tech Incorporated and
 * are protected by trade secret or copyright law. Dissemination and modification of this information or
 * reproduction of this material is strictly forbidden unless prior written permission is obtained from
 * Rhubarb Tech Incorporated.
 *
 * You should have received a copy of the `LICENSE` with this file. If not, please visit:
 * https://objectcache.pro/license.txt
 */

declare(strict_types=1);

namespace RedisCachePro\Clients;

use RedisCluster;
use LogicException;

use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\API\Trace\TracerProviderInterface;

/**
 * @mixin \RedisCluster
 * @property \RedisCluster $client
 */
class PhpRedisCluster extends Client
{
    use Concerns\PhpRedisTransactions;

    public const OPT_SERIALIZER = 1;

    public const OPT_PREFIX = 2;

    public const OPT_READ_TIMEOUT = 3;

    public const OPT_SCAN = 4;

    public const OPT_SLAVE_FAILOVER = 5;

    public const OPT_COMPRESSION = 7;

    public const OPT_COMPRESSION_LEVEL = 9;

    public const OPT_REPLY_LITERAL = 8;

    public const OPT_NULL_MULTIBULK_AS_NULL = 10;

    public const OPT_MAX_RETRIES = 11;

    public const OPT_BACKOFF_ALGORITHM = 12;

    public const OPT_BACKOFF_BASE = 13;

    public const OPT_BACKOFF_CAP = 14;

    public const ATOMIC = 0;

    public const MULTI = 2;

    public const FAILOVER_NONE = 0;

    public const FAILOVER_ERROR = 1;

    public const FAILOVER_DISTRIBUTE = 2;

    public const FAILOVER_DISTRIBUTE_SLAVES = 3;

    public const COMPRESSION_NONE = 0;

    public const COMPRESSION_LZF = 1;

    public const COMPRESSION_ZSTD = 2;

    public const COMPRESSION_LZ4 = 3;

    public const SERIALIZER_NONE = 0;

    public const SERIALIZER_PHP = 1;

    public const SERIALIZER_IGBINARY = 2;

    public const SERIALIZER_MSGPACK = 3;

    public const SERIALIZER_JSON = 4;

    public const BACKOFF_ALGORITHM_DEFAULT = 0;

    public const BACKOFF_ALGORITHM_DECORRELATED_JITTER = 1;

    public const BACKOFF_ALGORITHM_FULL_JITTER = 2;

    public const BACKOFF_ALGORITHM_EQUAL_JITTER = 3;

    public const BACKOFF_ALGORITHM_EXPONENTIAL = 4;

    public const BACKOFF_ALGORITHM_UNIFORM = 5;

    public const BACKOFF_ALGORITHM_CONSTANT = 6;

    public const SCAN_NORETRY = 0;

    public const SCAN_RETRY = 1;

    public const SCAN_PREFIX = 2;

    public const SCAN_NOPREFIX = 3;

    /**
     * Creates an OpenTelemetry tracer from given tracer provider.
     *
     * @param  TracerProviderInterface  $tracerProvider
     * @return TracerInterface
     */
    protected function createOpenTelemetryTracer(TracerProviderInterface $tracerProvider): TracerInterface
    {
        return $tracerProvider->getTracer(RedisCluster::class, (string) \phpversion('redis'));
    }

    /**
     * Block pipeline calls.
     *
     * @return \RedisCachePro\Clients\Transaction
     */
    public function pipeline()
    {
        throw new LogicException('RedisCluster does not support pipelines');
    }

    /**
     * Pass `scan()` calls to the client with iterator reference.
     *
     * @param  ?int  $iterator
     * @param  string|array<mixed>  $key
     * @param  ?string  $match
     * @param  int  $count
     * @return mixed
     */
    public function scan(&$iterator, $key, $match = null, $count = 0)
    {
        return $this->{$this->callback}(function () use (&$iterator, $key, $match, $count) {
            return $this->client->scan($iterator, $key, $match, $count);
        }, 'scan');
    }

    /**
     * Pass `hscan()` calls to the client with iterator reference.
     *
     * @param  string  $key
     * @param  ?int  $iterator
     * @param  ?string  $match
     * @param  int  $count
     * @return mixed
     */
    public function hscan($key, &$iterator, $match = null, $count = 0)
    {
        return $this->{$this->callback}(function () use ($key, &$iterator, $match, $count) {
            return $this->client->hscan($key, $iterator, $match, $count);
        }, 'hscan');
    }

    /**
     * Pass `sscan()` calls to the client with iterator reference.
     *
     * @param  string  $key
     * @param  ?int  $iterator
     * @param  ?string  $match
     * @param  int  $count
     * @return mixed
     */
    public function sscan($key, &$iterator, $match = null, $count = 0)
    {
        return $this->{$this->callback}(function () use ($key, &$iterator, $match, $count) {
            return $this->client->sscan($key, $iterator, $match, $count);
        }, 'sscan');
    }

    /**
     * Pass `zscan()` calls to the client with iterator reference.
     *
     * @param  string  $key
     * @param  ?int  $iterator
     * @param  ?string  $match
     * @param  int  $count
     * @return mixed
     */
    public function zscan($key, &$iterator, $match = null, $count = 0)
    {
        return $this->{$this->callback}(function () use ($key, &$iterator, $match, $count) {
            return $this->client->zscan($key, $iterator, $match, $count);
        }, 'zscan');
    }
}
