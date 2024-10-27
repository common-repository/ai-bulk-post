<?php

namespace AIBP\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

use AIBP\Admin\Admin_Page;
use WPSF;

class Admin_Page_Settings {

    public function __construct() {

        if ( class_exists( 'WPSF' ) ) {

            // load framework
            WPSF::instance()->init();
            
            // Create admin submenu for 'My Settings'
            $prefix = 'aibp_settings';
            WPSF::createAdminPage( $prefix, [
                'page_title' => esc_html__( 'Settings', 'ai-bulk-post' ), 
                'menu_title' => esc_html__( 'Settings', 'ai-bulk-post' ),
                'menu_slug' => 'aibp_settings',
                'parent' => 'aibp',
                'fields' => [
                    'general' => [
                        'label' => esc_html__( 'General', 'ai-bulk-post' ),
                        'sections' => [
                            'api' => [
                                'label' => esc_html__( 'API Keys', 'ai-bulk-post' ),
                                'fields' => [
                                    [
                                        'id' => 'openai_api_key',
                                        'label' => esc_html__( 'OpenAI API Key', 'ai-bulk-post' ),
                                        'description' => sprintf( 
                                            // translators: %s is a placeholder for the API key page URL.
                                            __( 'You can find your Secret API key on the <a href="%s" target="_blank">API key page.</a>', 'ai-bulk-post' ),
                                            esc_url( 'https://platform.openai.com/api-keys' )
                                        ),
                                        'type' => 'text',
                                    ],
                                ]
                            ]
                        ]
                    ],
                ]
            ] );
        }
        
    }
}
