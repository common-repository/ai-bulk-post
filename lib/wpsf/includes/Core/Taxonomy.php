<?php

namespace WPSF\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

use WPSF\Core\Field;

class Taxonomy {

    private static $args = array();

    function __construct() {
        // Check if in admin and meta fields exist
        if ( is_admin() && !empty( self::$args ) ) {
            // Loop through registered taxonomies
            foreach( self::$args as $taxonomy => $values ) {
                add_action( $taxonomy . '_add_form_fields', array( $this, 'create_fields' ), 10, 2 );
                add_action( $taxonomy . '_edit_form_fields', array( $this, 'edit_fields' ),  10, 2 );
                add_action( 'created_' . $taxonomy, array( $this, 'save_fields' ), 10, 1 );
                add_action( 'edited_' . $taxonomy,  array( $this, 'save_fields' ), 10, 1 );
            }
 
            // Additional actions for media fields
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        }
    }

    public static function create( $taxonomy = 'post_tag', $args = array() ){

        if ( isset( self::$args[$taxonomy] ) ) {
            wp_die( "WPSF: You used same option name twice: <code>$taxonomy</code>" );
        }
        self::$args[$taxonomy] = $args;
    }

    function admin_enqueue_scripts() {
        // Enqueue the WordPress media library script
        wp_enqueue_media();
        
        // Add inline script
        wp_add_inline_script( 'wpsf', "
            jQuery(document).ready(function($){
                // Check if wp.media is available
                if ( typeof wp.media !== 'undefined' ) {
                    // Define variables and actions for media selection
                    var _custom_media = true,
                    _orig_send_attachment = wp.media.editor.send.attachment;
                    // Click action for media selection
                    $('.wpsf-media-action').click(function(e) {
                        // Click action for media selection
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(this);
                        var id = button.attr('id').replace('_button', '');
                        _custom_media = true;
                        wp.media.editor.send.attachment = function(props, attachment){
                            if ( _custom_media ) {
                                $('input#'+id).val(attachment.id);
                                $('div#preview'+id).css('background-image', 'url('+attachment.url+')');
                            } else {
                                return _orig_send_attachment.apply( this, [props, attachment] );
                            }
                        }
                        wp.media.editor.open(button);
                        return false;
                    });
                    $('.add_media').on('click', function(){
                        _custom_media = false;
                    });
                    // Click action to remove selected media
                    $('.remove-media').on('click', function(){
                        var parent = $(this).parents('td');
                        parent.find('input[type=\"text\"]').val('');
                        parent.find('div').css('background-image', 'url()');
                    });
                }
            });
        ");
    }

	public function create_fields( $taxonomy ) {
        wp_nonce_field( 'aibp_meta_new', 'aibp_meta_new_nonce' );

		$output = '';
		foreach ( self::$args[$taxonomy] as $meta_field ) {
			$label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
            $meta_value = '';
			if ( empty( $meta_value ) ) {
				if ( isset( $meta_field['default'] ) ) {
					$meta_value = $meta_field['default'];
				}
			}
			$input = $this->generate_meta_field_input( $meta_field, $meta_value );
			$output .= '<div class="form-field">'.$this->format_rows( $label, $input ).'</div>';
		}
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_kses_post( $output );
	}

    public function edit_fields( $term, $taxonomy ) {
        wp_nonce_field( 'aibp_meta_edit', 'aibp_meta_edit_nonce' );

		$output = '';
		foreach ( self::$args[$taxonomy] as $meta_field ) {
			$label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
			$meta_value = get_term_meta( $term->term_id, $meta_field['id'], true );
			$input = $this->generate_meta_field_input( $meta_field, $meta_value );
			$output .= $this->format_rows( $label, $input );
		}
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_kses_post( '<div class="form-field">' . $output . '</div>' );
	}

    public function save_fields( $term_id ) {

        if ( !isset($_POST['aibp_meta_new_nonce']) && !isset($_POST['aibp_meta_edit_nonce']) ){
            return; 
        }
        if ( 
            !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['aibp_meta_new_nonce'] ) ),'aibp_meta_new' ) &&
            !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['aibp_meta_edit_nonce'] ) ), 'aibp_meta_edit' )
        ){
            return;
        }
        
        $taxonomy = get_term($term_id)->taxonomy;
		foreach ( self::$args[$taxonomy] as $meta_field ) {
			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
				switch ( $meta_field['type'] ) {
					case 'email':
						$_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
						break;
					case 'text':
						$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
						break;
				}
				update_term_meta( $term_id, $meta_field['id'], sanitize_text_field( $_POST[ $meta_field['id']] ) );
			} else if ( $meta_field['type'] === 'checkbox' ) {
				update_term_meta( $term_id, $meta_field['id'], '0' );
			}
		}
	}

    public function format_rows( $label, $input ) {
		return '<tr class="form-field"><th>'.$label.'</th><td>'.$input.'</td></tr>';
	}

    public function generate_meta_field_input( $meta_field, $meta_value ) {

        switch ( $meta_field['type'] ) {
            case 'media':
                $meta_url = '';
                    if ($meta_value) {
                        $meta_url = wp_get_attachment_url($meta_value);
                    }
                $input = sprintf(
                    '<input style="display:none;" id="%s" name="%s" type="text" value="%s"><div id="preview%s" style="margin-right:10px;border:2px solid #eee;display:inline-block;width: 100px;height:100px;background-image:url(%s);background-size:contain;background-repeat:no-repeat;"></div><input style="width: 19%%;margin-right:5px;" class="button wpsf-media-action" id="%s_button" name="%s_button" type="button" value="Select" /><input style="width: 19%%;" class="button remove-media" id="%s_buttonremove" name="%s_buttonremove" type="button" value="Clear" />',
                    $meta_field['id'],
                    $meta_field['id'],
                    $meta_value,
                    $meta_field['id'],
                    $meta_url,
                    $meta_field['id'],
                    $meta_field['id'],
                    $meta_field['id'],
                    $meta_field['id']
                );
                break;
            case 'checkbox':
                $input = sprintf(
                    '<input %s id=" %s" name="%s" type="checkbox" value="1">',
                    $meta_value === '1' ? 'checked' : '',
                    $meta_field['id'],
                    $meta_field['id']
                    );
                break;
            case 'radio':
                $input = '<fieldset>';
                $input .= '<legend class="screen-reader-text">' . $meta_field['label'] . '</legend>';
                $i = 0;
                foreach ( $meta_field['options'] as $key => $value ) {
                    $meta_field_value = !is_numeric( $key ) ? $key : $value;
                    $input .= sprintf(
                        '<label><input %s id=" %s" name="%s" type="radio" value="%s"> %s</label>%s',
                        $meta_value === $meta_field_value ? 'checked' : '',
                        $meta_field['id'],
                        $meta_field['id'],
                        $meta_field_value,
                        $value,
                        $i < count( $meta_field['options'] ) - 1 ? '<br>' : ''
                    );
                    $i++;
                }
                $input .= '</fieldset>';
                break;
            case 'select':
                $input = sprintf(
                    '<select id="%s" name="%s">',
                    $meta_field['id'],
                    $meta_field['id']
                );
                foreach ( $meta_field['options'] as $key => $value ) {
                    $meta_field_value = !is_numeric( $key ) ? $key : $value;
                    $input .= sprintf(
                        '<option %s value="%s">%s</option>',
                        $meta_value === $meta_field_value ? 'selected' : '',
                        $meta_field_value,
                        $value
                    );
                }
                $input .= '</select>';
                break;
            case 'textarea':
                $input = sprintf(
                    '<textarea id="%s" name="%s" rows="5">%s</textarea>',
                    $meta_field['id'],
                    $meta_field['id'],
                    $meta_value
                );
                break;
            case 'wysiwyg':
                ob_start();
                wp_editor($meta_value, $meta_field['id']);
                $input = ob_get_contents();
                ob_end_clean();
                break;
            case 'pages':
                $pagesargs = array(
                    'selected' => $meta_value,
                    'echo' => 0,
                    'name' => $meta_field['id'],
                    'id' => $meta_field['id'],
                    'show_option_none' => 'Select a page',
                );
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                $input = wp_dropdown_pages($pagesargs);
                break;
            case 'categories':
                $categoriesargs = array(
                    'selected' => $meta_value,
                    'hide_empty' => 0,
                    'echo' => 0,
                    'name' => $meta_field['id'],
                    'id' => $meta_field['id'],
                    'show_option_none' => 'Select a category',
                );
                $input = wp_dropdown_categories($categoriesargs);
                break;
            case 'users':
                $usersargs = array(
                    'selected' => $meta_value,
                    'echo' => 0,
                    'name' => $meta_field['id'],
                    'id' => $meta_field['id'],
                    'show_option_none' => 'Select a user',
                );
                $input = wp_dropdown_users($usersargs);
                break;
            default:
                $input = sprintf(
                    '<input %s id="%s" name="%s" type="%s" value="%s">',
                    $meta_field['type'] !== 'color' ? '' : '',
                    $meta_field['id'],
                    $meta_field['id'],
                    $meta_field['type'],
                    $meta_value
                );
        }

        return $input;
    }
}