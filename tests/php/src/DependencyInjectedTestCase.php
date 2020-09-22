<?php

namespace AmpProject\AmpWP\Tests;

use AmpProject\AmpWP\AmpWpPlugin;
use AmpProject\AmpWP\Infrastructure\Injector;
use AmpProject\AmpWP\Infrastructure\ServiceContainer;
use AmpProject\AmpWP\Services;
use AmpProject\AmpWP\Tests\Helpers\PrivateAccess;
use WP_UnitTestCase;

abstract class DependencyInjectedTestCase extends WP_UnitTestCase {

	use PrivateAccess;

	/**
	 * Plugin instance to test with.
	 *
	 * @var AmpWpPlugin
	 */
	protected $plugin;

	/**
	 * Service container instance to test with.
	 *
	 * @var ServiceContainer
	 */
	protected $container;

	/**
	 * Injector instance to test with.
	 *
	 * @var Injector
	 */
	protected $injector;

	/**
	 * Runs the routine before each test is executed.
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = new AmpWpPlugin();
		$this->plugin->register();

		$this->container = $this->plugin->get_container();
		$this->injector  = $this->container->get( 'injector' );

		// The static Services helper has to be modified to use the same objects
		// as the ones that are injected into the tests.
		$this->set_private_property( Services::class, 'plugin', $this->plugin );
		$this->set_private_property( Services::class, 'container', $this->container );
		$this->set_private_property( Services::class, 'injector', $this->injector );
	}
}
