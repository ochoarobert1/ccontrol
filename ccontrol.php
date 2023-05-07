<?php

/**
 * Client Control
 *
 * @link              http://robertochoaweb.com/
 * @since             1.0.0
 * @package           Ccontrol
 *
 * @wordpress-plugin
 * Plugin Name:       Client Control
 * Plugin URI:        http://robertochoaweb.com/
 * Description:       Custom CRM Plugin for WordPress.
 * Version:           1.0.0
 * Author:            Robert Ochoa
 * Author URI:        http://robertochoaweb.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ccontrol
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
	die;
}

/**
 * Added Autoload
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Currently plugin version.
 */
define('CCONTROL_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 */
function activate_ccontrol()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-ccontrol-activator.php';
	Ccontrol_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_ccontrol()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-ccontrol-deactivator.php';
	Ccontrol_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ccontrol');
register_deactivation_hook(__FILE__, 'deactivate_ccontrol');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ccontrol.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_ccontrol()
{

	$plugin = new Ccontrol();
	$plugin->run();
}
run_ccontrol();
