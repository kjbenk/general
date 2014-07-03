<?php
/*
Plugin Name: General
plugin URI: http://wpdevadvice.com/wordpress-general-plugin/
Description: The main reason I have created this WordPress general plugin repository is to give you, the developer, a base to work off of.
version: 1.0
Author: Kyle Benk
Author URI: http://kylebenkapps.com
License: GPL2
*/

/**
 * Global Definitions
 */

/* Plugin Name */

if (!defined('GENERAL_PLUGIN_NAME'))
    define('GENERAL_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

/* Plugin directory */

if (!defined('GENERAL_PLUGIN_DIR'))
    define('GENERAL_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . GENERAL_PLUGIN_NAME);

/* Plugin url */

if (!defined('GENERAL_PLUGIN_URL'))
    define('GENERAL_PLUGIN_URL', WP_PLUGIN_URL . '/' . GENERAL_PLUGIN_NAME);

/* Plugin verison */

if (!defined('GENERAL_VERSION_NUM'))
    define('GENERAL_VERSION_NUM', '1.0.0');


/**
 * Activatation / Deactivation
 */

register_activation_hook( __FILE__, array('General', 'register_activation'));

/**
 * Hooks / Filter
 */

add_action('init', array('General', 'load_textdomain'));
add_action('admin_menu', array('General', 'menu_page'));

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", array('General', 'plugin_links'));

/**
 *  General main class
 *
 * @since 1.0.0
 * @using Wordpress 3.8
 */

class General {

	/* Properties */

	private static $text_domain = 'general';

	private static $prefix = 'general_';

	private static $settings_page = 'general-admin-menu-settings';

	private static $tabs_settings_page = 'general-admin-menu-tab-settings';

	private static $usage_page = 'general-admin-menu-usage';

	private static $default = array(
		'text'		=> 'text',
		'textarea'	=> 'textarea',
		'checkbox'	=> true,
		'select'	=> '',
		'radio'		=> '',
		'url'		=> 'kylebenkapps.com'
	);

	/**
	 * Load the text domain
	 *
	 * @since 1.0.0
	 */
	static function load_textdomain() {
		load_plugin_textdomain(self::$text_domain, false, GENERAL_PLUGIN_DIR . '/languages');
	}

	/**
	 * Hooks to 'register_activation_hook'
	 *
	 * @since 1.0.0
	 */
	static function register_activation() {

		/* Check if multisite, if so then save as site option */

		if (is_multisite()) {
			add_site_option(self::$prefix . 'version', GENERAL_VERSION_NUM);
		} else {
			add_option(self::$prefix . 'version', GENERAL_VERSION_NUM);
		}
	}

	/**
	 * Hooks to 'plugin_action_links_' filter
	 *
	 * @since 1.0.0
	 */
	static function plugin_links($links) {
		$settings_link = '<a href="admin.php?page=' . self::$settings_page . '">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	/**
	 * Hooks to 'admin_menu'
	 *
	 * @since 1.0.0
	 */
	static function menu_page() {

		/* Add the menu Page */

		add_menu_page(
			__('General', self::$text_domain),							// Page Title
			__('General', self::$text_domain), 							// Menu Name
	    	'manage_options', 											// Capabilities
	    	self::$settings_page, 										// slug
	    	array('General', 'admin_settings')	// Callback function
	    );

	    /* Cast the first sub menu to the top menu */

	    $settings_page_load = add_submenu_page(
	    	self::$settings_page, 										// parent slug
	    	__('Settings', self::$text_domain), 						// Page title
	    	__('Settings', self::$text_domain), 						// Menu name
	    	'manage_options', 											// Capabilities
	    	self::$settings_page, 										// slug
	    	array('General', 'admin_settings')	// Callback function
	    );
	    add_action("admin_print_scripts-$settings_page_load", array('General', 'include_admin_scripts'));

	    /* Another sub menu */

	    $usage_page_load = add_submenu_page(
	    	self::$settings_page, 										// parent slug
	    	__('Usage', self::$text_domain),  							// Page title
	    	__('Usage', self::$text_domain),  							// Menu name
	    	'manage_options', 											// Capabilities
	    	self::$usage_page, 											// slug
	    	array('General', 'admin_usage')		// Callback function
	    );
	    add_action("admin_print_scripts-$usage_page_load", array('General', 'include_admin_scripts'));

	    /* Another sub menu */

	    $tabs_page_load = add_submenu_page(
	    	self::$settings_page, 										// parent slug
	    	__('Tabs', self::$text_domain),  							// Page title
	    	__('Tabs', self::$text_domain),  							// Menu name
	    	'manage_options', 											// Capabilities
	    	self::$tabs_settings_page, 											// slug
	    	array('General', 'admin_tabs')		// Callback function
	    );
	    add_action("admin_print_scripts-$tabs_page_load", array('General', 'include_admin_scripts'));
	}

	/**
	 * Hooks to 'admin_print_scripts-$page'
	 *
	 * @since 1.0.0
	 */
	static function include_admin_scripts() {

		/* CSS */

		wp_register_style(self::$prefix . 'settings_css', GENERAL_PLUGIN_URL . '/css/settings.css');
		wp_enqueue_style(self::$prefix . 'settings_css');

		/* Javascript */

		wp_register_script(self::$prefix . 'settings_js', GENERAL_PLUGIN_URL . '/js/settings.js');
		wp_enqueue_script(self::$prefix . 'settings_js');

		/* CSS */

		wp_register_style(self::$prefix . 'tabs_css', GENERAL_PLUGIN_URL . '/css/tabs.css');
		wp_enqueue_style(self::$prefix . 'tabs_css');
	}

	/**
	 * Displays the HTML for the 'general-admin-menu-settings' admin page
	 *
	 * @since 1.0.0
	 */
	static function admin_settings() {

		$settings = get_option(self::$prefix . 'settings');

		/* Default values */

		if ($settings === false) {
			$settings = self::$default;
		}

		/* Save data nd check nonce */

		if (isset($_POST['submit']) && check_admin_referer(self::$prefix . 'admin_settings')) {

			$settings = get_option(self::$prefix . 'settings');

			/* Default values */

			if ($settings === false) {
				$settings = self::$default;
			}

			$settings = array(
				'text'		=> stripcslashes(sanitize_text_field($_POST[self::$prefix . 'text'])),
				'textarea'	=> stripcslashes(sanitize_text_field($_POST[self::$prefix . 'textarea'])),
				'checkbox'	=> isset($_POST[self::$prefix . 'checkbox']) && $_POST[self::$prefix . 'checkbox'] ? true : false,
				'select'	=> $_POST[self::$prefix . 'select'],
				'radio'		=> $_POST[self::$prefix . 'radio'],
				'url'		=> stripcslashes(sanitize_text_field($_POST[self::$prefix . 'url']))
			);

			update_option(self::$prefix . 'settings', $settings);
		}

		require('admin/settings.php');
	}

	/**
	 * Displays the HTML for the 'general-admin-menu-usage' admin page
	 *
	 * @since 1.0.0
	 */
	static function admin_usage() {
		?>

		<h1><?php _e('Usage Page', self::$text_domain); ?></h1>

		<p><?php _e('Information about how to use this plugin.', self::$text_domain); ?></p>
		<?php
	}

	/**
	 * Displays the HTML for the 'general-admin-menu-tab-settings' admin page
	 *
	 * @since 1.0.0
	 */
	static function admin_tabs() {

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-tabs');
		?>

		<script type="text/javascript">
			jQuery(document).ready(function($){
				$("#tabs").tabs();
			});
		</script>

		<h1><?php _e('Tabs Page', self::$text_domain); ?></h1>

		<div id="tabs">
			<ul>
				<li><a href="#<?php echo self::$prefix;?>tab_1"><span class="<?php echo self::$prefix;?>admin_tabs"><?php _e('Tab 1', self::$text_domain); ?></span></a></li>
				<li><a href="#<?php echo self::$prefix;?>tab_2"><span class="<?php echo self::$prefix;?>admin_tabs"><?php _e('Tab 2', self::$text_domain); ?></span></a></li>
			</ul>

			<div id="<?php echo self::$prefix;?>tab_1">
				<p><?php _e('Content of Tab 1', self::$text_domain); ?></p>
			</div>

			<div id="<?php echo self::$prefix;?>tab_2">
				<p><?php _e('Content of Tab 2', self::$text_domain); ?></p>
			</div>

		</div>
		<?php
	}
}

?>