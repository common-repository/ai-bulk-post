<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

final class AIBP {

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var AIBP The single instance of the class.
	 */
	private static mixed $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return AIBP An instance of the class.
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 */
	public static function instance(): AIBP {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n(): void {
		load_plugin_textdomain( 'ai-bulk-post' );
	}

	/**
	 * On Plugins Loaded
	 *
	 * Checks the plugin has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_plugins_loaded(): void {
		if ( $this->is_compatible() ) {
			$this->init();
		}
	}

	/**
	 * Compatibility Checks
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function is_compatible(): bool {
		return true;
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the files required to run the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init(): void {
		do_action( 'aibp_before_init' );
		$this->i18n();
		$this->include_files();
		do_action( 'aibp_loaded' );
	}

	public function include_files(): void {

		require AIBP_PATH . '/vendor/autoload.php';
		require AIBP_PATH . '/lib/wpsf/wpsf.php';

		new \AIBP\Admin\Admin;
		new \AIBP\Admin\Admin_Page_Dashboard;
		new \AIBP\Admin\Admin_Page_Settings;
		new \AIBP\Post_Types\Post_Types;
		new \AIBP\Helper\Ajax;
		new \AIBP\AI\AI;

	}
}

AIBP::instance();

function aibp_plugin_activate(): void {
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'aibp_plugin_activate' );

function aibp_plugin_deactivate(): void {
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'aibp_plugin_deactivate' );
