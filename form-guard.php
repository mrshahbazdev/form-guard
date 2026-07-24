<?php
/**
 * Plugin Name: Form Guard
 * Description: Drag-and-drop style contact forms with spam protection, email notifications and entry export.
 * Version: 1.0.0
 * Author: mrshahbazdev
 * Author URI: https://github.com/mrshahbazdev
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: form-guard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FG_VERSION', '1.0.0' );
define( 'FG_FILE', __FILE__ );
define( 'FG_DIR', plugin_dir_path( __FILE__ ) );
define( 'FG_URL', plugin_dir_url( __FILE__ ) );
define( 'FG_TABLE', 'form_guard_entries' );

require_once FG_DIR . 'includes/class-form-guard.php';
add_action( 'plugins_loaded', array( 'Form_Guard', 'init' ) );

register_activation_hook( FG_FILE, array( 'Form_Guard', 'activate' ) );
