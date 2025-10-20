<?php
/**
 * Class Ccontrol
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Ccontrol
 * @subpackage Ccontrol/includes
 * @author     Robert Ochoa <ochoa.robert1@gmail.com>
 */

if (!defined('WPINC')) {
    die;
}

class Ccontrol
{
    protected $loader;
    protected $plugin_name;
    protected $version;

    /**
     * Method __construct
     *
     * @return void
     */
    public function __construct()
    {
        if (defined('CCONTROL_VERSION')) {
            $this->version = CCONTROL_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'ccontrol';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Method load_dependencies
     *
     * @return void
     */
    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ccontrol-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ccontrol-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ccontrol-cpt.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ccontrol-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ccontrol-dashboard.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/inc/class-ccontrol-budget-pdf.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/inc/class-ccontrol-invoice-pdf.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/metaboxes/class-ccontrol-clients.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/metaboxes/class-ccontrol-budget.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/metaboxes/class-ccontrol-invoices.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ccontrol-public.php';
        $this->loader = new Ccontrol_Loader();
    }

    /**
     * Method set_locale
     *
     * @return void
     */
    private function set_locale()
    {
        $plugin_i18n = new Ccontrol_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Method define_admin_hooks
     *
     * @return void
     */
    private function define_admin_hooks()
    {
        $plugin_dashboard = new Ccontrol_Dashboard($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_menu', $plugin_dashboard, 'cc_admin_menu', 0);
        $this->loader->add_action('admin_init', $plugin_dashboard, 'register_ccontrol_settings');
        $this->loader->add_action('admin_bar_menu', $plugin_dashboard, 'cc_admin_bar_item', 100);

        $plugin_metaboxes_client = new Ccontrol_Metaboxes_Client($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('add_meta_boxes', $plugin_metaboxes_client, 'ccontrol_metabox');
        $this->loader->add_action('save_post_cc_clientes', $plugin_metaboxes_client, 'cc_clientes_save_metabox');

        $plugin_metaboxes_budget = new Ccontrol_Metaboxes_Budget($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('add_meta_boxes', $plugin_metaboxes_budget, 'ccontrol_metabox');
        $this->loader->add_action('save_post_cc_presupuestos', $plugin_metaboxes_budget, 'cc_budget_save_metabox');

        $plugin_metaboxes_invoice = new Ccontrol_Metaboxes_Invoice($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('add_meta_boxes', $plugin_metaboxes_invoice, 'ccontrol_metabox');
        $this->loader->add_action('save_post_cc_invoices', $plugin_metaboxes_invoice, 'cc_invoices_save_metabox');

        $plugin_admin = new Ccontrol_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $plugin_budget_pdf = new Ccontrol_Admin_Budget_PDF($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wp_ajax_ccontrol_create_pdf', $plugin_budget_pdf, 'ccontrol_create_pdf_callback');
        $this->loader->add_action('wp_ajax_ccontrol_create_pdf_send', $plugin_budget_pdf, 'ccontrol_create_pdf_send_callback');

        $plugin_invoice_pdf = new Ccontrol_Admin_Invoice_PDF($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wp_ajax_ccontrol_create_invoice_pdf', $plugin_invoice_pdf, 'ccontrol_create_pdf_callback');
        $this->loader->add_action('wp_ajax_ccontrol_create_invoice_pdf_send', $plugin_invoice_pdf, 'ccontrol_create_pdf_send_callback');

        $plugin_cpt = new Ccontrol_CPT($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $plugin_cpt, 'ccontrol_clientes_cpt', 1);
        $this->loader->add_action('init', $plugin_cpt, 'ccontrol_presupuestos_cpt', 1);
        $this->loader->add_action('init', $plugin_cpt, 'ccontrol_invoices_cpt', 1);
        $this->loader->add_filter('manage_edit-cc_clientes_columns', $plugin_cpt, 'cc_clientes_custom_columns');
        $this->loader->add_action('manage_cc_clientes_posts_custom_column', $plugin_cpt, 'cc_clientes_promo_column_content', 10, 2);
        $this->loader->add_filter('manage_edit-cc_clientes_sortable_columns', $plugin_cpt, 'my_sortable_cc_clientes_column');
        $this->loader->add_filter('manage_edit-cc_presupuestos_columns', $plugin_cpt, 'cc_presupuestos_custom_columns');
        $this->loader->add_action('manage_cc_presupuestos_posts_custom_column', $plugin_cpt, 'cc_presupuestos_promo_column_content', 10, 2);
        $this->loader->add_filter('manage_edit-cc_invoices_columns', $plugin_cpt, 'cc_invoices_custom_columns');
        $this->loader->add_action('manage_cc_invoices_posts_custom_column', $plugin_cpt, 'cc_invoices_promo_column_content', 10, 2);
    }

    /**
     * Method define_public_hooks
     *
     * @return void
     */
    private function define_public_hooks()
    {
        /*
        $plugin_public = new Ccontrol_Public($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        */
    }

    /**
     * Method run
     *
     * @return object|void
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * Method get_plugin_name
     *
     * @return string
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * Method get_loader
     *
     * @return object
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Method get_version
     *
     * @return string
     */
    public function get_version()
    {
        return $this->version;
    }
}
