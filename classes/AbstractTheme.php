<?php


namespace Kntnt\Bricks;


abstract class AbstractTheme {

	private static $ns;

	private static $version;

	private static $dir;

	private static $url;

	private static $is_debugging;

	public function __construct() {

		// This theme's machine name a.k.a. slug.
		self::$ns = strtr( strtolower( __NAMESPACE__ ), '_\\', '--' );

		// This theme's version number.
		self::$version = wp_get_theme()->Version;

		// Path to this theme's directory.
		self::$dir = get_stylesheet_directory();

		// URL to this theme's directory.
		self::$url = get_stylesheet_directory_uri();

		// Enqueue this theme's styles and scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 50 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 50 );

		// Add pre-load links to header
		add_action( 'wp_head', [ $this, 'add_pre_load_links' ] );

		// Run theme specific code
		$this->run();

		// Add custom functions.
		if ( file_exists( self::$dir . '/custom/functions.php' ) ) {
			Theme::debug( 'Loading %s', self::$dir . '/custom/functions.php' );
			include self::$dir . '/custom/functions.php';
		}

	}

	protected abstract function run();

	public static final function ns(): string {
		return self::$ns;
	}

	public static final function version(): string {
		return self::$version;
	}

	public static final function dir(): string {
		return self::$dir;
	}

	public static final function url(): string {
		return self::$url;
	}

	public function enqueue_styles() {

		// Enqueue required styles.
		foreach ( $this->required_styles() as $style => $deps ) {
			$url = self::$url . "/css/$style";
			wp_enqueue_style( self::$ns . "-$style", $url, $deps, self::$version, 'all' );
			Theme::is_using( 'Logger' ) && Theme::debug( 'Added stylesheet: %s', $url );
		}

		// Enqueue font style if it exists.
		if ( file_exists( self::$dir . "/custom/fonts.css" ) ) {
			$url = self::$url . '/custom/fonts.css';
			wp_enqueue_style( self::$ns . '-custom-fonts.css', $url, [], self::$version, 'all' );
			Theme::is_using( 'Logger' ) && Theme::debug( 'Added stylesheet: %s', $url );
		}

		// Enqueue custom style if it exists.
		if ( file_exists( self::$dir . "/custom/style.css" ) ) {
			$url = self::$url . '/custom/style.css';
			wp_enqueue_style( self::$ns . '-custom-style.css', $url, $this->custom_style_dependencies(), self::$version, 'all' );
			Theme::is_using( 'Logger' ) && Theme::debug( 'Added stylesheet: %s', $url );
		}

	}

	public function enqueue_scripts() {

		// Enqueue required scripts.
		foreach ( $this->required_scripts() as $script => $deps ) {
			$url = self::$url . "/js/$script";
			wp_enqueue_script( self::$ns . "-$script", $url, $deps, self::$version, true );
			Theme::is_using( 'Logger' ) && Theme::debug( 'Added script: %s', $url );
		}

		// Enqueue custom script if it exists.
		if ( file_exists( self::$dir . "/custom/script.js" ) ) {
			$url = self::$url . '/custom/script.js';
			wp_enqueue_script( self::$ns . '-custom-script.js', $url, $this->custom_script_dependencies(), self::$version, true );
			Theme::is_using( 'Logger' ) && Theme::debug( 'Added script: %s', $url );
		}

	}

	public function add_pre_load_links() {
		$font_files = apply_filters( 'kntnt-bricks-preload-fonts', [] );
		foreach ( $font_files as $font_file ) {
			$html = '<link rel="preload" as="font" href="' . esc_url( $font_file ) . '" type="font/' . pathinfo( $font_file, PATHINFO_EXTENSION ) . '" crossorigin="anonymous">';
			echo $html;
			Theme::debug( "Added to header: %s", $html );
		}
	}

	// Returns true if this code is using the $trait.
	public static final function is_using( $trait ) {
		static $traits = null;
		if ( null === $traits ) {
			$traits = class_uses( static::class );
		}
		return isset( $traits[ __NAMESPACE__ . "\\$trait" ] );
	}

	// Returns true if the debug flag is set. The debug flag is a constant with
	// the plugin's namespace with `/` replaced with `_` and all letters in
	// uppercase and ending with _DEBUG.
	public static final function is_debugging() {
		if ( null == self::$is_debugging ) {
			$kntnt_debug = strtr( strtoupper( self::$ns ), '-', '_' ) . '_DEBUG';
			self::$is_debugging = defined( 'WP_DEBUG' ) && constant( 'WP_DEBUG' ) && defined( $kntnt_debug ) && constant( $kntnt_debug );
		}
		return self::$is_debugging;
	}

	protected function required_styles(): array {
		return [];
	}

	protected function required_scripts(): array {
		return [];
	}

	protected function custom_style_dependencies(): array {
		return [];
	}

	protected function custom_script_dependencies(): array {
		return [];
	}

}