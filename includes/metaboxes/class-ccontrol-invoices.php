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

class Ccontrol_Metaboxes_Invoice
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function ccontrol_metabox()
    {
        add_meta_box(
            'cc_invoices_main_metabox',
            esc_attr__('Información del Cliente', 'ccontrol'),
            array($this, 'cc_invoices_main_metabox'),
            'cc_invoices',
            'normal',
            'high'
        );

        add_meta_box(
            'cc_invoices_items_metabox',
            esc_attr__('Items de la Factura', 'ccontrol'),
            array($this, 'cc_invoices_items_metabox'),
            'cc_invoices',
            'normal',
            'high',
            ['class' => 'cc-invoice-items']
        );

        add_meta_box(
            'cc_invoices_payment_metabox',
            esc_attr__('Metodos de Pagos y Condiciones', 'ccontrol'),
            array($this, 'cc_invoices_payment_metabox'),
            'cc_invoices',
            'normal',
            'high'
        );

        add_meta_box(
            'cc_invoices_payment_metabox',
            esc_attr__('Metodos de Pagos y Condiciones', 'ccontrol'),
            array($this, 'cc_invoices_payment_metabox'),
            'cc_invoices'
        );

        add_meta_box(
            'cc_invoices_print_metabox',
            esc_attr__('Imprimir Factura', 'ccontrol'),
            array($this, 'cc_invoices_print_metabox'),
            'cc_invoices',
            'side',
            'high'
        );
    }

    public function cc_invoices_main_metabox($post)
    {
        wp_nonce_field('ccontrol_metabox', 'ccontrol_metabox_nonce'); ?>
		<div class="postmeta-wrapper">
			<div class="postmeta-item-wrapper">
				<?php $value = get_post_meta($post->ID, 'status_factura', true); ?>
				<label for="status_factura">
					<?php esc_html_e('Estatus', 'ccontrol'); ?>
				</label>
				<select name="status_factura" id="status_factura">
					<option value="" selected disabled><?php esc_attr_e('Seleccione el estatus', 'ccontrol'); ?></option>
					<option value="sent" <?php selected($value, 'sent'); ?>><?php esc_attr_e('Enviado', 'ccontrol'); ?></option>
					<option value="accepted" <?php selected($value, 'accepted'); ?>><?php esc_attr_e('Aceptado', 'ccontrol'); ?></option>
					<option value="rejected" <?php selected($value, 'rejected'); ?>><?php esc_attr_e('Rechazado', 'ccontrol'); ?></option>
					<option value="paid" <?php selected($value, 'paid'); ?>><?php esc_attr_e('Pagado', 'ccontrol'); ?></option>
				</select>
			</div>
			<div class="postmeta-item-wrapper cc-col-2">
				<?php $value = get_post_meta($post->ID, 'status_factura', true); ?>
				<label for="status_factura">
					<?php esc_html_e('Estatus', 'ccontrol'); ?>
				</label>
				<select name="status_factura" id="status_factura">
					<option value="" selected disabled><?php esc_attr_e('Seleccione el estatus', 'ccontrol'); ?></option>
					<option value="sent" <?php selected($value, 'sent'); ?>><?php esc_attr_e('Enviado', 'ccontrol'); ?></option>
					<option value="accepted" <?php selected($value, 'accepted'); ?>><?php esc_attr_e('Aceptado', 'ccontrol'); ?></option>
					<option value="rejected" <?php selected($value, 'rejected'); ?>><?php esc_attr_e('Rechazado', 'ccontrol'); ?></option>
					<option value="paid" <?php selected($value, 'paid'); ?>><?php esc_attr_e('Pagado', 'ccontrol'); ?></option>
				</select>
			</div>
			<div class="postmeta-item-wrapper cc-col-2">
				<?php $value = (get_post_meta($post->ID, 'numero_factura', true) != '') ? get_post_meta($post->ID, 'numero_factura', true) : get_option('ccontrol_invoice_number'); ?>
				<label for="numero_factura">
					<?php esc_html_e('Número de Factura', 'ccontrol'); ?>
				</label>
				<input type="text" id="numero_factura" name="numero_factura" value="<?php echo esc_attr($value); ?>" size="40" />
			</div>

			<div class="postmeta-item-wrapper cc-col-2">
				<?php $value = get_post_meta($post->ID, 'cliente_factura', true); ?>
				<label for="cliente_factura">
					<?php esc_html_e('Cliente', 'ccontrol'); ?>
				</label>
				<select name="cliente_factura" id="cliente_factura">
					<option value="" selected disabled><?php esc_attr_e('Seleccione el cliente', 'ccontrol'); ?></option>
					<?php $arr_clientes = new WP_Query(array('post_type' => 'cc_clientes', 'posts_per_page' => -1)); ?>
					<?php while ($arr_clientes->have_posts()) : $arr_clientes->the_post(); ?>
						<option value="<?php echo esc_attr(get_the_ID()); ?>" <?php selected($value, get_the_ID()); ?>><?php echo esc_html(get_the_title()); ?></option>
					<?php endwhile; ?>
					<?php wp_reset_query(); ?>
				</select>
			</div>

			<div class="postmeta-item-wrapper cc-col-2">
				<?php $value = (get_post_meta($post->ID, 'fecha_factura', true) != '') ? get_post_meta($post->ID, 'fecha_factura', true) : date('m/d/Y') ?>
				<label for="fecha_factura">
					<?php esc_html_e('Fecha Vencimiento de Factura', 'ccontrol'); ?>
				</label>
				<input type="date" id="fecha_factura" name="fecha_factura" value="<?php echo esc_attr($value); ?>" size="40" />
			</div>
		</div>
	<?php
    }

    public function cc_invoices_payment_metabox($post)
    {
        ?>
		<div class="payment-methods-container">
			<div class="postmeta-wrapper">
				<div class="postmeta-item-wrapper cc-complete">
					<label>
						<?php esc_html_e('Método de Pago a usar:', 'ccontrol'); ?>
					</label>
					<div class="payment-methods-selector">
						<?php $metodo_pago = get_post_meta($post->ID, 'metodo_pago', true); ?>
						<label for="bs"><input type="radio" name="metodo_pago" id="bs" value="bs" <?php checked($metodo_pago, 'bs', true); ?> /> <?php esc_html_e('Bolívares', 'ccontrol'); ?></label>
						<label for="usd"><input type="radio" name="metodo_pago" id="usd" value="usd" <?php checked($metodo_pago, 'usd', true); ?> /> <?php esc_html_e('Dólares', 'ccontrol'); ?></label>
						<label for="paypal"><input type="radio" name="metodo_pago" id="paypal" value="paypal" <?php checked($metodo_pago, 'paypal', true); ?> /> <?php esc_html_e('PayPal', 'ccontrol'); ?></label>
					</div>
				</div>
				<div class="postmeta-item-wrapper cc-complete">
					<?php $value = get_post_meta($post->ID, 'terminos_condiciones', true); ?>
					<label for="terminos_condiciones">
						<?php esc_html_e('Términos y Condiciones', 'ccontrol'); ?>
					</label>
					<?php wp_editor(htmlspecialchars($value), 'terminos_condiciones', $settings = array('textarea_name' => 'terminos_condiciones', 'textarea_rows' => 3)); ?>
				</div>
			</div>
		</div>

	<?php
    }

    public function cc_invoices_print_metabox($post)
    {
        ?>
		<div class="button-text">
			<p><?php esc_html_e('Haz click aquí para imprimir la factura en formato PDF', 'ccontrol'); ?></p>
		</div>
		<a id="printInvoice" data-id="<?php echo esc_attr($post->ID); ?>" class="button button-primary button-large cc-btn-100"><?php esc_html_e('Imprimir Factura', 'ccontrol'); ?></a>
		<div class="button-text">
			<p><?php esc_html_e('Haz click aquí para enviar vía correo electrónico la factura directamente al cliente', 'ccontrol'); ?></p>
		</div>
		<a id="sendInvoice" data-id="<?php echo esc_attr($post->ID); ?>" class="button button-primary button-large cc-btn-100"><?php esc_html_e('Enviar Factura', 'ccontrol'); ?></a>
		<div id="sendInvoiceResponse" class="send-quote-response"></div>
	<?php
    }



    public function cc_invoices_items_metabox($post)
    {
        ?>
		<div class="postmeta-wrapper">
			<div class="postmeta-item-wrapper cc-complete">
				<?php $items_factura = get_post_meta($post->ID, 'items_factura', true); ?>
				<div class="postmeta-items-container">
					<?php if (!empty($items_factura)) : ?>
						<?php $price = 0; ?>
						<?php $i = 0; ?>
						<?php foreach ($items_factura as $factura) : ?>
							<div data-id="<?php echo esc_attr($i); ?>" class="row-postmeta-items">
								<div class="col-postmeta-item">
									<label for="item_factura_name[]">
										<?php esc_html_e('Descripción', 'ccontrol'); ?>
									</label>
									<input type="text" name="item_factura_name[]" id="item_factura_name[]" value="<?php echo esc_attr($factura['item_factura_name']); ?>" size="40" />
								</div>
								<div class="col-postmeta-item">
									<label for="item_factura_qty[]">
										<?php esc_html_e('Cantidad', 'ccontrol'); ?>
									</label>
									<input type="number" min="1" name="item_factura_qty[]" id="item_factura_qty[]" value="<?php echo esc_attr($factura['item_factura_qty']); ?>" size="10" />
								</div>
								<div class="col-postmeta-item">
									<label for="item_factura_price[]">
										<?php esc_html_e('Precio', 'ccontrol'); ?>
									</label>
									<input type="text" name="item_factura_price[]" id="item_factura_price[]" value="<?php echo esc_attr($factura['item_factura_price']); ?>" size="10" />
									<?php $price = $price + ($factura['item_factura_qty'] * $factura['item_factura_price']); ?>
								</div>
								<div class="col-postmeta-item">
									<button class="item-factura-add">+</button>
									<button class="item-factura-remove" <?php echo esc_attr(($i <= 0) ? 'style="display:none;"' : ''); ?>>-</button>
								</div>
							</div>
						<?php $i++;
						endforeach; ?>
					<?php else : ?>
						<div data-id="0" class="row-postmeta-items">
							<div class="col-postmeta-item">
								<label for="item_factura_name[]">
									<?php esc_html_e('Descripción', 'ccontrol'); ?>
								</label>
								<input type="text" name="item_factura_name[]" id="item_factura_name[]" value="" size="40" />
							</div>
							<div class="col-postmeta-item">
								<label for="item_factura_qty[]">
									<?php esc_html_e('Cantidad', 'ccontrol'); ?>
								</label>
								<input type="number" min="1" name="item_factura_qty[]" id="item_factura_qty[]" value="" size="10" />
							</div>
							<div class="col-postmeta-item">
								<label for="item_factura_price[]">
									<?php esc_html_e('Precio', 'ccontrol'); ?>
								</label>
								<input type="text" name="item_factura_price[]" id="item_factura_price[]" value="" size="10" />
							</div>
							<div class="col-postmeta-item">
								<button class="item-factura-add">+</button>
								<button class="item-factura-remove" style="display:none;">-</button>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="postmeta-item-wrapper cc-invoice-total-price cc-complete">
				<?php $metodo_pago = get_post_meta($post->ID, 'metodo_pago', true); ?>
				<label for="price">
					<?php esc_html_e('Subtotal:', 'ccontrol'); ?>
				</label>
				<code class="price"><?php echo esc_html(strtoupper($metodo_pago)); ?> <?php echo esc_html(number_format($price, 2, ',', '.')); ?></code>
			</div>
			<div class="postmeta-item-wrapper postmeta-tax-wrapper cc-complete">
				<?php $activar_tax = (
				    get_post_meta($post->ID, 'activar_tax', true) !== ''
				    ? get_post_meta($post->ID, 'activar_tax', true)
				    : 'no'
				); ?>
				<div class="row-tax-wrapper">
					<div class="col-tax-item">
						<label for="activar_tax">
							<?php esc_html_e('¿Activar impuesto?', 'ccontrol'); ?>
						</label>
						<label for="activar_tax"><input type="radio" name="activar_tax" id="activar_tax" value="yes" <?php checked($activar_tax, 'yes'); ?> /> <?php esc_html_e('Si', 'ccontrol'); ?></label>
						<label for="desactivar_tax"><input type="radio" name="activar_tax" id="desactivar_tax" value="no" <?php checked($activar_tax, 'no'); ?> /> <?php esc_html_e('No', 'ccontrol'); ?></label>
					</div>
				</div>
				<div class="row-tax-wrapper">
					<div class="col-tax-item">
						<label class="tax-percentage" for="tax_percentage" <?php echo ($activar_tax === 'no') ? 'style="display:none;"' : ''; ?>>
							<?php esc_html_e('% Impuesto', 'ccontrol'); ?>
							<?php $tax_percentage = get_post_meta($post->ID, 'tax_percentage', true); ?>
							<input type="number" name="tax_percentage" id="tax_percentage" value="<?php echo esc_attr($tax_percentage); ?>" />
							<?php if (($tax_percentage !== '') && ($tax_percentage > 0)) : ?>
							<?php $price = $price + (($price * (float) $tax_percentage) / 100); ?>
							<?php endif; ?>
						</label>
					</div>
				</div>
			</div>
			<div class="postmeta-item-wrapper postmeta-tax-wrapper cc-complete">
				<?php $activar_discount = (
				    get_post_meta($post->ID, 'activar_discount', true) !== ''
				    ? get_post_meta($post->ID, 'activar_discount', true)
				    : 'no'
				); ?>
				<div class="row-tax-wrapper">
					<div class="col-tax-item">
						<label for="activar_tax">
							<?php esc_html_e('¿Activar descuento?', 'ccontrol'); ?>
						</label>
						<label for="activar_discount"><input type="radio" name="activar_discount" id="activar_discount" value="yes" <?php checked($activar_discount, 'yes'); ?> /> <?php esc_html_e('Si', 'ccontrol'); ?></label>
						<label for="desactivar_discount"><input type="radio" name="activar_discount" id="desactivar_discount" value="no" <?php checked($activar_discount, 'no'); ?> /> <?php esc_html_e('No', 'ccontrol'); ?></label>
					</div>
				</div>
				<div class="row-tax-wrapper">
					<div class="col-tax-item">
						<label class="discount-percentage" for="discount_percentage" <?php echo ($activar_discount === 'no') ? 'style="display:none;"' : ''; ?>>
							<?php esc_html_e('% Descuento', 'ccontrol'); ?>
							<?php $discount_percentage = get_post_meta($post->ID, 'discount_percentage', true); ?>
							<input type="text" name="discount_percentage" id="discount_percentage" value="<?php echo esc_attr($discount_percentage); ?>" />
							<?php if (($discount_percentage !== '') && ($discount_percentage > 0)) : ?>
							<?php $price = $price - (($price * (float) $discount_percentage) / 100); ?>
							<?php endif; ?>
						</label>
					</div>
				</div>
			</div>
			<div class="postmeta-item-wrapper cc-invoice-total-price cc-complete">
				<?php $metodo_pago = get_post_meta($post->ID, 'metodo_pago', true); ?>
				<label for="price">
					<?php esc_html_e('Total:', 'ccontrol'); ?>
				</label>
				<code class="price"><?php echo esc_html(strtoupper($metodo_pago)); ?> <?php echo esc_html(number_format($price, 2, ',', '.')); ?></code>
				<input type="hidden" name="price" id="price" value="<?php echo esc_attr($price); ?>" />
			</div>
		</div>
<?php
    }

    public function cc_invoices_save_metabox($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        if (!isset($_POST['ccontrol_metabox_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['ccontrol_metabox_nonce'];
        $arr_kses = array('br' => array(), 'p' => array(), 'strong' => array());

        if (!wp_verify_nonce($nonce, 'ccontrol_metabox')) {
            return $post_id;
        }

        if (isset($_POST['status_factura'])) {
            $status_factura = sanitize_text_field($_POST['status_factura']);
            update_post_meta($post_id, 'status_factura', $status_factura);
        }

        if (isset($_POST['numero_factura'])) {
            $numero_factura = sanitize_text_field($_POST['numero_factura']);
            update_post_meta($post_id, 'numero_factura', $numero_factura);
        }

        if (isset($_POST['cliente_factura'])) {
            $cliente_factura = sanitize_text_field($_POST['cliente_factura']);
            update_post_meta($post_id, 'cliente_factura', $cliente_factura);
        }

        if (isset($_POST['fecha_factura'])) {
            $fecha_factura = sanitize_text_field($_POST['fecha_factura']);
            update_post_meta($post_id, 'fecha_factura', $fecha_factura);
        }

        if (isset($_POST['metodo_pago'])) {
            $metodo_pago = sanitize_text_field($_POST['metodo_pago']);
            update_post_meta($post_id, 'metodo_pago', $metodo_pago);
        }

        if (isset($_POST['terminos_condiciones'])) {
            $terminos_condiciones = sanitize_text_field($_POST['terminos_condiciones']);
            update_post_meta($post_id, 'terminos_condiciones', $terminos_condiciones);
        }

        if (isset($_POST['activar_tax'])) {
            $activar_tax = sanitize_text_field($_POST['activar_tax']);
            update_post_meta($post_id, 'activar_tax', $activar_tax);
        }

        if (isset($_POST['tax_percentage'])) {
            $tax_percentage = sanitize_text_field($_POST['tax_percentage']);
            update_post_meta($post_id, 'tax_percentage', $tax_percentage);
        }

        if (isset($_POST['discount_percentage'])) {
            $discount_percentage = sanitize_text_field($_POST['discount_percentage']);
            update_post_meta($post_id, 'discount_percentage', $discount_percentage);
        }

        if (isset($_POST['activar_discount'])) {
            $activar_discount = sanitize_text_field($_POST['activar_discount']);
            update_post_meta($post_id, 'activar_discount', $activar_discount);
        }

        if (isset($_POST['price'])) {
            $price = sanitize_text_field($_POST['price']);
            update_post_meta($post_id, 'price', $price);
        }

        if (isset($_POST['item_factura_name'])) {
            $item_factura_name = $_POST['item_factura_name'];
            $item_factura_qty = $_POST['item_factura_qty'];
            $item_factura_price = $_POST['item_factura_price'];
            for ($i = 0; $i <= count($item_factura_name); $i++) {
                if (empty($item_factura_name[$i])) {
                    continue;
                } else {
                    $items_factura[$i] = [
                        'item_factura_name' => wp_kses($item_factura_name[$i], $arr_kses),
                        'item_factura_qty' => wp_kses($item_factura_qty[$i], $arr_kses),
                        'item_factura_price' => wp_kses($item_factura_price[$i], $arr_kses),
                    ];
                }
            }
            update_post_meta($post_id, 'items_factura', $items_factura);
        }

        $this->cc_invoices_invoice_update($post_id);
    }

    public function cc_invoices_invoice_update($post_id)
    {
        global $wpdb;

        $query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'cc_invoices' ORDER BY ID DESC LIMIT 0,1";

        $result = $wpdb->get_results($query);
        $row = $result[0];
        $last_id = $row->ID;

        $current_number = get_option('ccontrol_invoice_number');
        $last_post_change = get_option('ccontrol_invoice_last_post_change');

        if (($last_post_change != $post_id) && ($last_id == $post_id)) {
            $new_invoice_number = (int) $current_number + 1;
            update_option('ccontrol_invoice_number', $new_invoice_number);
            update_option('ccontrol_invoice_last_post_change', $post_id);
        }
    }
}
