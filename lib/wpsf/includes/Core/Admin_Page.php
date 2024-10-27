<?php

namespace WPSF\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

use WPSF\Core\Field;

class Admin_Page {

    private static $capability = 'manage_options';
    private static $args = array();
    private static $page_id;

    function __construct() {
        add_action( 'admin_init', [ $this, 'settings_init' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    public static function create( $prefix = 'wpsf', $args = array() ){

        $defaults = array(
            'page_title' => 'WPSF',
            'menu_title' => 'WPSF',
            'capability' => self::$capability,
            'menu_slug' => 'wpsf',
            'callback' => ['\WPSF\Core\Admin_Page', 'render_page'],
            'icon' => 'dashicons-admin-settings',
            'position' => 2,
        );
    
        $args = wp_parse_args( $args, $defaults );

        if ( isset( self::$args[$prefix] ) ) {
            wp_die( "WPSF: You used same option name twice: <code>$prefix</code>" );
        }
        self::$args[$prefix] = $args;
    }

    function admin_menu() {

        foreach ( self::$args as $args ) {
            $parent = $args['parent'] ?? '';
            if ( !$parent ) {
                add_menu_page(
                    $args['page_title'],
                    $args['menu_title'],
                    $args['capability'],
                    $args['menu_slug'],
                    $args['callback'],
                    $args['icon'],
                    $args['position']
                );
            } else {
                add_submenu_page(
                    $parent,
                    $args['page_title'],
                    $args['menu_title'],
                    $args['capability'],
                    $args['menu_slug'],
                    $args['callback'],
                );
            }
        }
    }

    function settings_init() {
        
		// Register a new setting this page.
        foreach ( self::$args as $option_key => $settings ) { 

			$page_id = 'wpsf_' . $settings['menu_slug'];

			register_setting( $page_id, $option_key );

            $fields = $settings['fields'] ?? array();

            foreach( $fields as $tab_id => $tab ) {

				$option_group = $page_id .'_'. $tab_id;

                /* Register All The Fields. */
                foreach( $tab['sections'] as $section_id => $section ) {

                    $full_section_id = 'wpsf_' . $section_id . '_section';
    
                    // Register a new section.
                    add_settings_section(
                        $full_section_id,
                        $section['label'],
                        [$this, 'render_section'],
                        $option_group
                    );
    
                    foreach ( $section['fields'] as $field ) {
                        // Register a new field in the main section.
                        add_settings_field(
                            $field['id'], /* ID for the field. Only used internally. To set the HTML ID attribute, use $args['label_for']. */
                            $field['label'], /* Label for the field. */
                            [$this, 'render_field'], /* The name of the callback function. */
                            $option_group, /* The menu page on which to display this field. */
                            $full_section_id, /* The section of the settings page in which to show the box. */
                            [
                                'label_for' => $field['id'], /* The ID of the field. */
                                'class' => 'custom_class', /* The class of the field. */
                                'field' => $field, /* Custom data for the field. */
                                'option_key' => $option_key,
                            ]
                        );
                    }
    
                }
    
            }
        }
	}

    static function render_page() {

		// check user capabilities
		if ( ! current_user_can( self::$capability ) ) {
			return;
		}

        self::$page_id = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;

		/**
		 * add error/update messages
		 * check if the user have submitted the settings
		 * WordPress will add the "settings-updated" $_GET parameter to the url
		 */
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'wpsf_messages', 'wpsf_message', __( 'Settings Saved', 'wpsf' ), 'updated' );
		}

		// show error/update messages
		settings_errors( 'wpsf_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <div id="wpsf-settings-tabs-wrapper" class="nav-tab-wrapper">
				<?php
                foreach ( self::$args as $settings ) {
                    if ( $settings['menu_slug'] !== self::$page_id ) {
                        continue;
                    }
                    $fields = $settings['fields'] ?? array();
					$first = true;
                    foreach ( $fields as $tab_id => $tab ) {
    
                        $active_class = '';
  
                        if ( $first ) {
                            $active_class = ' nav-tab-active';
							$first = false;
                        }
    
                        $sanitized_tab_id = esc_attr( $tab_id );
                        $sanitized_tab_label = esc_html( $tab['label'] );
    
                        // PHPCS - Escaped the relevant strings above.
                        echo "<a id='wpsf-settings-tab-{$sanitized_tab_id}' class='nav-tab{$active_class}' href='#tab-{$sanitized_tab_id}'>{$sanitized_tab_label}</a>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                }
				?>
			</div>

			<form action="options.php" method="post">
				<?php
				
                foreach ( self::$args as $settings ) {

					if ( $settings['menu_slug'] !== self::$page_id ) {
                        continue;
                    }

					$page_id = 'wpsf_' . $settings['menu_slug'];

					settings_fields( $page_id );
					$first = true;
                    $fields = $settings['fields'] ?? array();
                    foreach ( $fields as $tab_id => $tab ) {

						$option_group = $page_id .'_'. $tab_id;
                        $active_class = '';
    
                        if ( $first ) {
                            $active_class = ' active';
							$first = false;
                        }
    
                        $sanitized_tab_id = esc_attr( $tab_id );
    
                        // PHPCS - $active_class is a non-dynamic string and $sanitized_tab_id is escaped above.
                        echo "<div id='tab-{$sanitized_tab_id}' class='wpsf-settings-form-page{$active_class}'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        foreach ( $tab['sections'] as $section_id => $section ) {
                            $full_section_id = 'wpsf_' . $section_id . '_section';
    
                            if ( ! empty( $section['label'] ) ) {
                                echo '<h2>' . esc_html( $section['label'] ) . '</h2>';
                            }
    
                            if ( ! empty( $section['callback'] ) ) {
                                $section['callback']();
                            }
    
                            echo '<table class="form-table">';
                            do_settings_fields( $option_group, $full_section_id );
    
                            echo '</table>';
                        }
    
                        echo '</div>';
                    }
                }

                submit_button();
				?>
			</form>
		</div>
		<?php
	}

	static function render_field( array $args ) : void {
		$field = $args['field'];
        $option_key = $args['option_key'];

		// Get the value of the setting we've registered with register_setting()
		$options = get_option( $option_key );

        $function = $field['type'];
        
        Field::$function( $field, $options, $option_key );
	}

}
