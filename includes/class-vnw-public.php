<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class VNW_Public {

    public function __construct() {
        add_action('woocommerce_after_variations_table', [$this, 'display_variation_radio_buttons']);
        add_action('wp_footer', [$this, 'add_variation_radio_buttons_script']);
        add_filter('woocommerce_available_variation', [$this, 'add_custom_title_to_variations'], 10, 3);
    }

    /**
     * Helper function to check if it's a variable product and if "Display variation name" is enabled.
     */
    private function should_display_variation_names() {
        global $product;
        
        if (!is_product()) {
            return false; // Not a product page
        }

        if (!$product || !$product->is_type('variable')) {
            return false; // Not a variable product
        }

        // Check if "Display variation name" setting is enabled for this product
        return 'yes' === $product->get_meta('vnw_display_variation_name');
    }

    // Display variation radio buttons
    public function display_variation_radio_buttons() {
        if (!$this->should_display_variation_names()) {
            return; // Exit if the conditions are not met
        }     

        global $product;
        $available_variations = $product->get_available_variations();

        echo '<style>
                    .variations_form .variations { 
                        display: none !important; 
                    }
                </style>';
        echo '<div class="variation-options">';
        foreach ($available_variations as $variation_data) {
            $variation = wc_get_product($variation_data['variation_id']); // Get the variation object
            $variation_name = $variation->get_meta('vnw_variation_name') ?: implode(' / ', $variation_data['attributes']);

            echo '<label>';
            echo '<input type="radio" name="variation" value="' . esc_attr($variation->get_id()) . '"> ';
            echo esc_html($variation_name);
            echo '</label><br>';
        }
        echo '</div>';
    }

    // Add JavaScript for handling variation selection with radio buttons
    public function add_variation_radio_buttons_script() {
        if (!$this->should_display_variation_names()) {
            return; // Exit if the conditions are not met
        }

        ?>
        <script type="text/javascript">
        jQuery(document).ready(function ($) {
            const variationForm = $('.variations_form');
            const variationData = variationForm.data('product_variations');

            if (variationData) {
                variationForm.find('.variations').hide(); // Hide original dropdowns
            }

            variationForm.on('change', 'input[name="variation"]', function () {
                const selectedVariationId = $(this).val();
                selectAttributesForVariation(selectedVariationId);

                const variation = variationData.find(v => v.variation_id === parseInt(selectedVariationId));
                if (variation) {
                    variationForm.trigger('found_variation', [variation]);
                }
            });

            function selectAttributesForVariation(variationId) {
                const variation = variationData.find(v => v.variation_id === parseInt(variationId));
                if (variation && variation.attributes) {
                    $.each(variation.attributes, function (attributeName, attributeValue) {
                        const select = variationForm.find(`select[name="${attributeName}"]`);
                        if (select.length) {
                            select.val(attributeValue).trigger('change');
                        }
                    });
                }
            }
        });
        </script>
        <?php
    }

    // Add custom title to each variation data for the frontend
    public function add_custom_title_to_variations($variation_data, $product, $variation) {
        $variation_name = $variation->get_meta('vnw_variation_name');
        $variation_data['variation_name'] = $variation_name ?: implode(' / ', $variation_data['attributes']);
        return $variation_data;
    }
}
