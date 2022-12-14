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
            __('Informaci??n del Cliente', 'ccontrol'),
            array($this, 'cc_clientes_main_metabox'),
            'cc_clientes'
        );

        add_meta_box(
            'cc_presupuestos_metabox',
            __('Informaci??n del Presupuesto', 'ccontrol'),
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

    public function ccontrol_create_pdf_send_callback()
    {
        /*
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        */

        if (isset($_POST['postid'])) {
            $postid = $_POST['postid'];
            $presupuesto = get_post($postid);
            $id_cliente = get_post_meta($postid, 'cliente_presupuesto', true);
        } else {
            $presupuesto = 'presupuesto';
            $id_cliente = 1;
        }

        $cliente_correo = get_post_meta($id_cliente, 'correo_cliente', true);

        require_once __DIR__ . '/../vendor/autoload.php';
        $pdf = new tFPDF();
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Cell(50, 100, utf8_decode('??Hola, Mundasdasddo!'));
        $filename = __DIR__ . utf8_decode($presupuesto->post_title) . '.pdf';
        $pdf->Output($filename, "F");

        $subject = esc_html__('Presupuesto', 'ccontrol');
        /*
        ob_start();
        require_once get_theme_file_path('/templates/email-wholesale.php');
        $body = ob_get_clean();
        $body = str_replace([
                '{fullname}',
                '{email}',
                '{company}',
                '{address}',
                '{products}',
                '{phone}',
                '{message}',
                '{logo}'
            ], [
                $info['contactName'],
                $info['contactEmail'],
                $info['contactCompany'],
                $info['contactAddress'],
                $info['products'],
                $info['contactPhone'],
                $info['contactMessage'],
                $logo
            ], $body);
    */


        $body = 'hola panas';
        $to = $cliente_correo;


        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . esc_html(get_bloginfo('name')) . ' <noreply@' . strtolower($_SERVER['SERVER_NAME']) . '>';
        $attachment = array($filename);
        $sent = wp_mail($to, $subject, $body, $headers, $attachment);

        unlink($filename);
        wp_send_json_success($sent, 200);
        wp_die();
    }

    /**
     * Method ccontrol_pdf_first_page
     *
     * @param $arr_data $arr_data [explicite description]
     * @param $pdf $pdf [explicite description]
     *
     * @return void
     */
    public function ccontrol_pdf_first_page($arr_data, $pdf)
    {
        $pdf->Image('http://localhost/proy-propios/wp_robert/wp-content/uploads/2022/10/logo-black.jpg', 90, 115, -150);
        $pdf->SetXY(0, 155);
        $pdf->SetFont('Helvetica', '', 32);
        $pdf->Cell(0, 0, utf8_decode('PRESUPUESTO WEB'), 0, 1, 'C');
        $pdf->SetXY(0, 170);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, $arr_data['current_date'], 0, 1, 'C');
    }

    /**
     * Method ccontrol_pdf_top_page
     *
     * @param $arr_data $arr_data [explicite description]
     * @param $pdf $pdf [explicite description]
     *
     * @return void
     */
    public function ccontrol_pdf_top_page($arr_data, $pdf)
    {
        $pdf->SetXY(0, 10);
        $pdf->Image('http://localhost/proy-propios/wp_robert/wp-content/uploads/2022/10/logo-black.jpg', 190, 5, -350);
        $pdf->SetXY(155, 11);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Cell(0, 0, utf8_decode('PRESUPUESTO WEB'), 0, 1, 'L');
    }

    /**
     * Method ccontrol_pdf_second_page
     *
     * @param $arr_data $arr_data [explicite description]
     * @param $pdf $pdf [explicite description]
     *
     * @return void
     */
    public function ccontrol_pdf_second_page($arr_data, $pdf)
    {
        $pdf->SetXY(10, 25);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, utf8_decode('Datos del Proyecto'), 0, 1, 'L');
        $pdf->SetXY(10, 40);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 0, utf8_decode('Nombre: ' . $arr_data['client']), 0, 1, 'L');
        $pdf->SetXY(10, 50);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 0, utf8_decode('Tipo de Proyecto: ' . $arr_data['title']), 0, 1, 'L');
        $pdf->SetXY(10, 60);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 0, utf8_decode('Fecha del Presupuesto: ' . date('d-m-Y')), 0, 1, 'L');

        $pdf->SetXY(10, 80);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, utf8_decode('Detalles del Proyecto'), 0, 1, 'L');
        $pdf->SetXY(10, 90);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->MultiCell(185, 9, utf8_decode($arr_data['desc']), 0, 'J', false);

        $pdf->SetDrawColor(255, 0, 0);
        $pdf->SetLineWidth(1);
        $pdf->Rect(10, 175, 185, 100, 'D');
        $pdf->SetXY(10, 110);

        $pdf->SetXY(20, 185);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, utf8_decode('Te ofrezco lo siguiente:'), 0, 1, 'L');
        $pdf->SetXY(20, 195);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->MultiCell(165, 4, utf8_decode($arr_data['offering']), 0, 'L', false);

        $pdf->SetXY(18, 235);
        $pdf->SetFont('Helvetica', 'B', 15);
        $pdf->MultiCell(165, 8, utf8_decode('Y por ??ltimo pero no menos importante: estoy entreg??ndote un sitio con un dise??o que se mantendr?? actualizado que tendr?? todas las cualidades necesarias para que tu marca / empresa tenga una grandiosa presencia en la Internet.'), 0, 'C', false);
    }

    /**
     * Method ccontrol_pdf_third_page
     *
     * @param $arr_data $arr_data [explicite description]
     * @param $pdf $pdf [explicite description]
     *
     * @return void
     */
    public function ccontrol_pdf_third_page($arr_data, $pdf)
    {
        $header = array('Descripci??n', 'Costo');
        $data = explode(PHP_EOL, $arr_data['elements']);

        if ($arr_data['currency'] == 'Dolares') {
            $text_currency = '(Valuado en D??lares)';
            $value = '$ ' . number_format($arr_data['price_usd'], 2, ',', '.');
        }
        if ($arr_data['currency'] == 'Bolivares') {
            $text_currency = '(Valuado en Bolivares)';
            $value = 'Bs ' . number_format($arr_data['price_bs'], 2, ',', '.');
        }
        if ($arr_data['currency'] == 'Ambos') {
            $text_currency = '(Valuado en Bol??vares / D??lares)';
            $value = 'Bs ' . number_format($arr_data['price_bs'], 2, ',', '.') . '/ $ ' . number_format($arr_data['price_usd'], 2, ',', '.');
        }

        $pdf->SetXY(10, 25);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, utf8_decode('Costo del Proyecto'), 0, 1, 'L');

        $pdf->SetXY(10, 35);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 0, utf8_decode($text_currency), 0, 1, 'L');

        $pdf->SetXY(10, 40);
        $pdf->SetFillColor(255, 0, 0);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(128, 0, 0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 12);
        // Header
        $w = array(150, 35);
        for($i=0;$i<count($header);$i++) {
            $pdf->Cell($w[$i], 10, utf8_decode($header[$i]), 1, 0, 'C', true);
        }
        $pdf->Ln();
        // Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Helvetica', '', 10);
        // Data
        $fill = false;
        foreach($data as $row) {
            $pdf->SetX(10);
            $pdf->Cell($w[0], 9, '  ' . utf8_decode($row), 'LR', 0, 'L', $fill);
            $pdf->Cell($w[1], 9, '', 'LR', 0, 'L', $fill);
            $pdf->Ln();
            $fill = !$fill;
        }
        $pdf->SetX(10);
        $pdf->Cell($w[0], 9, '', 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 9, '', 'LR', 0, 'L', $fill);
        $fill = !$fill;
        $pdf->Ln();
        $pdf->SetX(10);
        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->Cell($w[0], 9, '  ' . utf8_decode('Total'), 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 9, utf8_decode($value), 'LR', 0, 'C', $fill);
        $pdf->Ln();
        // Closing line
        $pdf->SetX(10);
        $pdf->Cell(array_sum($w), 0, '', 'T');
    }

    public function ccontrol_pdf_fourth_page($arr_data, $pdf)
    {

        $data = 'El tiempo de entrega para el proyecto es de {estimate}, y comienzan a contar desde la entrega de los accesos necesarios para llevar a cabo la propuesta, (en caso de tenerlos, hosting, ftp, entre otros) y dem??s informaci??n relevante.
La informaci??n y los accesos deben ser enviados con la confirmaci??n de la cancelaci??n del 50% inicial y la firma de este documento en se??al de aceptaci??n de las condiciones.
El pago (si es en Bol??vares) se har?? en dos (2) partes: 50% adelantado, con la entrega firmada de este documento en se??al de aceptaci??n formal de la propuesta y las condiciones que en ??l se establecen. El 50% restante se cancelara al momento de la entrega final.
El Pago (si es en d??lares) se har?? al finalizar el proyecto. El cliente asumir?? la comisi??n de PayPal.
Una vez cancelada la segunda parte, se har?? entrega formal en un documento de todos los accesos, usuarios, claves y contrase??as que se hayan generado durante el proyecto.
El Cliente asumir?? cualquier responsabilidad en cuanto a los retrasos generados para la aprobaci??n de artes, Wireframes o cambios en la programaci??n y dem??s estructuras que requieran de su revisi??n.
Si el cliente declina a medio trabajo de continuar la relaci??n de trabajo y ha tomado la opci??n de pago en Bol??vares, el pago por haber iniciado el trabajo no ser?? devuelto, se tomar?? como parte del trabajo que ya empez?? a realizarse.
Si el cliente declina a medio trabajo de continuar la relaci??n de trabajo y hab??a decidido tomar la opci??n de pago en d??lares, ser?? sujeto a penalizaci??n y deber?? pagar el 25% de lo acordado v??a PayPal por el trabajo que ya empez?? a realizarse.
Si el cliente declina de continuar la relaci??n de trabajo antes de la fecha acordada, el contenido desarrollado y el c??digo ser?? removido del servidor de prueba y no podr?? ser usada la interfaz que se ha desarrollado.
El proyecto estar?? considerado a ser expuesto en la p??gina de Robert Ochoa, como parte de su portafolio y casos de ??xito (teniendo en cuenta la data sensible que el cliente pueda tener en su p??gina web).
El c??digo del proyecto estar?? considerado a ser expuesto en los perfiles de trabajo de Robert Ochoa (enti??ndase perfiles de trabajo como Github / Linkedin / Behance y otros sitios de resentaci??n de trabajos), los cuales el proyecto aplique.';

        $data = str_replace([
            '{estimate}'
        ], [
            $arr_data['estimate']
        ], $data);
        
        $pdf->SetXY(10, 25);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, utf8_decode('Condiciones del Proyecto'), 0, 1, 'L');

        $pdf->SetXY(10, 40);
        $pdf->SetFont('Helvetica', '', 11);
        $pdf->MultiCell(185, 8, utf8_decode($data), 0, 'J', false);
    }

    public function ccontrol_create_pdf_callback()
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }


        if (isset($_GET['postid'])) {
            $postid = $_GET['postid'];
            $presupuesto = get_post($postid);
        } else {
            $presupuesto = 'presupuesto';
        }

        $client_id = get_post_meta($postid, 'cliente_presupuesto', true);
        $cliente = get_post($client_id);

        $arr_data = array(
            'logo' => 'http://localhost/proy-propios/wp_robert/wp-content/uploads/2022/10/logo-1.png',
            'title' => $presupuesto->post_title,
            'desc' => $presupuesto->post_content,
            'client' => $cliente->post_title,
            'currency' => get_post_meta($postid, 'moneda_presupuesto', true),
            'offering' => get_post_meta($postid, 'elem_ofrecer_presupuesto', true),
            'elements' => get_post_meta($postid, 'elem_items_presupuesto', true),
            'price_bs' => get_post_meta($postid, 'precio_bs', true),
            'price_usd' => get_post_meta($postid, 'precio_usd', true),
            'estimate' => get_post_meta($postid, 'tiempo_presupuesto', true),
            'current_date' => date('F Y')
        );

        require_once __DIR__ . '/../vendor/autoload.php';

        $pdf = new tFPDF();
        $pdf->AddPage();
        $pdf->SetMargins(3, 2.5);
        // First Page
        $this->ccontrol_pdf_first_page($arr_data, $pdf);
        // Second Page
        $pdf->AddPage();
        $pdf->SetMargins(3, 2.5);
        $this->ccontrol_pdf_top_page($arr_data, $pdf);
        $this->ccontrol_pdf_second_page($arr_data, $pdf);
        // Third Page
        $pdf->AddPage();
        $pdf->SetMargins(3, 2.5);
        $this->ccontrol_pdf_top_page($arr_data, $pdf);
        $this->ccontrol_pdf_third_page($arr_data, $pdf);
        // Fourth Page
        $pdf->AddPage();
        $pdf->SetMargins(3, 2.5);
        $this->ccontrol_pdf_top_page($arr_data, $pdf);
        $this->ccontrol_pdf_fourth_page($arr_data, $pdf);
        $pdf->Output('I', utf8_decode($presupuesto->post_title) . '.pdf');

        wp_die();
    }

    public function cc_presupuestos_print_metabox($post)
    {
        ?>
<div class="button-text">
    <p><?php _e('Haz click aqu?? para imprimir el presupuesto en formato PDF', 'ccontrol'); ?></p>
</div>
<a id="printQuote" data-id="<?php echo $post->ID; ?>" class="button button-primary button-large cc-btn-100"><?php _e('Imprimir Presupuesto', 'ccontrol'); ?></a>
<div class="button-text">
    <p><?php _e('Haz click aqu?? para enviar v??a correo electr??nico el presupuesto directamente al cliente', 'ccontrol'); ?></p>
</div>
<a id="sendQuote" data-id="<?php echo $post->ID; ?>" class="button button-primary button-large cc-btn-100"><?php _e('Enviar Presupuesto', 'ccontrol'); ?></a>
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
            <?php _e('Correo Electr??nico', 'ccontrol'); ?>
        </label>
        <input type="email" id="correo_cliente" name="correo_cliente" value="<?php echo esc_attr($value); ?>" size="40" />
    </div>

    <div class="postmeta-item-wrapper cc-col-2">
        <?php $value = get_post_meta($post->ID, 'telf_cliente', true); ?>
        <label for="telf_cliente">
            <?php _e('Tel??fono', 'ccontrol'); ?>
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
            <?php _e('Tiempo de Ejecuci??n', 'ccontrol'); ?>
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

        $arr_kses = array( 'br' => array(), 'p' => array(), 'strong' => array() );

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
