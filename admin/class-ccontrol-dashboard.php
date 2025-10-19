<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ccontrol
 * @subpackage Ccontrol/admin
 * @author     Robert Ochoa <ochoa.robert1@gmail.com>
 */

if (!defined('WPINC')) {
    die;
}

class Ccontrol_Dashboard
{
    private $plugin_name;
    private $version;

    /**
     * Method __construct
     *
     * @param string $plugin_name [Plugin Name]
     * @param string $version [Current Version]
     *
     * @return void
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Method cc_admin_bar_item
     *
     * @param object WP_Admin_Bar [WP_Admin_Bar object]
     *
     * @return void
     */
    public function cc_admin_bar_item(WP_Admin_Bar $wp_admin_bar)
    {
        $ccontrol_mode = get_option('ccontrol_mode');

        if ($ccontrol_mode === 'dev') {
            $wp_admin_bar->add_menu([
                'id'    => 'ccontrol_mode',
                'parent' => 'top-secondary',
                'group'  => null,
                'title' => esc_attr__('Sandbox Client Control: Activado', 'ccontrol'),
                'href'  => admin_url('admin.php?page=ccontrol-options'),
                'meta'  => [
                    'class' => 'ccontrol-mode'
                ]
            ]);
        }
    }

    /**
     * Method cc_admin_menu
     *
     * @return void
     */
    public function cc_admin_menu()
    {
        add_menu_page(
            esc_attr__('Client Control', 'ccontrol'),
            esc_attr__('Client Control', 'ccontrol'),
            'manage_options',
            'ccontrol-dashboard',
            [$this, 'ccontrol_dashboard'],
            0
        );

        add_submenu_page(
            'ccontrol-dashboard',
            esc_attr__('Escritorio', 'ccontrol'),
            esc_attr__('Escritorio', 'ccontrol'),
            'manage_options',
            'ccontrol-dashboard',
            [$this, 'ccontrol_dashboard']
        );

        add_submenu_page(
            'ccontrol-dashboard',
            esc_attr__('Opciones', 'ccontrol'),
            esc_attr__('Opciones', 'ccontrol'),
            'manage_options',
            'ccontrol-options',
            [$this, 'ccontrol_options']
        );
    }

    /**
     * Method ccontrol_total_clients
     *
     * @return string
     */
    public function ccontrol_total_clients()
    {
        $arr_clients = get_posts([
            'post_type' => 'cc_clientes',
            'numberposts' => -1
        ]);

        return count($arr_clients);
    }

    /**
     * Method ccontrol_total_quotes
     *
     * @return string
     */
    public function ccontrol_total_quotes()
    {
        $arr_presupuestos = get_posts([
            'post_type' => 'cc_presupuestos',
            'numberposts' => -1
        ]);

        return count($arr_presupuestos);
    }

    /**
     * Method ccontrol_total_invoices
     *
     * @return string
     */
    public function ccontrol_total_invoices()
    {
        $arr_invoices = get_posts([
            'post_type' => 'cc_invoices',
            'numberposts' => -1
        ]);

        return count($arr_invoices);
    }

    /**
     * Method register_ccontrol_settings
     *
     * @return void
     */
    public function register_ccontrol_settings()
    {
        register_setting('ccontrol-group', 'ccontrol_logo');
        register_setting('ccontrol-group', 'ccontrol_name');
        register_setting('ccontrol-group', 'ccontrol_email');
        register_setting('ccontrol-group', 'ccontrol_telf');
        register_setting('ccontrol-group', 'ccontrol_address');
        register_setting('ccontrol-group', 'ccontrol_invoice_number');
        register_setting('ccontrol-group', 'ccontrol_invoice_last_post_change');
        register_setting('ccontrol-group', 'ccontrol_invoice_conditions');
        register_setting('ccontrol-group', 'ccontrol_invoice_accounts_venezuela');
        register_setting('ccontrol-group', 'ccontrol_invoice_accounts_usa');
        register_setting('ccontrol-group', 'ccontrol_invoice_accounts_paypal');
        register_setting('ccontrol-group', 'ccontrol_mode');
        register_setting('ccontrol-group', 'ccontrol_dev_email');
    }

    /**
     * Method ccontrol_options
     *
     * @return void
     */
    public function ccontrol_options()
    {
        require_once CCONTROL_PLUGIN_DIR . 'admin/partials/ccontrol-admin-options.php';
    }

    /**
     * Method ccontrol_dashboard
     *
     * @return void
     */
    public function ccontrol_dashboard()
    {
        require_once CCONTROL_PLUGIN_DIR . 'admin/partials/ccontrol-admin-display.php';
    }
}
