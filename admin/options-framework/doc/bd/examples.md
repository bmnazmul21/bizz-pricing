# উদাহরণ

এই ডকুমেন্ট সাধারণ ব্যবহারের ক্ষেত্রের জন্য সম্পূর্ণ, কার্যকরী কোড উদাহরণ প্রদান করে।

## মৌলিক প্লাগইন সেটআপ

### ন্যূনতম প্লাগইন উদাহরণ

```php
<?php
/**
 * Plugin Name: আমার সাধারণ প্লাগইন
 * Description: BizzPlugin Options Framework ব্যবহার করে একটি সাধারণ প্লাগইন
 * Version: 1.0.0
 * Text Domain: my-simple-plugin
 */

if (!defined('ABSPATH')) exit;

define('MSP_PATH', plugin_dir_path(__FILE__));
define('MSP_URL', plugin_dir_url(__FILE__));

// অপশন ফ্রেমওয়ার্ক লোড করুন
require_once MSP_PATH . 'options-framework/options-loader.php';

// সেটিংস ইনিশিয়ালাইজ করুন
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $framework->create_panel(array(
        'id'          => 'my_simple_plugin',
        'title'       => __('আমার সাধারণ প্লাগইন', 'my-simple-plugin'),
        'menu_title'  => __('সাধারণ প্লাগইন', 'my-simple-plugin'),
        'menu_slug'   => 'my-simple-plugin',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-generic',
        'position'    => 80,
        'option_name' => 'msp_options',
        'sections'    => array(
            array(
                'id'     => 'general',
                'title'  => __('সাধারণ সেটিংস', 'my-simple-plugin'),
                'icon'   => 'dashicons dashicons-admin-generic',
                'fields' => array(
                    array(
                        'id'          => 'welcome_text',
                        'type'        => 'text',
                        'title'       => __('স্বাগতম টেক্সট', 'my-simple-plugin'),
                        'description' => __('প্রদর্শনের জন্য টেক্সট লিখুন।', 'my-simple-plugin'),
                        'default'     => 'হ্যালো, বিশ্ব!',
                    ),
                    array(
                        'id'          => 'show_welcome',
                        'type'        => 'switch',
                        'title'       => __('স্বাগতম বার্তা দেখান', 'my-simple-plugin'),
                        'default'     => '1',
                    ),
                ),
            ),
        ),
    ));
});

// অপশন পেতে হেল্পার ফাংশন
function msp_get_option($key, $default = '') {
    $options = get_option('msp_options', array());
    return isset($options[$key]) ? $options[$key] : $default;
}

// শর্টকোড উদাহরণ
add_shortcode('msp_welcome', function() {
    if (msp_get_option('show_welcome', '1') !== '1') {
        return '';
    }
    return '<div class="msp-welcome">' . esc_html(msp_get_option('welcome_text', 'হ্যালো, বিশ্ব!')) . '</div>';
});
```

---

## চেইনেবল API সহ সম্পূর্ণ প্লাগইন

```php
<?php
/**
 * Plugin Name: অ্যাডভান্সড প্লাগইন
 * Description: সম্পূর্ণ ফ্রেমওয়ার্ক ফিচার সহ প্লাগইন
 * Version: 1.0.0
 * Text Domain: advanced-plugin
 */

if (!defined('ABSPATH')) exit;

define('AP_VERSION', '1.0.0');
define('AP_PATH', plugin_dir_path(__FILE__));
define('AP_URL', plugin_dir_url(__FILE__));

require_once AP_PATH . 'options-framework/options-loader.php';

class Advanced_Plugin {
    
    private static $instance = null;
    private $is_premium = false;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // লাইসেন্স চেক করুন
        $this->is_premium = $this->check_license();
        
        add_action('init', array($this, 'init_settings'));
        add_filter('bizzplugin_is_premium_advanced_plugin', array($this, 'get_premium_status'), 10, 2);
    }
    
    private function check_license() {
        $key = get_option('ap_license_key', '');
        $status = get_option('ap_license_status', '');
        return !empty($key) && $status === 'valid';
    }
    
    public function get_premium_status($is_premium, $panel_id) {
        return $this->is_premium;
    }
    
    public function init_settings() {
        $framework = bizzplugin_framework();
        
        // প্যানেল তৈরি করুন
        $panel = $framework->create_panel(array(
            'id'          => 'advanced_plugin',
            'title'       => __('অ্যাডভান্সড প্লাগইন', 'advanced-plugin'),
            'menu_title'  => __('অ্যাডভান্সড', 'advanced-plugin'),
            'menu_slug'   => 'advanced-plugin',
            'capability'  => 'manage_options',
            'icon'        => 'dashicons-chart-area',
            'position'    => 81,
            'option_name' => 'ap_options',
        ));
        
        // প্যানেল কনফিগার করুন
        $panel
            ->set_logo(AP_URL . 'assets/logo.png')
            ->set_version(AP_VERSION)
            ->set_panel_title(__('অ্যাডভান্সড প্লাগইন সেটিংস', 'advanced-plugin'))
            ->set_premium($this->is_premium)
            ->set_footer_text(__('© ২০২৪ আমার কোম্পানি', 'advanced-plugin'));
        
        // সাধারণ সেকশন
        $panel->add_section(array(
            'id'          => 'general',
            'title'       => __('সাধারণ', 'advanced-plugin'),
            'description' => __('সাধারণ কনফিগারেশন অপশন।', 'advanced-plugin'),
            'icon'        => 'dashicons dashicons-admin-generic',
            'fields'      => array(
                array(
                    'id'          => 'site_name',
                    'type'        => 'text',
                    'title'       => __('সাইট নাম', 'advanced-plugin'),
                    'description' => __('আপনার সাইটের নাম।', 'advanced-plugin'),
                    'default'     => get_bloginfo('name'),
                ),
                array(
                    'id'          => 'enable_plugin',
                    'type'        => 'switch',
                    'title'       => __('প্লাগইন সক্রিয়', 'advanced-plugin'),
                    'description' => __('প্লাগইন চালু/বন্ধ করুন।', 'advanced-plugin'),
                    'default'     => '1',
                ),
            ),
        ));
        
        // সাবসেকশন যোগ করুন
        $panel->add_subsection('general', array(
            'id'          => 'advanced_options',
            'title'       => __('অ্যাডভান্সড অপশন', 'advanced-plugin'),
            'description' => __('অ্যাডভান্সড কনফিগারেশন।', 'advanced-plugin'),
            'fields'      => array(
                array(
                    'id'          => 'cache_enabled',
                    'type'        => 'checkbox',
                    'title'       => __('ক্যাশ সক্রিয়', 'advanced-plugin'),
                    'default'     => '1',
                    'label'       => __('ক্যাশিং সক্রিয় করুন', 'advanced-plugin'),
                ),
                array(
                    'id'          => 'cache_time',
                    'type'        => 'number',
                    'title'       => __('ক্যাশ সময়কাল', 'advanced-plugin'),
                    'description' => __('সেকেন্ডে সময়।', 'advanced-plugin'),
                    'default'     => 3600,
                    'min'         => 60,
                    'max'         => 86400,
                    'dependency'  => array(
                        'field' => 'cache_enabled',
                        'value' => '1',
                    ),
                ),
            ),
        ));
        
        // চেহারা সেকশন
        $panel->add_section(array(
            'id'          => 'appearance',
            'title'       => __('চেহারা', 'advanced-plugin'),
            'description' => __('ভিজ্যুয়াল কাস্টমাইজেশন।', 'advanced-plugin'),
            'icon'        => 'dashicons dashicons-admin-appearance',
            'fields'      => array(
                array(
                    'id'          => 'primary_color',
                    'type'        => 'color',
                    'title'       => __('প্রাথমিক রঙ', 'advanced-plugin'),
                    'default'     => '#2271b1',
                ),
                array(
                    'id'          => 'layout',
                    'type'        => 'image_select',
                    'title'       => __('লেআউট', 'advanced-plugin'),
                    'default'     => 'sidebar-right',
                    'options'     => array(
                        'sidebar-left'  => AP_URL . 'options-framework/assets/images/sidebar-left.svg',
                        'no-sidebar'    => AP_URL . 'options-framework/assets/images/no-sidebar.svg',
                        'sidebar-right' => AP_URL . 'options-framework/assets/images/sidebar-right.svg',
                    ),
                ),
                array(
                    'id'          => 'font_size',
                    'type'        => 'slider',
                    'title'       => __('ফন্ট সাইজ', 'advanced-plugin'),
                    'default'     => 16,
                    'min'         => 12,
                    'max'         => 24,
                    'step'        => 1,
                    'unit'        => 'px',
                ),
            ),
        ));
        
        // প্রিমিয়াম সেকশন
        $panel->add_section(array(
            'id'          => 'premium',
            'title'       => __('প্রিমিয়াম ফিচার', 'advanced-plugin'),
            'description' => __('শুধু-প্রিমিয়াম ফিচার।', 'advanced-plugin'),
            'icon'        => 'dashicons dashicons-star-filled',
            'fields'      => array(
                array(
                    'id'          => 'analytics',
                    'type'        => 'switch',
                    'title'       => __('অ্যাডভান্সড অ্যানালিটিক্স', 'advanced-plugin'),
                    'default'     => '0',
                    'premium'     => true,
                ),
                array(
                    'id'          => 'custom_templates',
                    'type'        => 'switch',
                    'title'       => __('কাস্টম টেমপ্লেট', 'advanced-plugin'),
                    'default'     => '0',
                    'premium'     => true,
                ),
            ),
        ));
        
        // রিসোর্স
        $panel
            ->add_resource(array(
                'icon'  => 'dashicons dashicons-book',
                'title' => __('ডকুমেন্টেশন', 'advanced-plugin'),
                'url'   => 'https://example.com/docs',
            ))
            ->add_resource(array(
                'icon'  => 'dashicons dashicons-sos',
                'title' => __('সাপোর্ট', 'advanced-plugin'),
                'url'   => 'https://example.com/support',
            ));
        
        // প্রস্তাবিত প্লাগইন
        $panel->add_recommended_plugin(array(
            'slug'        => 'elementor',
            'name'        => 'Elementor',
            'description' => __('পেজ বিল্ডার।', 'advanced-plugin'),
            'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
            'author'      => 'Elementor.com',
            'file'        => 'elementor/elementor.php',
            'url'         => 'https://wordpress.org/plugins/elementor/',
        ));
    }
}

Advanced_Plugin::get_instance();

// হেল্পার ফাংশন
function ap_get_option($key, $default = '') {
    $options = get_option('ap_options', array());
    return isset($options[$key]) ? $options[$key] : $default;
}
```

---

## অ্যাডঅন প্লাগইন থেকে ফিল্ড যোগ করা

```php
<?php
/**
 * Plugin Name: অ্যাডভান্সড প্লাগইন অ্যাডঅন
 * Description: অ্যাডভান্সড প্লাগইনের জন্য অ্যাডঅন
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

// ফ্রেমওয়ার্ক লোড হওয়ার জন্য অপেক্ষা করুন
add_action('bizzplugin_framework_loaded', function($framework) {
    // মূল প্লাগইনের প্যানেল পান
    $panel = $framework->get_panel('advanced_plugin');
    
    if (!$panel) {
        return; // মূল প্লাগইন সক্রিয় নয়
    }
    
    // নতুন সেকশন যোগ করুন
    $panel->add_section(array(
        'id'          => 'addon_settings',
        'title'       => __('অ্যাডঅন সেটিংস', 'ap-addon'),
        'description' => __('অ্যাডঅন প্লাগইন থেকে সেটিংস।', 'ap-addon'),
        'icon'        => 'dashicons dashicons-admin-plugins',
        'fields'      => array(
            array(
                'id'          => 'addon_feature',
                'type'        => 'switch',
                'title'       => __('অ্যাডঅন ফিচার সক্রিয়', 'ap-addon'),
                'default'     => '1',
            ),
            array(
                'id'          => 'addon_option',
                'type'        => 'select',
                'title'       => __('অ্যাডঅন অপশন', 'ap-addon'),
                'default'     => 'option1',
                'options'     => array(
                    'option1' => __('অপশন এক', 'ap-addon'),
                    'option2' => __('অপশন দুই', 'ap-addon'),
                    'option3' => __('অপশন তিন', 'ap-addon'),
                ),
            ),
        ),
    ));
    
    // বিদ্যমান সেকশনে ফিল্ড যোগ করুন
    $panel->add_field('general', array(
        'id'          => 'addon_extra_field',
        'type'        => 'text',
        'title'       => __('অ্যাডঅন থেকে অতিরিক্ত ফিল্ড', 'ap-addon'),
        'description' => __('অ্যাডঅন প্লাগইন দ্বারা যোগ করা।', 'ap-addon'),
        'default'     => '',
    ));
});
```

---

## কাস্টম ফিল্ড টাইপ

```php
<?php
// একটি কাস্টম "icon_picker" ফিল্ড টাইপ রেজিস্টার করুন

add_action('bizzplugin_render_field_icon_picker', function($field, $value, $is_disabled) {
    $icons = isset($field['icons']) ? $field['icons'] : array(
        'dashicons-admin-site',
        'dashicons-admin-media',
        'dashicons-admin-links',
        'dashicons-admin-comments',
        'dashicons-admin-users',
        'dashicons-admin-tools',
        'dashicons-admin-settings',
    );
    
    $disabled = $is_disabled ? ' disabled="disabled"' : '';
    ?>
    <div class="icon-picker-wrap">
        <?php foreach ($icons as $icon) : ?>
            <label class="icon-picker-item <?php echo $value === $icon ? 'selected' : ''; ?>">
                <input 
                    type="radio" 
                    name="<?php echo esc_attr($field['id']); ?>"
                    value="<?php echo esc_attr($icon); ?>"
                    <?php checked($value, $icon); ?>
                    <?php echo $disabled; ?>
                />
                <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
            </label>
        <?php endforeach; ?>
    </div>
    <style>
        .icon-picker-wrap { display: flex; flex-wrap: wrap; gap: 10px; }
        .icon-picker-item { cursor: pointer; padding: 10px; border: 2px solid #ddd; border-radius: 4px; }
        .icon-picker-item:hover { border-color: #2271b1; }
        .icon-picker-item.selected { border-color: #2271b1; background: #f0f6fc; }
        .icon-picker-item input { display: none; }
        .icon-picker-item .dashicons { font-size: 24px; width: 24px; height: 24px; }
    </style>
    <?php
}, 10, 3);

// ফিল্ড সংজ্ঞায় ব্যবহার:
array(
    'id'      => 'selected_icon',
    'type'    => 'icon_picker',
    'title'   => __('আইকন নির্বাচন করুন', 'textdomain'),
    'default' => 'dashicons-admin-site',
    'icons'   => array(
        'dashicons-admin-home',
        'dashicons-admin-site',
        'dashicons-admin-media',
    ),
)
```

---

## ওয়েবহুক হ্যান্ডলার উদাহরণ

```php
<?php
/**
 * BizzPlugin Options Framework-এর জন্য ওয়েবহুক রিসিভার
 * ওয়েবহুক গ্রহণ করতে এই ফাইল একটি বাহ্যিক সার্ভারে রাখুন
 */

// কনফিগারেশন
$WEBHOOK_SECRET = 'your-webhook-secret-here';
$LOG_FILE = __DIR__ . '/webhook_log.txt';

// রিকোয়েস্ট ডেটা পান
$signature = $_SERVER['HTTP_X_BIZZPLUGIN_SIGNATURE'] ?? '';
$payload = file_get_contents('php://input');

// সিগনেচার যাচাই করুন
$expected_signature = hash_hmac('sha256', $payload, $WEBHOOK_SECRET);

if (!hash_equals($expected_signature, $signature)) {
    http_response_code(401);
    exit(json_encode(['error' => 'অবৈধ সিগনেচার']));
}

// পেলোড পার্স করুন
$data = json_decode($payload, true);

// ওয়েবহুক লগ করুন
$log_entry = sprintf(
    "[%s] ইভেন্ট: %s | অপশন: %s | পরিবর্তন: %s\n",
    date('Y-m-d H:i:s'),
    $data['event'] ?? 'অজানা',
    $data['option_name'] ?? 'অজানা',
    json_encode($data['changed_fields'] ?? [])
);
file_put_contents($LOG_FILE, $log_entry, FILE_APPEND);

// নির্দিষ্ট ইভেন্ট হ্যান্ডেল করুন
switch ($data['event']) {
    case 'settings_saved':
        // বাহ্যিক সার্ভিসে সিঙ্ক করুন
        sync_to_external_service($data['data']);
        break;
        
    case 'webhook_test':
        // শুধু লগ করুন
        break;
}

// রেসপন্ড করুন
http_response_code(200);
echo json_encode(['success' => true, 'received' => $data['event']]);

function sync_to_external_service($settings) {
    // আপনার সিঙ্ক লজিক এখানে
}
```

---

## সাবমেনু প্যানেল উদাহরণ

```php
<?php
// একটি বিদ্যমান মেনুর সাবমেনু হিসেবে প্যানেল তৈরি করুন

add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $framework->create_panel(array(
        'id'          => 'my_submenu_panel',
        'title'       => __('আমার সাবমেনু সেটিংস', 'textdomain'),
        'menu_title'  => __('সাব সেটিংস', 'textdomain'),
        'menu_slug'   => 'my-submenu-settings',
        'parent_slug' => 'options-general.php', // প্যারেন্ট মেনু স্লাগ
        'capability'  => 'manage_options',
        'option_name' => 'my_submenu_options',
        'sections'    => array(/* সেকশন */),
    ));
});
```

সাধারণ parent_slug মান:
- `index.php` - ড্যাশবোর্ড
- `edit.php` - পোস্ট
- `upload.php` - মিডিয়া
- `edit.php?post_type=page` - পেজ
- `edit-comments.php` - মন্তব্য
- `themes.php` - চেহারা
- `plugins.php` - প্লাগইন
- `users.php` - ব্যবহারকারী
- `tools.php` - টুলস
- `options-general.php` - সেটিংস

---

## পরবর্তী পদক্ষেপ

- [ফিল্ড টাইপ](field-types.md) - সব ফিল্ড টাইপ রেফারেন্স
- [চেইনেবল API](chainable-api.md) - API রেফারেন্স
- [ফিল্টার এবং হুক](filters-hooks.md) - এক্সটেনশন পয়েন্ট
