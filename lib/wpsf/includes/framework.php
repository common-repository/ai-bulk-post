<?php 

final class WPSF {

    /**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var WPSF The single instance of the class.
	 */
	private static $_instance = null;

    /**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return WPSF An instance of the class.
	 */
	public static function instance() {

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
	public function i18n() {

		load_plugin_textdomain( 'wpsf' );

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
	public function on_plugins_loaded() {

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
	public function is_compatible() {

        // Add any rule for compatible
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
    public function init() {

        $this->i18n();
		$this->hooks();

    } 

	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'init', [$this,'include_files'] );
	}

	function admin_enqueue_scripts() {
		wp_enqueue_script( 'wpsf', WPSF_URL . 'assets/js/wpsf.js', [], WPSF_VERSION, false );
		wp_enqueue_style( 'wpsf', WPSF_URL . 'assets/css/wpsf.css', [], WPSF_VERSION );
	}

    public function include_files() {
		new \WPSF\Core\Admin_Page;
		new \WPSF\Core\Taxonomy;
    }

	public static function createAdminPage( $prefix, $args ) {
		\WPSF\Core\Admin_Page::create( $prefix, $args );
	}

	public static function createTaxonomyFields( $prefix, $args ) {
		\WPSF\Core\Taxonomy::create( $prefix, $args );
	}
    
}