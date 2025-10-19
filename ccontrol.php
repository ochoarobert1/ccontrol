<?php

/**
 * Client Control
 * 
 * Custom CRM Plugin for WordPress.
 *
 * @link              https://robertochoaweb.com/
 * @since             1.0.0
 * @package           Ccontrol
 *
 * @wordpress-plugin
 * Plugin Name:       Client Control
 * Plugin URI:        https://robertochoaweb.com/
 * Description:       Custom CRM Plugin for WordPress.
 * Version:           1.0.0
 * Author:            Robert Ochoa
 * Author URI:        https://robertochoaweb.com/
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
define('CCONTROL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CCONTROL_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Method activate_ccontrol
 * The code that runs during plugin activation.
 *
 * @since   1.0.0
 * @return void
 */
function activate_ccontrol()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ccontrol-activator.php';
    Ccontrol_Activator::activate();
}

/**
 * Method deactivate_ccontrol
 * The code that runs during plugin deactivation.
 *
 * @since   1.0.0
 * @return void
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
 * Method run_ccontrol
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 * @return void
 */
function run_ccontrol()
{
    $plugin = new Ccontrol();
    $plugin->run();
}
run_ccontrol();
