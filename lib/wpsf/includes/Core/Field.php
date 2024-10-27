<?php

namespace WPSF\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class Field {

    public static function text( $field, $options, $option_key ) {
        ?>
            <input
                type="text"
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( sprintf( '%s[%s]', $option_key, $field['id'] ) );?>"
                value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
            >
            <p class="description">
                <?php echo wp_kses_post( $field['description'] ); ?> 
            </p>
        <?php
    }

    public static function checkbox( $field, $options, $option_key ) {
        ?>
            <input
                type="checkbox"
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( sprintf( '%s[%s]', $option_key, $field['id'] ) );?>"
                value="1"
                <?php echo isset( $options[ $field['id'] ] ) ? ( checked( $options[ $field['id'] ], 1, false ) ) : ( '' ); ?>
            >
            <p class="description">
                <?php echo wp_kses_post( $field['description'] ); ?>
            </p>
        <?php
        
    }

    public static function textarea( $field, $options, $option_key ) {
        ?>
            <textarea
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( sprintf( '%s[%s]', $option_key, $field['id'] ) );?>"
            ><?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?></textarea>
            <p class="description">
                <?php echo wp_kses_post( $field['description'] ); ?>
            </p>
        <?php
    }

    public static function select( $field, $options, $option_key ) {
        ?>
            <select
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( sprintf( '%s[%s]', $option_key, $field['id'] ) );?>"
            >
                <?php foreach( $field['options'] as $key => $option ) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" 
                        <?php echo isset( $options[ $field['id'] ] ) ? ( selected( $options[ $field['id'] ], $key, false ) ) : ( '' ); ?>
                    >
                        <?php echo esc_html( $option ); ?>
                    </option>
                <?php } ?>
            </select>
            <p class="description">
                <?php echo wp_kses_post( $field['description'] ); ?>
            </p>
        <?php

    }

    public static function password( $field, $options, $option_key ) {
        ?>
            <input
                type="password"
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( sprintf( '%s[%s]', $option_key, $field['id'] ) );?>"
                value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
            >
            <p class="description">
                <?php echo wp_kses_post( $field['description'] ); ?>
            </p>
        <?php

    }

    public static function wysiwyg( $field, $options, $option_key ) {
        wp_editor(
            isset( $options[ $field['id'] ] ) ? $options[ $field['id'] ] : '',
            $field['id'],
            array(
                'textarea_name' => sprintf( '%s[%s]', $option_key, $field['id'] ),
                'textarea_rows' => 5,
            )
        );
    }

    public static function email( $field, $options, $option_key ) {
        ?>
            <input
                type="email"
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( sprintf( '%s[%s]', $option_key, $field['id'] ) );?>"
                value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
            >
            <p class="description">
                <?php echo wp_kses_post( $field['description'] ); ?>
            </p>
        <?php
    }

    public static function url( $field, $options, $option_key ) {
        ?>
            <input
                type="url"
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( sprintf( '%s[%s]', $option_key, $field['id'] ) );?>"
                value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
            >
            <p class="description">
                <?php echo wp_kses_post( $field['description'] ); ?>
            </p>
        <?php
    }

    public static function color( $field, $options, $option_key ) {
        ?>
            <input
                type="color"
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( sprintf( '%s[%s]', $option_key, $field['id'] ) );?>"
                value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
            >
            <p class="description">
                <?php echo wp_kses_post( $field['description'] ); ?>
            </p>
        <?php
    }

    public static function date( $field, $options, $option_key ) {
        ?>
            <input
                type="date"
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( sprintf( '%s[%s]', $option_key, $field['id'] ) );?>"
                value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
            >
            <p class="description">
                <?php echo wp_kses_post( $field['description'] ); ?>
            </p>
        <?php
    }
}