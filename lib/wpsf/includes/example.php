<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( class_exists( 'WPSF' ) ) {

    WPSF::instance()->init();
    // Create admin page: My Settings
    $prefix = 'wpsf_options';
    WPSF::createAdminPage( $prefix, [
        'page_title' => esc_html__( 'My Settings', 'wpsf' ),
        'menu_title' => esc_html__( 'My Settings', 'wpsf' ),
        'menu_slug' => 'wpsf',
        'fields' => [
            'general' => [
                'label' => esc_html__( 'General', 'wpsf' ),
                'sections' => [
                    'general' => [
                       'label' => esc_html__( 'General Settings', 'wpsf' ),
                       'fields' => [
                            [
                                'id' => 'field_one',
                                'label' => __( 'Field one', 'wpsf' ),
                                'description' => __( 'This is a demo option', 'wpsf' ),
                                'type' => 'text',
                            ],
                       ]
                    ],
                    'api' => [
                       'label' => esc_html__( 'API Keys', 'wpsf' ),
                       'fields' => [
                            [
                                'id' => 'my_api_key',
                                'label' => __( 'Framework API Key', 'wpsf' ),
                                'description' => __( 'This is a demo option', 'wpsf' ),
                                'type' => 'text',
                            ],
                       ]
                    ]
                ]
            ],
            'page_options' => [
                'label' => esc_html__( 'Page Options', 'wpsf' ),
                'sections' =>[
                    'discussion' => [
                       'label' => esc_html__( 'Discussion', 'wpsf' ),
                       'fields' => [
                            [
                                'id' => 'enable_comments',
                                'label' => esc_html__( 'Enable comments?', 'wpsf' ),
                                'description' => esc_html__( 'Enable comments on the single pages?', 'wpsf' ),
                                'type' => 'checkbox',
                            ],
                            [
                                'id' => 'page_layout',
                                'label' => esc_html__( 'Page Layout', 'wpsf' ),
                                'description' => esc_html__( 'Select page layout.', 'wpsf' ),
                                'type' => 'select',
                                'options' => [
                                    'boxed' => esc_html__( 'Boxed', 'wpsf' ),
                                    'full-width' => esc_html__( 'Full Width', 'wpsf' ),
                                ]
                            ],
                       ]
                    ]
                ]
            ]
        ]
    ] );
    
    // Create admin submenu for 'My Settings'
    $prefix = 'wpsf_advanced_options';
    WPSF::createAdminPage( $prefix, [
        'page_title' => esc_html__( 'Advanced', 'wpsf' ), 
        'menu_title' => esc_html__( 'Advanced', 'wpsf' ),
        'menu_slug' => 'my-advanced-settings',
        'parent' => 'wpsf',
        'fields' => [
            'general' => [
                'label' => esc_html__( 'Post', 'wpsf' ),
                'sections' => [
                    'general' => [
                       'label' => esc_html__( 'General Settings', 'wpsf' ),
                       'fields' => [
                            [
                                'id' => 'before_content',
                                'label' => esc_html__( 'Before Content', 'wpsf' ),
                                'description' => esc_html__( 'Add custom content before the post', 'wpsf' ),
                                'type' => 'wysiwyg',
                            ],
                            [
                                'id' => 'after_content',
                                'label' => esc_html__( 'After Content', 'wpsf' ),
                                'description' => esc_html__( 'Add custom content after the post', 'wpsf' ),
                                'type' => 'wysiwyg',
                            ],
                       ]
                    ],
                    'style' => [
                       'label' => esc_html__( 'Post Title Color', 'wpsf' ),
                       'fields' => [
                            [
                                'id' => 'post_title_color',
                                'label' => esc_html__( 'Post Title Color', 'wpsf' ),
                                'description' => esc_html__( 'Select a color', 'wpsf' ),
                                'type' => 'color',
                            ],
                       ]
                    ]
                ]
            ],
            'seo' => [
                'label' => 'SEO',
                'sections' =>[
                    'general' => [
                       'label' => esc_html__('General'),
                       'fields' => [
                            [
                                'id' => 'site_title',
                                'label' => esc_html__( 'Site Title', 'wpsf' ),
                                'description' => esc_html__( 'Set site title', 'wpsf' ),
                                'type' => 'text',
                            ],
                            [
                                'id' => 'site_description',
                                'label' => esc_html__( 'Site Description', 'wpsf' ),
                                'description' => esc_html__( 'In a few words, explain what this site is about. Example: “Just another WordPress site.”', 'wpsf' ),
                                'type' => 'textarea',
                            ],
                       ]
                    ]
                ]
            ]
        ]
    ] );
    
    // Create admin submenu for 'Tools'
    $prefix = 'wpsf_tools_option';
    WPSF::createAdminPage( $prefix, [
        'page_title' => esc_html__( 'WPSF Tools', 'wpsf' ), 
        'menu_title' => esc_html__( 'WPSF Tools', 'wpsf' ),
        'menu_slug' => 'wpsf-tools-settings',
        'parent' => 'tools.php',
        'fields' => [
            'general' => [
                'label' => esc_html__( 'Post', 'wpsf' ),
                'sections' => [
                    'general' => [
                       'label' => esc_html__( 'General Settings', 'wpsf' ),
                       'fields' => [
                            [
                                'id' => 'before_content',
                                'label' => esc_html__( 'Before Content', 'wpsf' ),
                                'description' => esc_html__( 'Add custom content before the post', 'wpsf' ),
                                'type' => 'wysiwyg',
                            ],
                            [
                                'id' => 'after_content',
                                'label' => esc_html__( 'After Content', 'wpsf' ),
                                'description' => esc_html__( 'Add custom content after the post', 'wpsf' ),
                                'type' => 'wysiwyg',
                            ],
                       ]
                    ],
                    'style' => [
                       'label' => esc_html__( 'Post Title Color', 'wpsf' ),
                       'fields' => [
                            [
                                'id' => 'post_title_color',
                                'label' => esc_html__( 'Post Title Color', 'wpsf' ),
                                'description' => esc_html__( 'Select a color', 'wpsf' ),
                                'type' => 'color',
                            ],
                       ]
                    ]
                ]
            ],
            'seo' => [
                'label' => 'SEO',
                'sections' =>[
                    'general' => [
                       'label' => esc_html__('General'),
                       'fields' => [
                            [
                                'id' => 'site_title',
                                'label' => esc_html__( 'Site Title', 'wpsf' ),
                                'description' => esc_html__( 'Set site title', 'wpsf' ),
                                'type' => 'text',
                            ],
                            [
                                'id' => 'site_description',
                                'label' => esc_html__( 'Site Description', 'wpsf' ),
                                'description' => esc_html__( 'In a few words, explain what this site is about. Example: “Just another WordPress site.”', 'wpsf' ),
                                'type' => 'textarea',
                            ],
                       ]
                    ]
                ]
            ]
        ]
    ] );
    
    // Create Taxonomy custom fields for Post Tags
    WPSF::createTaxonomyFields( 'post_tag', [
        [
            'label' => esc_html__( 'Text Field', 'wpsf' ),
            'id' => 'text_field',
            'type' => 'text',
        ],
        [
            'label' => esc_html__( 'Checkbox', 'wpsf' ),
            'id' => 'checkbox_1',
            'type' => 'checkbox',
        ],
        [
            'label' => esc_html__( 'Media', 'wpsf' ),
            'id' => 'media',
            'type' => 'media',
        ],
    ] );
}


add_action('wp_footer',function(){
    // How can you get the Option datas?
    $prefix = 'wpsf_options';
    $options = get_option( $prefix );
    $field_one =  $options['field_one']; // print this if you want
    
    // How can you get the Taxonomy datas?
    // Get the current term/tag ID
    $term_id = 25; // Replace this with the actual term/tag ID

    // Get the field data for 'text_field'
    $text_field_value = get_term_meta($term_id, 'text_field', true);

    // Get the field data for 'checkbox_1'
    $checkbox_value = get_term_meta($term_id, 'checkbox_1', true);

    // Get the field data for 'media' (assuming it's an attachment ID)
    $media_id = get_term_meta($term_id, 'media', true);
    $media_url = wp_get_attachment_url($media_id);
});

