<?php

namespace AIBP\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

use AIBP\AI\AI;

class Admin {
    function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts'] );
        add_action( 'init', [$this, 'aibp_schedule_events'] );
        add_action( 'delete_post', [$this, 'after_delete_post'] );
    }

    function admin_enqueue_scripts( $hook ) {
        wp_enqueue_script( 'ai-bulk-post', AIBP_URL . 'dist/ai-bulk-post.js', ['jquery'], AIBP_VERSION, true);
        wp_enqueue_style( 'ai-bulk-post', AIBP_URL . 'dist/ai-bulk-post.css', [], AIBP_VERSION );
        wp_localize_script( 'ai-bulk-post', 'AIBulkPost', [
            'i18n' => [],
            'nonce' => wp_create_nonce( 'aibp-nonce' )
        ] );
    }

    function aibp_schedule_events() {
        $args = array(
            'post_type' => 'aibp_events',
            'posts_per_page' => -1
        );
        $posts = get_posts($args);

        foreach ($posts as $post) {
            $recurrence = get_post_meta($post->ID, 'aibp_recurrence', true);
            $event_hook = 'aibp_event_' . $post->ID;

            if (!wp_next_scheduled($event_hook, array($post->ID))) {
                wp_clear_scheduled_hook($event_hook, array($post->ID));

                if (!wp_next_scheduled($event_hook, array($post->ID))) {
                    wp_schedule_event(time(), $recurrence, $event_hook, array($post->ID));
                }
            }

            add_action($event_hook, [$this, 'aibp_handle_event']);
        }
    }

    function aibp_handle_event($post_id) {

        $status = get_post_meta($post_id, 'aibp_status', true);
        if ( $status != 'active' ) {
            return;
        }
        $count = (int) get_post_meta($post_id, 'aibp_count', true);
        update_post_meta($post_id, 'aibp_count', ++$count);
        update_post_meta($post_id, 'aibp_last_run', gmdate('Y-m-d H:i:s', current_time( 'timestamp' )));
        AI::generate_post( $post_id, $args = array() );
    }
    
    function aibp_clear_scheduled_events() {
        $posts = get_posts(array('post_type' => 'aibp_events', 'posts_per_page' => -1));
        foreach ($posts as $post) {
            $event_hook = 'aibp_event_' . $post->ID;
            wp_clear_scheduled_hook($event_hook, array($post->ID));
        }
    }

    function after_delete_post( $post_id ) {
        if ( get_post_type( $post_id ) == 'aibp_events' ) {
            $event_hook = 'aibp_event_' . $post_id;
            wp_clear_scheduled_hook($event_hook, array($post_id));
        }
    }

}
