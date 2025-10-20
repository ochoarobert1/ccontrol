<?php

/**
 * Custom Metaboxes for Budgets
 *
 * @package    Ccontrol
 * @subpackage Ccontrol/includes
 * @author     Robert Ochoa <ochoa.robert1@gmail.com>
 */

if (!defined('WPINC')) {
    die;
}

class Ccontrol_Metaboxes_Budget
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
            'cc_presupuestos_metabox',
            esc_attr__('Información del Presupuesto', 'ccontrol'),
            array($this, 'cc_presupuestos_main_metabox'),
            'cc_presupuestos'
        );

        add_meta_box(
            'cc_presupuestos_print_metabox',
            esc_attr__('Imprimir Presupuesto', 'ccontrol'),
            array($this, 'cc_presupuestos_print_metabox'),
            'cc_presupuestos',
            'side'
        );
    }

    /**
     * Method cc_presupuestos_print_metabox
     *
     * @param object $post [Current Post]
     *
     * @return void
     */
    public function cc_presupuestos_print_metabox($post)
    {
?>
        <div class="button-text">
            <p><?php esc_html_e('Haz click aquí para imprimir el presupuesto en formato PDF', 'ccontrol'); ?></p>
        </div>
        <a id="printQuote" data-id="<?php echo esc_attr($post->ID); ?>" class="button button-primary button-large cc-btn-100"><?php esc_html_e('Imprimir Presupuesto', 'ccontrol'); ?></a>
        <div class="button-text">
            <p><?php esc_html_e('Haz click aquí para enviar vía correo electrónico el presupuesto directamente al cliente', 'ccontrol'); ?></p>
        </div>
        <a id="sendQuote" data-id="<?php echo esc_attr($post->ID); ?>" class="button button-primary button-large cc-btn-100"><?php esc_html_e('Enviar Presupuesto', 'ccontrol'); ?></a>
        <div id="sendQuoteResponse" class="send-quote-response"></div>
    <?php
    }

    /**
     * Method cc_presupuestos_main_metabox
     *
     * @param object $post [Current Post]
     *
     * @return void
     */
    public function cc_presupuestos_main_metabox($post)
    {
        wp_nonce_field('ccontrol_metabox', 'ccontrol_metabox_nonce'); ?>
        <div class="postmeta-wrapper">
            <div class="postmeta-item-wrapper">
                <?php $value = get_post_meta($post->ID, 'status_presupuesto', true); ?>
                <label for="status_presupuesto">
                    <?php esc_html_e('Estatus', 'ccontrol'); ?>
                </label>
                <select name="status_presupuesto" id="status_presupuesto">
                    <option value="" selected disabled>
                        <?php esc_html_e('Seleccione el estatus', 'ccontrol'); ?>
                    </option>
                    <option value="sent" <?php selected($value, 'sent'); ?>>
                        <?php esc_html_e('Enviado', 'ccontrol'); ?>
                    </option>
                    <option value="accepted" <?php selected($value, 'accepted'); ?>>
                        <?php esc_html_e('Aceptado', 'ccontrol'); ?>
                    </option>
                    <option value="rejected" <?php selected($value, 'rejected'); ?>>
                        <?php esc_html_e('Rechazado', 'ccontrol'); ?>
                    </option>
                </select>
            </div>
            
            <div class="postmeta-item-wrapper">
                <?php $value = get_post_meta($post->ID, 'cliente_presupuesto', true); ?>
                <label for="cliente_presupuesto">
                    <?php esc_html_e('Cliente', 'ccontrol'); ?>
                </label>
                <select name="cliente_presupuesto" id="cliente_presupuesto">
                    <option value="" selected disabled>
                        <?php esc_html_e('Seleccione el cliente', 'ccontrol'); ?>
                    </option>
                    <?php
                    $cliente_ids = get_posts(
                        [
                            'post_type' => 'cc_clientes',
                            'posts_per_page' => -1,
                            'fields' => 'ids'
                        ]
                    );
                    foreach ($cliente_ids as $cliente_id) : ?>
                        <option value="<?php echo esc_attr($cliente_id); ?>" <?php selected($value, $cliente_id); ?>>
                            <?php echo esc_html(get_the_title($cliente_id)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="postmeta-item-wrapper cc-complete">
                <?php $value = get_post_meta($post->ID, 'moneda_presupuesto', true); ?>
                <label for="moneda_presupuesto">
                    <?php esc_html_e('Tipo de Moneda', 'ccontrol'); ?>
                </label>
                <div class="radio-group">
                    <label for="moneda_presupuesto_bs">
                        <input type="radio" <?php checked($value, 'Bolivares'); ?> id="moneda_presupuesto_bs" name="moneda_presupuesto" value="Bolivares" />
                        <?php esc_html_e('Bolívares', 'ccontrol'); ?>
                    </label>
                    <label for="moneda_presupuesto_dl">
                        <input type="radio" <?php checked($value, 'Dolares'); ?> id="moneda_presupuesto_dl" name="moneda_presupuesto" value="Dolares" />
                        <?php esc_html_e('Dólares', 'ccontrol'); ?>
                    </label>
                    <label for="moneda_presupuesto_both">
                        <input type="radio" <?php checked($value, 'Ambos'); ?> id="moneda_presupuesto_both" name="moneda_presupuesto" value="Ambos" />
                        <?php esc_html_e('Ambos', 'ccontrol'); ?>
                    </label>
                </div>
            </div>

            <div class="postmeta-item-wrapper cc-complete">
                <?php $value = get_post_meta($post->ID, 'elem_ofrecer_presupuesto', true); ?>
                <label for="elem_ofrecer_presupuesto">
                    <?php esc_html_e('Elementos a Ofrecer', 'ccontrol'); ?>
                </label>
                <?php wp_editor(
                    $value,
                    'elem_ofrecer_presupuesto',
                    [
                        'textarea_name' => 'elem_ofrecer_presupuesto',
                        'textarea_rows' => 8,
                        'teeny' => true,
                        'media_buttons' => false
                    ]
                ); ?>
            </div>

            <div class="postmeta-item-wrapper cc-complete">
                <?php $value = get_post_meta($post->ID, 'elem_items_presupuesto', true); ?>
                <label for="elem_items_presupuesto">
                    <?php esc_html_e('Elementos del Presupuesto', 'ccontrol'); ?>
                </label>
                <?php wp_editor(
                    $value,
                    'elem_items_presupuesto',
                    [
                        'textarea_name' => 'elem_items_presupuesto',
                        'textarea_rows' => 8,
                        'teeny' => true,
                        'media_buttons' => false
                    ]
                );
                ?>
            </div>

            <div class="postmeta-item-wrapper cc-complete">
                <?php $value = get_post_meta($post->ID, 'precio_bs', true); ?>
                <label for="precio_bs">
                    <?php esc_html_e('Precio en Bs', 'ccontrol'); ?>
                </label>
                <input type="text" id="precio_bs" name="precio_bs" value="<?php echo esc_attr($value); ?>" size="40" />
            </div>

            <div class="postmeta-item-wrapper cc-complete">
                <?php $value = get_post_meta($post->ID, 'precio_usd', true); ?>
                <label for="precio_usd">
                    <?php esc_html_e('Precio en $', 'ccontrol'); ?>
                </label>
                <input type="text" id="precio_usd" name="precio_usd" value="<?php echo esc_attr($value); ?>" size="40" />
            </div>

            <div class="postmeta-item-wrapper cc-complete">
                <?php $value = get_post_meta($post->ID, 'tiempo_presupuesto', true); ?>
                <label for="tiempo_presupuesto">
                    <?php esc_html_e('Tiempo de Ejecución', 'ccontrol'); ?>
                </label>
                <input type="text" id="tiempo_presupuesto" name="tiempo_presupuesto" value="<?php echo esc_attr($value); ?>" size="40" />
            </div>
        </div>
<?php
    }

    /**
     * Method cc_budget_save_metabox
     *
     * @param string $post_id [Current Post ID]
     *
     * @return string|void
     */
    public function cc_budget_save_metabox($post_id)
    {
        if (!isset($_POST['ccontrol_metabox_nonce'])) {
            return $post_id;
        }

        if (!wp_verify_nonce($_POST['ccontrol_metabox_nonce'], 'ccontrol_metabox')) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        $fields = [
            'tipo_cliente' => 'sanitize_text_field',
            'cliente_presupuesto' => 'sanitize_text_field',
            'moneda_presupuesto' => 'sanitize_text_field',
            'precio_bs' => 'sanitize_text_field',
            'precio_usd' => 'sanitize_text_field',
            'tiempo_presupuesto' => 'sanitize_text_field',
            'status_presupuesto' => 'sanitize_text_field'
        ];

        $arr_kses = ['br' => [], 'p' => [], 'strong' => []];
        $fields_kses = [
            'elem_ofrecer_presupuesto' => $arr_kses,
            'elem_items_presupuesto' => $arr_kses
        ];

        foreach ($fields as $field => $sanitize_callback) {
            if (isset($_POST[$field])) {
                $mydata = $sanitize_callback($_POST[$field]);
                update_post_meta($post_id, $field, $mydata);
            }
        }

        foreach ($fields_kses as $field => $allowed_html) {
            if (isset($_POST[$field])) {
                $mydata = wp_kses($_POST[$field], $allowed_html);
                update_post_meta($post_id, $field, $mydata);
            }
        }
    }
}
