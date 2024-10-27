<?php

namespace AIBP\Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class Post_Types {
    public function __construct() {
        add_action( 'init', [$this, 'aibp_events'], 0 );
    }

    /**
     * Registers a custom post type 'aibp_events'.
     *
     * @since 1.0.0
     *
     * @return void
     */
    function aibp_events() {
        register_post_type( 'aibp_events',
            [
            'labels' => [
                'name' => esc_html__( 'AIBP Events', 'ai-bulk-post' ),
            ],
            'public' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'capability_type' => 'page',
            'show_in_menu' => false,
            ]
        );
    }

}
