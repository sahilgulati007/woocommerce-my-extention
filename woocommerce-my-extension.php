<?php
/**
 * Plugin Name: WooCommerce My Extension
 * Plugin URI: http://yourdomain.com/
 * Description: Your extension's description text.
 * Version: 1.0.0
 * Author: Sahil Gulati
 * Author URI: http://yourdomain.com/
 * Developer: Your Name
 * Developer URI: http://yourdomain.com/
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 *
 * Woo: 12345:342928dfsfhsf8429842374wdf4234sfd
 * WC requires at least: 2.2
 * WC tested up to: 2.3
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Put your plugin code here
    /**
     * Add a custom product data tab in front end
     */
    add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
    function woo_new_product_tab( $tabs ) {

        // Adds the new tab

        $tabs['test_tab'] = array(
            'title' 	=> __( 'New Product Tab', 'woocommerce' ),
            'priority' 	=> 50,
            'callback' 	=> 'woo_new_product_tab_content'
        );

        return $tabs;

    }
    function woo_new_product_tab_content() {

        // The new tab content

        echo '<h2>New Product Tab</h2>';
        echo '<p>Here\'s your new product tab.</p>';
        global $post;

        // retrieve the global notice for the current post
        $global_notice = esc_attr( get_post_meta( $post->ID, '_global_notice', true ) );

        $notice = "<div class='sp_global_notice'>$global_notice</div>";
        echo '<p>'.$notice.'</p>';

    }

    /**
     * Remove product data tabs frontend
     */
    add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

    function woo_remove_product_tabs( $tabs ) {

        //unset( $tabs['description'] );      	// Remove the description tab
        unset( $tabs['reviews'] ); 			// Remove the reviews tab
        //unset( $tabs['additional_information'] );  	// Remove the additional information tab

        return $tabs;
    }

    /**
     * Rename product data tabs forntend
     */
    add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
    function woo_rename_tabs( $tabs ) {

        $tabs['description']['title'] = __( 'More Information' );		// Rename the description tab
        $tabs['reviews']['title'] = __( 'Ratings' );				// Rename the reviews tab
        $tabs['additional_information']['title'] = __( 'Product Data' );	// Rename the additional information tab

        return $tabs;

    }

    /**
     * Reorder product data tabs forntend
     */
    add_filter( 'woocommerce_product_tabs', 'woo_reorder_tabs', 98 );
    function woo_reorder_tabs( $tabs ) {

        $tabs['reviews']['priority'] = 5;			// Reviews first
        $tabs['description']['priority'] = 10;			// Description second
        $tabs['additional_information']['priority'] = 15;	// Additional information third

        return $tabs;
    }
    /**
     * Customize product data tabs
     */
    add_filter( 'woocommerce_product_tabs', 'woo_custom_description_tab', 98 );
    function woo_custom_description_tab( $tabs ) {

        $tabs['description']['callback'] = 'woo_custom_description_tab_content';	// Custom description callback

        return $tabs;
    }

    function woo_custom_description_tab_content() {
        echo '<h2>Custom Description</h2>';
        echo '<p>Here\'s a custom description</p>';
    }


    // Add a custom product setting tab to edit product pages options FOR SIMPLE PRODUCTS only
    add_filter( 'woocommerce_product_data_tabs', 'discount_new_product_data_tab', 50, 1 );
    function discount_new_product_data_tab( $tabs ) {
        $tabs['discount'] = array(
            'label' => __( 'Discount', 'woocommerce' ),
            'target' => 'discount_product_data', // <== to be used in the <div> class of the content
            'class' => array('show_if_simple'), // or 'hide_if_simple' or 'show_if_variable'…
        );

        return $tabs;
    }

// Add/display custom Fields in the custom product settings tab
    add_action( 'woocommerce_product_data_panels', 'add_custom_fields_product_options_discount', 10 );
    function add_custom_fields_product_options_discount() {
        global $post;

        echo '<div id="discount_product_data" class="panel woocommerce_options_panel">'; // <== Here we use the target attribute

        woocommerce_wp_text_input(  array(
            'type'          => 'number', // Add an input number Field
            'id'            => '_discount_info',
            'label'         => __( 'Percentage Discount', 'woocommerce' ),
            'placeholder'   => __( 'Enter the % discount.', 'woocommerce' ),
            'description'   => __( 'Explanations about the field info discount.', 'woocommerce' ),
            'desc_tip'      => 'true',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '1'
            ),
        ) );

        echo '</div>';
    }

// Save the data value from the custom fields for simple products
    add_action( 'woocommerce_process_product_meta_simple', 'save_custom_fields_product_options_discount', 50, 1 );
    function save_custom_fields_product_options_discount( $post_id ) {
        // Save Number Field value
        $number_field = $_POST['_discount_info'];

        if( ! empty( $number_field ) ) {
            update_post_meta( $post_id, '_discount_info', esc_attr( $number_field ) );
        }
    }

// First Register the Tab by hooking into the 'woocommerce_product_data_tabs' filter
    add_filter('woocommerce_product_data_tabs', 'add_my_custom_product_data_tab');
    function add_my_custom_product_data_tab($product_data_tabs)
    {
        $product_data_tabs['my-custom-tab'] = array(
            'label' => __('My Custom Tab', 'woocommerce'),
            'target' => 'my_custom_product_data',
            'class' => array('show_if_simple'),
        );
        return $product_data_tabs;
    }

    /** CSS To Add Custom tab Icon */
    function wcpp_custom_style()
    { ?>
        <style>
            #woocommerce-product-data ul.wc-tabs li.my-custom-tab_options a:before {
                font-family: WooCommerce;
                content: '\e006';
            }
        </style>
        <?php
    }

    add_action('admin_head', 'wcpp_custom_style');

    // functions you can call to output text boxes, select boxes, etc.
    add_action('woocommerce_product_data_panels', 'woocom_custom_product_data_fields');
    function woocom_custom_product_data_fields() {
        global $post;
        // Note the 'id' attribute needs to match the 'target' parameter set above
        ?> <div id = 'my_custom_product_data'
                class = 'panel woocommerce_options_panel' > <?php
        ?> <div class = 'options_group' > <?php
            // Text Field
            woocommerce_wp_text_input(
                array(
                    'id' => '_text_field',
                    'label' => __( 'Custom Text Field', 'woocommerce' ),
                    'wrapper_class' => 'show_if_simple', //show_if_simple or show_if_variable
                    'placeholder' => 'Custom text field',
                    'desc_tip' => 'true',
                    'description' => __( 'Enter the custom value here.', 'woocommerce' )
                )
            );
            // Number Field
            woocommerce_wp_text_input(
                array(
                    'id' => '_number_field',
                    'label' => __( 'Custom Number Field', 'woocommerce' ),
                    'placeholder' => '',
                    'description' => __( 'Enter the custom value here.', 'woocommerce' ),
                    'type' => 'number',
                    'custom_attributes' => array(
                        'step' => 'any',
                        'min' => '15'
                    )
                )
            );
            // Checkbox
            woocommerce_wp_checkbox(
                array(
                    'id' => '_checkbox',
                    'label' => __('Custom Checkbox Field', 'woocommerce' ),
                    'description' => __( 'Check me!', 'woocommerce' )
                )
            );
            // Select
            woocommerce_wp_select(
                array(
                    'id' => '_select',
                    'label' => __( 'Custom Select Field', 'woocommerce' ),
                    'options' => array(
                        'one' => __( 'Custom Option 1', 'woocommerce' ),
                        'two' => __( 'Custom Option 2', 'woocommerce' ),
                        'three' => __( 'Custom Option 3', 'woocommerce' )
                    )
                )
            );
            // Textarea
            woocommerce_wp_textarea_input(
                array(
                    'id' => '_textarea',
                    'label' => __( 'Custom Textarea', 'woocommerce' ),
                    'placeholder' => '',
                    'description' => __( 'Enter the value here.', 'woocommerce' )
                )
            );
            ?> </div>
        </div><?php
    }
    /** Hook callback function to save custom fields information */
    function woocom_save_proddata_custom_fields($post_id) {
        // Save Text Field
        $text_field = $_POST['_text_field'];
        if (!empty($text_field)) {
            update_post_meta($post_id, '_text_field', esc_attr($text_field));
        }
        // Save Number Field
        $number_field = $_POST['_number_field'];
        if (!empty($number_field)) {
            update_post_meta($post_id, '_number_field', esc_attr($number_field));
        }
        // Save Textarea
        $textarea = $_POST['_textarea'];
        if (!empty($textarea)) {
            update_post_meta($post_id, '_textarea', esc_html($textarea));
        }
        // Save Select
        $select = $_POST['_select'];
        if (!empty($select)) {
            update_post_meta($post_id, '_select', esc_attr($select));
        }
        // Save Checkbox
        $checkbox = isset($_POST['_checkbox']) ? 'yes' : 'no';
        update_post_meta($post_id, '_checkbox', $checkbox);
        // Save Hidden field
        $hidden = $_POST['_hidden_field'];
        if (!empty($hidden)) {
            update_post_meta($post_id, '_hidden_field', esc_attr($hidden));
        }
    }
    add_action( 'woocommerce_process_product_meta_simple', 'woocom_save_proddata_custom_fields'  );
// You can uncomment the following line if you wish to use those fields for "Variable Product Type"
//add_action( 'woocommerce_process_product_meta_variable', 'woocom_save_proddata_custom_fields'  );

    function global_notice_meta_box() {

        $screens = array( 'product' );

        //fetching post type
        //$screens = get_post_types();

        foreach ( $screens as $screen ) {
            add_meta_box(
                'global-notice',
                __( 'Global Notice', 'sitepoint' ),
                'global_notice_meta_box_callback',
                $screen,
                'side'
            );
        }
    }

    add_action( 'add_meta_boxes', 'global_notice_meta_box' );

    function global_notice_meta_box_callback( $post ) {

        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'global_notice_nonce', 'global_notice_nonce' );

        $value = get_post_meta( $post->ID, '_global_notice', true );

        echo '<textarea style="width:100%" id="global_notice" name="global_notice">' . esc_attr( $value ) . '</textarea>';
    }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id
     */
    function save_global_notice_meta_box_data( $post_id ) {

        // Check if our nonce is set.
        if ( ! isset( $_POST['global_notice_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['global_notice_nonce'], 'global_notice_nonce' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }

        }
        else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Make sure that it is set.
        if ( ! isset( $_POST['global_notice'] ) ) {
            return;
        }

        // Sanitize user input.
        $my_data = sanitize_text_field( $_POST['global_notice'] );

        // Update the meta field in the database.
        update_post_meta( $post_id, '_global_notice', $my_data );
    }

    add_action( 'save_post', 'save_global_notice_meta_box_data' );

//    function global_notice_before_post( $content ) {
//
//        global $post;
//
//        // retrieve the global notice for the current post
//        $global_notice = esc_attr( get_post_meta( $post->ID, '_global_notice', true ) );
//
//        $notice = "<div class='sp_global_notice'>$global_notice</div>";
//
//        return $notice . $content;
//
//    }
//
//    add_filter( 'the_content', 'global_notice_before_post' );
}