<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the dashboard  of the plugin.
 *
 * @link       http://robertochoaweb.com/
 * @since      1.0.0
 *
 * @package    Ccontrol
 * @subpackage Ccontrol/admin/partials
 */

if (!defined('WPINC')) {
    die;
}

$totalClients = $this->ccontrol_total_clients();
$totalQuotes = $this->ccontrol_total_quotes();
$totalInvoices = $this->ccontrol_total_invoices();
$logo_url = (get_option('ccontrol_logo') !== '') ? get_option('ccontrol_logo') : CCONTROL_PLUGIN_URL . '/admin/img/logo.png';
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
            <h2 class="wp-ui-text-primary"><?php esc_html_e('Total Clientes', 'ccontrol'); ?></h2>
            <a
                href="<?php echo esc_url(admin_url('post-new.php?post_type=cc_clientes')); ?>"
                tabindex="0"
                title="<?php esc_attr_e('Haz click aqui para crear un nuevo cliente', 'ccontrol'); ?>"
                class="button primary">
                <?php esc_html_e('Agregar nuevo cliente', 'ccontrol'); ?>
            </a>
            <div class="dashboard-divider"></div>
            <p><?php echo esc_html($totalClients); ?></p>
        </div>

        <div class="dashboard-widget">
            <h2 class="wp-ui-text-primary"><?php esc_html_e('Total Presupuestos', 'ccontrol'); ?></h2>
            <a
                href="<?php echo esc_url(admin_url('post-new.php?post_type=cc_presupuestos')); ?>"
                tabindex="0"
                title="<?php esc_attr_e('Haz click aqui para crear un nuevo presupuesto', 'ccontrol'); ?>"
                class="button primary">
                <?php esc_html_e('Agregar nuevo presupuesto', 'ccontrol'); ?>
            </a>
            <div class="dashboard-divider"></div>
            <p><?php echo esc_html($totalQuotes); ?></p>
        </div>

        <div class="dashboard-widget">
            <h2 class="wp-ui-text-primary"><?php esc_html_e('Total Facturas', 'ccontrol'); ?></h2>
            <a
                href="<?php echo esc_url(admin_url('post-new.php?post_type=cc_invoices')); ?>"
                tabindex="0"
                title="<?php esc_attr_e('Haz click aqui para crear una nueva factura', 'ccontrol'); ?>"
                class="button primary">
                <?php esc_html_e('Agregar nueva factura', 'ccontrol'); ?>
            </a>
            <div class="dashboard-divider"></div>
            <p><?php echo esc_html($totalInvoices); ?></p>
        </div>
    </div>
</div>