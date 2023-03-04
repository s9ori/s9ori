<?php

namespace Wptool\adminDash\controllers;

use Wptool\adminDash\services\container\ServiceContainer;

class BaseController extends \WP_REST_Controller {

	protected $container;

	/**
	 * Setting Service container and namespace for API endpoints.
	 *
	 * @param ServiceContainer $container
	 */
	public function __construct( $container ) {

		$this->container = $container;
		$this->namespace = 'hosting-admin';
	}

	/**
	 * Checks if user is authenticated.
	 *
	 * @return bool
	 */
	public function is_authenticated() {

		return is_user_logged_in();
	}
}
