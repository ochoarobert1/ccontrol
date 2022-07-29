<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://robertochoaweb.com/
 * @since      1.0.0
 *
 * @package    Ccontrol
 * @subpackage Ccontrol/admin
 */

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
class Ccontrol_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ccontrol-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ccontrol-admin.js', array( 'jquery' ), $this->version, false);
    }
    
    /**
     * Method cc_clientes_metabox
     *
     * @return void
     */
    public function cc_clientes_metabox()
    {
        add_meta_box(
            'cc_clientes_metabox',
            __('Información del Cliente', 'ccontrol'),
            array($this, 'cc_clientes_main_metabox'),
            'cc_clientes'
        );
    }

    public function cc_clientes_main_metabox($post)
    {
        wp_nonce_field('cc_clientes_metabox', 'cc_clientes_metabox_nonce'); ?>
<div class="postmeta-wrapper">
    <div class="postmeta-item-wrapper cc-col-2">
        <?php $value = get_post_meta($post->ID, 'nombre_cliente', true); ?>
        <label for="nombre_cliente">
            <?php _e('Persona de Contacto', 'ccontrol'); ?>
        </label>
        <input type="text" id="nombre_cliente" name="nombre_cliente" value="<?php echo esc_attr($value); ?>" size="40" />
    </div>

    <div class="postmeta-item-wrapper cc-col-2">
        <?php $value = get_post_meta($post->ID, 'correo_cliente', true); ?>
        <label for="correo_cliente">
            <?php _e('Correo Electrónico', 'ccontrol'); ?>
        </label>
        <input type="email" id="correo_cliente" name="correo_cliente" value="<?php echo esc_attr($value); ?>" size="40" />
    </div>

    <div class="postmeta-item-wrapper cc-col-2">
        <?php $value = get_post_meta($post->ID, 'telf_cliente', true); ?>
        <label for="telf_cliente">
            <?php _e('Teléfono', 'ccontrol'); ?>
        </label>
        <input type="tel" id="telf_cliente" name="telf_cliente" value="<?php echo esc_attr($value); ?>" size="40" />
    </div>

    <div class="postmeta-item-wrapper cc-col-2">
        <?php $value = get_post_meta($post->ID, 'tipo_cliente', true); ?>
        <label for="tipo_cliente">
            <?php _e('Tipo de Cliente', 'ccontrol'); ?>
        </label>
        <select name="tipo_cliente" id="tipo_cliente">
            <option value="" selected disabled><?php _e('Seleccione tipo de cliente', 'ccontrol'); ?></option>
            <option value="Potencial" <?php selected($value, 'Potencial'); ?>><?php _e('Potencial', 'ccontrol'); ?></option>
            <option value="Recurrente" <?php selected($value, 'Recurrente'); ?>><?php _e('Recurrente', 'ccontrol'); ?></option>
            <option value="Saliente" <?php selected($value, 'Saliente'); ?>><?php _e('Saliente', 'ccontrol'); ?></option>
        </select>
    </div>
</div>
<?php
    }

    public function cc_clientes_save_metabox($post_id)
    {
        if (! isset($_POST['cc_clientes_metabox_nonce'])) {
            return $post_id;
        }
 
        $nonce = $_POST['cc_clientes_metabox_nonce'];
 
        if (! wp_verify_nonce($nonce, 'cc_clientes_metabox')) {
            return $post_id;
        }
 
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
 
        $mydata = sanitize_text_field($_POST['nombre_cliente']);
        update_post_meta($post_id, 'nombre_cliente', $mydata);

        $mydata = sanitize_text_field($_POST['correo_cliente']);
        update_post_meta($post_id, 'correo_cliente', $mydata);

        $mydata = sanitize_text_field($_POST['telf_cliente']);
        update_post_meta($post_id, 'telf_cliente', $mydata);

        $mydata = sanitize_text_field($_POST['tipo_cliente']);
        update_post_meta($post_id, 'tipo_cliente', $mydata);
    }

    /**
     * Method cc_admin_menu
     *
     * @return void
     */
    public function cc_admin_menu()
    {
        add_menu_page(
            __('Client Control', 'ccontrol'),
            __('Client Control', 'ccontrol'),
            'manage_options',
            'ccontrol-dashboard',
            array($this, 'ccontrol_dashboard'),
            plugins_url('myplugin/images/icon.png'),
            0
        );

        add_submenu_page(
            'ccontrol-dashboard',
            __('Escritorio', 'ccontrol'),
            __('Escritorio', 'ccontrol'),
            'manage_options',
            'ccontrol-dashboard',
            array($this, 'ccontrol_dashboard'),
        );
    }
    
    /**
     * Method ccontrol_dashboard
     *
     * @return void
     */
    public function ccontrol_dashboard()
    {
        echo 'hasdasd';
    }
}
