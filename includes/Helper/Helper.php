<?php

namespace AIBP\Helper;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class Helper {
    public static function get_events_table() {
        ?>

        <table class="table widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Title', 'ai-bulk-post' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'ai-bulk-post' ); ?></th>
                    <th><?php esc_html_e( 'AI', 'ai-bulk-post' ); ?></th>
                    <th><?php esc_html_e( 'Recurrence', 'ai-bulk-post' ); ?></th>
                    <th><?php esc_html_e( 'Next Run', 'ai-bulk-post' ); ?></th>
                    <th><?php esc_html_e( 'Last Run', 'ai-bulk-post' ); ?></th>
                    <th><?php esc_html_e( 'Total Run', 'ai-bulk-post' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $posts = get_posts( [
                        'post_type' => 'aibp_events',
                        'posts_per_page' => -1
                    ] );
                    
                    foreach( $posts as $post ) {

                        $actions = sprintf(
                            '<div class="row-actions">
                                <span class="0"><a href="#" class="aibp-action-edit">%1$s</a></span> |
                                <span class="1"><a href="#" class="aibp-action-status">%2$s</a></span> |
                                <span class="delete"><a href="#" class="aibp-action-delete">%3$s</a></span>
                            </div>',
                            esc_html__( 'Edit', 'ai-bulk-post' ),
                            esc_html__( 'Change Status', 'ai-bulk-post' ),
                            esc_html__( 'Delete', 'ai-bulk-post' ),
                        );

                        printf( 
                            '<tr data-post-id="%9$s">
                                <td class="aibp-column-title">%1$s</td>
                                <td class="aibp-column-status">%2$s</td>
                                <td class="aibp-column-ai">%3$s<br><small>%4$s<small></td>
                                <td class="aibp-column-recurrence">%5$s</td>
                                <td class="aibp-column-next-run">%6$s</td>
                                <td class="aibp-column-last-run">%7$s</td>
                                <td class="aibp-column-total-run">%8$s</td>
                            </tr>',
                            esc_html( $post->post_title ) . wp_kses_post( $actions ),
                            esc_html( get_post_meta( $post->ID, 'aibp_status', true ) ),
                            esc_html( get_post_meta( $post->ID, 'aibp_ai', true ) ),
                            esc_html( get_post_meta( $post->ID, 'aibp_model', true ) ),
                            esc_html( get_post_meta( $post->ID, 'aibp_recurrence', true ) ),
                            wp_kses_post( Helper::get_schedule_event_next_run_date( $post->ID ) ),
                            wp_kses_post( Helper::get_schedule_event_last_run_date( $post->ID ) ),
                            !empty( get_post_meta( $post->ID, 'aibp_count', true ) ) ? esc_html( get_post_meta( $post->ID, 'aibp_count', true ) ) : '0',
                            esc_attr( $post->ID )
                        );
                    }

                ?>
            </tbody>
        </table>
        <?php
    }

    public static function esc_attrs( $attrs ) {
        $parsedAttrs = "";
      
        if ( ! is_array( $attrs ) ) {
            return $parsedAttrs;
        }
      
        foreach ( $attrs as $attrName => $attrValue ) {
            if ( empty( $attrValue ) || in_array( $attrName, [ 'options', 'selected' ] ) ) {
                continue;
            }
            $parsedAttrs .= " $attrName=\"" . esc_attr( $attrValue ) . "\"";
        }

        return trim($parsedAttrs);
    }
    
    public static function print_esc_attrs( $attrs ) {
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        echo self::esc_attrs( $attrs );
    }

    public static function array_to_dropdown( $args = array() ) {

        $defaults = array(
            'name' => '',
            'id' => '',
            'class' => '',
            'selected' => '',
            'options' => array(),
        );
    
        $args = wp_parse_args( $args, $defaults );

        ?>
            <select <?php self::print_esc_attrs( $args ) ?>>
                <?php 
                    foreach ( $args['options'] as $value => $text ) {
                        printf( 
                            '<option value="%1$s" %3$s>%2$s</option>',
                            esc_attr( $value ),
                            esc_html( $text ),
                            selected( $value, $args['selected'] ),
                        );
                    }
                ?>
            </select>
        <?php

    }

    public static function get_wp_get_schedules() {
        $schedules = array();
        foreach( wp_get_schedules() as $recurrence => $data ) {
            $schedules[$recurrence] = $data['display'];
        }
    
        return $schedules;
    }

    public static function get_schedule_event_next_run_date( $post_id ) {

        $event_hook = "aibp_event_$post_id";
        // Get the next scheduled event
        $next_event = wp_get_scheduled_event($event_hook, array( $post_id ));
        if ($next_event) {
            // Get the timestamp of the next run
            $next_run_timestamp = $next_event->timestamp;
    
            // Convert the timestamp to a readable date and time format
            $next_run_date_time = gmdate('Y-m-d H:i:s', $next_run_timestamp);
            $next_run_date_utc = gmdate( 'c', $next_run_timestamp );
    
            // Calculate remaining time in seconds
            $remaining_time_seconds = $next_run_timestamp - time();
    
            // Convert remaining time to minutes and seconds
            $remaining_minutes = floor($remaining_time_seconds / 60);
            $remaining_seconds = $remaining_time_seconds % 60;
    
            // Format the remaining time
            $formatted_remaining_time = sprintf( 
                // translators: %1$s: minutes, %2$s seconds
                __('%1$s minutes %2$s seconds', 'ai-bulk-post'),
                $remaining_minutes,
                $remaining_seconds
            );

            $time = sprintf(
                '<time datetime="%1$s">%2$s<br><small>%3$s</small></time>',
                esc_attr( $next_run_date_utc ),
                esc_html( $next_run_date_time ),
                esc_html( $formatted_remaining_time )
            );
    
            return $time;
        } else {
            return 'N/A';
        }
    }

    public static function get_schedule_event_last_run_date( $post_id ) {

        $date = get_post_meta( $post_id, 'aibp_last_run', true );

        if ( empty( $date ) ) {
            return 'N\A';
        }

        $time = sprintf(
            '<time datetime="%1$s">%2$s<br><small>%3$s</small></time>',
            esc_attr( gmdate( 'c', strtotime( $date ) ) ),
            esc_html( $date ),
            sprintf(
                // translators: %s: time
                esc_html__( '%s ago', 'ai-bulk-post' ),
                human_time_diff( strtotime( $date ), current_time( 'timestamp' ) )
            )
        );

        return $time;

    }

    public static function get_category_checklist( $args = array() ) {

        $defaults = array(
            'name' => 'post_categories',
            'id' => '',
            'class' => '',
            'selected' => '',
            'options' => array(),
            'echo' => false,
            'selected' => array()
        );
    
        $args = wp_parse_args( $args, $defaults );

        $categories = get_categories( [
            'post_type' => 'post',
            'hide_empty' => false,
        ] );

        if ( ! empty( $categories ) ) {
            $output .= '<ul>';
            foreach ( $categories as $category ) {
                $output .= sprintf(
                    '<li><label class="checklist"><input type="checkbox" name="%1$s[]" value="%2$s" %4$s>%3$s</label></li>',
                    esc_attr( $args['name'] ),
                    esc_attr( $category->term_id ),
                    esc_html( $category->name ),
                    esc_attr( in_array( $category->term_id, $args['selected'] ) ? 'checked' : '' )
                );
            }
            $output .= '</ul>';

            $allowed_html = array(
                'input' => array(
                    'type' => array(),
                    'name' => array(),
                    'value' => array(),
                    'checked' => array(),
                ),
                'label' => array(
                    'class' => array(),
                ),
                'li' => array(),
                'ul' => array(),
            );
        
            if ( $args['echo'] ) {
                echo wp_kses( $output, $allowed_html ); // Sanitize the output with the custom list of allowed HTML tags
                return;
            }

            return $output;
        }
    }

    public static function is_chat_model( $model ) {
        if ( $model == 'gpt-3.5-turbo-instruct' ) {
            return false;
        }
        return true;
    }

    public static function get_post_types() {
        $post_types = get_post_types( array( 'public' => true, '_builtin' => false), 'objects' );

        $post_types_array = array( 'post' => esc_html__( 'Posts', 'ai-bulk-post' ) );
        foreach ( $post_types as $post_type ) {
            $post_types_array[ $post_type->name ] = $post_type->labels->name;
        }

        return $post_types_array;
    }

    public static function insert_post( $args = array() ) {
        $defaults = array(
            'post_type' => 'post',
            'post_status' => 'publish',
        );
        
        $args = wp_parse_args( $args, $defaults );

        $post_id = wp_insert_post( $args );

        if ( is_wp_error( $post_id ) ) {
            return new WP_Error( 'error', $post_id->get_error_message() );
        } 
    }

    public static function remove_quote( $str ) {
        $length = strlen( $str );
        if ( $length >= 2 && ( $str[0] === '"' && $str[$length - 1] === '"' ) ) {
            return substr( $str, 1, -1 );
        } else {
            return $str;
        }
    }
    
}