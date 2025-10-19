<?php

/**
 * Metaboxes for Client Custom post type
 *
 * @package    Ccontrol
 * @subpackage Ccontrol/includes
 * @author     Robert Ochoa <ochoa.robert1@gmail.com>
 */

if (!defined('WPINC')) {
    die;
}

class Ccontrol_Metaboxes_Client
{
    private $plugin_name;
    private $version;

    /**
     * Method __construct
     *
     * @param string $plugin_name [Plugin Name]
     * @param string $version [Current Version]
     *
     * @return void
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Method ccontrol_metabox
     *
     * @return void
     */
    public function ccontrol_metabox()
    {
        add_meta_box(
            'cc_clientes_metabox',
            esc_attr__('Información del Cliente', 'ccontrol'),
            array($this, 'cc_clientes_main_metabox'),
            'cc_clientes'
        );
    }

    /**
     * Method cc_clientes_main_metabox
     *
     * @param object $post [Current Post]
     *
     * @return string|void
     */
    public function cc_clientes_main_metabox($post)
    {
        wp_nonce_field('ccontrol_metabox', 'ccontrol_metabox_nonce'); ?>
        <div class="postmeta-wrapper">
            <div class="postmeta-item-wrapper cc-col-2">
                <?php $value = get_post_meta($post->ID, 'nombre_cliente', true); ?>
                <label for="nombre_cliente">
                    <?php esc_html_e('Persona de Contacto', 'ccontrol'); ?>
                </label>
                <input type="text" id="nombre_cliente" tabindex="0" title="<?php esc_attr_e('Ingrese el nombre de la persona de contacto', 'ccontrol'); ?>" name="nombre_cliente" value="<?php echo esc_attr($value); ?>" size="40" />
            </div>

            <div class="postmeta-item-wrapper cc-col-2">
                <?php $value = get_post_meta($post->ID, 'correo_cliente', true); ?>
                <label for="correo_cliente">
                    <?php esc_html_e('Correo Electrónico', 'ccontrol'); ?>
                </label>
                <input type="email" id="correo_cliente" tabindex="0" title="<?php esc_attr_e('Ingrese el correo electrónico de contacto', 'ccontrol'); ?>" name="correo_cliente" value="<?php echo esc_attr($value); ?>" size="40" />
            </div>

            <div class="postmeta-item-wrapper cc-col-2">
                <?php $value = get_post_meta($post->ID, 'telf_cliente', true); ?>
                <label for="telf_cliente">
                    <?php esc_html_e('Teléfono', 'ccontrol'); ?>
                </label>
                <input type="tel" id="telf_cliente" tabindex="0" title="<?php esc_attr_e('Ingrese el número telefónico de contacto', 'ccontrol'); ?>" name="telf_cliente" value="<?php echo esc_attr($value); ?>" size="40" />
            </div>

            <div class="postmeta-item-wrapper cc-col-2">
                <?php $value = get_post_meta($post->ID, 'tipo_cliente', true); ?>
                <label for="tipo_cliente">
                    <?php esc_html_e('Tipo de Cliente', 'ccontrol'); ?>
                </label>
                <select name="tipo_cliente" id="tipo_cliente" tabindex="0" title="<?php esc_attr_e('Seleccione el tipo de cliente', 'ccontrol'); ?>">
                    <option value="" selected disabled><?php esc_attr_e('Seleccione tipo de cliente', 'ccontrol'); ?></option>
                    <option value="Potencial" <?php selected($value, 'Potencial'); ?>><?php esc_attr_e('Potencial', 'ccontrol'); ?></option>
                    <option value="Recurrente" <?php selected($value, 'Recurrente'); ?>><?php esc_attr_e('Recurrente', 'ccontrol'); ?></option>
                    <option value="Saliente" <?php selected($value, 'Saliente'); ?>><?php esc_attr_e('Saliente', 'ccontrol'); ?></option>
                </select>
            </div>

            <div class="postmeta-item-wrapper cc-col-12">
                <?php $value = get_post_meta($post->ID, 'direccion_cliente', true); ?>
                <label for="direccion_cliente">
                    <?php esc_html_e('Direccion del Cliente (Usado para facturación)', 'ccontrol'); ?>
                </label>
                <textarea name="direccion_cliente" id="direccion_cliente" tabindex="0" title="<?php esc_attr_e('Ingrese la dirección del cliente', 'ccontrol'); ?>" cols="30" rows="5"><?php echo esc_attr($value); ?></textarea>
            </div>
        </div>
<?php
    }

    /**
     * Method cc_clientes_save_metabox
     *
     * @param string $post_id [Current Post ID]
     *
     * @return string|void
     */
    public function cc_clientes_save_metabox($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        if (!isset($_POST['ccontrol_metabox_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['ccontrol_metabox_nonce'];

        if (!wp_verify_nonce($nonce, 'ccontrol_metabox')) {
            return $post_id;
        }

        if (isset($_POST['nombre_cliente'])) {
            $nombre_cliente = sanitize_text_field($_POST['nombre_cliente']);
            update_post_meta($post_id, 'nombre_cliente', $nombre_cliente);
        }

        if (isset($_POST['correo_cliente'])) {
            $correo_cliente = sanitize_text_field($_POST['correo_cliente']);
            update_post_meta($post_id, 'correo_cliente', $correo_cliente);
        }

        if (isset($_POST['telf_cliente'])) {
            $telf_cliente = sanitize_text_field($_POST['telf_cliente']);
            update_post_meta($post_id, 'telf_cliente', $telf_cliente);
        }

        if (isset($_POST['tipo_cliente'])) {
            $tipo_cliente = sanitize_text_field($_POST['tipo_cliente']);
            update_post_meta($post_id, 'tipo_cliente', $tipo_cliente);
        }

        if (isset($_POST['direccion_cliente'])) {
            $tipo_cliente = sanitize_text_field($_POST['direccion_cliente']);
            update_post_meta($post_id, 'direccion_cliente', $tipo_cliente);
        }
    }
}
