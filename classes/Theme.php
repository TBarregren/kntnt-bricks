<?php

namespace Kntnt\Bricks;

final class Theme extends AbstractTheme {

	use Logger;

	/**
	 * Makes an object of this class run.
	 */
	protected function run() {
		add_action( 'after_setup_theme', [ $this, 'allow_breakpoints_to_be_altered' ] );
	}

	/**
	 * Returns an associative array of Bricks breakpoints.
	 *
	 * @return array Bricks breakpoints. The keys reflect the target device (e.g. `mobile_landscape`) and the
	 *     corresponding value that is value is the largest width in pixels that is allowed for that device.
	 */
	public function get_breakpoints() {
		return \Bricks\Theme::instance()->setup::$breakpoints;
	}

	/**
	 * Provides the filter `kntnt-bricks-breakpoints` for altering
	 * the breakpoints.
	 *
	 * @see get_breakpoints()
	 */
	public function allow_breakpoints_to_be_altered() {
		$breakpoints =& \Bricks\Theme::instance()->setup::$breakpoints;
		$breakpoints = apply_filters( 'kntnt-bricks-breakpoints', $breakpoints );
		Theme::debug( 'Breakpoints: %s', $breakpoints );
	}

}