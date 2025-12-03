<?php

class biz_pricing_loader{
    public function __construct(){

        // var_dump('biz pricing loaded');
        require_once plugin_dir_path(__FILE__) . 'options-framework/options-loader.php';
        add_action('init', array($this, 'init'));
    }

    public function init(){
        $options = bizzplugin_framework();
        $panel = $options->create_panel(array(
            'id'          => 'biz_pricing_option',        // Unique panel ID
            'title'       => __('Biz Pricing ', 'biz-pricing'),
            'menu_title'  => __('Biz Pricing ', 'biz-pricing'),
            'menu_slug'   => 'biz-pricing-menu-slug',
            'capability'  => 'manage_options',
            'icon'        => 'dashicons-nametag',  // WordPress dashicon
            'position'    => 6,                          // Menu position
            'option_name' => 'biz_pricing_option',         // Database option name
            'is_premium'  => false,
            'sections'    => $this->get_sections(),
                                // You can define sections here or add them later
        ));
    }
    public function get_sections(){
        return array(
                array(
                'id'    => 'shortcode',
                'title' => __('Shortcode ', 'biz-pricing'),
                'icon'  => 'dashicons-visibility',

                'fields' => array(
                    array(
                        'id'    => 'shortcode',
                        'type'  => 'callback',
                        'title' => __('Shortcode', 'biz-pricing'),
                        'render_callback' => array($this, 'render_shortcode_preview'),
                    ),
                ),
            ),

            array(
                'id'    => 'Freemius Settings',
                'title' => __('Freemius Settings', 'biz-pricing'),
                'icon'  => 'dashicons dashicons-admin-settings',
                'fields' => array(
                    array(
                        'id'      => 'plugin_id',
                        'type'    => 'text',
                        'title'   => __('Plugin ID', 'biz-pricing'),
                        'default' => '56845',
                    ),
                    array(
                        'id'      => 'plan_id',
                        'type'    => 'text',
                        'title'   => __('Plan ID', 'biz-pricing'),
                        'default' => '56785',
                    ),
                    array(
                        'id'      => 'coupon_code',
                        'type'    => 'text',
                        'title'   => __('Coupon Code', 'biz-pricing'),
                        'default' => 'bizzplugin35',
                    ),
                    array(
                        'id'      => 'coupon_message',
                        'type'    => 'text',
                        'title'   => __('Coupon Message', 'biz-pricing'),
                        'default' => 'DISCOUNT AVAILABLE FOR YOU',
                    ),
                ),
            ),
            array(
                'id'    => 'general_settings',
                'title' => __('General Settings', 'biz-pricing'),
                'icon'  => 'dashicons dashicons-admin-generic',
                'fields' => array(
                    array(
                        'id'      => 'text_field',
                        'type'    => 'text',
                        'title'   => __('Button Text', 'biz-pricing'),
                        'default' => 'Purchase Now',
                    ),
                    array(
                        'id'      => 'tab_title',
                        'type'    => 'text',
                        'title'   => __('Tab Title (Regular)', 'biz-pricing'),
                        'default' => 'Regular',
                    ),
                    array(
                        'id'      => 'tab_title',
                        'type'    => 'text',
                        'title'   => __('Tab Title (Bundle)', 'biz-pricing'),
                        'default' => 'Bundle',
                    ),
                    //Enable Bundle
                    array(
                        'id'      => 'enable_bundle',
                        'type'    => 'checkbox',
                        'title'   => __('Enable Bundle Tab', 'biz-pricing'),
                        'default' => true,
                    )
                ),
            ),
            // yearly pricing
            array(
                'id'    => 'pricing',
                'title' => __('Pricing', 'biz-pricing'),
                'icon'  => 'dashicons dashicons-calendar',
                'fields' => array( 
                ),
                'subsections' => array(
                    array(
                        'id'    => 'yearly_pricing',
                        'title' => __('Yearly Pricing Section', 'biz-pricing'),
                        'fields' => array(
                            array(
                                'id'      => 'yearly_pricing',
                                'type'    => 'repeater',
                                'title'   => __('Add Yearly Pricing', 'biz-pricing'),
                                'button_title' => __('Add New Pricing', 'biz-pricing'),
                                // 'allow_add' => false,
                                'sortable'   => false,
                                'fields'  => array(

                                    array(
                                        'id'      => 'title',
                                        'type'    => 'text',
                                        'title'   => __('Title', 'biz-pricing'),
                                        'default' => '1 Site',
                                    ),

                                    array(
                                        'id'      => 'name',
                                        'type'    => 'text',
                                        'title'   => __('Name', 'biz-pricing'),
                                        'default' => 'Starter',
                                    ),

                                    array(
                                        'id'      => 'price',
                                        'type'    => 'text',
                                        'title'   => __('Price', 'biz-pricing'),
                                        'default' => '$59.99',
                                    ),

                                ),
                            ),
                        ),
                    ),
                    array(
                        'id'    => 'lifetime_pricing',
                        'title' => __('Lifetime Pricing', 'biz-pricing'),
                        'fields' => array(

                            array(
                                'id'      => 'lifetime_pricing',
                                'type'    => 'repeater',
                                'title'   => __('Add Lifetime Pricing', 'biz-pricing'),
                                'button_title' => __('Add New Pricing', 'biz-pricing'),
                                // 'allow_add' => false,
                                'sortable'   => false,
                                'fields'  => array(

                                    array(
                                        'id'      => 'title',
                                        'type'    => 'text',
                                        'title'   => __('Title', 'biz-pricing'),
                                        'default' => '1 Site',
                                    ),

                                    array(
                                        'id'      => 'name',
                                        'type'    => 'text',
                                        'title'   => __('Name', 'biz-pricing'),
                                        'default' => 'Starter',
                                    ),

                                    array(
                                        'id'      => 'price',
                                        'type'    => 'text',
                                        'title'   => __('Price', 'biz-pricing'),
                                        'default' => '$199.99',
                                    ),

                                ),
                            ),
                        ),
                    ),
                ),
            ),
            
            // //Yearly Features
            array(
                'id'    => 'features',
                'title' => __('Features', 'biz-pricing'),
                'icon'  => 'dashicons dashicons-list-view',
                'fields' => array(  
                ),
                'subsections' => array(
                    array(
                        'id'    => 'yearly_features_',
                        'title' => __('Yearly Features ', 'biz-pricing'),
                        'fields' => array(
                            array(
                                'id'      => 'yearly_features',
                                'type'    => 'repeater',
                                'title'   => __('Add Yearly Features', 'biz-pricing'),
                                'button_title' => __('Add New Feature', 'biz-pricing'),
                                // 'allow_add' => false,
                                'sortable'   => false,
                                'fields'  => array(

                                    array(
                                        'id'      => 'feature',
                                        'type'    => 'text',
                                        'title'   => __('Feature', 'biz-pricing'),
                                        'default' => 'Feature Name',
                                    ),

                                ),
                            ),
                        ),
                    ),
                    array(
                        'id'    => 'lifetime_features_',
                        'title' => __('Lifetime Features ', 'biz-pricing'),
                        'fields' => array(
                            array(
                                'id'      => 'lifetime_features',
                                'type'    => 'repeater',
                                'title'   => __('Add Lifetime Features', 'biz-pricing'),
                                'button_title' => __('Add New Feature', 'biz-pricing'),
                                // 'allow_add' => false,
                                'sortable'   => false,
                                'fields'  => array(

                                    array(
                                        'id'      => 'feature',
                                        'type'    => 'text',
                                        'title'   => __('Feature', 'biz-pricing'),
                                        'default' => 'Feature Name',
                                    ),

                                ),
                            ),
                        ),
                    ),
                ),
            ),
            
            //bundel features
            array(
                'id'    => 'bundle_settings',
                'title' => __('Bundle', 'biz-pricing'),
                'icon'  => 'dashicons-cart',

                //  SHOW ONLY IF BUNDLE ENABLED
                'dependency' => array(
                    'field' => 'enable_bundle',
                    'value' => '1',
                ),

                'fields' => array(
                    array(
                        'id'      => 'bundle_main_title',
                        'type'    => 'text',
                        'title'   => __('Main Title', 'biz-pricing'),
                        'default' => 'Bundle 1',
                    ),

                    array(
                        'id'      => 'bundle_plugin_id',
                        'type'    => 'text',
                        'title'   => __('Plugin ID', 'biz-pricing'),
                        'default' => '',
                        'desc'    => __('Freemius Plugin ID for Bundle', 'biz-pricing'),
                    ),

                    array(
                        'id'      => 'bundle_plan_id',
                        'type'    => 'text',
                        'title'   => __('Plan ID', 'biz-pricing'),
                        'default' => '',
                        'desc'    => __('Freemius Plan ID for Bundle', 'biz-pricing'),
                    ),

                    // =============== Pricing Repeater (1 Site, 5 Site, Unlimited etc)
                    array(
                        'id'      => 'bundle_pricing_items',
                        'type'    => 'repeater',
                        'title'   => __('Bundle Pricing', 'biz-pricing'),
                        'button_title' => __('Add New Bundle Plan', 'biz-pricing'),
                        'sortable' => false,

                        'fields' => array(
                            array(
                                'id'      => 'title',
                                'type'    => 'text',
                                'title'   => __('Title', 'biz-pricing'),
                                'default' => '1 Site',
                            ),

                            array(
                                'id'      => 'name',
                                'type'    => 'text',
                                'title'   => __('Name', 'biz-pricing'),
                                'default' => 'Starter',
                            ),

                            array(
                                'id'      => 'price',
                                'type'    => 'text',
                                'title'   => __('Price', 'biz-pricing'),
                                'default' => '$99.99',
                            ),
                        ),
                    ),
                ),
            ),
        );
    } 

    public function render_shortcode_preview($field, $value,) {
        echo '<div style="padding:20px; margin-bottom:20px; background:#fff; border:1px solid #ddd; border-radius:6px;">';
        echo do_shortcode('[biz_pricing]');
        echo '</div>';
    }   
}