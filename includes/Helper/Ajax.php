<?php

namespace AIBP\Helper;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class Ajax {
    function __construct() {
        add_action( 'wp_ajax_aibp/get/add_events_form', [$this, 'add_events_form'] );
        add_action( 'wp_ajax_aibp/add/event', [$this, 'add_event'] );
        add_action( 'wp_ajax_aibp/update/event', [$this, 'update_event'] );
        add_action( 'wp_ajax_aibp/delete/event', [$this, 'delete_event'] );
        add_action( 'wp_ajax_aibp/update/event/status', [$this, 'update_event_status'] );
    }

    function add_events_form() {

        check_ajax_referer( 'aibp-nonce', 'nonce' );

        // Default form values
        $defaults = [
            'title' => '',
            'ai' => '',
            'model' => '',
            'subject' => '',
            'length' => 300,
            'prompt_title' => 'Write one blog post title about [subject].',
            'prompt_content' => 'Write blog post about [subject]. Use html characters and subheadings. And content length should be [length] words.',
            'recurrence' => 'hourly',
            'categories' => array(),
            'publish_date' => 'directly',
        ];

        $args = [];
        // Check 
        if ( $_POST['post_id'] ){
            $post_id = intval( $_POST['post_id'] );
            $args = [
                'title' => get_the_title( $post_id ),
                'ai' => get_post_meta( $post_id, 'aibp_ai', true ),
                'model' => get_post_meta( $post_id, 'aibp_model', true ),
                'subject' => get_post_meta( $post_id, 'aibp_subject', true ),
                'length' => get_post_meta( $post_id, 'aibp_length', true ),
                'prompt_title' => get_post_meta( $post_id, 'aibp_prompt_title', true ),
                'prompt_content' => get_post_meta( $post_id, 'aibp_prompt_content', true ),
                'recurrence' => get_post_meta( $post_id, 'aibp_recurrence', true ),
                'categories' => get_post_meta( $post_id, 'aibp_post_categories', true ),
                'publish_date' => get_post_meta( $post_id, 'aibp_post_publish_date', true ),
            ];
        }
        
        $args = wp_parse_args( $args, $defaults );

        ob_start();
        ?>
            <form class="ai-bulk-post-form">

                <h3><?php esc_html_e( 'General', 'ai-bulk-post' ); ?></h3>

                <div class="form-field">
                    <label for="title"><?php esc_html_e( 'Title', 'ai-bulk-post' ); ?></label>
                    <input type="text" name="title" placeholder="<?php esc_attr_e( 'Title', 'ai-bulk-post' ); ?>" value="<?php echo esc_attr( $args['title'] ); ?>" required>
                </div>

                <div class="form-field">
                    <label for="ai"><?php esc_html_e( 'AI', 'ai-bulk-post' ); ?></label>
                    <select name="ai" id="ai">
                        <option <?php selected( $args['ai'], 'openai' ); ?> value="openai"><?php esc_html_e( 'OpenAI', 'ai-bulk-post' ); ?></option>
                        <option <?php selected( $args['ai'], 'gemini' ); ?> value="gemini" disabled><?php esc_html_e( 'Google Gemini', 'ai-bulk-post' ); ?></option>
                    </select>
                </div>

                <div class="form-field">
                    <label for="model"><?php esc_html_e( 'Model', 'ai-bulk-post' ); ?></label>
                    <select name="model" id="model">
                        <option class="openai" <?php selected( $args['model'], 'gpt-3.5-turbo-instruct' ); ?> value="gpt-3.5-turbo-instruct"><?php esc_html_e( 'gpt-3.5-turbo-instruct', 'ai-bulk-post' ); ?></option>
                        <option class="openai" <?php selected( $args['model'], 'gpt-3.5-turbo' ); ?> value="gpt-3.5-turbo"><?php esc_html_e( 'gpt-3.5-turbo', 'ai-bulk-post' ); ?></option>
                        <option class="openai" <?php selected( $args['model'], 'gpt-3.5-turbo-0125' ); ?> value="gpt-3.5-turbo-0125"><?php esc_html_e( 'gpt-3.5-turbo-0125', 'ai-bulk-post' ); ?></option>
                        <option class="openai" <?php selected( $args['model'], 'gpt-4' ); ?> value="gpt-4"><?php esc_html_e( 'gpt-4', 'ai-bulk-post' ); ?></option>
                        <option class="openai" <?php selected( $args['model'], 'gpt-4-turbo' ); ?> value="gpt-4-turbo"><?php esc_html_e( 'gpt-4-turbo', 'ai-bulk-post' ); ?></option>
                        <option class="gemini" <?php selected( $args['model'], 'gemini-1.0-pro' ); ?> value="gemini-1.0-pro"><?php esc_html_e( 'gemini-1.0-pro', 'ai-bulk-post' ); ?></option>
                        <option class="gemini" <?php selected( $args['model'], 'gemini-1.5-pro' ); ?> value="gemini-1.5-pro"><?php esc_html_e( 'gemini-1.5-pro', 'ai-bulk-post' ); ?></option>
                    </select>
                </div>

                <div class="form-field">
                    <label for="subject"><?php esc_html_e( 'Post subject', 'ai-bulk-post' ); ?></label>
                    <input type="text" name="subject" placeholder="<?php esc_attr_e( 'describe the post subject', 'ai-bulk-post' ); ?>" value="<?php echo esc_attr( $args['subject'] ); ?>" required>
                </div>

                <div class="form-field">
                    <label for="length"><?php esc_html_e( 'Length', 'ai-bulk-post' ); ?></label>
                    <input type="number" name="length" id="length" min="50" max="1000" value="<?php echo esc_attr( $args['length'] ); ?>">
                </div>

                <div class="form-field">
                    <label for="prompt_title"><?php esc_html_e( 'Title Prompt', 'ai-bulk-post' ); ?></label>
                    <input type="text" name="prompt_title" value="<?php echo esc_attr( $args['prompt_title'] ); ?>" required>
                </div>

                <div class="form-field">
                    <label for="prompt_content"><?php esc_html_e( 'Content Prompt', 'ai-bulk-post' ); ?></label>
                    <textarea name="prompt_content" cols="30" rows="4" required><?php echo esc_html( $args['prompt_content'] ); ?></textarea>
                </div>

                <h3><?php esc_html_e( 'Publish' ); ?></h3>

                <?php
                // TODO: Add post type selector
                ?>
                <div class="form-field row">
                    <div class="form-field checklist">
                        <label for="category"><?php esc_html_e( 'Post Category', 'ai-bulk-post' ); ?></label>
                        <?php
                            Helper::get_category_checklist( [
                                'name' => 'post[categories]',
                                'echo' => true,
                                'selected' => (array) $args['categories'],
                            ] );
                        ?>
                    </div>
                    <div class="form-field">
                        <label><?php esc_html_e( 'Publish Date', 'ai-bulk-post' ); ?></label>

                        <div>
                            <label class="inline" for="post[publish_date]"><?php esc_html_e( 'Directly', 'ai-bulk-post' ); ?></label>
                            <input type="radio" name="post[publish_date]" value="directly" checked>
                        </div>

                       <div>
                            <label class="inline" for="post[publish_date]"><?php esc_html_e( 'Schedule', 'ai-bulk-post' ); ?></label>
                            <input type="radio" name="post[publish_date]" value="schedule" disabled>
                       </div>
                    </div>
                </div>

                <h3><?php esc_html_e( 'Recurrence', 'ai-bulk-post' ); ?></h3>
                
                <div class="form-field">
                    <label for="recurrence"><?php esc_html_e( 'Recurrence', 'ai-bulk-post' ); ?></label>
                    <?php 
                        Helper::array_to_dropdown( [
                            'name' => 'recurrence',
                            'id' => 'recurrence',
                            'options' => Helper::get_wp_get_schedules(),
                            'selected' => $args['recurrence']
                        ] );
                    ?>
                </div>

            </form>
        <?php

        $output_string = ob_get_contents();
        ob_end_clean();
        
        wp_send_json( [
            'title' => isset( $_POST['post_id'] ) ? esc_html__( 'Edit Event', 'ai-bulk-post' ) : esc_html__( 'Add Event', 'ai-bulk-post' ),
            'output' => $output_string
        ] );

    }

    function add_event() {

        check_ajax_referer( 'aibp-nonce', 'nonce' );

        $sanitized_form = filter_input(INPUT_POST, 'form', FILTER_SANITIZE_STRING);
        parse_str($sanitized_form, $parsed_form);

        $title = sanitize_text_field( $parsed_form['title'] );
        $ai = sanitize_text_field( $parsed_form['ai'] );
        $model = sanitize_text_field( $parsed_form['model'] );
        $subject = sanitize_text_field( $parsed_form['subject'] );
        $length = intval( $parsed_form['length'] );
        $prompt_title = sanitize_text_field( $parsed_form['prompt_title'] );
        $prompt_content = sanitize_text_field( $parsed_form['prompt_content'] );
        $recurrence = sanitize_text_field( $parsed_form['recurrence'] );
        $post_categories = $parsed_form['post']['categories'] ?? array();
        $post_publish_date = $parsed_form['post']['publish_date'] ?? 'directly';

        // @see: https://developer.wordpress.org/reference/functions/wp_insert_post/
        $post_id = wp_insert_post( [
            'post_type' => 'aibp_events',
            'post_status' => 'publish',
            'post_title' => $title,
            'meta_input' => [
                'aibp_status' => 'passive', // passive, active
                'aibp_ai' => $ai,
                'aibp_model' => $model,
                'aibp_subject' => $subject,
                'aibp_length' => $length,
                'aibp_prompt_title' => $prompt_title,
                'aibp_prompt_content' => $prompt_content,
                'aibp_recurrence' => $recurrence,
                'aibp_post_categories' => $post_categories,
                'aibp_post_publish_date' => $post_publish_date,
            ]
        ] );
        
        if ( is_wp_error( $post_id ) ) {
            wp_send_json( [
                'error' => true,
                $post_id->get_error_message()
            ] );
        }

        wp_send_json( $post_id );

    }

    function update_event() {

        check_ajax_referer( 'aibp-nonce', 'nonce' );

        $sanitized_form = filter_input(INPUT_POST, 'form', FILTER_SANITIZE_STRING);
        parse_str($sanitized_form, $parsed_form);

        $post_id = intval( $_POST['post_id'] );

        $title = sanitize_text_field( $parsed_form['title'] );
        $ai = sanitize_text_field( $parsed_form['ai'] );
        $model = sanitize_text_field( $parsed_form['model'] );
        $subject = sanitize_text_field( $parsed_form['subject'] );
        $length = intval( $parsed_form['length'] );
        $prompt_title = sanitize_text_field( $parsed_form['prompt_title'] );
        $prompt_content = sanitize_text_field( $parsed_form['prompt_content'] );
        $recurrence = sanitize_text_field( $parsed_form['recurrence'] );
        $post_categories = $parsed_form['post']['categories'] ?? array();
        $post_publish_date = $parsed_form['post']['publish_date'] ?? 'directly';

        // @see: https://developer.wordpress.org/reference/functions/wp_insert_post/
        $post_id = wp_update_post( [
            'ID' => $post_id,
            'post_type' => 'aibp_events',
            'post_status' => 'publish',
            'post_title' => $title,
            'meta_input' => [
                'aibp_ai' => $ai,
                'aibp_model' => $model,
                'aibp_subject' => $subject,
                'aibp_length' => $length,
                'aibp_prompt_title' => $prompt_title,
                'aibp_prompt_content' => $prompt_content,
                'aibp_recurrence' => $recurrence,
                'aibp_post_categories' => $post_categories,
                'aibp_post_publish_date' => $post_publish_date,
            ]
        ] );
        
        if ( is_wp_error( $post_id ) ) {
            wp_send_json( [
                'error' => true,
                $post_id->get_error_message()
            ] );
        }

        wp_send_json( $post_id );

    }

    function delete_event() {

        check_ajax_referer( 'aibp-nonce', 'nonce' );

        $post_id = intval( $_POST['post_id'] );
        if ( $post_id ) {
            wp_delete_post( $post_id );
            wp_send_json( [
                'message' => esc_html__( 'Deleted!', 'ai-bulk-post' )
            ] );
        }

        wp_send_json( [
            'error' => true,
            'message' => esc_html__( 'Event cannot found!', 'ai-bulk-post' )
        ] );

    }

    function update_event_status() {

        check_ajax_referer( 'aibp-nonce', 'nonce' );

        $post_id = intval( $_POST['post_id'] );
        if ( $post_id ) {
            $get_status = get_post_meta( $post_id, 'aibp_status', true );
            $status = '';
            if ( empty( $get_status ) || $get_status == 'passive' ) {
                $status = 'active';
            } else {
                $status = 'passive';
            }
            update_post_meta( $post_id, 'aibp_status', $status );
            wp_send_json( [
                'message' => esc_html__( 'Updated!', 'ai-bulk-post' ),
                'status' => $status,
            ] );
        }

        wp_send_json( [
            'error' => true,
            'message' => esc_html__( 'Event cannot found!', 'ai-bulk-post' )
        ] );

    }
}
