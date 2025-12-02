<?php
/**
 * BizzPlugin Options Framework - Panel Class
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Panel Class
 */
class BizzPlugin_Panel {
    
    /**
     * Panel ID
     */
    private $id;
    
    /**
     * Panel title
     */
    private $title;
    
    /**
     * Menu title
     */
    private $menu_title;
    
    /**
     * Menu slug
     */
    private $menu_slug;
    
    /**
     * Parent slug for submenu
     */
    private $parent_slug;

    private $route_namespace;
    
    /**
     * Required capability
     */
    private $capability;
    
    /**
     * Menu icon
     */
    private $icon;
    
    /**
     * Menu position
     */
    private $position;
    
    /**
     * Option name in database
     */
    private $option_name;
    
    /**
     * Is premium active
     */
    private $is_premium;
    
    /**
     * Sections
     */
    private $sections = array();
    
    /**
     * Framework instance
     */
    private $framework;
    
    /**
     * Current page hook
     */
    private $page_hook;

    /**
     * Current section
     */
    private $current_section;

    
    
    /**
     * Current subsection
     */
    private $current_subsection;
    
    /**
     * Panel configuration (logo, version, footer_text, etc.)
     */
    private $panel_config = array();
    
    /**
     * Enable search functionality
     */
    private $enable_search = true;
    
    /**
     * Enable Export/Import section
     */
    private $show_export_import = true;
    
    /**
     * Enable API & Webhook section
     */
    private $show_api = true;
    
    /**
     * Resource links for sidebar
     */
    private $resources = array();
    
    /**
     * Recommended plugins for sidebar
     */
    private $recommended_plugins = array();
    
    /**
     * Constructor
     */
    public function __construct($args, $framework) {
        $this->id = $args['id'];
        $this->title = $args['title'];
        $this->menu_title = !empty($args['menu_title']) ? $args['menu_title'] : $args['title'];
        $this->menu_slug = !empty($args['menu_slug']) ? $args['menu_slug'] : sanitize_title($args['id']);
        $this->parent_slug = $args['parent_slug'];
        $this->capability = $args['capability'];
        $this->icon = $args['icon'];
        $this->position = $args['position'];
        $this->option_name = $args['option_name'];
        $this->is_premium = $args['is_premium'];
        $this->route_namespace = $args['route_namespace'];
        $this->enable_search = isset($args['enable_search']) ? (bool) $args['enable_search'] : true;
        $this->show_export_import = isset($args['show_export_import']) ? (bool) $args['show_export_import'] : true;
        $this->show_api = isset($args['show_api']) ? (bool) $args['show_api'] : true;
        $this->framework = $framework;
        
        // Add sections if provided
        if (!empty($args['sections'])) {
            foreach ($args['sections'] as $section) {
                $this->add_section($section);
            }
        }
        
        // Register admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        if (!empty($this->parent_slug)) {
            $this->page_hook = add_submenu_page(
                $this->parent_slug,
                $this->title,
                $this->menu_title,
                $this->capability,
                $this->menu_slug,
                array($this, 'render_page')
            );
        } else {
            $this->page_hook = add_menu_page(
                $this->title,
                $this->menu_title,
                $this->capability,
                $this->menu_slug,
                array($this, 'render_page'),
                $this->icon,
                $this->position
            );
        }
    }
    
    /**
     * Check if current page
     */
    public function is_current_page($hook) {
        return $hook === $this->page_hook || strpos($hook, $this->menu_slug) !== false;
    }
    
    /**
     * Add section
     */
    public function add_section($args) {
        $defaults = array(
            'id' => '',
            'title' => '',
            'description' => '',
            'icon' => '',
            'fields' => array(),
            'subsections' => array(),
            'hide_reset_button' => false,
            'dependency' => array(), // Section-level dependency support
        );
        
        $section = wp_parse_args($args, $defaults);
        
        if (empty($section['id'])) {
            return false;
        }
        
        // Apply filter for adding fields from other plugins
        $section['fields'] = apply_filters('bizzplugin_section_fields', $section['fields'], $section['id'], $this->id);
        // Apply panel-specific filter for individual panel control
        $section['fields'] = apply_filters('bizzplugin_section_fields_' . $this->id, $section['fields'], $section['id'], $this->id);
        
        $this->sections[$section['id']] = $section;
        
        return $this;
    }
    
    /**
     * Get sections
     */
    public function get_sections() {
        // $sections = apply_filters('bizzplugin_panel_sections', $this->sections, $this->id);
        // Apply panel-specific filter for individual panel control
        return apply_filters('bizzplugin_panel_sections_' . $this->id, $this->sections, $this->id);
    }
    
    /**
     * Get section by ID
     */
    public function get_section($section_id) {
        return isset($this->sections[$section_id]) ? $this->sections[$section_id] : null;
    }
    
    /**
     * Get option name
     */
    public function get_option_name() {
        return $this->option_name;
    }
    
    /**
     * Get panel ID
     */
    public function get_id() {
        return $this->id;
    }
    
    /**
     * Is premium active
     */
    public function is_premium() {
        // $is_premium = apply_filters('bizzplugin_is_premium', $this->is_premium, $this->id);
        // Apply panel-specific filter for individual panel control
        return apply_filters('bizzplugin_is_premium_' . $this->id, $this->is_premium, $this->id);
    }
    
    /**
     * Get all fields
     */
    public function get_all_fields() {
        $fields = array();
        
        foreach ($this->sections as $section) {
            if (!empty($section['fields'])) {
                foreach ($section['fields'] as $field) {
                    $fields[$field['id']] = $field;
                }
            }
            
            // Get fields from subsections
            if (!empty($section['subsections'])) {
                foreach ($section['subsections'] as $subsection) {
                    if (!empty($subsection['fields'])) {
                        foreach ($subsection['fields'] as $field) {
                            $fields[$field['id']] = $field;
                        }
                    }
                }
            }
        }
        
        return $fields;
    }
    
    /**
     * GET SECTION DEFAULTS on SCREEN 
     * **********************************************
     * Get section defaults (specific section without subsections)
     * only sections fields, not subsections
     * If on subsection, only that subsection fields of specific subsection by id
     * 
     * @param string $section_id The ID of the section
     * @param string|null $subsection_id Optional ID of the subsection
     * 
     * @return array Default values array of the section fields or subsection fields
     */
    public function get_section_defaults($section_id, $subsection_id = null) {
        $defaults = array();
        $section = $this->get_section($section_id);
        
        if (!$section) {
            return $defaults;
        }
        
        
        
        // // Include subsection fields
        if ( ! empty( $subsection_id ) && !empty($section['subsections'])) {
            foreach ($section['subsections'] as $subsection) {
                if ( $subsection['id'] !== $subsection_id ) {
                    continue;
                }
                if (!empty($subsection['fields'])) {
                    foreach ($subsection['fields'] as $field) {
                        $defaults[$field['id']] = isset($field['default']) ? $field['default'] : '';
                    }
                }
            }
        }else if (!empty($section['fields'])) {
            foreach ($section['fields'] as $field) {
                $defaults[$field['id']] = isset($field['default']) ? $field['default'] : '';
            }
        }
        
        return $defaults;
    }
    
    /**
     * Get all defaults
     */
    public function get_all_defaults() {
        $defaults = array();
        
        foreach ($this->sections as $section_id => $section) {
            $defaults = array_merge($defaults, $this->get_section_defaults($section_id));
        }
        
        return $defaults;
    }
    
    /**
     * Check if section/subsection has editable fields (not just html, info, plugins types)
     */
    private function has_editable_fields($fields) {
        if (empty($fields)) {
            return false;
        }
        
        $non_editable_types = array('html', 'info', 'plugins', 'link', 'heading', 'divider', 'notice');
        
        foreach ($fields as $field) {
            $field_type = isset($field['type']) ? $field['type'] : 'text';
            if (!in_array($field_type, $non_editable_types, true)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Build search keywords from title, description and ID
     * 
     * @param string $title Item title
     * @param string $description Item description
     * @param string $id Item ID
     * @return string Lowercase search keywords string
     */
    private function build_search_keywords($title, $description, $id) {
        return strtolower(
            (isset($title) ? $title : '') . ' ' . 
            (isset($description) ? $description : '') . ' ' . 
            (isset($id) ? $id : '')
        );
    }
    
    /**
     * Build search keywords for fields array
     * 
     * @param array $fields Array of field configurations
     * @return string Lowercase search keywords string
     */
    private function build_fields_search_keywords($fields) {
        $keywords = '';
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $keywords .= ' ' . $this->build_search_keywords(
                    isset($field['title']) ? $field['title'] : '',
                    isset($field['description']) ? $field['description'] : '',
                    isset($field['id']) ? $field['id'] : ''
                );
            }
        }
        return $keywords;
    }
    
    /**
     * Get panel args
     */
    public function get_args() {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'menu_title' => $this->menu_title,
            'menu_slug' => $this->menu_slug,
            'parent_slug' => $this->parent_slug,
            'capability' => $this->capability,
            'icon' => $this->icon,
            'position' => $this->position,
            'option_name' => $this->option_name,
            'is_premium' => $this->is_premium,
        );
    }
    
    /**
     * Add a subsection to an existing section
     * 
     * @param string $section_id The ID of the parent section
     * @param array  $subsection Subsection configuration array
     * @return $this For method chaining
     */
    public function add_subsection($section_id, $subsection) {
        if (!isset($this->sections[$section_id])) {
            return $this;
        }
        
        $defaults = array(
            'id' => '',
            'title' => '',
            'description' => '',
            'icon' => '',
            'fields' => array(),
            'hide_reset_button' => false,
        );
        
        $subsection = wp_parse_args($subsection, $defaults);
        
        if (empty($subsection['id'])) {
            return $this;
        }
        
        if (!isset($this->sections[$section_id]['subsections'])) {
            $this->sections[$section_id]['subsections'] = array();
        }
        
        // Check for duplicate subsection ID and replace if exists
        $found = false;
        foreach ($this->sections[$section_id]['subsections'] as $key => $existing) {
            if ($existing['id'] === $subsection['id']) {
                $this->sections[$section_id]['subsections'][$key] = $subsection;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $this->sections[$section_id]['subsections'][] = $subsection;
        }
        
        return $this;
    }
    
    /**
     * Add a field to a section
     * 
     * @param string $section_id The ID of the section
     * @param array  $field Field configuration array
     * @return $this For method chaining
     */
    public function add_field($section_id, $field) {
        if (!isset($this->sections[$section_id])) {
            return $this;
        }
        
        if (empty($field['id'])) {
            return $this;
        }
        
        $this->sections[$section_id]['fields'][] = $field;
        
        return $this;
    }
    
    /**
     * Add a field to a subsection
     * 
     * @param string $section_id The ID of the parent section
     * @param string $subsection_id The ID of the subsection
     * @param array  $field Field configuration array
     * @return $this For method chaining
     */
    public function add_subsection_field($section_id, $subsection_id, $field) {
        if (!isset($this->sections[$section_id])) {
            return $this;
        }
        
        if (empty($field['id'])) {
            return $this;
        }
        
        if (!isset($this->sections[$section_id]['subsections'])) {
            return $this;
        }
        
        foreach ($this->sections[$section_id]['subsections'] as &$subsection) {
            if ($subsection['id'] === $subsection_id) {
                $subsection['fields'][] = $field;
                break;
            }
        }
        
        return $this;
    }
    
    /**
     * Set resource links for the sidebar
     * 
     * @param array $resources Array of resource links
     * @return $this For method chaining
     */
    public function add_resources($resources) {
        return $this->set_resources($resources);
    }
    public function set_resources($resources) {
        $this->resources = array_merge($this->resources, $resources);
        return $this;
    }
    
    /**
     * Add a single resource link
     * 
     * @param array $resource Resource link configuration (icon, title, url)
     * @return $this For method chaining
     */
    public function add_resource($resource) {
        $defaults = array(
            'icon' => 'dashicons-admin-links',
            'title' => '',
            'url' => '#',
        );
        $this->resources[] = wp_parse_args($resource, $defaults);
        return $this;
    }
    
    /**
     * Get resource links
     * 
     * @return array Resource links array
     */
    public function get_resources() {
        return $this->resources;
    }
    
    /**
     * Set recommended plugins for the sidebar
     * 
     * @param array $plugins Array of recommended plugins
     * @return $this For method chaining
     */
    public function add_recommended_plugins($plugins) {
        return $this->set_recommended_plugins($plugins);
    }
    public function set_recommended_plugins($plugins) {
        $this->recommended_plugins = array_merge($this->recommended_plugins, $plugins);
        return $this;
    }
    
    /**
     * Add a single recommended plugin
     * 
     * @param array $plugin Plugin configuration array
     * @return $this For method chaining
     */
    public function add_recommended_plugin($plugin) {
        $defaults = array(
            'slug' => '',
            'name' => '',
            'description' => '',
            'thumbnail' => '',
            'author' => '',
            'file' => '',
            'url' => '',
        );
        $this->recommended_plugins[] = wp_parse_args($plugin, $defaults);
        return $this;
    }
    
    /**
     * Get recommended plugins
     * 
     * @return array Recommended plugins array
     */
    public function get_recommended_plugins() {
        return $this->recommended_plugins;
    }
    
    /**
     * Set panel configuration value
     * 
     * @param string $key Configuration key (logo, version, footer_text, title, is_premium)
     * @param mixed  $value Configuration value
     * @return $this For method chaining
     */
    public function set_config($key, $value) {
        $this->panel_config[$key] = $value;
        return $this;
    }
    
    /**
     * set route namespace
     *
     * @param [type] $config
     * @return void
     */
    public function set_route_namespace( $namespace ) {
        $this->route_namespace = $namespace;
        return $this;
    }
    public function add_route_namespace( $namespace ) {
        $this->route_namespace = $namespace;
        return $this;
    }
    public function get_route_namespace() {
        return $this->route_namespace;
    }
    /**
     * Set multiple panel configuration values at once
     * 
     * @param array $config Configuration array
     * @return $this For method chaining
     */
    public function set_panel_config($config) {
        $this->panel_config = array_merge($this->panel_config, $config);
        return $this;
    }
    
    /**
     * Get panel configuration
     * 
     * @param string|null $key Optional specific key to get
     * @return mixed Panel configuration array or specific value
     */
    public function get_panel_config($key = null) {
        if ($key !== null) {
            return isset($this->panel_config[$key]) ? $this->panel_config[$key] : null;
        }
        return $this->panel_config;
    }
    
    /**
     * Set the panel logo
     * 
     * @param string $logo_url URL to the logo image
     * @return $this For method chaining
     */
    public function set_logo($logo_url) {
        return $this->set_config('logo', $logo_url);
    }
    
    /**
     * Set the panel version
     * 
     * @param string $version Version string
     * @return $this For method chaining
     */
    public function set_version($version) {
        return $this->set_config('version', $version);
    }
    
    /**
     * Set the panel title (for header display)
     * 
     * @param string $title Title string
     * @return $this For method chaining
     */
    public function set_panel_title($title) {
        return $this->set_config('title', $title);
    }
    
    /**
     * Set premium status
     * 
     * Updates both the instance property (used by is_premium() method and filter)
     * and the panel config (used in render_page() display).
     * 
     * @param bool $is_premium Premium status
     * @return $this For method chaining
     */
    public function set_premium($is_premium) {
        // Update property for is_premium() method
        $this->is_premium = $is_premium;
        // Also store in config for render_page() panel_config merge
        return $this->set_config('is_premium', $is_premium);
    }
    
    /**
     * Set footer text
     * 
     * @param string $text Footer text
     * @return $this For method chaining
     */
    public function set_footer_text($text) {
        return $this->set_config('footer_text', $text);
    }
    
    /**
     * Enable search functionality
     * 
     * @param bool $enable Whether to enable search (default: true)
     * @return $this For method chaining
     */
    public function enable_search($enable = true) {
        $this->enable_search = (bool) $enable;
        return $this;
    }
    
    /**
     * Disable search functionality
     * 
     * @return $this For method chaining
     */
    public function disable_search() {
        $this->enable_search = false;
        return $this;
    }
    
    /**
     * Check if search is enabled
     * 
     * @return bool Whether search is enabled
     */
    public function is_search_enabled() {
        return $this->enable_search;
    }
    
    /**
     * Enable Export/Import section
     * 
     * @param bool $enable Whether to enable Export/Import section (default: true)
     * @return $this For method chaining
     */
    public function enable_export_import($enable = true) {
        $this->show_export_import = (bool) $enable;
        return $this;
    }
    
    /**
     * Disable Export/Import section
     * 
     * @return $this For method chaining
     */
    public function disable_export_import() {
        $this->show_export_import = false;
        return $this;
    }
    
    /**
     * Check if Export/Import section is enabled
     * 
     * @return bool Whether Export/Import section is enabled
     */
    public function is_export_import_enabled() {
        return $this->show_export_import;
    }
    
    /**
     * Enable API & Webhook section
     * 
     * @param bool $enable Whether to enable API section (default: true)
     * @return $this For method chaining
     */
    public function enable_api($enable = true) {
        $this->show_api = (bool) $enable;
        return $this;
    }
    
    /**
     * Disable API & Webhook section
     * 
     * @return $this For method chaining
     */
    public function disable_api() {
        $this->show_api = false;
        return $this;
    }
    
    /**
     * Check if API section is enabled
     * 
     * @return bool Whether API section is enabled
     */
    public function is_api_enabled() {
        return $this->show_api;
    }
    
    /**
     * Remove a section by ID
     * 
     * @param string $section_id Section ID to remove
     * @return $this For method chaining
     */
    public function remove_section($section_id) {
        if (isset($this->sections[$section_id])) {
            unset($this->sections[$section_id]);
        }
        return $this;
    }
    
    /**
     * Remove a field from a section
     * 
     * @param string $section_id Section ID
     * @param string $field_id Field ID to remove
     * @return $this For method chaining
     */
    public function remove_field($section_id, $field_id) {
        if (!isset($this->sections[$section_id])) {
            return $this;
        }
        
        foreach ($this->sections[$section_id]['fields'] as $key => $field) {
            if ($field['id'] === $field_id) {
                array_splice($this->sections[$section_id]['fields'], $key, 1);
                break;
            }
        }
        
        return $this;
    }
    
    /**
     * Remove a subsection from a section
     * 
     * @param string $section_id Section ID
     * @param string $subsection_id Subsection ID to remove
     * @return $this For method chaining
     */
    public function remove_subsection($section_id, $subsection_id) {
        if (!isset($this->sections[$section_id]) || !isset($this->sections[$section_id]['subsections'])) {
            return $this;
        }
        
        foreach ($this->sections[$section_id]['subsections'] as $key => $subsection) {
            if ($subsection['id'] === $subsection_id) {
                array_splice($this->sections[$section_id]['subsections'], $key, 1);
                break;
            }
        }
        
        return $this;
    }
    
    /**
     * Render the settings page
     */
    public function render_page() {
        $options = get_option($this->option_name, array());
        $sections = $this->get_sections();
        
        // Get current section and subsection from URL parameter (sanitized)
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- This is a read-only navigation parameter, not a state-changing action
        $current_section = isset($_GET['section']) ? sanitize_key(wp_unslash($_GET['section'])) : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- This is a read-only navigation parameter, not a state-changing action
        $current_subsection = isset($_GET['subsection']) ? sanitize_key(wp_unslash($_GET['subsection'])) : '';

        $this->current_section = $current_section;
        $this->current_subsection = $current_subsection;
        
        // Validate that the section exists
        if (!empty($current_section) && !isset($sections[$current_section]) && $current_section !== 'api' && $current_section !== 'export_import') {
            $current_section = '';
        }
        
        if (empty($current_section) && !empty($sections)) {
            $first_section = reset($sections);
            $current_section = $first_section['id'];
            
            // If section has no fields but has subsections, auto-select first subsection
            if (empty($first_section['fields']) && !empty($first_section['subsections'])) {
                $first_subsection = reset($first_section['subsections']);
                $current_subsection = $first_subsection['id'];
            }
        }
        
        // Auto-select first subsection if section has no fields
        if (!empty($current_section) && isset($sections[$current_section])) {
            $section_data = $sections[$current_section];
            if (empty($section_data['fields']) && !empty($section_data['subsections']) && empty($current_subsection)) {
                $first_subsection = reset($section_data['subsections']);
                $current_subsection = $first_subsection['id'];
            }
        }
        
        // Allow custom rendering
        if (has_action('bizzplugin_render_panel_' . $this->id)) {
            do_action('bizzplugin_render_panel_' . $this->id, $this, $options, $sections, $current_section, $current_subsection);
            return;
        }
        
        // Build default panel config
        $default_config = array(
            'logo' => $this->framework->get_url() . 'assets/images/logo.png',
            'version' => defined('BIZZPLUGIN_PLUGIN_VERSION') ? BIZZPLUGIN_PLUGIN_VERSION : '1.0.0',
            'is_premium' => $this->is_premium(),
            'resources' => array(
                // array(
                //     'icon' => 'dashicons-media-document',
                //     'title' => __('Documentation', 'bizzplugin-framework'),
                //     'url' => '#',
                // ),
            ),
            'recommended_plugins' => array(),
        );
        
        // Merge with instance-level panel config (set via chainable methods)
        $instance_config = $this->panel_config;
        if( ! is_array( $this->panel_config ) ){
            $this->panel_config = $default_config;
        }
        
        $instance_config = array_merge($default_config, $this->panel_config);
        
        // Add instance-level resources if set
        if (! empty($this->resources) && is_array( $this->resources ) && is_array( $instance_config['resources'] ) ) {
            $instance_config['resources'] = array_merge($instance_config['resources'], $this->resources);
        }else if( is_array( $this->resources ) && ! empty( $this->resources ) ){
            $instance_config['resources'] = $this->resources;
        }
        
        // Add instance-level recommended plugins if set
        if (!empty($this->recommended_plugins) && is_array( $this->recommended_plugins ) && is_array( $instance_config['recommended_plugins'] ) ) {
            $instance_config['recommended_plugins'] = array_merge($instance_config['recommended_plugins'], $this->recommended_plugins);
        }else if( is_array( $this->recommended_plugins ) && ! empty( $this->recommended_plugins ) ){
            $instance_config['recommended_plugins'] = $this->recommended_plugins;
        }
        
        // Merge instance config with defaults (instance config takes priority)
        $panel_config = array_merge($default_config, $instance_config);
        
        // Get panel config for branding (filter allows additional modifications)
        // $panel_config = apply_filters('bizzplugin_panel_config', $panel_config, $this->id);
        // Apply panel-specific filter for individual panel control
        $panel_config = apply_filters('bizzplugin_panel_config_' . $this->id, $panel_config, $this->id);
        ?>
        <div class="wrap bizzplugin-framework-wrap" data-panel-id="<?php echo esc_attr($this->id); ?>" data-option-name="<?php echo esc_attr($this->option_name); ?>">
            <div class="wp-notice-area-wrapper">
                <h2><!-- For Notice Area --></h2>
            </div>           
            <div class="bizzplugin-framework-container">
                <!-- Navigation Sidebar -->
                <div class="bizzplugin-nav">
                    <!-- Logo and Branding -->
                    <div class="bizzplugin-nav-header">
                        <?php if (!empty($panel_config['logo'])) : ?>
                            <img src="<?php echo esc_url($panel_config['logo']); ?>" alt="<?php echo esc_attr($this->title); ?>" class="bizzplugin-nav-logo" />
                        <?php endif; if (!empty($panel_config['title'])) : ?>
                            <h4 class="bizzplugin-nav-title-main"><?php echo esc_html( $panel_config['title'] ); ?></h4>
                        <?php endif; ?>
                        <div class="bizzplugin-nav-meta">
                            <?php if ($panel_config['is_premium']) : ?>
                                <span class="bizzplugin-badge bizzplugin-badge-pro"><?php esc_html_e('Premium', 'bizzplugin-framework'); ?></span>
                            <?php else : ?>
                                <span class="bizzplugin-badge bizzplugin-badge-free"><?php esc_html_e('Free', 'bizzplugin-framework'); ?></span>
                            <?php endif; ?>
                            <span class="bizzplugin-version">v<?php echo esc_html($panel_config['version']); ?></span>
                        </div>
                    </div>
                    
                    <?php if ($this->is_search_enabled()) : ?>
                    <!-- Search Field -->
                    <div class="bizzplugin-search-wrap">
                        <div class="bizzplugin-search-field">
                            <!-- <span class="dashicons dashicons-search bizzplugin-search-icon"></span> -->
                            <input type="text" 
                                   id="bizzplugin-settings-search" 
                                   class="bizzplugin-search-input" 
                                   placeholder="<?php esc_attr_e('Search settings...', 'bizzplugin-framework'); ?>" 
                                   autocomplete="off" />
                            <button type="button" class="bizzplugin-search-clear" style="display:none;" aria-label="<?php esc_attr_e('Clear search', 'bizzplugin-framework'); ?>">
                                <span class="dashicons dashicons-no-alt"></span>
                            </button>
                        </div>
                        <div class="bizzplugin-search-results-info" style="display:none;"></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php do_action('bizzplugin_nav_before_menu', $this->id); ?>
                    
                    <ul class="bizzplugin-nav-menu">
                        <?php foreach ($sections as $section) : 
                            $has_fields = !empty($section['fields']);
                            $has_subsections = !empty($section['subsections']);
                            $is_active_section = ($current_section === $section['id']);
                            $is_expanded = $is_active_section && $has_subsections;
                            
                            // Section dependency support
                            $section_dependency = isset($section['dependency']) ? $section['dependency'] : array();
                            $section_dependency_attr = '';
                            if (!empty($section_dependency)) {
                                $section_dependency_attr = sprintf(
                                    'data-section-dependency="%s" data-section-dependency-value="%s"',
                                    esc_attr($section_dependency['field']),
                                    esc_attr($section_dependency['value'])
                                );
                            }
                            
                            // Build search keywords for section using helper methods
                            $section_search_keywords = $this->build_search_keywords(
                                $section['title'],
                                isset($section['description']) ? $section['description'] : '',
                                $section['id']
                            );
                            if ($has_fields) {
                                $section_search_keywords .= $this->build_fields_search_keywords($section['fields']);
                            }
                            if ($has_subsections) {
                                foreach ($section['subsections'] as $subsection) {
                                    $section_search_keywords .= ' ' . $this->build_search_keywords(
                                        isset($subsection['title']) ? $subsection['title'] : '',
                                        isset($subsection['description']) ? $subsection['description'] : '',
                                        isset($subsection['id']) ? $subsection['id'] : ''
                                    );
                                    if (!empty($subsection['fields'])) {
                                        $section_search_keywords .= $this->build_fields_search_keywords($subsection['fields']);
                                    }
                                }
                            }
                        ?>
                            <li class="bizzplugin-nav-item <?php echo $is_active_section ? 'active' : ''; ?> <?php echo $has_subsections ? 'has-subsections' : ''; ?> <?php echo $is_expanded ? 'expanded' : ''; ?>" data-search-keywords="<?php echo esc_attr($section_search_keywords); ?>" <?php echo $section_dependency_attr; ?>>
                                <a href="#" 
                                   data-section="<?php echo esc_attr($section['id']); ?>" 
                                   data-has-fields="<?php echo $has_fields ? '1' : '0'; ?>"
                                   data-has-subsections="<?php echo $has_subsections ? '1' : '0'; ?>"
                                   class="bizzplugin-nav-link <?php echo ($is_active_section && empty($current_subsection)) ? 'current' : ''; ?>">
                                    <?php if (!empty($section['icon'])) : ?>
                                        <span class="bizzplugin-nav-icon <?php echo esc_attr($section['icon']); ?>"></span>
                                    <?php endif; ?>
                                    <span class="bizzplugin-nav-title"><?php echo esc_html($section['title']); ?></span>
                                    <?php if ($has_subsections) : ?>
                                        <span class="bizzplugin-nav-arrow dashicons dashicons-arrow-down-alt2"></span>
                                    <?php endif; ?>
                                </a>
                                
                                <?php if ($has_subsections) : ?>
                                    <ul class="bizzplugin-nav-submenu" <?php echo $is_expanded ? '' : 'style="display:none;"'; ?>>
                                        <?php foreach ($section['subsections'] as $subsection) : 
                                            $is_active_subsection = ($is_active_section && $current_subsection === $subsection['id']);
                                            
                                            // Build search keywords for subsection using helper methods
                                            $subsection_search_keywords = $this->build_search_keywords(
                                                isset($subsection['title']) ? $subsection['title'] : '',
                                                isset($subsection['description']) ? $subsection['description'] : '',
                                                isset($subsection['id']) ? $subsection['id'] : ''
                                            );
                                            if (!empty($subsection['fields'])) {
                                                $subsection_search_keywords .= $this->build_fields_search_keywords($subsection['fields']);
                                            }
                                        ?>
                                            <li class="bizzplugin-nav-subitem <?php echo $is_active_subsection ? 'active' : ''; ?>" data-search-keywords="<?php echo esc_attr($subsection_search_keywords); ?>">
                                                <a href="#" 
                                                   data-section="<?php echo esc_attr($section['id']); ?>" 
                                                   data-subsection="<?php echo esc_attr($subsection['id']); ?>" 
                                                   class="bizzplugin-nav-sublink <?php echo $is_active_subsection ? 'current' : ''; ?>">
                                                    <?php if (!empty($subsection['icon'])) : ?>
                                                        <span class="bizzplugin-nav-subicon <?php echo esc_attr($subsection['icon']); ?>"></span>
                                                    <?php endif; ?>
                                                    <?php echo esc_html($subsection['title']); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                        
                        <?php if ($this->show_export_import) : ?>
                        <!-- Export/Import Section -->
                        <li class="bizzplugin-nav-item <?php echo ($current_section === 'export_import') ? 'active' : ''; ?>">
                            <a href="#" data-section="export_import" class="bizzplugin-nav-link <?php echo ($current_section === 'export_import') ? 'current' : ''; ?>">
                                <span class="bizzplugin-nav-icon dashicons dashicons-database-export"></span>
                                <span class="bizzplugin-nav-title"><?php esc_html_e('Export/Import', 'bizzplugin-framework'); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($this->show_api) : ?>
                        <!-- API Section -->
                        <li class="bizzplugin-nav-item <?php echo ($current_section === 'api') ? 'active' : ''; ?>">
                            <a href="#" data-section="api" class="bizzplugin-nav-link <?php echo ($current_section === 'api') ? 'current' : ''; ?>">
                                <span class="bizzplugin-nav-icon dashicons dashicons-rest-api"></span>
                                <span class="bizzplugin-nav-title"><?php esc_html_e('API & Webhook', 'bizzplugin-framework'); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <?php do_action('bizzplugin_nav_after_menu', $this->id); ?>
                    
                    <!-- Footer Branding -->
                    <div class="bizzplugin-nav-footer">
                        <span class="bizzplugin-nav-by"><?php esc_html_e('by', 'bizzplugin-framework'); ?></span>
                        <img src="<?php echo esc_url($this->framework->get_url() . 'assets/images/logo.png'); ?>" alt="BizzPlugin" class="bizzplugin-nav-footer-logo" />
                    </div>
                </div>
                
                <!-- Content -->
                <div class="bizzplugin-content">
                    <div class="bizzplugin-save-status"></div>
                    
                    <form id="bizzplugin-options-form" class="bizzplugin-form">
                        <?php wp_nonce_field('bizzplugin_framework_nonce', 'bizzplugin_nonce'); ?>
                        
                        <?php foreach ($sections as $section) : 
                            $has_fields = !empty($section['fields']);
                            $has_subsections = !empty($section['subsections']);
                            $show_section = ($current_section === $section['id']) && (empty($current_subsection) || !$has_subsections);
                            $section_has_editable_fields = $has_fields && $this->has_editable_fields($section['fields']);
                            $hide_reset = isset($section['hide_reset_button']) && $section['hide_reset_button'];
                            $show_reset_button = $section_has_editable_fields && !$hide_reset;
                        ?>
                            <!-- Main Section Content (only fields, not subsections) -->
                            <?php if ($has_fields) : ?>
                            <div class="bizzplugin-section" 
                                 id="section-<?php echo esc_attr($section['id']); ?>" 
                                 data-section="<?php echo esc_attr($section['id']); ?>" 
                                 style="<?php echo !$show_section ? 'display:none;' : ''; ?>">
                                <div class="bizzplugin-section-header">
                                    
                                    <h2><?php echo esc_html($section['title']); ?></h2>
                                    <?php if (!empty($section['description'])) : ?>
                                        <p class="bizzplugin-section-desc"><?php echo esc_html($section['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="bizzplugin-section-content">
                                    <?php $this->render_fields($section['fields'], $options); ?>
                                </div>
                                
                                <?php if ($show_reset_button) : ?>
                                <div class="bizzplugin-section-footer">
                                    <button type="button" class="button bizzplugin-reset-section" data-section="<?php echo esc_attr($section['id']); ?>">
                                        <?php esc_html_e('Reset Section', 'bizzplugin-framework'); ?>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Subsections as separate content areas -->
                            <?php if ($has_subsections) : ?>
                                <?php foreach ($section['subsections'] as $subsection) : 
                                    $show_subsection = ($current_section === $section['id'] && $current_subsection === $subsection['id']);
                                    $subsection_has_editable_fields = !empty($subsection['fields']) && $this->has_editable_fields($subsection['fields']);
                                    $subsection_hide_reset = isset($subsection['hide_reset_button']) && $subsection['hide_reset_button'];
                                    $show_subsection_reset = $subsection_has_editable_fields && !$subsection_hide_reset;
                                ?>
                                <div class="bizzplugin-section bizzplugin-subsection-content" 
                                     id="subsection-<?php echo esc_attr($subsection['id']); ?>" 
                                     data-section="<?php echo esc_attr($section['id']); ?>"
                                     data-subsection="<?php echo esc_attr($subsection['id']); ?>" 
                                     style="<?php echo !$show_subsection ? 'display:none;' : ''; ?>">
                                    <div class="bizzplugin-section-header">
                                        <h2><?php echo esc_html($subsection['title']); ?></h2>
                                        <?php if (!empty($subsection['description'])) : ?>
                                            <p class="bizzplugin-section-desc"><?php echo esc_html($subsection['description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="bizzplugin-section-content">
                                        <?php 
                                        if (!empty($subsection['fields'])) {
                                            $this->render_fields($subsection['fields'], $options);
                                        }
                                        ?>
                                    </div>
                                    
                                    <?php if ($show_subsection_reset) : ?>
                                    <div class="bizzplugin-section-footer">
                                        <button type="button" class="button bizzplugin-reset-section" data-section="<?php echo esc_attr($section['id']); ?>" data-subsection="<?php echo esc_attr($subsection['id']); ?>">
                                            <?php esc_html_e('Reset Section', 'bizzplugin-framework'); ?>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <?php if ($this->show_export_import) : ?>
                        <!-- Export/Import Section -->
                        <?php $this->render_export_import_section(); ?>
                        <?php endif; ?>
                        
                        <?php if ($this->show_api) : ?>
                        <!-- API Section -->
                        <?php $this->render_api_section(); ?>
                        <?php endif; ?>
                    </form>
                    
                    <!-- Footer with Buttons -->
                    <div class="bizzplugin-footer">
                        <button type="button" class="button bizzplugin-reset-all">
                            <?php esc_html_e('RESET', 'bizzplugin-framework'); ?>
                        </button>
                        <button type="button" id="bizzplugin-save-options" class="button button-primary button-large">
                            <?php esc_html_e('SAVE', 'bizzplugin-framework'); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Right Sidebar -->
                <div class="bizzplugin-sidebar">
                    <?php do_action('bizzplugin_sidebar_top', $this->id); ?>
                    
                    <!-- Resources Box -->
                    <?php if (!empty($panel_config['resources'])) : ?>
                    <div class="bizzplugin-sidebar-box bizzplugin-resources-box">
                        
                        <ul class="bizzplugin-resource-list">
                            <?php foreach ($panel_config['resources'] as $resource) : ?>
                            <li class="bizzplugin-resource-item">
                                <a href="<?php echo esc_url($resource['url']); ?>" target="_blank" rel="noopener noreferrer">
                                    <span class="dashicons <?php echo esc_attr($resource['icon']); ?>"></span>
                                    <?php echo esc_html($resource['title']); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php do_action('bizzplugin_sidebar_middle', $this->id); ?>

                    <!-- Recommended Plugins Section / user render plugin -->
                    <?php
                    if (! empty($panel_config['recommended_plugins'])) {
                        $plugins = $panel_config['recommended_plugins'] ?? array();
                        $this->sidebar_recommended_plugins($plugins);
                    }
                    ?>
                    
                    <?php do_action('bizzplugin_sidebar_bottom', $this->id); ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render subsection
     */
    private function render_subsection($subsection, $options) {
        ?>
        <div class="bizzplugin-subsection" id="subsection-<?php echo esc_attr($subsection['id']); ?>" data-subsection="<?php echo esc_attr($subsection['id']); ?>">
            <h3 class="bizzplugin-subsection-title"><?php echo esc_html($subsection['title']); ?></h3>
            <?php if (!empty($subsection['description'])) : ?>
                <p class="bizzplugin-subsection-desc"><?php echo esc_html($subsection['description']); ?></p>
            <?php endif; ?>
            
            <div class="bizzplugin-subsection-fields">
                <?php
                if (!empty($subsection['fields'])) {
                    $this->render_fields($subsection['fields'], $options);
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render fields
     */
    private function render_fields($fields, $options) {
        foreach ($fields as $field) {
            $this->render_field($field, $options);
        }
    }
    
    /**
     * Render single field
     */
    private function render_field($field, $options) {
        $field_id = $field['id'];
        $field_type = isset($field['type']) ? $field['type'] : 'text';
        $field_title = isset($field['title']) ? $field['title'] : '';
        $field_desc = isset($field['description']) ? $field['description'] : '';
        $field_default = isset($field['default']) ? $field['default'] : '';
        $field_value = isset($options[$field_id]) ? $options[$field_id] : $field_default;
        $field_class = isset($field['class']) ? $field['class'] : '';
        $field_premium = isset($field['premium']) ? $field['premium'] : false;
        $is_premium_active = $this->is_premium();
        
        // Build search keywords for this field using helper method
        $search_keywords = $this->build_search_keywords($field_title, $field_desc, $field_id);
        
        // Check for conditional field
        $dependency = isset($field['dependency']) ? $field['dependency'] : array();
        $dependency_attr = '';
        if (!empty($dependency)) {
            $dependency_attr = sprintf(
                'data-dependency="%s" data-dependency-value="%s"',
                esc_attr($dependency['field']),
                esc_attr($dependency['value'])
            );
        }
        
        // Premium field styling
        $premium_class = '';
        $is_disabled = false;
        if ($field_premium && !$is_premium_active) {
            $premium_class = 'bizzplugin-field-premium-locked';
            $is_disabled = true;
        }
        
        ?>
        <div class="bizzplugin-field bizzplugin-field-<?php echo esc_attr($field_type); ?> <?php echo esc_attr($field_class); ?> <?php echo esc_attr($premium_class); ?>" <?php echo $dependency_attr; ?> data-search-keywords="<?php echo esc_attr($search_keywords); ?>">
            <div class="bizzplugin-field-header">
                <label for="<?php echo esc_attr($field_id); ?>" class="bizzplugin-field-title">
                    <?php echo esc_html($field_title); ?>
                    <?php if ($field_premium && !$is_premium_active) : ?>
                        <span class="bizzplugin-premium-badge"><?php esc_html_e('Blocked', 'bizzplugin-framework'); ?></span>
                    <?php endif; ?>
                </label>
            </div>
            
            <div class="bizzplugin-field-content">
                <?php
                // Allow custom field rendering
                if (has_action('bizzplugin_render_field_' . $field_type)) {
                    do_action('bizzplugin_render_field_' . $field_type, $field, $field_value, $is_disabled);
                } else {
                    // Render field based on type
                    switch ($field_type) {
                        case 'text':
                        case 'email':
                        case 'url':
                        case 'number':
                        case 'password':
                            $this->render_input_field($field, $field_value, $field_type, $is_disabled);
                            break;
                        case 'textarea':
                            $this->render_textarea_field($field, $field_value, $is_disabled);
                            break;
                        case 'select':
                            $this->render_select_field($field, $field_value, $is_disabled);
                            break;
                        case 'multi_select':
                            $this->render_multi_select_field($field, $field_value, $is_disabled);
                            break;
                        case 'checkbox':
                            $this->render_checkbox_field($field, $field_value, $is_disabled);
                            break;
                        case 'checkbox_group':
                            $this->render_checkbox_group_field($field, $field_value, $is_disabled);
                            break;
                        case 'radio':
                            $this->render_radio_field($field, $field_value, $is_disabled);
                            break;
                        case 'on_off':
                        case 'switch':
                            $this->render_switch_field($field, $field_value, $is_disabled);
                            break;
                        case 'color':
                            $this->render_color_field($field, $field_value, $is_disabled);
                            break;
                        case 'date':
                            $this->render_date_field($field, $field_value, $is_disabled);
                            break;
                        case 'image':
                            $this->render_image_field($field, $field_value, $is_disabled);
                            break;
                        case 'file':
                            $this->render_file_field($field, $field_value, $is_disabled);
                            break;
                        case 'image_select':
                            $this->render_image_select_field($field, $field_value, $is_disabled);
                            break;
                        case 'option_select':
                            $this->render_option_select_field($field, $field_value, $is_disabled);
                            break;
                        case 'post_select':
                            $this->render_post_select_field($field, $field_value, $is_disabled);
                            break;
                        case 'html':
                            $this->render_html_field($field);
                            break;
                        case 'callback':
                            $this->render_callback_field($field, $field_value, $is_disabled);
                            break;
                        case 'plugins':
                            $this->render_plugins_field($field);
                            break;
                        case 'slider':
                        case 'range':
                            $this->render_slider_field($field, $field_value, $is_disabled);
                            break;
                        case 'repeater':
                            $this->render_repeater_field($field, $field_value, $is_disabled);
                            break;
                        default:
                            // Allow other plugins to add custom field types
                            do_action('bizzplugin_render_custom_field', $field, $field_value, $is_disabled);
                            break;
                    }
                }
                
                // Check for callback
                if (isset($field['callback']) && is_callable($field['callback'])) {
                    call_user_func($field['callback'], $field, $field_value);
                }
                ?>
                
                <?php if (!empty($field_desc)) : ?>
                    <p class="bizzplugin-field-desc"><?php echo wp_kses_post($field_desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render input field (text, email, url, number, password)
     */
    private function render_input_field($field, $value, $type = 'text', $disabled = false) {
        $attrs = '';
        if (isset($field['placeholder'])) {
            $attrs .= sprintf(' placeholder="%s"', esc_attr($field['placeholder']));
        }
        if (isset($field['min']) && $type === 'number') {
            $attrs .= sprintf(' min="%s"', esc_attr($field['min']));
        }
        if (isset($field['max']) && $type === 'number') {
            $attrs .= sprintf(' max="%s"', esc_attr($field['max']));
        }
        if (isset($field['step']) && $type === 'number') {
            $attrs .= sprintf(' step="%s"', esc_attr($field['step']));
        }
        if ($disabled) {
            $attrs .= ' disabled="disabled"';
        }
        ?>
        <input 
            type="<?php echo esc_attr($type); ?>" 
            id="<?php echo esc_attr($field['id']); ?>" 
            name="<?php echo esc_attr($field['id']); ?>" 
            value="<?php echo esc_attr($value); ?>" 
            class="bizzplugin-input bizzplugin-input-<?php echo esc_attr($type); ?>"
            <?php echo $attrs; ?>
        />
        <?php
    }
    
    /**
     * Render textarea field
     */
    private function render_textarea_field($field, $value, $disabled = false) {
        $rows = isset($field['rows']) ? $field['rows'] : 5;
        $attrs = '';
        if (isset($field['placeholder'])) {
            $attrs .= sprintf(' placeholder="%s"', esc_attr($field['placeholder']));
        }
        if ($disabled) {
            $attrs .= ' disabled="disabled"';
        }
        ?>
        <textarea 
            id="<?php echo esc_attr($field['id']); ?>" 
            name="<?php echo esc_attr($field['id']); ?>" 
            rows="<?php echo esc_attr($rows); ?>"
            class="bizzplugin-textarea"
            <?php echo $attrs; ?>
        ><?php echo esc_textarea($value); ?></textarea>
        <?php
    }
    
    /**
     * Render select field
     */
    private function render_select_field($field, $value, $disabled = false) {
        $options = isset($field['options']) ? $field['options'] : array();
        $attrs = $disabled ? ' disabled="disabled"' : '';
        ?>
        <select 
            id="<?php echo esc_attr($field['id']); ?>" 
            name="<?php echo esc_attr($field['id']); ?>"
            class="bizzplugin-select"
            <?php echo $attrs; ?>
        >
            <?php foreach ($options as $opt_value => $opt_label) : ?>
                <option value="<?php echo esc_attr($opt_value); ?>" <?php selected($value, $opt_value); ?>>
                    <?php echo esc_html($opt_label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    /**
     * Render multi select field
     */
    private function render_multi_select_field($field, $value, $disabled = false) {
        $options = isset($field['options']) ? $field['options'] : array();
        $value = is_array($value) ? $value : array();
        $attrs = $disabled ? ' disabled="disabled"' : '';
        ?>
        <select 
            id="<?php echo esc_attr($field['id']); ?>" 
            name="<?php echo esc_attr($field['id']); ?>[]"
            class="bizzplugin-multi-select"
            multiple="multiple"
            <?php echo $attrs; ?>
        >
            <?php foreach ($options as $opt_value => $opt_label) : ?>
                <option value="<?php echo esc_attr($opt_value); ?>" <?php selected(in_array($opt_value, $value), true); ?>>
                    <?php echo esc_html($opt_label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    /**
     * Render checkbox field
     */
    private function render_checkbox_field($field, $value, $disabled = false) {
        $attrs = $disabled ? ' disabled="disabled"' : '';
        $label = isset($field['label']) ? $field['label'] : '';
        ?>
        <label class="bizzplugin-checkbox-label">
            <input 
                type="checkbox" 
                id="<?php echo esc_attr($field['id']); ?>" 
                name="<?php echo esc_attr($field['id']); ?>" 
                value="1"
                class="bizzplugin-checkbox"
                <?php checked($value, '1'); ?>
                <?php echo $attrs; ?>
            />
            <span class="bizzplugin-checkbox-text"><?php echo esc_html($label); ?></span>
        </label>
        <?php
    }
    
    /**
     * Render checkbox group field
     */
    private function render_checkbox_group_field($field, $value, $disabled = false) {
        $options = isset($field['options']) ? $field['options'] : array();
        $value = is_array($value) ? $value : array();
        $attrs = $disabled ? ' disabled="disabled"' : '';
        ?>
        <div class="bizzplugin-checkbox-group">
            <?php foreach ($options as $opt_value => $opt_label) : ?>
                <label class="bizzplugin-checkbox-label">
                    <input 
                        type="checkbox" 
                        name="<?php echo esc_attr($field['id']); ?>[]" 
                        value="<?php echo esc_attr($opt_value); ?>"
                        class="bizzplugin-checkbox"
                        <?php checked(in_array($opt_value, $value), true); ?>
                        <?php echo $attrs; ?>
                    />
                    <span class="bizzplugin-checkbox-text"><?php echo esc_html($opt_label); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render radio field
     */
    private function render_radio_field($field, $value, $disabled = false) {
        $options = isset($field['options']) ? $field['options'] : array();
        $attrs = $disabled ? ' disabled="disabled"' : '';
        ?>
        <div class="bizzplugin-radio-group">
            <?php foreach ($options as $opt_value => $opt_label) : ?>
                <label class="bizzplugin-radio-label">
                    <input 
                        type="radio" 
                        name="<?php echo esc_attr($field['id']); ?>" 
                        value="<?php echo esc_attr($opt_value); ?>"
                        class="bizzplugin-radio"
                        <?php checked($value, $opt_value); ?>
                        <?php echo $attrs; ?>
                    />
                    <span class="bizzplugin-radio-text"><?php echo esc_html($opt_label); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render switch toggle field (also known as on_off)
     */
    private function render_switch_field($field, $value, $disabled = false) {
        $on_label = isset($field['on_label']) ? $field['on_label'] : __('On', 'bizzplugin-framework');
        $off_label = isset($field['off_label']) ? $field['off_label'] : __('Off', 'bizzplugin-framework');
        $attrs = $disabled ? ' disabled="disabled"' : '';
        ?>
        <div class="bizzplugin-toggle-wrap">
            <label class="bizzplugin-toggle">
                <input 
                    type="checkbox" 
                    id="<?php echo esc_attr($field['id']); ?>" 
                    name="<?php echo esc_attr($field['id']); ?>" 
                    value="1"
                    class="bizzplugin-toggle-input"
                    <?php checked($value, '1'); ?>
                    <?php echo $attrs; ?>
                />
                <span class="bizzplugin-toggle-slider"></span>
                <span class="bizzplugin-toggle-on"><?php echo esc_html($on_label); ?></span>
                <span class="bizzplugin-toggle-off"><?php echo esc_html($off_label); ?></span>
            </label>
        </div>
        <?php
    }
    
    /**
     * Render color picker field
     */
    private function render_color_field($field, $value, $disabled = false) {
        $default = isset($field['default']) ? $field['default'] : '';
        $attrs = $disabled ? ' disabled="disabled"' : '';
        ?>
        <input 
            type="text" 
            id="<?php echo esc_attr($field['id']); ?>" 
            name="<?php echo esc_attr($field['id']); ?>" 
            value="<?php echo esc_attr($value); ?>"
            class="bizzplugin-color-picker"
            data-default-color="<?php echo esc_attr($default); ?>"
            <?php echo $attrs; ?>
        />
        <?php
    }
    
    /**
     * Render date picker field
     */
    private function render_date_field($field, $value, $disabled = false) {
        $attrs = '';
        if (isset($field['placeholder'])) {
            $attrs .= sprintf(' placeholder="%s"', esc_attr($field['placeholder']));
        }
        if ($disabled) {
            $attrs .= ' disabled="disabled"';
        }
        ?>
        <input 
            type="text" 
            id="<?php echo esc_attr($field['id']); ?>" 
            name="<?php echo esc_attr($field['id']); ?>" 
            value="<?php echo esc_attr($value); ?>"
            class="bizzplugin-date-picker"
            <?php echo $attrs; ?>
        />
        <?php
    }
    
    /**
     * Render image upload field
     */
    private function render_image_field($field, $value, $disabled = false) {
        $preview = '';
        if (!empty($value)) {
            $preview = wp_get_attachment_image_url($value, 'thumbnail');
        }
        $attrs = $disabled ? ' disabled="disabled"' : '';
        ?>
        <div class="bizzplugin-image-upload">
            <input 
                type="hidden" 
                id="<?php echo esc_attr($field['id']); ?>" 
                name="<?php echo esc_attr($field['id']); ?>" 
                value="<?php echo esc_attr($value); ?>"
                class="bizzplugin-image-input"
            />
            <div class="bizzplugin-image-preview" <?php echo empty($preview) ? 'style="display:none;"' : ''; ?>>
                <img src="<?php echo esc_url($preview); ?>" alt="" />
                <button type="button" class="bizzplugin-image-remove" <?php echo $attrs; ?>>&times;</button>
            </div>
            <button type="button" class="button bizzplugin-image-select" <?php echo $attrs; ?>>
                <?php esc_html_e('Select Image', 'bizzplugin-framework'); ?>
            </button>
        </div>
        <?php
    }
    
    /**
     * Render file upload field
     */
    private function render_file_field($field, $value, $disabled = false) {
        $file_name = '';
        if (!empty($value)) {
            $file_name = basename(get_attached_file($value));
        }
        $attrs = $disabled ? ' disabled="disabled"' : '';
        ?>
        <div class="bizzplugin-file-upload">
            <input 
                type="hidden" 
                id="<?php echo esc_attr($field['id']); ?>" 
                name="<?php echo esc_attr($field['id']); ?>" 
                value="<?php echo esc_attr($value); ?>"
                class="bizzplugin-file-input"
            />
            <span class="bizzplugin-file-name"><?php echo esc_html($file_name); ?></span>
            <button type="button" class="button bizzplugin-file-select" <?php echo $attrs; ?>>
                <?php esc_html_e('Select File', 'bizzplugin-framework'); ?>
            </button>
            <button type="button" class="button bizzplugin-file-remove" <?php echo $attrs; ?> <?php echo empty($value) ? 'style="display:none;"' : ''; ?>>
                <?php esc_html_e('Remove', 'bizzplugin-framework'); ?>
            </button>
        </div>
        <?php
    }
    
    /**
     * Render image select field
     */
    private function render_image_select_field($field, $value, $disabled = false) {
        $options = isset($field['options']) ? $field['options'] : array();
        ?>
        <div class="bizzplugin-image-select-wrap">
            <?php foreach ($options as $opt_value => $opt_image) : ?>
                <label class="bizzplugin-image-select-item <?php echo ($value === $opt_value) ? 'selected' : ''; ?>">
                    <input 
                        type="radio" 
                        name="<?php echo esc_attr($field['id']); ?>" 
                        value="<?php echo esc_attr($opt_value); ?>"
                        class="bizzplugin-image-select-input"
                        <?php checked($value, $opt_value); ?>
                        <?php echo $disabled ? ' disabled="disabled"' : ''; ?>
                    />
                    <img src="<?php echo esc_url($opt_image); ?>" alt="<?php echo esc_attr($opt_value); ?>" />
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render option select field (text-based option selection similar to image_select)
     */
    private function render_option_select_field($field, $value, $disabled = false) {
        $options = isset($field['options']) ? $field['options'] : array();
        ?>
        <div class="bizzplugin-option-select-wrap">
            <?php foreach ($options as $opt_value => $opt_label) : ?>
                <label class="bizzplugin-option-select-item <?php echo ($value === $opt_value) ? 'selected' : ''; ?>">
                    <input 
                        type="radio" 
                        name="<?php echo esc_attr($field['id']); ?>" 
                        value="<?php echo esc_attr($opt_value); ?>"
                        class="bizzplugin-option-select-input"
                        <?php checked($value, $opt_value); ?>
                        <?php echo $disabled ? ' disabled="disabled"' : ''; ?>
                    />
                    <span class="bizzplugin-option-select-label"><?php echo esc_html($opt_label); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render post select field
     */
    private function render_post_select_field($field, $value, $disabled = false) {
        $post_type = isset($field['post_type']) ? $field['post_type'] : 'post';
        $multiple = isset($field['multiple']) ? $field['multiple'] : false;
        $value = $multiple ? (is_array($value) ? $value : array()) : $value;
        
        $posts = get_posts(array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => 'publish'
        ));
        
        $attrs = $disabled ? ' disabled="disabled"' : '';
        ?>
        <select 
            id="<?php echo esc_attr($field['id']); ?>" 
            name="<?php echo esc_attr($field['id']); ?><?php echo $multiple ? '[]' : ''; ?>"
            class="bizzplugin-post-select"
            <?php echo $multiple ? 'multiple="multiple"' : ''; ?>
            <?php echo $attrs; ?>
        >
            <?php if (!$multiple) : ?>
                <option value=""><?php esc_html_e('Select...', 'bizzplugin-framework'); ?></option>
            <?php endif; ?>
            <?php foreach ($posts as $post) : ?>
                <?php
                $selected = $multiple ? in_array($post->ID, $value) : ($value == $post->ID);
                ?>
                <option value="<?php echo esc_attr($post->ID); ?>" <?php selected($selected, true); ?>>
                    <?php echo esc_html($post->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    /**
     * Render HTML field (for custom content)
     */
    private function render_html_field($field) {
        if (isset($field['content'])) {
            echo wp_kses_post($field['content']);
        }
    }
    
    /**
     * Render callback field
     */
    private function render_callback_field($field, $value, $disabled = false) {
        if (isset($field['render_callback']) && is_callable($field['render_callback'])) {
            call_user_func($field['render_callback'], $field, $value, $disabled);
        }
    }
    
    /**
     * Render plugins field for recommended plugins
     */
    private function render_plugins_field($field) {
        
        $plugins = isset($field['plugins']) ? $field['plugins'] : array();
        
        if (empty($plugins)) {
            return;
        }
        
        // Include plugin functions if not available
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $installed_plugins = get_plugins();



        ?>
        <div class="bizzplugin-plugins-field" data-field-id="<?php echo esc_attr($field['id'] ?? ''); ?>">
            <div class="bizzplugin-plugins-grid">
                <?php foreach ($plugins as $plugin) : 
                    $slug = isset($plugin['slug']) ? $plugin['slug'] : '';
                    $name = isset($plugin['name']) ? $plugin['name'] : $slug;
                    $description = isset($plugin['description']) ? $plugin['description'] : '';
                    $thumbnail = isset($plugin['thumbnail']) ? $plugin['thumbnail'] : '';
                    $plugin_file = isset($plugin['file']) ? $plugin['file'] : $slug . '/' . $slug . '.php';
                    $author = isset($plugin['author']) ? $plugin['author'] : '';
                    $url = isset($plugin['url']) ? $plugin['url'] : 'https://wordpress.org/plugins/' . $slug . '/';
                    
                    // Check plugin status - first check if installed, then if active
                    $is_installed = array_key_exists($plugin_file, $installed_plugins);
                    $is_active = $is_installed && is_plugin_active($plugin_file);
                    
                    // Determine status
                    if ($is_active) {
                        $status = 'active';
                        $status_text = __('Active', 'bizzplugin-framework');
                        $status_class = 'bizzplugin-plugin-status-active';
                    } elseif ($is_installed) {
                        $status = 'installed';
                        $status_text = __('Installed (Inactive)', 'bizzplugin-framework');
                        $status_class = 'bizzplugin-plugin-status-installed';
                    } else {
                        $status = 'not_installed';
                        $status_text = __('Not Installed', 'bizzplugin-framework');
                        $status_class = 'bizzplugin-plugin-status-not-installed';
                    }
                ?>
                <div class="bizzplugin-plugin-card" data-slug="<?php echo esc_attr($slug); ?>" data-file="<?php echo esc_attr($plugin_file); ?>">
                    <?php if (!empty($thumbnail)) : ?>
                    <div class="bizzplugin-plugin-thumbnail">
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($name); ?>">
                    </div>
                    <?php endif; ?>
                    
                    <div class="bizzplugin-plugin-info">
                        <h4 class="bizzplugin-plugin-name">
                            <?php if (!empty($url)) : ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo esc_html($name); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html($name); ?>
                            <?php endif; ?>
                        </h4>
                        
                        <?php if (!empty($author)) : ?>
                        <p class="bizzplugin-plugin-author">
                            <?php echo esc_html__('By', 'bizzplugin-framework') . ' ' . esc_html($author); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($description)) : ?>
                        <p class="bizzplugin-plugin-description"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                        
                        <div class="bizzplugin-plugin-footer">
                            <span class="bizzplugin-plugin-status <?php echo esc_attr($status_class); ?>">
                                <?php echo esc_html($status_text); ?>
                            </span>
                            
                            <div class="bizzplugin-plugin-actions">
                                <?php if ($status === 'not_installed') : ?>
                                    <button type="button" class="button button-primary bizzplugin-install-plugin" 
                                            data-slug="<?php echo esc_attr($slug); ?>" 
                                            data-file="<?php echo esc_attr($plugin_file); ?>">
                                        <span class="dashicons dashicons-download"></span>
                                        <?php esc_html_e('Install', 'bizzplugin-framework'); ?>
                                    </button>
                                <?php elseif ($status === 'installed') : ?>
                                    <button type="button" class="button button-primary bizzplugin-activate-plugin" 
                                            data-slug="<?php echo esc_attr($slug); ?>" 
                                            data-file="<?php echo esc_attr($plugin_file); ?>">
                                        <span class="dashicons dashicons-yes-alt"></span>
                                        <?php esc_html_e('Activate', 'bizzplugin-framework'); ?>
                                    </button>
                                <?php else : ?>
                                    <span class="bizzplugin-plugin-activated">
                                        <span class="dashicons dashicons-yes"></span>
                                        <?php esc_html_e('Activated', 'bizzplugin-framework'); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render slider/range field
     */
    private function render_slider_field($field, $value, $disabled = false) {
        $min = isset($field['min']) ? $field['min'] : 0;
        $max = isset($field['max']) ? $field['max'] : 100;
        $step = isset($field['step']) ? $field['step'] : 1;
        $unit = isset($field['unit']) ? $field['unit'] : '';
        ?>
        <div class="bizzplugin-slider-wrap">
            <div class="bizzplugin-slider-container">
                <input 
                    type="range" 
                    id="<?php echo esc_attr($field['id']); ?>" 
                    name="<?php echo esc_attr($field['id']); ?>" 
                    value="<?php echo esc_attr($value); ?>"
                    min="<?php echo esc_attr($min); ?>"
                    max="<?php echo esc_attr($max); ?>"
                    step="<?php echo esc_attr($step); ?>"
                    class="bizzplugin-slider"
                    <?php disabled($disabled, true); ?>
                />
                <div class="bizzplugin-slider-value">
                    <span class="bizzplugin-slider-value-number"><?php echo esc_html($value); ?></span>
                    <?php if (!empty($unit)) : ?>
                        <span class="bizzplugin-slider-value-unit"><?php echo esc_html($unit); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="bizzplugin-slider-labels">
                <span class="bizzplugin-slider-min"><?php echo esc_html($min); ?><?php echo esc_html($unit); ?></span>
                <span class="bizzplugin-slider-max"><?php echo esc_html($max); ?><?php echo esc_html($unit); ?></span>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render repeater field
     * 
     * @param array $field Field configuration
     * @param mixed $value Current field value (array of items)
     * @param bool  $disabled Whether the field is disabled
     */
    private function render_repeater_field($field, $value, $disabled = false) {
        $sub_fields = isset($field['fields']) ? $field['fields'] : array();
        $button_text = isset($field['button_text']) ? $field['button_text'] : __('Add Item', 'bizzplugin-framework');
        $max_items = isset($field['max_items']) ? intval($field['max_items']) : 0;
        $min_items = isset($field['min_items']) ? intval($field['min_items']) : 0;
        $sortable = isset($field['sortable']) ? (bool) $field['sortable'] : true;
        // allow_add: true by default, set to false to disable adding/removing items (fixed number of items)
        $allow_add = isset($field['allow_add']) ? (bool) $field['allow_add'] : true;
        
        // Ensure value is an array
        if (!is_array($value) || empty($value)) {
            $value = array();
        }
        
        // Ensure minimum items
        while (count($value) < $min_items) {
            $value[] = array();
        }
        ?>
        <div class="bizzplugin-repeater-wrap" 
             data-field-id="<?php echo esc_attr($field['id']); ?>"
             data-max-items="<?php echo esc_attr($max_items); ?>"
             data-min-items="<?php echo esc_attr($min_items); ?>"
             data-sortable="<?php echo esc_attr($sortable ? '1' : '0'); ?>"
             data-allow-add="<?php echo esc_attr($allow_add ? '1' : '0'); ?>">
            
            <div class="bizzplugin-repeater-items">
                <?php 
                if (!empty($value)) {
                    foreach ($value as $index => $item_value) {
                        $this->render_repeater_item($field, $sub_fields, $index, $item_value, $disabled, $min_items, count($value), false, $allow_add);
                    }
                }
                ?>
            </div>
            
            <?php if ($allow_add) : ?>
            <div class="bizzplugin-repeater-footer">
                <button type="button" 
                        class="button button-primary bizzplugin-repeater-add" 
                        <?php echo $disabled ? ' disabled="disabled"' : ''; ?>
                        <?php echo ($max_items > 0 && count($value) >= $max_items) ? ' style="display:none;"' : ''; ?>>
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php echo esc_html($button_text); ?>
                </button>
            </div>
            
            <!-- Template for new items (hidden div container) -->
            <div class="bizzplugin-repeater-template" style="display:none !important;" aria-hidden="true">
                <?php $this->render_repeater_item($field, $sub_fields, '{{INDEX}}', array(), $disabled, $min_items, 0, true, $allow_add); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render a single repeater item
     * 
     * @param array  $field Parent field configuration
     * @param array  $sub_fields Sub-fields configuration
     * @param mixed  $index Item index (numeric or {{INDEX}} for template)
     * @param array  $item_value Current item values
     * @param bool   $disabled Whether the field is disabled
     * @param int    $min_items Minimum items allowed
     * @param int    $current_count Current number of items
     * @param bool   $is_template Whether this is a template render
     * @param bool   $allow_add Whether adding/removing items is allowed
     */
    private function render_repeater_item($field, $sub_fields, $index, $item_value, $disabled, $min_items, $current_count, $is_template = false, $allow_add = true) {
        // If allow_add is false, never show remove button
        $can_remove = $allow_add && ($current_count > $min_items || $is_template);
        ?>
        <div class="bizzplugin-repeater-item" data-index="<?php echo esc_attr($index); ?>">
            <div class="bizzplugin-repeater-item-header">
                <span class="bizzplugin-repeater-item-handle dashicons dashicons-move" title="<?php esc_attr_e('Drag to reorder', 'bizzplugin-framework'); ?>"></span>
                <span class="bizzplugin-repeater-item-title">
                    <?php 
                    // Try to get title from first text field value
                    $item_title = '';
                    if (!$is_template && !empty($sub_fields)) {
                        foreach ($sub_fields as $sf) {
                            $sf_type = isset($sf['type']) ? $sf['type'] : 'text';
                            if (in_array($sf_type, array('text', 'email', 'url'), true) && !empty($item_value[$sf['id']])) {
                                $item_title = $item_value[$sf['id']];
                                break;
                            }
                        }
                    }
                    if (empty($item_title)) {
                        $display_index = $is_template ? '{{DISPLAY_INDEX}}' : ($index + 1);
                        echo sprintf(esc_html__('Item #%s', 'bizzplugin-framework'), $display_index);
                    } else {
                        echo esc_html($item_title);
                    }
                    ?>
                </span>
                <div class="bizzplugin-repeater-item-actions">
                    <button type="button" class="bizzplugin-repeater-item-toggle" title="<?php esc_attr_e('Toggle', 'bizzplugin-framework'); ?>">
                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                    </button>
                    <?php if ($allow_add) : ?>
                    <button type="button" 
                            class="bizzplugin-repeater-item-remove" 
                            title="<?php esc_attr_e('Remove', 'bizzplugin-framework'); ?>"
                            <?php echo (!$can_remove && !$is_template) ? ' style="display:none;"' : ''; ?>
                            <?php echo $disabled ? ' disabled="disabled"' : ''; ?>>
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="bizzplugin-repeater-item-content">
                <?php
                foreach ($sub_fields as $sub_field) {
                    $sub_field_id = $sub_field['id'];
                    $sub_field_type = isset($sub_field['type']) ? $sub_field['type'] : 'text';
                    $sub_field_default = isset($sub_field['default']) ? $sub_field['default'] : '';
                    $sub_field_value = isset($item_value[$sub_field_id]) ? $item_value[$sub_field_id] : $sub_field_default;
                    
                    // Build the input name: fieldname[index][subfield_id]
                    $input_name = $field['id'] . '[' . $index . '][' . $sub_field_id . ']';
                    $input_id = $field['id'] . '_' . $index . '_' . $sub_field_id;
                    ?>
                    <div class="bizzplugin-repeater-subfield bizzplugin-repeater-subfield-<?php echo esc_attr($sub_field_type); ?>">
                        <label class="bizzplugin-repeater-subfield-label" for="<?php echo esc_attr($input_id); ?>">
                            <?php echo esc_html(isset($sub_field['title']) ? $sub_field['title'] : $sub_field_id); ?>
                        </label>
                        <div class="bizzplugin-repeater-subfield-input">
                            <?php
                            $this->render_repeater_subfield($sub_field, $sub_field_value, $input_name, $input_id, $disabled);
                            ?>
                        </div>
                        <?php if (!empty($sub_field['description'])) : ?>
                            <p class="bizzplugin-repeater-subfield-desc"><?php echo esc_html($sub_field['description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render a subfield inside repeater item
     * 
     * @param array  $sub_field Subfield configuration
     * @param mixed  $value Current value
     * @param string $name Input name attribute
     * @param string $id Input id attribute
     * @param bool   $disabled Whether the field is disabled
     */
    private function render_repeater_subfield($sub_field, $value, $name, $id, $disabled) {
        $type = isset($sub_field['type']) ? $sub_field['type'] : 'text';
        $placeholder = isset($sub_field['placeholder']) ? $sub_field['placeholder'] : '';
        $attrs = $disabled ? ' disabled="disabled"' : '';
        
        switch ($type) {
            case 'text':
            case 'email':
            case 'url':
            case 'number':
            case 'password':
                $min_attr = isset($sub_field['min']) ? ' min="' . esc_attr($sub_field['min']) . '"' : '';
                $max_attr = isset($sub_field['max']) ? ' max="' . esc_attr($sub_field['max']) . '"' : '';
                $step_attr = isset($sub_field['step']) ? ' step="' . esc_attr($sub_field['step']) . '"' : '';
                ?>
                <input 
                    type="<?php echo esc_attr($type); ?>" 
                    id="<?php echo esc_attr($id); ?>" 
                    name="<?php echo esc_attr($name); ?>" 
                    value="<?php echo esc_attr($value); ?>" 
                    class="bizzplugin-input bizzplugin-input-<?php echo esc_attr($type); ?>"
                    placeholder="<?php echo esc_attr($placeholder); ?>"
                    <?php echo $min_attr . $max_attr . $step_attr . $attrs; ?>
                />
                <?php
                break;
                
            case 'textarea':
                $rows = isset($sub_field['rows']) ? $sub_field['rows'] : 3;
                ?>
                <textarea 
                    id="<?php echo esc_attr($id); ?>" 
                    name="<?php echo esc_attr($name); ?>" 
                    rows="<?php echo esc_attr($rows); ?>"
                    class="bizzplugin-textarea"
                    placeholder="<?php echo esc_attr($placeholder); ?>"
                    <?php echo $attrs; ?>
                ><?php echo esc_textarea($value); ?></textarea>
                <?php
                break;
                
            case 'select':
                $options = isset($sub_field['options']) ? $sub_field['options'] : array();
                ?>
                <select 
                    id="<?php echo esc_attr($id); ?>" 
                    name="<?php echo esc_attr($name); ?>"
                    class="bizzplugin-select"
                    <?php echo $attrs; ?>
                >
                    <?php foreach ($options as $opt_value => $opt_label) : ?>
                        <option value="<?php echo esc_attr($opt_value); ?>" <?php selected($value, $opt_value); ?>>
                            <?php echo esc_html($opt_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php
                break;
                
            case 'checkbox':
                $label = isset($sub_field['label']) ? $sub_field['label'] : '';
                ?>
                <label class="bizzplugin-checkbox-label">
                    <input 
                        type="checkbox" 
                        id="<?php echo esc_attr($id); ?>" 
                        name="<?php echo esc_attr($name); ?>" 
                        value="1"
                        class="bizzplugin-checkbox"
                        <?php checked($value, '1'); ?>
                        <?php echo $attrs; ?>
                    />
                    <span class="bizzplugin-checkbox-text"><?php echo esc_html($label); ?></span>
                </label>
                <?php
                break;
                
            case 'color':
                ?>
                <input 
                    type="text" 
                    id="<?php echo esc_attr($id); ?>" 
                    name="<?php echo esc_attr($name); ?>" 
                    value="<?php echo esc_attr($value); ?>"
                    class="bizzplugin-color-picker bizzplugin-repeater-color"
                    <?php echo $attrs; ?>
                />
                <?php
                break;
                
            case 'image':
                $preview = '';
                if (!empty($value)) {
                    $preview = wp_get_attachment_image_url($value, 'thumbnail');
                }
                ?>
                <div class="bizzplugin-image-upload bizzplugin-repeater-image">
                    <input 
                        type="hidden" 
                        id="<?php echo esc_attr($id); ?>" 
                        name="<?php echo esc_attr($name); ?>" 
                        value="<?php echo esc_attr($value); ?>"
                        class="bizzplugin-image-input"
                    />
                    <div class="bizzplugin-image-preview" <?php echo empty($preview) ? 'style="display:none;"' : ''; ?>>
                        <img src="<?php echo esc_url($preview); ?>" alt="" />
                        <button type="button" class="bizzplugin-image-remove" <?php echo $attrs; ?>>&times;</button>
                    </div>
                    <button type="button" class="button bizzplugin-image-select" <?php echo $attrs; ?>>
                        <?php esc_html_e('Select Image', 'bizzplugin-framework'); ?>
                    </button>
                </div>
                <?php
                break;
                
            default:
                // Fallback to text input
                ?>
                <input 
                    type="text" 
                    id="<?php echo esc_attr($id); ?>" 
                    name="<?php echo esc_attr($name); ?>" 
                    value="<?php echo esc_attr($value); ?>" 
                    class="bizzplugin-input"
                    placeholder="<?php echo esc_attr($placeholder); ?>"
                    <?php echo $attrs; ?>
                />
                <?php
                break;
        }
    }
    
    /**
     * Render plugins field for recommended plugins
     */
    private function sidebar_recommended_plugins($plugins) {
        if (empty($plugins)) {
            return;
        }
        
        // Include plugin functions if not available
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $installed_plugins = get_plugins();
        ?>
        <div class="sidebar-recommended-plugins-area bizzplugin-plugins-field">
            <div class="bizzplugin-plugins-grid">
                <?php foreach ($plugins as $plugin) : 
                    $slug = isset($plugin['slug']) ? $plugin['slug'] : '';
                    $name = isset($plugin['name']) ? $plugin['name'] : $slug;
                    $description = isset($plugin['description']) ? $plugin['description'] : '';
                    $thumbnail = isset($plugin['thumbnail']) ? $plugin['thumbnail'] : '';
                    $plugin_file = isset($plugin['file']) ? $plugin['file'] : $slug . '/' . $slug . '.php';
                    $author = isset($plugin['author']) ? $plugin['author'] : '';
                    $url = isset($plugin['url']) ? $plugin['url'] : 'https://wordpress.org/plugins/' . $slug . '/';
                    
                    // Check plugin status - first check if installed, then if active
                    $is_installed = array_key_exists($plugin_file, $installed_plugins);
                    $is_active = $is_installed && is_plugin_active($plugin_file);
                    
                    // Determine status
                    if ($is_active) {
                        $status = 'active';
                        $status_text = __('Active', 'bizzplugin-framework');
                        $status_class = 'bizzplugin-plugin-status-active';
                    } elseif ($is_installed) {
                        $status = 'installed';
                        $status_text = __('Installed (Inactive)', 'bizzplugin-framework');
                        $status_class = 'bizzplugin-plugin-status-installed';
                    } else {
                        $status = 'not_installed';
                        $status_text = __('Not Installed', 'bizzplugin-framework');
                        $status_class = 'bizzplugin-plugin-status-not-installed';
                    }
                ?>
                <div class="bizzplugin-plugin-card" data-slug="<?php echo esc_attr($slug); ?>" data-file="<?php echo esc_attr($plugin_file); ?>">
                    <?php if (!empty($thumbnail)) : ?>
                    <div class="bizzplugin-plugin-thumbnail">
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($name); ?>">
                    </div>
                    <?php endif; ?>
                    
                    <div class="bizzplugin-plugin-info">
                        <h4 class="bizzplugin-plugin-name">
                            <?php if (!empty($url)) : ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo esc_html($name); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html($name); ?>
                            <?php endif; ?>
                        </h4>
                        
                        <?php if (!empty($author)) : ?>
                        <p class="bizzplugin-plugin-author">
                            <?php echo esc_html__('By', 'bizzplugin-framework') . ' ' . esc_html($author); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($description)) : ?>
                        <p class="bizzplugin-plugin-description"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                        
                        <div class="bizzplugin-plugin-footer">
                            <span class="bizzplugin-plugin-status <?php echo esc_attr($status_class); ?>">
                                <?php echo esc_html($status_text); ?>
                            </span>
                            
                            <div class="bizzplugin-plugin-actions">
                                <?php if ($status === 'not_installed') : ?>
                                    <button type="button" class="button button-primary bizzplugin-install-plugin" 
                                            data-slug="<?php echo esc_attr($slug); ?>" 
                                            data-file="<?php echo esc_attr($plugin_file); ?>">
                                        <span class="dashicons dashicons-download"></span>
                                        <?php esc_html_e('Install', 'bizzplugin-framework'); ?>
                                    </button>
                                <?php elseif ($status === 'installed') : ?>
                                    <button type="button" class="button button-primary bizzplugin-activate-plugin" 
                                            data-slug="<?php echo esc_attr($slug); ?>" 
                                            data-file="<?php echo esc_attr($plugin_file); ?>">
                                        <span class="dashicons dashicons-yes-alt"></span>
                                        <?php esc_html_e('Activate', 'bizzplugin-framework'); ?>
                                    </button>
                                <?php else : ?>
                                    <span class="bizzplugin-plugin-activated">
                                        <span class="dashicons dashicons-yes"></span>
                                        <?php esc_html_e('Activated', 'bizzplugin-framework'); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Export/Import section
     */
    private function render_export_import_section() {
        $show = ( $this->current_section === 'export_import' ) ? '' : 'display:none;';
        ?>
        <div class="bizzplugin-section bizzplugin-export-import-section" id="section-export_import" data-section="export_import" style="<?php echo esc_attr( $show ); ?>">
            <div class="bizzplugin-section-header">
                <h2><?php esc_html_e('Export & Import Settings', 'bizzplugin-framework'); ?></h2>
                <p class="bizzplugin-section-desc"><?php esc_html_e('Export your current settings to a JSON file or import settings from a previously exported file.', 'bizzplugin-framework'); ?></p>
            </div>
            
            <div class="bizzplugin-section-content">
                <!-- Export Card -->
                <div class="bizzplugin-api-card">
                    <div class="bizzplugin-api-card-header">
                        <span class="dashicons dashicons-database-export"></span>
                        <h3><?php esc_html_e('Export Settings', 'bizzplugin-framework'); ?></h3>
                    </div>
                    <div class="bizzplugin-api-card-body">
                        <p class="bizzplugin-field-desc" style="margin-bottom: 15px;">
                            <?php esc_html_e('Export your current settings to a JSON file. This file can be used to restore your settings later or transfer them to another site.', 'bizzplugin-framework'); ?>
                        </p>
                        <button type="button" id="bizzplugin-export-options" class="button button-primary">
                            <span class="dashicons dashicons-download" style="margin-right: 5px; margin-top: 3px;"></span>
                            <?php esc_html_e('Export Settings', 'bizzplugin-framework'); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Import Card -->
                <div class="bizzplugin-api-card">
                    <div class="bizzplugin-api-card-header">
                        <span class="dashicons dashicons-database-import"></span>
                        <h3><?php esc_html_e('Import Settings', 'bizzplugin-framework'); ?></h3>
                    </div>
                    <div class="bizzplugin-api-card-body">
                        <p class="bizzplugin-field-desc" style="margin-bottom: 15px;">
                            <?php esc_html_e('Import settings from a previously exported JSON file. After importing, click the Save button to apply the imported settings.', 'bizzplugin-framework'); ?>
                        </p>
                        <input type="file" id="bizzplugin-import-file" accept=".json" style="display: none;" />
                        <button type="button" id="bizzplugin-import-trigger" class="button button-secondary">
                            <span class="dashicons dashicons-upload" style="margin-right: 5px; margin-top: 3px;"></span>
                            <?php esc_html_e('Import Settings', 'bizzplugin-framework'); ?>
                        </button>
                        <p class="bizzplugin-api-note" style="margin-top: 15px;">
                            <span class="dashicons dashicons-warning"></span>
                            <?php esc_html_e('Warning: Importing will overwrite your current settings. Make sure to export your current settings first if you want to keep them.', 'bizzplugin-framework'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render API section
     */
    private function render_api_section() {
        $rest_url = rest_url( $this->get_route_namespace() . '/' . $this->option_name);
        $webhook_url = get_option('bizzplugin_webhook_' . $this->option_name, '');
        $webhook_secret = get_option('bizzplugin_webhook_secret_' . $this->option_name, '');
        $api_key = BizzPlugin_API_Handler::get_api_key($this->id);

        $show = ( $this->current_section === 'api' ) ? '' : 'display:none;';
        ?>
        <div class="bizzplugin-section bizzplugin-api-section" id="section-api" data-section="api" style="<?php echo esc_attr( $show ); ?>">
            <div class="bizzplugin-section-header">
                <h2><?php esc_html_e('API & Webhook Settings', 'bizzplugin-framework'); ?></h2>
                <p class="bizzplugin-section-desc"><?php esc_html_e('Configure API access and webhook notifications for this settings panel.', 'bizzplugin-framework'); ?></p>
            </div>
            
            <div class="bizzplugin-section-content">
                <!-- API Key Card -->
                <div class="bizzplugin-api-card">
                    <div class="bizzplugin-api-card-header">
                        <span class="dashicons dashicons-admin-network"></span>
                        <h3><?php esc_html_e('API Key Authentication', 'bizzplugin-framework'); ?></h3>
                    </div>
                    <div class="bizzplugin-api-card-body">
                        <p class="bizzplugin-field-desc" style="margin-bottom: 15px;">
                            Generate an API key to authenticate REST API requests. This key is specific to this panel only. Include the API key in the <code>x-api-key</code> header of your requests.
                        </p>
                        
                        <div class="bizzplugin-api-key-field">
                            <label class="bizzplugin-field-title">
                                API Key
                            </label>
                            <div class="bizzplugin-api-key-display">
                                <?php if (!empty($api_key)) : ?>
                                <div class="bizzplugin-api-key-row">
                                    <code class="bizzplugin-api-key-code" id="bizzplugin-api-key-value"><?php echo esc_html($api_key); ?></code>
                                    <button type="button" class="button bizzplugin-copy-btn" data-copy="<?php echo esc_attr($api_key); ?>">
                                        <span class="dashicons dashicons-admin-page"></span>
                                    </button>
                                </div>
                                <?php else : ?>
                                <div class="bizzplugin-api-key-row" id="bizzplugin-no-api-key">
                                    <span class="bizzplugin-no-key-text">
                                        No API key generated yet.
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div style="margin-top: 15px;">
                                <button type="button" id="bizzplugin-generate-api-key" class="button button-primary">
                                    <span class="dashicons dashicons-update" style="margin-right: 5px; margin-top: 3px;"></span>
                                    <?php echo empty($api_key) ? esc_html__('Generate API Key', 'bizzplugin-framework') : esc_html__('Regenerate API Key', 'bizzplugin-framework'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <p class="bizzplugin-api-note">
                            <span class="dashicons dashicons-warning"></span>
                            Keep your API key secure. Regenerating will invalidate the previous key for this panel only.
                        </p>
                    </div>
                </div>
                
                <!-- REST API Info Card -->
                <div class="bizzplugin-api-card">
                    <div class="bizzplugin-api-card-header">
                        <span class="dashicons dashicons-rest-api"></span>
                        <h3><?php esc_html_e('REST API Endpoints', 'bizzplugin-framework'); ?></h3>
                    </div>
                    <div class="bizzplugin-api-card-body">
                        <div class="bizzplugin-api-endpoint-row">
                            <span class="bizzplugin-method bizzplugin-method-get">GET</span>
                            <code class="bizzplugin-endpoint-url"><?php echo esc_url($rest_url); ?></code>
                            <button type="button" class="button bizzplugin-copy-btn" data-copy="<?php echo esc_attr($rest_url); ?>">
                                <span class="dashicons dashicons-admin-page"></span>
                            </button>
                        </div>
                        <div class="bizzplugin-api-endpoint-row">
                            <span class="bizzplugin-method bizzplugin-method-get">GET</span>
                            <code class="bizzplugin-endpoint-url"><?php echo esc_url($rest_url); ?>/{field_id}</code>
                            <button type="button" class="button bizzplugin-copy-btn" data-copy="<?php echo esc_attr($rest_url); ?>">
                                <span class="dashicons dashicons-admin-page"></span>
                            </button>
                        </div>
                        <div class="bizzplugin-api-endpoint-row">
                            <span class="bizzplugin-method bizzplugin-method-post">POST</span>
                            <code class="bizzplugin-endpoint-url"><?php echo esc_url($rest_url); ?></code>
                            <button type="button" class="button bizzplugin-copy-btn" data-copy="<?php echo esc_attr($rest_url); ?>">
                                <span class="dashicons dashicons-admin-page"></span>
                            </button>
                        </div>
                        <div class="bizzplugin-api-endpoint-row">
                            <span class="bizzplugin-method bizzplugin-method-post">POST</span>
                            <code class="bizzplugin-endpoint-url"><?php echo esc_url($rest_url); ?>/{field_id}</code>
                            <button type="button" class="button bizzplugin-copy-btn" data-copy="<?php echo esc_attr($rest_url); ?>">
                                <span class="dashicons dashicons-admin-page"></span>
                            </button>
                        </div>
                        <p class="bizzplugin-api-note">
                            <span class="dashicons dashicons-info-outline"></span>
                            <?php esc_html_e('Authentication required: Include x-api-key header with your API key in all requests.', 'bizzplugin-framework'); ?>
                        </p>
                    </div>
                </div>
                
                <!-- Webhook Settings Card -->
                <div class="bizzplugin-api-card bizzplugin-webhook-card">
                    <div class="bizzplugin-api-card-header">
                        <span class="dashicons dashicons-randomize"></span>
                        <h3><?php esc_html_e('Webhook Configuration', 'bizzplugin-framework'); ?></h3>
                    </div>
                    <div class="bizzplugin-api-card-body">
                        <?php $this->render_advanced_webhooks_ui(); ?>
                        
                        <?php if (!empty($webhook_secret)) : ?>
                        <div class="bizzplugin-webhook-secret" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                            <label class="bizzplugin-field-title">
                                <?php esc_html_e('Webhook Secret (for signature verification)', 'bizzplugin-framework'); ?>
                                <span class="bizzplugin-field-info dashicons dashicons-info-outline" title="<?php esc_attr_e('Use this secret to verify webhook signatures in X-BizzPlugin-Signature header', 'bizzplugin-framework'); ?>"></span>
                            </label>
                            <div class="bizzplugin-secret-display">
                                <code class="bizzplugin-secret-code"><?php echo esc_html(substr($webhook_secret, 0, 8) . '...' . substr($webhook_secret, -8)); ?></code>
                                <button type="button" class="button bizzplugin-copy-btn" data-copy="<?php echo esc_attr($webhook_secret); ?>">
                                    <span class="dashicons dashicons-admin-page"></span>
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Webhook Test Response -->
                        <div id="bizzplugin-webhook-response" class="bizzplugin-webhook-response" style="display:none;">
                            <h4><?php esc_html_e('Test Response', 'bizzplugin-framework'); ?></h4>
                            <pre class="bizzplugin-code-block"></pre>
                        </div>
                    </div>
                </div>
                
                <!-- API Documentation Card -->
                <div class="bizzplugin-api-card bizzplugin-api-docs-card">
                    <div class="bizzplugin-api-card-header">
                        <span class="dashicons dashicons-media-document"></span>
                        <h3><?php esc_html_e('Quick Reference', 'bizzplugin-framework'); ?></h3>
                    </div>
                    <div class="bizzplugin-api-card-body">
                        <div class="bizzplugin-docs-tabs">
                            <button type="button" class="bizzplugin-docs-tab active" data-tab="get"><?php esc_html_e('GET Request', 'bizzplugin-framework'); ?></button>
                            <button type="button" class="bizzplugin-docs-tab" data-tab="post"><?php esc_html_e('POST Request', 'bizzplugin-framework'); ?></button>
                            <button type="button" class="bizzplugin-docs-tab" data-tab="webhook"><?php esc_html_e('Webhook Payload', 'bizzplugin-framework'); ?></button>
                        </div>
                        
                        <div class="bizzplugin-docs-content active" data-tab-content="get">
                            <pre class="bizzplugin-code-block">curl -X GET "<?php echo esc_url($rest_url); ?>" \
  -H "x-api-key: YOUR_API_KEY"</pre><br>
  <pre class="bizzplugin-code-block">curl -X GET "<?php echo esc_url($rest_url); ?>/{field_id}" \
  -H "x-api-key: YOUR_API_KEY"</pre>
                        </div>
                        
                        <div class="bizzplugin-docs-content" data-tab-content="post">
                            <pre class="bizzplugin-code-block">curl -X POST "<?php echo esc_url($rest_url); ?>" \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"field_name": "new_value"}'</pre>
                        <br>
                        <pre class="bizzplugin-code-block">curl -X POST "<?php echo esc_url($rest_url); ?>/{field_id}" \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"value": "new_value"}'</pre>
                        </div>
                        
                        <div class="bizzplugin-docs-content" data-tab-content="webhook">
                            <pre class="bizzplugin-code-block">{
  "event": "settings_saved",
  "option_name": "<?php echo esc_js($this->option_name); ?>",
  "timestamp": "2024-01-01T12:00:00Z",
  "site_url": "<?php echo esc_url(get_site_url()); ?>",
  "data": { "field_name": "value", ... },
  "changed_fields": { "field_name": { "old": "...", "new": "..." } }
}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render advanced webhooks UI
     * 
     * Displays multiple webhook configuration with custom JSON payload,
     * authentication options, and available shortcodes.
     */
    private function render_advanced_webhooks_ui() {
        // Get saved webhooks
        $webhooks = get_option('bizzplugin_webhooks_' . $this->option_name, array());
        
        // Get legacy webhook URL for backward compatibility
        $legacy_url = get_option('bizzplugin_webhook_' . $this->option_name, '');
        if (!empty($legacy_url) && empty($webhooks)) {
            $webhooks = array(
                array(
                    'url' => $legacy_url,
                    'enabled' => true,
                    'auth_type' => 'none',
                    'custom_payload' => '',
                ),
            );
        }
        
        // Get available shortcodes
        $shortcodes = BizzPlugin_Webhook_Handler::get_available_shortcodes();
        ?>
        <div class="bizzplugin-webhooks-container" data-option-name="<?php echo esc_attr($this->option_name); ?>">
            <p class="bizzplugin-field-desc" style="margin-bottom: 15px;">
                <?php esc_html_e('Configure multiple webhooks to receive POST notifications when settings are saved. Each webhook can have its own URL, authentication, and custom payload format.', 'bizzplugin-framework'); ?>
            </p>
            
            <div class="bizzplugin-webhooks-list">
                <?php 
                if (!empty($webhooks)) {
                    foreach ($webhooks as $index => $webhook) {
                        $this->render_webhook_item($index, $webhook);
                    }
                }
                ?>
            </div>
            
            <div class="bizzplugin-webhooks-footer" style="margin-top: 15px;">
                <button type="button" class="button button-primary bizzplugin-add-webhook">
                    <span class="dashicons dashicons-plus-alt2" style="margin-top: 3px;"></span>
                    <?php esc_html_e('Add Webhook', 'bizzplugin-framework'); ?>
                </button>
            </div>
            
            <!-- Webhook Template (hidden) -->
            <div class="bizzplugin-webhook-template" style="display: none !important;" aria-hidden="true">
                <?php $this->render_webhook_item('{{INDEX}}', array(), true); ?>
            </div>
            
            <!-- Available Shortcodes Reference -->
            <div class="bizzplugin-shortcodes-reference" style="margin-top: 20px; padding: 15px; background: #f0f0f1; border-radius: 4px;">
                <h4 style="margin-top: 0; margin-bottom: 10px;">
                    <span class="dashicons dashicons-shortcode" style="margin-right: 5px;"></span>
                    <?php esc_html_e('Available Shortcodes for Custom Payload', 'bizzplugin-framework'); ?>
                </h4>
                <div class="bizzplugin-shortcodes-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 8px;">
                    <?php foreach ($shortcodes as $shortcode => $description) : ?>
                        <div class="bizzplugin-shortcode-item" style="display: flex; align-items: center; gap: 8px;">
                            <code style="background: #fff; padding: 2px 6px; border-radius: 3px; font-size: 12px;"><?php echo esc_html($shortcode); ?></code>
                            <span style="color: #646970; font-size: 12px;"><?php echo esc_html($description); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render a single webhook configuration item
     * 
     * @param int|string $index   Item index (number or {{INDEX}} for template)
     * @param array      $webhook Webhook configuration
     * @param bool       $is_template Whether this is a template render
     */
    private function render_webhook_item($index, $webhook = array(), $is_template = false) {
        $defaults = array(
            'url' => '',
            'enabled' => true,
            'auth_type' => 'none',
            'auth_token' => '',
            'auth_username' => '',
            'auth_password' => '',
            'auth_header_name' => 'X-API-Key',
            'auth_api_key' => '',
            'custom_payload' => '',
        );
        $webhook = wp_parse_args($webhook, $defaults);
        
        $name_prefix = 'bizzplugin_webhooks[' . $index . ']';
        $id_prefix = 'bizzplugin_webhook_' . $index;
        ?>
        <div class="bizzplugin-webhook-item" data-index="<?php echo esc_attr($index); ?>" style="border: 1px solid #c3c4c7; border-radius: 4px; padding: 15px; margin-bottom: 15px; background: #fff;">
            <div class="bizzplugin-webhook-item-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="dashicons dashicons-menu" style="cursor: move; color: #c3c4c7;" title="<?php esc_attr_e('Drag to reorder', 'bizzplugin-framework'); ?>"></span>
                    <strong><?php 
                        if ($is_template) {
                            echo esc_html__('Webhook #', 'bizzplugin-framework') . '{{DISPLAY_INDEX}}';
                        } else {
                            echo esc_html(sprintf(__('Webhook #%d', 'bizzplugin-framework'), $index + 1));
                        }
                    ?></strong>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label class="bizzplugin-webhook-enable" style="display: flex; align-items: center; gap: 5px;">
                        <input 
                            type="checkbox" 
                            name="<?php echo esc_attr($name_prefix); ?>[enabled]" 
                            value="1" 
                            <?php checked($webhook['enabled'], true); ?>
                        />
                        <?php esc_html_e('Enabled', 'bizzplugin-framework'); ?>
                    </label>
                    <button type="button" class="button bizzplugin-remove-webhook" title="<?php esc_attr_e('Remove', 'bizzplugin-framework'); ?>">
                        <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
                    </button>
                </div>
            </div>
            
            <div class="bizzplugin-webhook-item-body">
                <!-- URL Field -->
                <div class="bizzplugin-webhook-field-row" style="margin-bottom: 15px;">
                    <label for="<?php echo esc_attr($id_prefix); ?>_url" class="bizzplugin-field-title" style="display: block; margin-bottom: 5px;">
                        <?php esc_html_e('Webhook URL', 'bizzplugin-framework'); ?> <span style="color: #dc3232;">*</span>
                    </label>
                    <input 
                        type="url" 
                        id="<?php echo esc_attr($id_prefix); ?>_url"
                        name="<?php echo esc_attr($name_prefix); ?>[url]" 
                        value="<?php echo esc_url($webhook['url']); ?>"
                        class="bizzplugin-input"
                        style="width: 100%;"
                        placeholder="https://example.com/webhook"
                        required
                    />
                </div>
                
                <!-- Authentication -->
                <div class="bizzplugin-webhook-field-row" style="margin-bottom: 15px;">
                    <label class="bizzplugin-field-title" style="display: block; margin-bottom: 5px;">
                        <?php esc_html_e('Authentication', 'bizzplugin-framework'); ?>
                    </label>
                    <select 
                        name="<?php echo esc_attr($name_prefix); ?>[auth_type]" 
                        class="bizzplugin-select bizzplugin-webhook-auth-type"
                        style="width: 200px;"
                    >
                        <option value="none" <?php selected($webhook['auth_type'], 'none'); ?>><?php esc_html_e('No Authentication', 'bizzplugin-framework'); ?></option>
                        <option value="bearer" <?php selected($webhook['auth_type'], 'bearer'); ?>><?php esc_html_e('Bearer Token', 'bizzplugin-framework'); ?></option>
                        <option value="basic" <?php selected($webhook['auth_type'], 'basic'); ?>><?php esc_html_e('Basic Auth', 'bizzplugin-framework'); ?></option>
                        <option value="api_key" <?php selected($webhook['auth_type'], 'api_key'); ?>><?php esc_html_e('API Key Header', 'bizzplugin-framework'); ?></option>
                    </select>
                    
                    <!-- Bearer Token Fields -->
                    <div class="bizzplugin-auth-fields bizzplugin-auth-bearer" style="margin-top: 10px; <?php echo $webhook['auth_type'] !== 'bearer' ? 'display:none;' : ''; ?>">
                        <input 
                            type="text" 
                            name="<?php echo esc_attr($name_prefix); ?>[auth_token]" 
                            value="<?php echo esc_attr($webhook['auth_token']); ?>"
                            class="bizzplugin-input"
                            style="width: 100%;"
                            placeholder="<?php esc_attr_e('Enter Bearer Token', 'bizzplugin-framework'); ?>"
                        />
                    </div>
                    
                    <!-- Basic Auth Fields -->
                    <div class="bizzplugin-auth-fields bizzplugin-auth-basic" style="margin-top: 10px; display: flex; gap: 10px; <?php echo $webhook['auth_type'] !== 'basic' ? 'display:none;' : ''; ?>">
                        <input 
                            type="text" 
                            name="<?php echo esc_attr($name_prefix); ?>[auth_username]" 
                            value="<?php echo esc_attr($webhook['auth_username']); ?>"
                            class="bizzplugin-input"
                            style="flex: 1;"
                            placeholder="<?php esc_attr_e('Username', 'bizzplugin-framework'); ?>"
                        />
                        <input 
                            type="password" 
                            name="<?php echo esc_attr($name_prefix); ?>[auth_password]" 
                            value="<?php echo esc_attr($webhook['auth_password']); ?>"
                            class="bizzplugin-input"
                            style="flex: 1;"
                            placeholder="<?php esc_attr_e('Password', 'bizzplugin-framework'); ?>"
                        />
                    </div>
                    
                    <!-- API Key Fields -->
                    <div class="bizzplugin-auth-fields bizzplugin-auth-api_key" style="margin-top: 10px; display: flex; gap: 10px; <?php echo $webhook['auth_type'] !== 'api_key' ? 'display:none;' : ''; ?>">
                        <input 
                            type="text" 
                            name="<?php echo esc_attr($name_prefix); ?>[auth_header_name]" 
                            value="<?php echo esc_attr($webhook['auth_header_name']); ?>"
                            class="bizzplugin-input"
                            style="width: 150px;"
                            placeholder="<?php esc_attr_e('Header Name', 'bizzplugin-framework'); ?>"
                        />
                        <input 
                            type="text" 
                            name="<?php echo esc_attr($name_prefix); ?>[auth_api_key]" 
                            value="<?php echo esc_attr($webhook['auth_api_key']); ?>"
                            class="bizzplugin-input"
                            style="flex: 1;"
                            placeholder="<?php esc_attr_e('API Key Value', 'bizzplugin-framework'); ?>"
                        />
                    </div>
                </div>
                
                <!-- Custom Payload -->
                <div class="bizzplugin-webhook-field-row">
                    <label for="<?php echo esc_attr($id_prefix); ?>_payload" class="bizzplugin-field-title" style="display: block; margin-bottom: 5px;">
                        <?php esc_html_e('Custom JSON Payload (Optional)', 'bizzplugin-framework'); ?>
                        <span class="bizzplugin-field-info dashicons dashicons-info-outline" title="<?php esc_attr_e('Leave empty to use default payload. Use shortcodes for dynamic values.', 'bizzplugin-framework'); ?>"></span>
                    </label>
                    <textarea 
                        id="<?php echo esc_attr($id_prefix); ?>_payload"
                        name="<?php echo esc_attr($name_prefix); ?>[custom_payload]" 
                        class="bizzplugin-textarea"
                        rows="6"
                        style="width: 100%; font-family: monospace; font-size: 12px;"
                        placeholder='<?php echo esc_attr('{
  "event": "{{event}}",
  "site": "{{site_url}}",
  "data": {{data}},
  "changed": {{changed_fields}}
}'); ?>'
                    ><?php echo esc_textarea($webhook['custom_payload']); ?></textarea>
                    <p class="bizzplugin-field-desc" style="margin-top: 5px;">
                        <?php esc_html_e('Enter valid JSON with shortcodes. For JSON objects like {{data}}, do not wrap in quotes.', 'bizzplugin-framework'); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function __call($name, $arguments)
    {
        error_log( "Undefined method: " . $name . " has been call from " . __CLASS__ );
        return null;
    }
}
