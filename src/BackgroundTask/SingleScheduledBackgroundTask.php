<?php
/**
 * Abstract class SingleScheduledBackgroundTask.
 *
 * @package AmpProject\AmpWP
 */

namespace AmpProject\AmpWP\BackgroundTask;

use AmpProject\AmpWP\Infrastructure\Conditional;
use AmpProject\AmpWP\Infrastructure\Registerable;
use AmpProject\AmpWP\Infrastructure\Service;

/**
 * Abstract base class for using cron to execute a background task.
 *
 * @package AmpProject\AmpWP
 * @since 2.0
 * @internal
 */
abstract class SingleScheduledBackgroundTask implements Service, Registerable, Conditional {

	/**
	 * The args passed to the schedule event callback through the specified action hook.
	 *
	 * @var array
	 */
	protected $action_hook_args = [];

	/**
	 * Class constructor.
	 *
	 * @param BackgroundTaskDeactivator $background_task_deactivator Service that deactivates background events.
	 */
	public function __construct( BackgroundTaskDeactivator $background_task_deactivator ) {
		$background_task_deactivator->add_event( $this->get_event_name() );
	}

	/**
	 * Check whether the conditional object is currently needed.
	 *
	 * @return bool Whether the conditional object is needed.
	 */
	public static function is_needed() {
		return is_admin() || wp_doing_cron();
	}

	/**
	 * Register the service with the system.
	 *
	 * @return void
	 */
	public function register() {
		add_action( $this->get_action_hook(), [ $this, 'schedule_event' ], 10, $this->get_action_hook_arg_count() );
		add_action( $this->get_event_name(), [ $this, 'process' ] );
	}

	/**
	 * Schedule the event.
	 *
	 * This does nothing if the event is already scheduled.
	 *
	 * @params array $args Arguments passed to the function from the action hook.
	 * @return void
	 */
	public function schedule_event( ...$args ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_schedule_single_event( time(), $this->get_timestamp(), $this->get_event_name(), ...$args );
	}

	/**
	 * Get the interval to use for the event.
	 *
	 * @return string An existing interval name. Valid values are 'hourly', 'twicedaily' or 'daily'.
	 */
	protected function get_timestamp() {
		return time();
	}

	/**
	 * Provides arguments to pass to the event callback.
	 *
	 * @return array Array of arguments that will be passed to the process function.
	 */
	protected function get_event_args() {
		return [];
	}

	/**
	 * The number of args expected from the action hook. Default 1.
	 *
	 * @return int
	 */
	protected function get_action_hook_arg_count() {
		return 1;
	}

	/**
	 * Gets the hook on which to schedule the event.
	 *
	 * @return string The action hook name.
	 */
	abstract protected function get_action_hook();

	/**
	 * Get the event name.
	 *
	 * This is the "slug" of the event, not the display name.
	 *
	 * Note: the event name should be prefixed to prevent naming collisions.
	 *
	 * @return string Name of the event.
	 */
	abstract protected function get_event_name();

	/**
	 * Process a single cron tick.
	 *
	 * @param mixed ...$args The args received with the action hook where the event was scheduled.
	 */
	abstract public function process( ...$args );
}