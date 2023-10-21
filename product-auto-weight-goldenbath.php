<?php
/**
 * Plugin Name: Product Auto Weight For Golden Bath
 * Plugin URI: https://goldenbath.gr/
 * Description: Automatically calculates and sets product weight based on dimensions in WooCommerce product admin. The user can change the Divisor per their needs. Default Value: 5000
 * Version: 1.2.2
 * Author: Mike Lavdanitis
 * Author URI: https://goldenbath.gr/
 * Requires at least: 5.8
 * Tested up to: 5.9
 * Requires PHP: 8.1
 * WC requires at least: 5.0
 * WC tested up to: 6.0
 * License: GPL2
 */

// Enqueue the script on WooCommerce product admin page
function enqueue_auto_weight_script() {
    if ( function_exists('get_current_screen') ) {
        $screen = get_current_screen();
        if ( $screen && $screen->id === 'product' ) {
            wp_enqueue_script('product-auto-weight-goldenbath', plugin_dir_url(__FILE__) . 'js/auto-weight-goldenbath.js', array('jquery'), '1.0', true);
            
            $divisor = get_option('woocommerce_auto_weight_divisor') ? get_option('woocommerce_auto_weight_divisor') : '5000';
            $enableFieldsCheck = get_option('woocommerce_enable_fields_check') ? get_option('woocommerce_enable_fields_check') : 'no';
            wp_localize_script('product-auto-weight-goldenbath', 'autoWeightSettings', array(
                'divisor' => $divisor,
                'enableFieldsCheck' => $enableFieldsCheck
            ));
        }
    }
}
add_action('admin_enqueue_scripts', 'enqueue_auto_weight_script');

// Add a new section to the WooCommerce Products settings tab
function auto_weight_add_settings_section( $sections ) {
    $sections['auto_weight'] = __('Υπολογισμός βάρους', 'woocommerce');
    return $sections;
}
add_filter('woocommerce_get_sections_products', 'auto_weight_add_settings_section');

// Add settings fields for the new section
function auto_weight_add_settings_fields( $settings, $current_section ) {
    if ( 'auto_weight' == $current_section ) {
        $settings_auto_weight = array();
        
        // Add Title to the Settings
        $settings_auto_weight[] = array(
            'name' => __('Ρυθμίσεις αυτόματου υπολογισμού βάρους', 'woocommerce'),
            'type' => 'title',
            'desc' => __('Προσαρμόστε τον διαιρέτη που χρησιμοποιείται στον υπολογισμό του βάρους με βάση τις διαστάσεις. Default Value: 5000', 'woocommerce'),
            'id' => 'auto_weight'
        );
        
        // Add the divisor field
        $settings_auto_weight[] = array(
            'name' => __('Διαιρέτης για τον υπολογισμό του βάρους', 'woocommerce'),
            'type' => 'text',
            'desc_tip' => true,
            'desc' => __('Εισαγάγετε την τιμή του διαιρέτη για τον υπολογισμό του βάρους.', 'woocommerce'),
            'id' => 'woocommerce_auto_weight_divisor',
            'default' => '5000'
        );
 
        // Add the checkbox for enabling/disabling fields check
        $settings_auto_weight[] = array(
            'name' => __('Enable Fields Check', 'woocommerce'),
            'type' => 'checkbox',
            'desc' => __('Check to enable dimensions and weight fields check before publishing.', 'woocommerce'),
            'desc_tip' => false,		
            'id' => 'woocommerce_enable_fields_check',
            'default' => 'no'
        );
		
        // End the settings
        $settings_auto_weight[] = array( 'type' => 'sectionend', 'id' => 'auto_weight' );
        
        return $settings_auto_weight;
    }
    
    return $settings;
}
add_filter('woocommerce_get_settings_products', 'auto_weight_add_settings_fields', 10, 2);
