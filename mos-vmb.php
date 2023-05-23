<?php
/**
 * Plugin Name:       Mos Marginal Vat
 * Plugin URI:        http://www.mdmostakshahid.com/
 * Description:       This plugin will calculate marginal vat of your choosen product
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Md. Mostak Shahid
 * Author URI:        http://www.mdmostakshahid.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        http://www.mdmostakshahid.com/
 * Text Domain:       mos-form-pdf
 * Domain Path:       /languages
**/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define MOS_VMB_FILE.
if ( ! defined( 'MOS_VMB_FILE' ) ) {
	define( 'MOS_VMB_FILE', __FILE__ );
}
// Define MOS_VMB_SETTINGS.
if ( ! defined( 'MOS_VMB_SETTINGS' ) ) {
  //define( 'MOS_VMB_SETTINGS', admin_url('/edit.php?post_type=post_type&page=plugin_settings') );
	define( 'MOS_VMB_SETTINGS', admin_url('/options-general.php?page=mos_vmb_settings') );
}
$mos_vmb_options = get_option( 'mos_vmb_options' );
$plugin = plugin_basename(MOS_VMB_FILE); 
require_once ( plugin_dir_path( MOS_VMB_FILE ) . 'mos-vmb-functions.php' );
require_once ( plugin_dir_path( MOS_VMB_FILE ) . 'mos-vmb-settings.php' );
//require_once ( plugin_dir_path( MOS_VMB_FILE ) . 'custom-settings.php' );

require_once('plugins/update/plugin-update-checker.php');
$pluginInit = Puc_v4_Factory::buildUpdateChecker(
	'https://raw.githubusercontent.com/mostak-shahid/update/master/mos-vmb.json',
	MOS_VMB_FILE,
	'mos-vmb'
);


register_activation_hook(MOS_VMB_FILE, 'mos_vmb_activate');
add_action('admin_init', 'mos_vmb_redirect');
 
function mos_vmb_activate() {
    $mos_vmb_option = array();
    // $mos_vmb_option['mos_login_type'] = 'basic';
    // update_option( 'mos_vmb_option', $mos_vmb_option, false );
    add_option('mos_vmb_do_activation_redirect', true);
}
 
function mos_vmb_redirect() {
    if (get_option('mos_vmb_do_activation_redirect', false)) {
        delete_option('mos_vmb_do_activation_redirect');
        if(!isset($_GET['activate-multi'])){
            wp_safe_redirect(MOS_VMB_SETTINGS);
        }
    }
}

// Add settings link on plugin page
function mos_vmb_settings_link($links) { 
  $settings_link = '<a href="'.MOS_VMB_SETTINGS.'">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
} 
add_filter("plugin_action_links_$plugin", 'mos_vmb_settings_link' );



