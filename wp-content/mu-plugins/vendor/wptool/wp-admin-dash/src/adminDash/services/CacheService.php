<?php

namespace Wptool\adminDash\services;

use \WPaaS\Cache;
use Wptool\adminDash\constants\PageConstants;
use Wptool\adminDash\utils\QueryHelpers;

class CacheService {

	/**
	 * Gets cache link.
	 *
	 * @return string|null
	 */
	public function get_flush_cache_url() {

		if ( class_exists( Cache::class ) && method_exists( Cache::class, 'get_flush_url' ) ) {
			$url = Cache::get_flush_url();
			$url = str_replace( '&#038;', '&', $url );

			$parts = parse_url( $url );

			$query_args = QueryHelpers::filter_query_args( $parts['query'] );

			return get_site_url() . '/wp-admin/admin.php?page=' . PageConstants::PAGE_NAME . '&' . $query_args;
		}

		return null;
	}

}

