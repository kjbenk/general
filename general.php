<?php
/*
Plugin Name: General
plugin URI: kylebenkapps.com
Description:
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
add_action('admin_menu', array('General', 'general_menu_page'));

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
	
	private static $usage_page = 'general-admin-menu-usage';
	
	private static $default = array(
		'title'	=> 'Title',
		'size'	=> '100'
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
	 * Hooks to 'init' 
	 * 
	 * @since 1.0.0
	 */
	static function register_activation() {
	
		/* Check if multisite, if so then save as site option */
		
		if (is_multisite()) {
			add_site_option('general_version', GENERAL_VERSION_NUM);
		} else {
			add_option('general_version', GENERAL_VERSION_NUM);
		}
	}
	
	/**
	 * Hooks to 'admin_menu' 
	 * 
	 * @since 1.0.0
	 */
	static function general_menu_page() {
		
		/* Add the menu Page */
		
		add_menu_page(
			__('General', self::$text_domain),							// Page Title
			__('General', self::$text_domain), 							// Menu Name
	    	'manage_options', 											// Capabilities
	    	self::$settings_page, 										// slug
	    	array('General', 'general_admin_settings')	// Callback function
	    );
	    
	    /* Cast the first sub menu to the top menu */
	    
	    $settings_page_load = add_submenu_page(
	    	self::$settings_page, 										// parent slug
	    	__('Settings', self::$text_domain), 						// Page title
	    	__('Settings', self::$text_domain), 						// Menu name
	    	'manage_options', 											// Capabilities
	    	self::$settings_page, 										// slug
	    	array('General', 'general_admin_settings')	// Callback function
	    );
	    add_action("admin_print_scripts-$settings_page_load", array('General', 'general_include_admin_scripts'));
	    
	    /* Another sub menu */
	    
	    $usage_page_load = add_submenu_page(
	    	self::$settings_page, 										// parent slug 
	    	__('Usage', self::$text_domain),  							// Page title
	    	__('Usage', self::$text_domain),  							// Menu name
	    	'manage_options', 											// Capabilities 
	    	self::$usage_page, 											// slug 
	    	array('General', 'general_admin_usage')		// Callback function
	    );
	    add_action("admin_print_scripts-$usage_page_load", array('General', 'general_include_admin_scripts'));
	}
	
	/**
	 * Hooks to 'admin_print_scripts-$page' 
	 * 
	 * @since 1.0.0
	 */
	static function general_include_admin_scripts() {
		
		/* CSS */
		
		wp_register_style('general_admin_css', GENERAL_PLUGIN_URL . '/include/css/general_admin.css');
		wp_enqueue_style('general_admin_css');
	
		/* Javascript */
		
		wp_register_script('general_admin_js', GENERAL_PLUGIN_URL . '/include/js/general_admin.js');
		wp_enqueue_script('general_admin_js');	
	}
	
	/**
	 * Displays the HTML for the 'general-admin-menu-settings' admin page
	 * 
	 * @since 1.0.0
	 */
	static function general_admin_settings() {
		
		$settings = get_option('general_settings');
			
		/* Default values */
		
		if ($settings === false) {
			$settings = self::$default;
		}
		
		/* Save data nd check nonce */
		
		if (isset($_POST['submit']) && check_admin_referer('general_admin_settings')) {
			
			$settings = get_option('general_settings');
			
			/* Default values */
			
			if ($settings === false) {
				$settings = self::$default;
			}
				
			$settings = array(
				'title'	=> stripcslashes(sanitize_text_field($_POST['general_settings_title'])),
				'size'	=> stripcslashes(sanitize_text_field($_POST['general_settings_size']))
			);
			
			update_option('general_settings', $settings);
		}
		
		?>
		
		<h1><?php _e('Settings Page', self::$text_domain); ?></h1>
		
		<form method="post">
			
			<h3><?php _e('General', self::$text_domain); ?></h3>
			
			<table>
				<tbody>
				
					<!-- Title -->
				
					<tr>
						<th class="general_admin_table_th">
							<label><?php _e('Title', self::$text_domain); ?></label>
							<td class="general_admin_table_td">
								<input id="general_settings_title" name="general_settings_title" type="text" size="60" value="<?php echo esc_attr($settings['title']); ?>">
							</td>
						</th>
					</tr>
				
				</tbody>
			</table>
			
			<h3><?php _e('Advanced', self::$text_domain); ?></h3>
			
			<table>
				<tbody>
				
					<!-- Size -->
				
					<tr>
						<th class="general_admin_table_th">
							<label><?php _e('Size', self::$text_domain); ?></label>
							<td class="general_admin_table_td">
								<input id="general_settings_size" name="general_settings_size" type="text" size="60" value="<?php echo esc_attr($settings['size']); ?>">
							</td>
						</th>
					</tr>
				
				</tbody>
			</table>
			
		<?php wp_nonce_field('general_admin_settings'); ?>
		
		<?php submit_button(); ?>
		
		</form>
		
		<?php
	}
	
	/**
	 * Displays the HTML for the 'general-admin-menu-usage' admin page 
	 * 
	 * @since 1.0.0
	 */
	static function general_admin_usage() {
		?>
		
		<h1><?php _e('Usage Page', self::$text_domain); ?></h1>
		
		<p><?php _e('Information about how to use this plugin.', self::$text_domain); ?></p>
		<?php
	}
}

?>