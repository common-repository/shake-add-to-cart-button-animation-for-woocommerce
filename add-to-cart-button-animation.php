<?php
/**
 * Plugin Name: Shake Add to Cart Button Animation for WooCommerce
 * Description: Adds a custom shake animation to the "Add to Cart" button on WooCommerce product pages.
 * Version: 1.1
 * Author: Nishat Sharma
 * License: GPL2
 * WC requires at least: 6.5
 * WC tested up to: 9.3
 * Requires Plugins: woocommerce
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Sanitize callback functions for each setting
function satcba_sanitize_checkbox($input) {
    return ($input === '1') ? '1' : '';  // Sanitize checkbox
}

function satcba_sanitize_number($input) {
    return is_numeric($input) ? floatval($input) : 0;  // Sanitize numeric input
}

function satcba_sanitize_color($input) {
    return sanitize_hex_color($input);  // Sanitize color picker input
}

// Register the settings (Free Version)
function satcba_button_animation_register_settings() {
    register_setting('satcba_button_animation_settings_group', 'satcba_animation_active', 'satcba_sanitize_checkbox');
    register_setting('satcba_button_animation_settings_group', 'satcba_animation_speed', 'satcba_sanitize_number');
    register_setting('satcba_button_animation_settings_group', 'satcba_animation_delay', 'satcba_sanitize_number');
    register_setting('satcba_button_animation_settings_group', 'satcba_box_shadow', 'satcba_sanitize_checkbox');
    register_setting('satcba_button_animation_settings_group', 'satcba_background_color', 'satcba_sanitize_color');
    register_setting('satcba_button_animation_settings_group', 'satcba_text_color', 'satcba_sanitize_color');
}
add_action('admin_init', 'satcba_button_animation_register_settings');

// Set default options on activation
function satcba_button_animation_activate() {
    if (false === get_option('satcba_animation_active')) {
        update_option('satcba_animation_active', false);
    }
}
register_activation_hook(__FILE__, 'satcba_button_animation_activate');

// Create the admin menu
function satcba_button_animation_create_menu() {
    add_options_page(
        'Add to Cart Button Animation Settings', 
        'SATCBA Button Animation', 
        'manage_options', 
        'satcba-button-animation', 
        'satcba_button_animation_settings_page'
    );
}
add_action('admin_menu', 'satcba_button_animation_create_menu');

// Admin settings page (Free Version)
function satcba_button_animation_settings_page() {
    ?>
    <div class="wrap">
        <h1>Add to Cart Button Animation Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('satcba_button_animation_settings_group');
            do_settings_sections('satcba_button_animation_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Animation</th>
                    <td><input type="checkbox" name="satcba_animation_active" value="1" <?php checked(1, get_option('satcba_animation_active'), true); ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Animation Speed (seconds) - Pro</th>
                    <td><input type="number" step="0.1" name="satcba_animation_speed" value="<?php echo esc_attr(get_option('satcba_animation_speed', '3')); ?>" disabled/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Animation Delay (seconds) - Pro</th>
                    <td><input type="number" step="0.1" name="satcba_animation_delay" value="<?php echo esc_attr(get_option('satcba_animation_delay', '2')); ?>" disabled/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Box Shadow - Pro</th>
                    <td><input type="checkbox" name="satcba_box_shadow" value="1" <?php checked(1, get_option('satcba_box_shadow'), true); ?> disabled/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background Color - Pro</th>
                    <td><input type="text" name="satcba_background_color" value="<?php echo esc_attr(get_option('satcba_background_color', '#ff0000')); ?>" class="my-color-field" disabled/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text Color - Pro</th>
                    <td><input type="text" name="satcba_text_color" value="<?php echo esc_attr(get_option('satcba_text_color', '#ffffff')); ?>" class="my-color-field" disabled/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable more customization? - Pro</th>
                    <td><a href="https://nishatsharma.net/product/shake-add-to-cart-button-animation-for-woocommerce-pro/" target="_blank">Upgrade to Pro</a></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Enqueue the custom style based on settings (Free Version)
function satcba_button_animation_enqueue_styles() {
    if (is_product()) {
        $active = get_option('satcba_animation_active', false);
        if ($active) {
            $version = '1.0.0';
            wp_register_style('satcba_button_animation_style', plugin_dir_url(__FILE__) . 'style.css', array(), $version);

            $custom_css = "
            .single_add_to_cart_button {
                animation: shake 3s cubic-bezier(.36,.07,.59,.97) both;
                transform: translate3d(0, 0, 0);
                perspective: 1000px;
                animation-delay: 2s;
                animation-iteration-count: infinite;
            }
            .single_add_to_cart_button:hover {
                animation-play-state: paused;
            }
            @keyframes shake {
                10%, 90% {
                    transform: translate3d(-1px, 0, 0);
                }
                20%, 80% {
                    transform: translate3d(2px, 0, 0);
                }
                30%, 50%, 70% {
                    transform: translate3d(-4px, 0, 0);
                }
                40%, 60% {
                    transform: translate3d(4px, 0, 0);
                }
            }
            ";
            wp_add_inline_style('satcba_button_animation_style', $custom_css);
            wp_enqueue_style('satcba_button_animation_style');
        }
    }
}
add_action('wp_enqueue_scripts', 'satcba_button_animation_enqueue_styles');

// Add the color picker to the admin settings page (Free Version)
function satcba_button_animation_enqueue_color_picker($hook_suffix) {
    if ($hook_suffix === 'settings_page_satcba-button-animation') {
        wp_enqueue_style('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'satcba_button_animation_enqueue_color_picker');

// Hook to run on plugin deactivation to clean up settings if needed
function satcba_button_animation_deactivate() {
    delete_option('satcba_animation_active');
}
register_deactivation_hook(__FILE__, 'satcba_button_animation_deactivate');
