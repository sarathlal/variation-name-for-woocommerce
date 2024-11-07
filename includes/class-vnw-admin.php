<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class VNW_Admin {

    public function __construct() {
        // Hooks for the custom variation name field
        add_action('woocommerce_product_after_variable_attributes', [$this, 'add_variation_name_field'], 10, 3);
        add_action('woocommerce_save_product_variation', [$this, 'save_variation_name_field'], 10, 2);

        // Hooks for the custom settings tab
        add_filter('woocommerce_product_data_tabs', [$this, 'add_variation_name_settings_tab']);
        add_action('woocommerce_product_data_panels', [$this, 'add_variation_name_settings_tab_content']);
        add_action('woocommerce_process_product_meta', [$this, 'save_variation_name_settings']);
    }

    // Add custom variation name field
    public function add_variation_name_field($loop, $variation_data, $variation) {
        woocommerce_wp_text_input([
            'id' => "vnw_variation_name_$loop",
            'name' => "vnw_variation_name_$loop",
            'label' => __('Variation Name', 'variation-name-for-woocommerce'),
            'value' => get_post_meta($variation->ID, 'vnw_variation_name', true),
            'desc_tip' => true,
        ]);
    }

    // Save the variation name field
    public function save_variation_name_field($variation_id, $i) {
        if (isset($_POST["vnw_variation_name_$i"])) {
            $variation = wc_get_product($variation_id); // Get the variation object
            $variation->update_meta_data('vnw_variation_name', sanitize_text_field(wp_unslash($_POST["vnw_variation_name_$i"])));
            $variation->save(); // Save the changes
        }
    }

    // Add a new tab in the Product Data section for variable products
    public function add_variation_name_settings_tab($tabs) {
        global $product_object;

        if ($product_object && $product_object->is_type('variable')) {
            $tabs['variation_name_settings'] = [
                'label'    => __('Variation Name', 'variation-name-for-woocommerce'),
                'target'   => 'vnw_variation_name_settings_options',
                'class'    => ['show_if_variable'],
                'priority' => 60,
            ];
        }

        return $tabs;
    }

    // Add settings content to the custom tab
    public function add_variation_name_settings_tab_content() {
        echo '<div id="vnw_variation_name_settings_options" class="panel woocommerce_options_panel show_if_variable">';
        echo '<div class="options_group">';
        woocommerce_wp_checkbox([
            'id'          => 'vnw_display_variation_name',
            'label'       => __('Display variation name', 'variation-name-for-woocommerce'),
            'description' => __('Enable to show variation names instead of dropdowns on the product page.', 'variation-name-for-woocommerce'),
        ]);
        echo '</div>';
        echo '</div>';
    }

    // Save the display setting
    public function save_variation_name_settings($post_id) {
        $product = wc_get_product($post_id); // Get the product object
        $display_variation_name = isset($_POST['vnw_display_variation_name']) ? 'yes' : 'no';
        $product->update_meta_data('vnw_display_variation_name', $display_variation_name);
        $product->save(); // Save the product to persist changes
    }
}
