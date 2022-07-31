<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://robertochoaweb.com/
 * @since      1.0.0
 *
 * @package    Ccontrol
 * @subpackage Ccontrol/includes
 */

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

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Ccontrol_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
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
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Ccontrol_Loader. Orchestrates the hooks of the plugin.
     * - Ccontrol_i18n. Defines internationalization functionality.
     * - Ccontrol_Admin. Defines all hooks for the admin area.
     * - Ccontrol_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ccontrol-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ccontrol-i18n.php';

        /**
         * The class responsible for defining all custom post type.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ccontrol-cpt.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ccontrol-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ccontrol-public.php';

        $this->loader = new Ccontrol_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Ccontrol_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Ccontrol_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Ccontrol_Admin($this->get_plugin_name(), $this->get_version());
        

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'cc_admin_menu', 0);

        $plugin_cpt = new Ccontrol_CPT($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $plugin_cpt, 'ccontrol_clientes_cpt', 1);
        $this->loader->add_action('init', $plugin_cpt, 'ccontrol_presupuestos_cpt', 1);
        $this->loader->add_filter('manage_edit-cc_clientes_columns', $plugin_cpt, 'cc_clientes_custom_columns');
        $this->loader->add_action('manage_cc_clientes_posts_custom_column', $plugin_cpt, 'cc_clientes_promo_column_content', 10, 2);
        $this->loader->add_filter('manage_edit-cc_clientes_sortable_columns', $plugin_cpt, 'my_sortable_cc_clientes_column');

        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'ccontrol_metabox');
        $this->loader->add_action('save_post', $plugin_admin, 'cc_clientes_save_metabox');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_ccontrol_settings');

        $this->loader->add_action('wp_ajax_ccontrol_create_pdf', $plugin_admin, 'ccontrol_create_pdf_callback');
        $this->loader->add_action('wp_ajax_ccontrol_create_pdf_send', $plugin_admin, 'ccontrol_create_pdf_send_callback');

        
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Ccontrol_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Ccontrol_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
