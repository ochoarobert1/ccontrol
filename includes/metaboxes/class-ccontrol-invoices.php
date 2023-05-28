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
			__('Información del Cliente', 'ccontrol'),
			array($this, 'cc_invoices_main_metabox'),
			'cc_invoices'
		);

		add_meta_box(
			'cc_invoices_items_metabox',
			__('Items de la Factura', 'ccontrol'),
			array($this, 'cc_invoices_items_metabox'),
			'cc_invoices'
		);

		add_meta_box(
			'cc_invoices_print_metabox',
			__('Imprimir Invoice', 'ccontrol'),
			array($this, 'cc_invoices_print_metabox'),
			'cc_invoices',
			'side'
		);
	}

	public function cc_invoices_print_metabox($post)
	{
?>
		<div class="button-text">
			<p><?php _e('Haz click aquí para imprimir el invoice en formato PDF', 'ccontrol'); ?></p>
		</div>
		<a id="printInvoice" data-id="<?php echo $post->ID; ?>" class="button button-primary button-large cc-btn-100"><?php _e('Imprimir Invoice', 'ccontrol'); ?></a>
		<div class="button-text">
			<p><?php _e('Haz click aquí para enviar vía correo electrónico el invoice directamente al cliente', 'ccontrol'); ?></p>
		</div>
		<a id="sendInvoice" data-id="<?php echo $post->ID; ?>" class="button button-primary button-large cc-btn-100"><?php _e('Enviar Invoice', 'ccontrol'); ?></a>
	<?php
	}

	public function cc_invoices_main_metabox($post)
	{
		wp_nonce_field('ccontrol_metabox', 'ccontrol_metabox_nonce'); ?>
		<div class="postmeta-wrapper">
			<div class="postmeta-item-wrapper cc-col-2">
				<?php $value = (get_post_meta($post->ID, 'numero_factura', true) != '') ? get_post_meta($post->ID, 'numero_factura', true) : get_option('ccontrol_invoice_number'); ?>
				<label for="numero_factura">
					<?php _e('Número de Factura', 'ccontrol'); ?>
				</label>
				<input type="text" id="numero_factura" name="numero_factura" value="<?php echo esc_attr($value); ?>" size="40" />
			</div>

			<div class="postmeta-item-wrapper cc-col-2">
				<?php $value = get_post_meta($post->ID, 'cliente_factura', true); ?>
				<label for="cliente_factura">
					<?php _e('Cliente', 'ccontrol'); ?>
				</label>
				<select name="cliente_factura" id="cliente_factura">
					<option value="" selected disabled><?php _e('Seleccione el cliente', 'ccontrol'); ?></option>
					<?php $arr_clientes = new WP_Query(array('post_type' => 'cc_clientes', 'posts_per_page' => -1)); ?>
					<?php while ($arr_clientes->have_posts()) : $arr_clientes->the_post(); ?>
						<option value="<?php echo get_the_ID(); ?>" <?php selected($value, get_the_ID()); ?>><?php echo get_the_title(); ?></option>
					<?php endwhile; ?>
					<?php wp_reset_query(); ?>
				</select>
			</div>
		</div>
	<?php
	}

	public function cc_invoices_items_metabox($post)
	{
	?>
		<div class="postmeta-wrapper">
			<div class="postmeta-item-wrapper cc-complete">
				<?php $value = get_post_meta($post->ID, 'items_factura', true); ?>
				<label for="elem_items_presupuesto">
					<?php _e('Elementos de la Factura', 'ccontrol'); ?>
				</label>
				<?php wp_editor(htmlspecialchars($value), 'items_factura', $settings = array('textarea_name' => 'items_factura', 'textarea_rows' => 6)); ?>
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

		if (isset($_POST['numero_factura'])) {
			$numero_factura = sanitize_text_field($_POST['numero_factura']);
			update_post_meta($post_id, 'numero_factura', $numero_factura);
		}

		if (isset($_POST['cliente_factura'])) {
			$cliente_factura = sanitize_text_field($_POST['cliente_factura']);
			update_post_meta($post_id, 'cliente_factura', $cliente_factura);
		}

		if (isset($_POST['items_factura'])) {
			$items_factura = wp_kses($_POST['items_factura'], $arr_kses);
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
