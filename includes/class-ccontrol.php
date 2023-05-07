<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ccontrol
 * @subpackage Ccontrol/includes
 * @author     Robert Ochoa <ochoa.robert1@gmail.com>
 */
class Ccontrol
{
    protected $loader;
    protected $plugin_name;
    protected $version;

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

    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ccontrol-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ccontrol-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ccontrol-cpt.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ccontrol-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ccontrol-dashboard.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ccontrol-metaboxes.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ccontrol-public.php';
        $this->loader = new Ccontrol_Loader();
    }

    private function set_locale()
    {
        $plugin_i18n = new Ccontrol_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks()
    {
        $plugin_dashboard = new Ccontrol_Dashboard($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_menu', $plugin_dashboard, 'cc_admin_menu', 0);
        $this->loader->add_action('admin_init', $plugin_dashboard, 'register_ccontrol_settings');

        $plugin_metaboxes = new Ccontrol_Metaboxes($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('add_meta_boxes', $plugin_metaboxes, 'ccontrol_metabox');
        $this->loader->add_action('save_post', $plugin_metaboxes, 'cc_clientes_save_metabox');

        $plugin_admin = new Ccontrol_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('wp_ajax_ccontrol_create_pdf', $plugin_admin, 'ccontrol_create_pdf_callback');
        $this->loader->add_action('wp_ajax_ccontrol_create_pdf_send', $plugin_admin, 'ccontrol_create_pdf_send_callback');

        $plugin_cpt = new Ccontrol_CPT($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $plugin_cpt, 'ccontrol_clientes_cpt', 1);
        $this->loader->add_action('init', $plugin_cpt, 'ccontrol_presupuestos_cpt', 1);
        $this->loader->add_filter('manage_edit-cc_clientes_columns', $plugin_cpt, 'cc_clientes_custom_columns');
        $this->loader->add_action('manage_cc_clientes_posts_custom_column', $plugin_cpt, 'cc_clientes_promo_column_content', 10, 2);
        $this->loader->add_filter('manage_edit-cc_clientes_sortable_columns', $plugin_cpt, 'my_sortable_cc_clientes_column');
    }

    private function define_public_hooks()
    {
        $plugin_public = new Ccontrol_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    public function run()
    {
        $this->loader->run();
    }

    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    public function get_loader()
    {
        return $this->loader;
    }

    public function get_version()
    {
        return $this->version;
    }
}
