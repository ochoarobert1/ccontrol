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
        
        wp_enqueue_media();
    }

    /**
     * Method cc_clientes_metabox
     *
     * @return void
     */
    public function ccontrol_metabox()
    {
        add_meta_box(
            'cc_clientes_metabox',
            __('Información del Cliente', 'ccontrol'),
            array($this, 'cc_clientes_main_metabox'),
            'cc_clientes'
        );

        add_meta_box(
            'cc_presupuestos_metabox',
            __('Información del Presupuesto', 'ccontrol'),
            array($this, 'cc_presupuestos_main_metabox'),
            'cc_presupuestos'
        );

        add_meta_box(
            'cc_presupuestos_print_metabox',
            __('Imprimir Presupuesto', 'ccontrol'),
            array($this, 'cc_presupuestos_print_metabox'),
            'cc_presupuestos',
            'side'
        );
    }

    public function ccontrol_create_pdf_callback()
    {
        /*
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        */

        if (isset($_GET['postid'])) {
            $postid = $_GET['postid'];
            $presupuesto = get_post($postid);
        } else {
            $presupuesto = 'presupuesto';
        }

        require_once __DIR__ . '/../vendor/autoload.php';

        $pdf = new tFPDF();
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(40, 10, utf8_decode('¡Hola, Mundo!'));
        $pdf->Output('I', utf8_decode($presupuesto->post_title) . '.pdf');
        wp_die();
    }

    public function cc_presupuestos_print_metabox($post)
    {
        ?>
<div class="button-text">
    <p><?php _e('Haz click aquí para imprimir el presupuesto en formato PDF', 'ccontrol'); ?></p>
</div>
<a id="printQuote" data-id="<?php echo $post->ID; ?>" class="button button-primary button-large cc-btn-100"><?php _e('Imprimir Presupuesto', 'ccontrol'); ?></a>
<?php
    }
    
    /**
     * Method cc_clientes_main_metabox
     *
     * @param $post $post [explicite description]
     *
     * @return void
     */
    public function cc_clientes_main_metabox($post)
    {
        wp_nonce_field('ccontrol_metabox', 'ccontrol_metabox_nonce'); ?>
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

    /**
     * Method cc_presupuestos_main_metabox
     *
     * @param $post $post [explicite description]
     *
     * @return void
     */
    public function cc_presupuestos_main_metabox($post)
    {
        wp_nonce_field('ccontrol_metabox', 'ccontrol_metabox_nonce'); ?>
<div class="postmeta-wrapper">
    <div class="postmeta-item-wrapper">
        <?php $value = get_post_meta($post->ID, 'cliente_presupuesto', true); ?>
        <label for="cliente_presupuesto">
            <?php _e('Cliente', 'ccontrol'); ?>
        </label>
        <select name="cliente_presupuesto" id="cliente_presupuesto">
            <option value="" selected disabled><?php _e('Seleccione el cliente', 'ccontrol'); ?></option>
            <?php $arr_clientes = new WP_Query(array('post_type' => 'cc_clientes', 'posts_per_page' => -1)); ?>
            <?php while ($arr_clientes->have_posts()) : $arr_clientes->the_post(); ?>
            <option value="<?php echo get_the_title(); ?>" <?php selected($value, get_the_title()); ?>><?php echo get_the_title(); ?></option>
            <?php endwhile; ?>
            <?php wp_reset_query(); ?>
        </select>
    </div>

    <div class="postmeta-item-wrapper cc-complete">
        <?php $value = get_post_meta($post->ID, 'moneda_presupuesto', true); ?>
        <label for="moneda_presupuesto">
            <?php _e('Tipo de Moneda', 'ccontrol'); ?>
        </label>
        <div class="radio-group">
            <label for="moneda_presupuesto_bs"><input type="radio" <?php checked($value, 'Bolivares'); ?> id="moneda_presupuesto_bs" name="moneda_presupuesto" value="Bolivares" />Bolivares</label>
            <label for="moneda_presupuesto_dl"><input type="radio" <?php checked($value, 'Dolares'); ?> id="moneda_presupuesto_dl" name="moneda_presupuesto" value="Dolares" />Dolares</label>
            <label for="moneda_presupuesto_both"><input type="radio" <?php checked($value, 'Ambos'); ?> id="moneda_presupuesto_both" name="moneda_presupuesto" value="Ambos" />Ambos</label>
        </div>
    </div>

    <div class="postmeta-item-wrapper cc-complete">
        <?php $value = get_post_meta($post->ID, 'elem_ofrecer_presupuesto', true); ?>
        <label for="elem_ofrecer_presupuesto">
            <?php _e('Elementos a Ofrecer', 'ccontrol'); ?>
        </label>
        <?php wp_editor(htmlspecialchars($value), 'elem_ofrecer_presupuesto', $settings = array('textarea_name'=>'elem_ofrecer_presupuesto', 'textarea_rows' => 3)); ?>
    </div>

    <div class="postmeta-item-wrapper cc-complete">
        <?php $value = get_post_meta($post->ID, 'elem_items_presupuesto', true); ?>
        <label for="elem_items_presupuesto">
            <?php _e('Elementos del Presupuesto', 'ccontrol'); ?>
        </label>
        <?php wp_editor(htmlspecialchars($value), 'elem_items_presupuesto', $settings = array('textarea_name'=>'elem_items_presupuesto', 'textarea_rows' => 3)); ?>
    </div>


    <div class="postmeta-item-wrapper cc-complete">
        <?php $value = get_post_meta($post->ID, 'precio_bs', true); ?>
        <label for="precio_bs">
            <?php _e('Precio en Bs', 'ccontrol'); ?>
        </label>
        <input type="text" id="precio_bs" name="precio_bs" value="<?php echo esc_attr($value); ?>" size="40" />
    </div>

    <div class="postmeta-item-wrapper cc-complete">
        <?php $value = get_post_meta($post->ID, 'precio_usd', true); ?>
        <label for="precio_usd">
            <?php _e('Precio en $', 'ccontrol'); ?>
        </label>
        <input type="text" id="precio_usd" name="precio_usd" value="<?php echo esc_attr($value); ?>" size="40" />
    </div>

    <div class="postmeta-item-wrapper cc-complete">
        <?php $value = get_post_meta($post->ID, 'tiempo_presupuesto', true); ?>
        <label for="tiempo_presupuesto">
            <?php _e('Tiempo de Ejecución', 'ccontrol'); ?>
        </label>
        <input type="text" id="tiempo_presupuesto" name="tiempo_presupuesto" value="<?php echo esc_attr($value); ?>" size="40" />
    </div>
</div>
<?php
    }
    
    /**
     * Method cc_clientes_save_metabox
     *
     * @param $post_id $post_id [explicite description]
     *
     * @return void
     */
    public function cc_clientes_save_metabox($post_id)
    {
        if (! isset($_POST['ccontrol_metabox_nonce'])) {
            return $post_id;
        }
 
        $nonce = $_POST['ccontrol_metabox_nonce'];
 
        if (! wp_verify_nonce($nonce, 'ccontrol_metabox')) {
            return $post_id;
        }
 
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
 
        if (isset($_POST['nombre_cliente'])) {
            $mydata = sanitize_text_field($_POST['nombre_cliente']);
            update_post_meta($post_id, 'nombre_cliente', $mydata);
        }

        if (isset($_POST['correo_cliente'])) {
            $mydata = sanitize_text_field($_POST['correo_cliente']);
            update_post_meta($post_id, 'correo_cliente', $mydata);
        }

        if (isset($_POST['telf_cliente'])) {
            $mydata = sanitize_text_field($_POST['telf_cliente']);
            update_post_meta($post_id, 'telf_cliente', $mydata);
        }

        if (isset($_POST['tipo_cliente'])) {
            $mydata = sanitize_text_field($_POST['tipo_cliente']);
            update_post_meta($post_id, 'tipo_cliente', $mydata);
        }

        if (isset($_POST['cliente_presupuesto'])) {
            $mydata = sanitize_text_field($_POST['cliente_presupuesto']);
            update_post_meta($post_id, 'cliente_presupuesto', $mydata);
        }

        if (isset($_POST['moneda_presupuesto'])) {
            $mydata = sanitize_text_field($_POST['moneda_presupuesto']);
            update_post_meta($post_id, 'moneda_presupuesto', $mydata);
        }

        if (isset($_POST['elem_ofrecer_presupuesto'])) {
            $mydata = sanitize_text_field($_POST['elem_ofrecer_presupuesto']);
            update_post_meta($post_id, 'elem_ofrecer_presupuesto', $mydata);
        }

        if (isset($_POST['elem_items_presupuesto'])) {
            $mydata = sanitize_text_field($_POST['elem_items_presupuesto']);
            update_post_meta($post_id, 'elem_items_presupuesto', $mydata);
        }

        if (isset($_POST['precio_bs'])) {
            $mydata = sanitize_text_field($_POST['precio_bs']);
            update_post_meta($post_id, 'precio_bs', $mydata);
        }

        if (isset($_POST['precio_usd'])) {
            $mydata = sanitize_text_field($_POST['precio_usd']);
            update_post_meta($post_id, 'precio_usd', $mydata);
        }

        if (isset($_POST['tiempo_presupuesto'])) {
            $mydata = sanitize_text_field($_POST['tiempo_presupuesto']);
            update_post_meta($post_id, 'tiempo_presupuesto', $mydata);
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

        add_submenu_page(
            'ccontrol-dashboard',
            __('Opciones', 'ccontrol'),
            __('Opciones', 'ccontrol'),
            'manage_options',
            'ccontrol-options',
            array($this, 'ccontrol_options'),
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

    public function register_ccontrol_settings()
    { // whitelist options
        register_setting('ccontrol-group', 'ccontrol_logo');
        register_setting('ccontrol-group', 'ccontrol_name');
        register_setting('ccontrol-group', 'ccontrol_email');
    }
    
    /**
     * Method ccontrol_options
     *
     * @return void
     */
    public function ccontrol_options()
    {
        ob_start(); ?>
<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields('ccontrol-group'); ?>
        <?php do_settings_sections('ccontrol-group'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">New Option Name</th>
                <td><input type="text" name="ccontrol_logo" value="<?php echo esc_attr(get_option('ccontrol_logo')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Some Other Option</th>
                <td><input type="text" name="ccontrol_name" value="<?php echo esc_attr(get_option('ccontrol_name')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Options, Etc.</th>
                <td><input type="text" name="ccontrol_email" value="<?php echo esc_attr(get_option('ccontrol_email')); ?>" /></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
<?php
        $content = ob_get_clean();
        echo $content;
    }
}
