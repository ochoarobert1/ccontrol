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

class Ccontrol_Dashboard
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function cc_admin_bar_item(WP_Admin_Bar $wp_admin_bar)
    {
        $ccontrol_mode = get_option('ccontrol_mode');

        if ($ccontrol_mode !== 'prod') {
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

    public function cc_admin_menu()
    {
        add_menu_page(
            esc_attr__('Client Control', 'ccontrol'),
            esc_attr__('Client Control', 'ccontrol'),
            'manage_options',
            'ccontrol-dashboard',
            array($this, 'ccontrol_dashboard'),
            0
        );

        add_submenu_page(
            'ccontrol-dashboard',
            esc_attr__('Escritorio', 'ccontrol'),
            esc_attr__('Escritorio', 'ccontrol'),
            'manage_options',
            'ccontrol-dashboard',
            array($this, 'ccontrol_dashboard'),
        );

        add_submenu_page(
            'ccontrol-dashboard',
            esc_attr__('Opciones', 'ccontrol'),
            esc_attr__('Opciones', 'ccontrol'),
            'manage_options',
            'ccontrol-options',
            array($this, 'ccontrol_options'),
        );
    }

    public function ccontrol_total_clients()
    {
        $arr_clients = get_posts([
            'post_type' => 'cc_clientes',
            'numberposts' => -1
        ]);

        return count($arr_clients);
    }

    public function ccontrol_total_quotes()
    {
        $arr_presupuestos = get_posts([
            'post_type' => 'cc_presupuestos',
            'numberposts' => -1
        ]);

        return count($arr_presupuestos);
    }

    public function ccontrol_total_invoices()
    {
        $arr_invoices = get_posts([
            'post_type' => 'cc_invoices',
            'numberposts' => -1
        ]);

        return count($arr_invoices);
    }

    public function ccontrol_dashboard()
    {
        $totalClients = $this->ccontrol_total_clients();
        $totalQuotes = $this->ccontrol_total_quotes();
        $totalInvoices = $this->ccontrol_total_invoices();
        $logo_url = get_option('ccontrol_logo');
        $company_name = get_option('ccontrol_name') ? get_option('ccontrol_name') : '';
        ?>
<div class="wrap dashboard-wrapper">
    <div class="dashboard-widget-header">
        <div>
            <img src="<?php echo esc_url($logo_url); ?>" title="<?php echo esc_attr($company_name); ?>" alt="Logo" />
        </div>
        <div>
            <h1 class="wp-ui-text-primary"><?php echo wp_kses_post(get_admin_page_title()); ?></h1>
            <small class="wp-ui-text-icon"><?php esc_html_e('Administre sus operaciones comerciales con facilidad', 'ccontrol'); ?></small>
        </div>
    </div>
    <div class="dashboard-widget-container">
        <div class="dashboard-widget">
            <h2 class="wp-ui-text-primary"><?php esc_html_e('Total Clientes', 'ccontrol'); ?></h2> <a href="<?php echo admin_url('post-new.php?post_type=cc_clientes'); ?>" tab-index="0" title="<?php esc_html_e('Haz click aqui para crear un nuevo cliente', 'ccontrol'); ?>" class="button primary"><?php esc_html_e('Agregar nuevo cliente', 'ccontrol'); ?></a>
            <div class="dashboard-divider"></div>
            <p><?php echo $totalClients; ?></p>
        </div>
        <div class="dashboard-widget">
            <h2 class="wp-ui-text-primary"><?php esc_html_e('Total Presupuestos', 'ccontrol'); ?></h2> <a href="<?php echo admin_url('post-new.php?post_type=cc_presupuestos'); ?>" tab-index="0" title="<?php esc_html_e('Haz click aqui para crear un nuevo presupuesto', 'ccontrol'); ?>" class="button primary"><?php esc_html_e('Agregar nuevo presupuesto', 'ccontrol'); ?></a>
            <div class="dashboard-divider"></div>
            <p><?php echo $totalQuotes; ?></p>
        </div>
        <div class="dashboard-widget">
            <h2 class="wp-ui-text-primary"><?php esc_html_e('Total Facturas', 'ccontrol'); ?></h2> <a href="<?php echo admin_url('post-new.php?post_type=cc_invoices'); ?>" tab-index="0" title="<?php esc_html_e('Haz click aqui para crear una nueva factura', 'ccontrol'); ?>" class="button primary"><?php esc_html_e('Agregar nueva factura', 'ccontrol'); ?></a>
            <div class="dashboard-divider"></div>
            <p><?php echo $totalInvoices; ?></p>
        </div>
    </div>
</div>
    <?php
    }

    public function register_ccontrol_settings()
    {
        register_setting('ccontrol-group', 'ccontrol_logo');
        register_setting('ccontrol-group', 'ccontrol_name');
        register_setting('ccontrol-group', 'ccontrol_email');
        register_setting('ccontrol-group', 'ccontrol_telf');
        register_setting('ccontrol-group', 'ccontrol_invoice_number');
        register_setting('ccontrol-group', 'ccontrol_invoice_last_post_change');
        register_setting('ccontrol-group', 'ccontrol_invoice_conditions');
        register_setting('ccontrol-group', 'ccontrol_invoice_accounts_venezuela');
        register_setting('ccontrol-group', 'ccontrol_invoice_accounts_usa');
        register_setting('ccontrol-group', 'ccontrol_invoice_accounts_paypal');
        register_setting('ccontrol-group', 'ccontrol_mode');
        register_setting('ccontrol-group', 'ccontrol_dev_email');
    }

    public function ccontrol_options()
    {
        ob_start(); ?>
        <div class="wrap">
            <h1><?php echo get_admin_page_title(); ?></h1>
            <form class="ccontrol-options-wrapper" method="post" action="options.php">
                <?php settings_fields('ccontrol-group'); ?>
                <?php do_settings_sections('ccontrol-group'); ?>
                <div class="tabs-links-wrapper">
                    <ul id="ccTabLinks" class="tabs-links">
                        <li>
                            <a href="#tab-general" title="<?php esc_attr_e('Opciones Generales', 'ccontrol'); ?>" class="active"><?php _e('General', 'ccontrol'); ?></a>
                        </li>
                        <li>
                            <a href="#tab-company" title="<?php esc_attr_e('Opciones referentes a la información de la compañia', 'ccontrol'); ?>"><?php _e('Información de la Compañia', 'ccontrol'); ?></a>
                        </li>
                        <li>
                            <a href="#tab-budget" title="<?php esc_attr_e('Opciones referentes a la administración de presupuestos', 'ccontrol'); ?>"><?php _e('Presupuestos', 'ccontrol'); ?></a>
                        </li>
                        <li>
                            <a href="#tab-invoice" title="<?php esc_attr_e('Opciones referentes a la administración de facturas', 'ccontrol'); ?>"><?php _e('Facturas', 'ccontrol'); ?></a>
                        </li>
                        <li>
                            <a href="#tab-clients" title="<?php esc_attr_e('Opciones referentes a la administración de clientes', 'ccontrol'); ?>"><?php _e('Clientes', 'ccontrol'); ?></a>
                        </li>
                        
                    </ul>
                </div>
                <div class="tabs-content-wrapper">
                <div class="tabs-content active" id="tab-general">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">
                                    <h4><?php esc_html_e('Modo del Plugin', 'ccontrol'); ?></h4>
                                </th>
                                <td>
                                    <label for="production"><?php esc_html_e('Producción', 'ccontrol'); ?> <input type="radio" name="ccontrol_mode" title="<?php esc_attr_e('Seleccione este modo para utilizar el plugin en modo producción.', 'ccontrol'); ?>" id="production" value="prod" <?php checked(get_option('ccontrol_mode'), 'prod'); ?>></label>
                                    <label for="development"><?php esc_html_e('Desarrollo', 'ccontrol'); ?> <input type="radio" name="ccontrol_mode" title="<?php esc_attr_e('Seleccione este modo para utilizar el plugin en modo desarrollo.', 'ccontrol'); ?>" id="development" value="dev" <?php checked(get_option('ccontrol_mode'), 'dev'); ?>></label>
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Usar en caso de que se necesite probar la recepcion de correos y evitar enviar presupuestos o facturas a los clientes registrados', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <h4><?php esc_html_e('Correo para pruebas de envio de correos', 'ccontrol'); ?></h4>
                                </th>
                                <td>
                                    <input type="email" name="ccontrol_dev_email" size="78" title="<?php esc_attr_e('Ingresa el correo electrónico para usar en pruebas de envío', 'ccontrol'); ?>" placeholder="email@domain.com" value="<?php echo esc_attr(get_option('ccontrol_dev_email')); ?>" />
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Usar en caso de que se necesite probar la recepcion de correos y evitar enviar presupuestos o facturas a los clientes registrados', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="tabs-content " id="tab-company">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">
                                    <h4><?php esc_html_e('Logo', 'ccontrol');  ?></h4>
                                </th>
                                <td>
                                    <?php $image = (get_option('ccontrol_logo') != '') ? get_option('ccontrol_logo') : 'https://placehold.it/70x70'; ?>
                                    <img id="ccontrol_logo" src="<?php echo $image; ?>" alt="logo" />
                                    <br />
                                    <input type="hidden" name="ccontrol_logo" id="image_url" class="regular-text" value="<?php echo $image; ?>" />
                                    <input type="button" name="upload-btn" id="upload-btn" title="<?php esc_attr_e('Haz click aquí para cargar el logo en la biblioteca de medios', 'ccontrol'); ?>" class="button-secondary" value="<?php esc_attr_e('Cargar Logo', 'ccontrol'); ?>" />
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Debe ser en formato .jpg para poder ser usado dentro de la generación de archivos PDF', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e('Nombre', 'ccontrol'); ?></th>
                                <td>
                                    <input type="text" name="ccontrol_name" size="78" title="<?php esc_attr_e('Ingrese el nombre de su compañía / razon social / negocio', 'ccontrol'); ?>" value="<?php echo esc_attr(get_option('ccontrol_name')); ?>" />
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Ingrese el nombre de su compañía / razon social / negocio', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e('Correo Electrónico', 'ccontrol'); ?></th>
                                <td>
                                    <input type="text" name="ccontrol_email" size="78" title="<?php esc_attr_e('Ingrese su correo electrónico', 'ccontrol'); ?>" value="<?php echo esc_attr(get_option('ccontrol_email')); ?>" />
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Ingrese su correo electrónico', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e('Teléfono', 'ccontrol'); ?></th>
                                <td>
                                    <input type="text" name="ccontrol_telf" size="78"  title="<?php esc_attr_e('Ingrese su número telefónico', 'ccontrol'); ?>" value="<?php echo esc_attr(get_option('ccontrol_telf')); ?>" />
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Ingrese su número telefónico', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="tabs-content" id="tab-budget">
                        <table class="form-table">
                            <h2><?php esc_html_e('Proximamente', 'ccontrol'); ?></h2>
                        </table>
                    </div>
                    <div class="tabs-content" id="tab-invoice">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e('Número de Factura', 'ccontrol'); ?></th>
                                <td>
                                    <input type="text" name="ccontrol_invoice_number" title="<?php esc_attr_e('Ingrese el número inicial de su facturación', 'ccontrol'); ?>" size="78" value="<?php echo esc_attr(get_option('ccontrol_invoice_number')); ?>" />
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Ingrese el número inicial de su facturación', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e('Términos y Condiciones en Factura por defecto', 'ccontrol'); ?></th>
                                <td>
                                    <textarea name="ccontrol_invoice_conditions" id="ccontrol_invoice_conditions" title="<?php esc_attr_e('Ingrese los términos y condiciones a usar en las facturas', 'ccontrol'); ?>" cols="80" rows="5"><?php echo esc_html(get_option('ccontrol_invoice_conditions')); ?></textarea>
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Ingrese los términos y condiciones a usar en las facturas', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e('Métodos de Pago en Factura (Venezuela)', 'ccontrol'); ?></th>
                                <td>
                                    <textarea name="ccontrol_invoice_accounts_venezuela" id="ccontrol_invoice_accounts_venezuela" title="<?php esc_attr_e('Ingrese los métodos de pago para Venezuela', 'ccontrol'); ?>" cols="80" rows="8"><?php echo esc_html(get_option('ccontrol_invoice_accounts_venezuela')); ?></textarea>
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Ingrese los métodos de pago para Venezuela', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e('Metodos de Pago en Factura (USA)', 'ccontrol'); ?></th>
                                <td>
                                    <textarea name="ccontrol_invoice_accounts_usa" id="ccontrol_invoice_accounts_usa" title="<?php esc_attr_e('Ingrese los métodos de pago para USA', 'ccontrol'); ?>" cols="80" rows="15"><?php echo esc_html(get_option('ccontrol_invoice_accounts_usa')); ?></textarea>
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Ingrese los métodos de pago para USA', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e('Metodos de Pago en Factura (PayPal)', 'ccontrol'); ?></th>
                                <td>
                                    <textarea name="ccontrol_invoice_accounts_paypal" id="ccontrol_invoice_accounts_paypal" title="<?php esc_attr_e('Ingrese los métodos de pago para PayPal', 'ccontrol'); ?>" cols="80" rows="5"><?php echo esc_html(get_option('ccontrol_invoice_accounts_paypal')); ?></textarea>
                                    <br />
                                    <small class="wp-ui-text-icon"><?php esc_html_e('Ingrese los métodos de pago para PayPal', 'ccontrol'); ?></small>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="tabs-content" id="tab-clients">
                        <table class="form-table">
                            <h2><?php esc_html_e('Proximamente', 'ccontrol'); ?></h2>
                        </table>
                    </div>
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
<?php
        $content = ob_get_clean();
        echo $content;
    }
}
