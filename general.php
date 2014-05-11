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
add_action('admin_menu', array('General', 'general_menu_page'));

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", array('General', 'general_settings_link'));

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
			add_site_option('general_version', GENERAL_VERSION_NUM);
		} else {
			add_option('general_version', GENERAL_VERSION_NUM);
		}
	}
	
	/**
	 * Hooks to 'plugin_action_links_' filter 
	 * 
	 * @since 1.0.0
	 */
	static function general_settings_link($links) { 
		$settings_link = '<a href="admin.php?page=' . self::$settings_page . '">Settings</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
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
	    
	    /* Another sub menu */
	    
	    $tabs_page_load = add_submenu_page(
	    	self::$settings_page, 										// parent slug 
	    	__('Tabs', self::$text_domain),  							// Page title
	    	__('Tabs', self::$text_domain),  							// Menu name
	    	'manage_options', 											// Capabilities 
	    	self::$tabs_settings_page, 											// slug 
	    	array('General', 'general_admin_tabs')		// Callback function
	    );
	    add_action("admin_print_scripts-$tabs_page_load", array('General', 'general_include_admin_scripts'));
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
				'text'		=> stripcslashes(sanitize_text_field($_POST['general_settings_text'])),
				'textarea'	=> stripcslashes(sanitize_text_field($_POST['general_settings_textarea'])),
				'checkbox'	=> isset($_POST['general_settings_checkbox']) && $_POST['general_settings_checkbox'] ? true : false,
				'select'	=> $_POST['general_settings_select'],
				'radio'		=> $_POST['general_settings_radio'],
				'url'		=> stripcslashes(sanitize_text_field($_POST['general_settings_url']))
			);
			
			update_option('general_settings', $settings);
		}
		
		?>
		
		<h1><?php _e('Settings Page', self::$text_domain); ?></h1>
		
		<form method="post">
			
			<h3><?php _e('General Section', self::$text_domain); ?></h3>
			
			<table>
				<tbody>
				
					<!-- Text -->
				
					<tr>
						<th class="general_admin_table_th">
							<label><?php _e('Text', self::$text_domain); ?></label>
							<td class="general_admin_table_td">
								<input id="general_settings_text" name="general_settings_text" type="text" size="50" value="<?php echo esc_attr($settings['text']); ?>">
							</td>
						</th>
					</tr>
					
					<!-- TextArea -->
				
					<tr>
						<th class="general_admin_table_th">
							<label><?php _e('TextArea', self::$text_domain); ?></label>
							<td class="general_admin_table_td">
								<textarea rows="10" cols="50" id="general_settings_textarea" name="general_settings_textarea"><?php echo esc_attr($settings['textarea']); ?></textarea>
							</td>
						</th>
					</tr>
					
					<!-- Checkbox -->
				
					<tr>
						<th class="general_admin_table_th">
							<label><?php _e('Checkbox', self::$text_domain); ?></label>
							<td class="general_admin_table_td">
								<input type="checkbox" id="general_settings_checkbox" name="general_settings_checkbox" <?php echo isset($settings['checkbox']) && $settings['checkbox'] ? 'checked="checked"' : ''; ?>/>
							</td>
						</th>
					</tr>
					
					<!-- Select -->
				
					<tr>
						<th class="general_admin_table_th">
							<label><?php _e('Select', self::$text_domain); ?></label>
							<td class="general_admin_table_td">
								<select id="general_settings_select" name="general_settings_select">
									<option value="small" <?php echo isset($settings['select']) && $settings['select'] == 'small' ? 'selected' : ''; ?>><?php _e('small', self::$text_domain); ?></option>
									<option value="medium" <?php echo isset($settings['select']) && $settings['select'] == 'medium' ? 'selected' : ''; ?>><?php _e('medium', self::$text_domain); ?></option>
									<option value="large" <?php echo isset($settings['select']) && $settings['select'] == 'large' ? 'selected' : ''; ?>><?php _e('large', self::$text_domain); ?></option>
								</select>
							</td>
						</th>
					</tr>
					
					<!-- Radio -->
				
					<tr>
						<th class="general_admin_table_th">
							<label><?php _e('Radio', self::$text_domain); ?></label>
							<td class="general_admin_table_td">
								<input type="radio" name="general_settings_radio" value="start" <?php echo isset($settings['radio']) && $settings['radio'] == 'start' ? 'checked="checked"' : ''; ?>/><label><?php _e('start', self::$text_domain); ?></label><br/>
								<input type="radio" name="general_settings_radio" value="middle" <?php echo isset($settings['radio']) && $settings['radio'] == 'middle' ? 'checked="checked"' : ''; ?>/><label><?php _e('middle', self::$text_domain); ?></label><br/>
								<input type="radio" name="general_settings_radio" value="end" <?php echo isset($settings['radio']) && $settings['radio'] == 'end' ? 'checked="checked"' : ''; ?>/><label><?php _e('end', self::$text_domain); ?></label><br/>
							</td>
						</th>
					</tr>
				
				</tbody>
			</table>
			
			<h3><?php _e('Other Section', self::$text_domain); ?></h3>
			
			<table>
				<tbody>
				
					<!-- URL -->
				
					<tr>
						<th class="general_admin_table_th">
							<label><?php _e('URL', self::$text_domain); ?></label>
							<td class="general_admin_table_td">
								<input id="general_settings_url" name="general_settings_url" type="url" size="50" value="<?php echo esc_url($settings['url']); ?>">
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
	
	/**
	 * Displays the HTML for the 'general-admin-menu-tab-settings' admin page 
	 * 
	 * @since 1.0.0
	 */
	static function general_admin_tabs() {
		
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
				<li><a href="#general_tab_1"><span class="general_admin_tabs"><?php _e('Tab 1', self::$text_domain); ?></span></a></li>
				<li><a href="#general_tab_2"><span class="general_admin_tabs"><?php _e('Tab 2', self::$text_domain); ?></span></a></li>
			</ul>
			
			<div id="general_tab_1">
				<p><?php _e('Content of Tab 1', self::$text_domain); ?></p>
			</div>
			
			<div id="general_tab_2">
				<p><?php _e('Content of Tab 2', self::$text_domain); ?></p>
			</div>
					
		</div>
		<?php
	}
}

?>