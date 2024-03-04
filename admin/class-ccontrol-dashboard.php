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

    public function cc_admin_menu()
    {
        add_menu_page(
            __('Client Control', 'ccontrol'),
            __('Client Control', 'ccontrol'),
            'manage_options',
            'ccontrol-dashboard',
            array($this, 'ccontrol_dashboard'),
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

    public function ccontrol_dashboard()
    {
        echo 'hasdasd';
    }

    public function register_ccontrol_settings()
    { // whitelist options
        register_setting('ccontrol-group', 'ccontrol_logo');
        register_setting('ccontrol-group', 'ccontrol_name');
        register_setting('ccontrol-group', 'ccontrol_email');
        register_setting('ccontrol-group', 'ccontrol_telf');
        register_setting('ccontrol-group', 'ccontrol_invoice_number');
        register_setting('ccontrol-group', 'ccontrol_invoice_last_post_change');
        register_setting('ccontrol-group', 'ccontrol_budget_conditions');
        register_setting('ccontrol-group', 'ccontrol_budget_accounts_venezuela');
        register_setting('ccontrol-group', 'ccontrol_budget_accounts_usa');
        register_setting('ccontrol-group', 'ccontrol_budget_accounts_paypal');
    }

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
                        <th scope="row">
                            <p><?php _e('Logo', 'ccontrol');  ?></p>
                            <small><?php _e('Debe ser en formato jpg'); ?></small>
                        </th>
                        <td>
                            <?php $image = (get_option('ccontrol_logo') != '') ? get_option('ccontrol_logo') : 'https://placehold.it/70x70'; ?>
                            <img id="ccontrol_logo" src="<?php echo $image; ?>" alt="logo" />
                            <br />
                            <input type="hidden" name="ccontrol_logo" id="image_url" class="regular-text" value="<?php echo $image; ?>" />
                            <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Nombre', 'ccontrol'); ?></th>
                        <td><input type="text" name="ccontrol_name" value="<?php echo esc_attr(get_option('ccontrol_name')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Correo Electrónico', 'ccontrol'); ?></th>
                        <td><input type="text" name="ccontrol_email" value="<?php echo esc_attr(get_option('ccontrol_email')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Teléfono', 'ccontrol'); ?></th>
                        <td><input type="text" name="ccontrol_telf" value="<?php echo esc_attr(get_option('ccontrol_telf')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Número de Factura', 'ccontrol'); ?></th>
                        <td><input type="text" name="ccontrol_invoice_number" value="<?php echo esc_attr(get_option('ccontrol_invoice_number')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Términos y Condiciones en Factura por defecto', 'ccontrol'); ?></th>
                        <td>
                            <textarea name="ccontrol_budget_conditions" id="ccontrol_budget_conditions" cols="80" rows="5"><?php echo esc_html(get_option('ccontrol_budget_conditions')); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Números de Cuentas para Pago en Factura (Venezuela)', 'ccontrol'); ?></th>
                        <td>
                            <textarea name="ccontrol_budget_accounts_venezuela" id="ccontrol_budget_accounts_venezuela" cols="80" rows="8"><?php echo esc_html(get_option('ccontrol_budget_accounts_venezuela')); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Números de Cuentas para Pago en Factura (USA)', 'ccontrol'); ?></th>
                        <td>
                            <textarea name="ccontrol_budget_accounts_usa" id="ccontrol_budget_accounts_usa" cols="80" rows="15"><?php echo esc_html(get_option('ccontrol_budget_accounts_usa')); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Números de Cuentas para Pago en Factura (PayPal)', 'ccontrol'); ?></th>
                        <td>
                            <textarea name="ccontrol_budget_accounts_paypal" id="ccontrol_budget_accounts_paypal" cols="80" rows="5"><?php echo esc_html(get_option('ccontrol_budget_accounts_paypal')); ?></textarea>
                        </td>
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
