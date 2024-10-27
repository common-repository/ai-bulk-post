<?php

namespace AIBP\AI;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

use Orhanerday\OpenAi\OpenAi;
use AIBP\Helper\Helper;

class AI {

    function __construct() {
    }

    public static function generate_post( $post_id = null, $args = array() ) {

        $options = get_option( 'aibp_settings' );
        $api_key = $options['openai_api_key'] ?? '';

        $open_ai = new OpenAi( $api_key );

        $defaults = [
            'model' => get_post_meta( $post_id, 'aibp_model', true ),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => '',
                ],
            ],
            'temperature' => 1.0,
            'max_tokens' => 4000,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ];
    
        $args = wp_parse_args( $args, $defaults );

        $prompts = [
            'post_title' => get_post_meta( $post_id, 'aibp_prompt_title', true ),
            'post_content' => get_post_meta( $post_id, 'aibp_prompt_content', true ),
        ];
        $subject = get_post_meta( $post_id, 'aibp_subject', true );

        $results = [];
        foreach ( $prompts as $prompt_key => $prompt ) {

            $prompt = str_replace( '[subject]', $subject, $prompt );

            if ( Helper::is_chat_model( $args['model'] ) ) {
                $args['messages'][0]['content'] = $prompt;
                $response = $open_ai->chat( $args );
                $response = json_decode( $response );
                $response = trim( $response->choices[0]->message->content );
            } else {
                $args['prompt'] = $prompt;
                unset($args['messages']);
                $response = $open_ai->completion( $args );
                $response = json_decode( $response );
                $response = trim( $response->choices[0]->text );
            }

            if ( $prompt_key == 'post_title' ) {
                $response = Helper::remove_quote( $response );
            }

            $results[$prompt_key] = $response;
        }

        $post_category = (array) get_post_meta( $post_id, 'aibp_post_categories', true );
        $post_category = array_map( 'intval', $post_category );

        $insert_post_args = [
            'post_author' => get_post_field( 'post_author', $post_id ),
            'post_category' => $post_category
        ];
        $insert_post_args = array_merge( $results, $insert_post_args );

        Helper::insert_post( $insert_post_args );
        // update_post_meta( $post_id, 'aibp_debug', $results );
        
    }
}
