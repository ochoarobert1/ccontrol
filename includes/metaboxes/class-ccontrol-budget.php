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
class Ccontrol_Metaboxes_Budget
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

	public function cc_presupuestos_print_metabox($post)
	{
?>
		<div class="button-text">
			<p><?php _e('Haz click aquí para imprimir el presupuesto en formato PDF', 'ccontrol'); ?></p>
		</div>
		<a id="printQuote" data-id="<?php echo $post->ID; ?>" class="button button-primary button-large cc-btn-100"><?php _e('Imprimir Presupuesto', 'ccontrol'); ?></a>
		<div class="button-text">
			<p><?php _e('Haz click aquí para enviar vía correo electrónico el presupuesto directamente al cliente', 'ccontrol'); ?></p>
		</div>
		<a id="sendQuote" data-id="<?php echo $post->ID; ?>" class="button button-primary button-large cc-btn-100"><?php _e('Enviar Presupuesto', 'ccontrol'); ?></a>
	<?php
	}

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
						<option value="<?php echo get_the_ID(); ?>" <?php selected($value, get_the_ID()); ?>><?php echo get_the_title(); ?></option>
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
				<?php wp_editor(htmlspecialchars($value), 'elem_ofrecer_presupuesto', $settings = array('textarea_name' => 'elem_ofrecer_presupuesto', 'textarea_rows' => 3)); ?>
			</div>

			<div class="postmeta-item-wrapper cc-complete">
				<?php $value = get_post_meta($post->ID, 'elem_items_presupuesto', true); ?>
				<label for="elem_items_presupuesto">
					<?php _e('Elementos del Presupuesto', 'ccontrol'); ?>
				</label>
				<?php wp_editor(htmlspecialchars($value), 'elem_items_presupuesto', $settings = array('textarea_name' => 'elem_items_presupuesto', 'textarea_rows' => 3)); ?>
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

	public function cc_budget_save_metabox($post_id)
	{
		if (!isset($_POST['ccontrol_metabox_nonce'])) {
			return $post_id;
		}

		$nonce = $_POST['ccontrol_metabox_nonce'];

		if (!wp_verify_nonce($nonce, 'ccontrol_metabox')) {
			return $post_id;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
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

		$arr_kses = array('br' => array(), 'p' => array(), 'strong' => array());

		if (isset($_POST['elem_ofrecer_presupuesto'])) {
			$mydata = wp_kses($_POST['elem_ofrecer_presupuesto'], $arr_kses);
			update_post_meta($post_id, 'elem_ofrecer_presupuesto', $mydata);
		}

		if (isset($_POST['elem_items_presupuesto'])) {
			$mydata = wp_kses($_POST['elem_items_presupuesto'], $arr_kses);
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
}
