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

class Ccontrol_Admin
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
     * Method enqueue_styles
     *
     * @return void
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/ccontrol-admin.css',
            [],
            $this->version,
            'all'
        );
    }

    /**
     * Method enqueue_scripts
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/ccontrol-admin.js',
            ['jquery'],
            $this->version,
            false
        );
        wp_localize_script(
            $this->plugin_name,
            'ccontrol_admin_object',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ccontrol_nonce')
            ]
        );
        wp_enqueue_media();
    }

    /**
     * Method ccontrol_get_months_array
     *
     * @return array
     */
    public function ccontrol_get_months_array()
    {
        $months = [
            esc_attr__('Enero', 'ccontrol'),
            esc_attr__('Febrero', 'ccontrol'),
            esc_attr__('Marzo', 'ccontrol'),
            esc_attr__('Abril', 'ccontrol'),
            esc_attr__('Mayo', 'ccontrol'),
            esc_attr__('Junio', 'ccontrol'),
            esc_attr__('Julio', 'ccontrol'),
            esc_attr__('Agosto', 'ccontrol'),
            esc_attr__('Septiembre', 'ccontrol'),
            esc_attr__('Octubre', 'ccontrol'),
            esc_attr__('Noviembre', 'ccontrol'),
            esc_attr__('Diciembre', 'ccontrol')
        ];

        return $months;
    }

    /**
     * Method ccontrol_create_pdf_send_callback
     *
     * @return void
     */
    public function ccontrol_create_pdf_send_callback()
    {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        if (isset($_POST['postid'])) {
            $postid = $_POST['postid'];
            $quote = get_post($postid);
        } else {
            $quote = __('Presupuesto', 'ccontrol');
        }

        $ccontrol_mode = get_option('ccontrol_mode');
        $client_id = get_post_meta($postid, 'cliente_presupuesto', true);
        $client_email = get_post_meta($client_id, 'correo_cliente', true);
        $client = get_post($client_id);
        $months = self::ccontrol_get_months_array();
        $current_user = wp_get_current_user();
        $user_name = $current_user->display_name;
        $user_email = $current_user->user_email;

        $arr_data = [
            'logo' => get_option('ccontrol_logo'),
            'title' => $quote->post_title,
            'desc' => $quote->post_content,
            'client' => $client->post_title,
            'currency' => get_post_meta($postid, 'moneda_presupuesto', true),
            'offering' => get_post_meta($postid, 'elem_ofrecer_presupuesto', true),
            'elements' => get_post_meta($postid, 'elem_items_presupuesto', true),
            'price_bs' => get_post_meta($postid, 'precio_bs', true),
            'price_usd' => get_post_meta($postid, 'precio_usd', true),
            'estimate' => get_post_meta($postid, 'tiempo_presupuesto', true),
            'current_date' => $months[gmdate('n') - 1] . ' ' . gmdate('Y')
        ];

        $wp_upload_dir = wp_upload_dir();
        self::cc_create_pdf_sequence($quote, $arr_data, 'F');
        $uploadedfile = trailingslashit($wp_upload_dir['path']) . sanitize_title(mb_convert_encoding($quote->post_title, 'ISO-8859-1', 'UTF-8')) . '.pdf';

        $attachment = [
            $uploadedfile
        ];
        $subject = mb_convert_encoding($quote->post_title, 'ISO-8859-1', 'UTF-8');
        ob_start();
        require_once plugin_dir_path(__FILE__) . 'partials/ccontrol-email-budget.php';
        $body = ob_get_clean();

        if ($ccontrol_mode === 'prod') {
            $to = $client_email;
        } else {
            $to = get_option('ccontrol_dev_email');
        }

        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . esc_html(get_bloginfo('name')) . ' <noreply@' . strtolower(isset($_SERVER['SERVER_NAME']) ?? $_SERVER['SERVER_NAME']) . '>';
        $headers[] = 'Reply-To: ' . $user_name . ' <' . $user_email . '>';
        $sent = wp_mail($to, $subject, $body, $headers, $attachment);

        if ($sent == true) {
            wp_send_json_success(esc_html__('Correo enviado exitosamente', 'ccontrol'), 200);
        }

        wp_die();
    }

    /**
     * Method ccontrol_pdf_first_page
     *
     * @param array $arr_data [Array of Current Data]
     * @param object $pdf [PDF Object]
     *
     * @return void
     */
    public function ccontrol_pdf_first_page($arr_data, $pdf)
    {
        $pdf->Image(get_option('ccontrol_logo'), 90, 115, -150);
        $pdf->SetXY(0, 155);
        $pdf->SetFont('Helvetica', '', 32);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('PRESUPUESTO WEB', 'ccontrol'), 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $pdf->SetXY(0, 170);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, $arr_data['current_date'], 0, 1, 'C');
    }

    /**
     * Method ccontrol_pdf_top_page
     *
     * @param array $arr_data [Array of Current Data]
     * @param object $pdf [PDF Object]
     *
     * @return void
     */
    public function ccontrol_pdf_top_page($arr_data, $pdf)
    {
        $pdf->SetXY(0, 10);
        $pdf->Image(get_option('ccontrol_logo'), 190, 5, -350);
        $pdf->SetXY(155, 11);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('PRESUPUESTO WEB', 'ccontrol'), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
    }

    /**
     * Method ccontrol_pdf_second_page
     *
     * @param array $arr_data [Array of Current Data]
     * @param object $pdf [PDF Object]
     *
     * @return void
     */
    public function ccontrol_pdf_second_page($arr_data, $pdf)
    {
        $pdf->SetXY(10, 25);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('Datos del Proyecto', 'ccontrol'), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $pdf->SetXY(10, 40);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('Nombre: ', 'ccontrol') . $arr_data['client'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $pdf->SetXY(10, 50);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('Tipo de Proyecto: ', 'ccontrol') . $arr_data['title'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $pdf->SetXY(10, 60);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('Fecha del Presupuesto: ', 'ccontrol') . gmdate('d-m-Y'), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetXY(10, 80);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('Detalles del Proyecto', 'ccontrol'), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $pdf->SetXY(10, 90);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->MultiCell(185, 9, mb_convert_encoding($arr_data['desc'], 'ISO-8859-1', 'UTF-8'), 0, 'J', false);

        $pdf->SetDrawColor(255, 0, 0);
        $pdf->SetLineWidth(1);
        $pdf->Rect(10, 175, 185, 100, 'D');
        $pdf->SetXY(10, 110);

        $pdf->SetXY(20, 185);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('Te ofrezco lo siguiente:', 'ccontrol'), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $pdf->SetXY(20, 195);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->MultiCell(165, 4, mb_convert_encoding($arr_data['offering'], 'ISO-8859-1', 'UTF-8'), 0, 'L', false);

        $pdf->SetXY(18, 235);
        $pdf->SetFont('Helvetica', 'B', 15);
        $pdf->MultiCell(165, 8, mb_convert_encoding('Y por último pero no menos importante: estoy entregándote un sitio con un diseño que se mantendrá actualizado que tendrá todas las cualidades necesarias para que tu marca / empresa tenga una grandiosa presencia en la Internet.', 'ISO-8859-1', 'UTF-8'), 0, 'C', false);
    }

    /**
     * Method ccontrol_pdf_third_page
     *
     * @param array $arr_data [Array of Current Data]
     * @param object $pdf [PDF Object]
     *
     * @return void
     */
    public function ccontrol_pdf_third_page($arr_data, $pdf)
    {
        $header = [
            esc_html__('Descripción', 'ccontrol'),
            esc_html__('Costo', 'ccontrol')
        ];
        $data = explode(PHP_EOL, $arr_data['elements']);

        if ($arr_data['currency'] == 'Dolares') {
            $text_currency = esc_html__('(Valuado en Dólares)', 'ccontrol');
            $value = '$ ' . number_format($arr_data['price_usd'], 2, ',', '.');
        }
        if ($arr_data['currency'] == 'Bolivares') {
            $text_currency = esc_html__('(Valuado en Bolivares)', 'ccontrol');
            $value = 'Bs ' . number_format($arr_data['price_bs'], 2, ',', '.');
        }
        if ($arr_data['currency'] == 'Ambos') {
            $text_currency = esc_html__('(Valuado en Bolívares / Dólares)', 'ccontrol');
            $value = 'Bs ' . number_format($arr_data['price_bs'], 2, ',', '.') . '/ $ ' . number_format($arr_data['price_usd'], 2, ',', '.');
        }

        $pdf->SetXY(10, 25);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('Costo del Proyecto', 'ccontrol'), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetXY(10, 35);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 0, mb_convert_encoding($text_currency, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetXY(10, 40);
        $pdf->SetFillColor(255, 0, 0);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(128, 0, 0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 12);
        // Header
        $w = [150, 35];
        for ($i = 0; $i < count($header); $i++) {
            $pdf->Cell($w[$i], 10, mb_convert_encoding($header[$i], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        }
        $pdf->Ln();
        // Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Helvetica', '', 10);
        // Data
        $fill = false;
        foreach ($data as $row) {
            $pdf->SetX(10);
            $pdf->Cell($w[0], 9, '  ' . mb_convert_encoding($row, 'ISO-8859-1', 'UTF-8'), 'LR', 0, 'L', $fill);
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
        $pdf->Cell($w[0], 9, '  ' . mb_convert_encoding('Total', 'ISO-8859-1', 'UTF-8'), 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 9, mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8'), 'LR', 0, 'C', $fill);
        $pdf->Ln();
        // Closing line
        $pdf->SetX(10);
        $pdf->Cell(array_sum($w), 0, '', 'T');
    }

    /**
     * Method ccontrol_pdf_fourth_page
     *
     * @param array $arr_data [Array of Current Data]
     * @param object $pdf [PDF Object]
     *
     * @return void
     */
    public function ccontrol_pdf_fourth_page($arr_data, $pdf)
    {

        $data = 'El tiempo de entrega para el proyecto es de {estimate}, y comienzan a contar desde la entrega de los accesos necesarios para llevar a cabo la propuesta, (en caso de tenerlos, hosting, ftp, entre otros) y demás información relevante.
La información y los accesos deben ser enviados con la confirmación de la cancelación del 50% inicial y la firma de este documento en señal de aceptación de las condiciones.
El pago (si es en Bolívares) se hará en dos (2) partes: 50% adelantado, con la entrega firmada de este documento en señal de aceptación formal de la propuesta y las condiciones que en él se establecen. El 50% restante se cancelara al momento de la entrega final.
El Pago (si es en dólares) se hará al finalizar el proyecto. El cliente asumirá la comisión de PayPal.
Una vez cancelada la segunda parte, se hará entrega formal en un documento de todos los accesos, usuarios, claves y contraseñas que se hayan generado durante el proyecto.
El Cliente asumirá cualquier responsabilidad en cuanto a los retrasos generados para la aprobación de artes, Wireframes o cambios en la programación y demás estructuras que requieran de su revisión.
Si el cliente declina a medio trabajo de continuar la relación de trabajo y ha tomado la opción de pago en Bolívares, el pago por haber iniciado el trabajo no será devuelto, se tomará como parte del trabajo que ya empezó a realizarse.
Si el cliente declina a medio trabajo de continuar la relación de trabajo y había decidido tomar la opción de pago en dólares, será sujeto a penalización y deberá pagar el 25% de lo acordado vía PayPal por el trabajo que ya empezó a realizarse.
Si el cliente declina de continuar la relación de trabajo antes de la fecha acordada, el contenido desarrollado y el código será removido del servidor de prueba y no podrá ser usada la interfaz que se ha desarrollado.
El proyecto estará considerado a ser expuesto en la página de Robert Ochoa, como parte de su portafolio y casos de éxito (teniendo en cuenta la data sensible que el cliente pueda tener en su página web).
El código del proyecto estará considerado a ser expuesto en los perfiles de trabajo de Robert Ochoa (entiéndase perfiles de trabajo como Github / Linkedin / Behance y otros sitios de resentación de trabajos), los cuales el proyecto aplique.';

        $data = str_replace([
            '{estimate}'
        ], [
            $arr_data['estimate']
        ], $data);

        $pdf->SetXY(10, 25);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, mb_convert_encoding(esc_html__('Condiciones del Proyecto', 'ccontrol'), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

        $pdf->SetXY(10, 40);
        $pdf->SetFont('Helvetica', '', 11);
        $pdf->MultiCell(185, 8, mb_convert_encoding($data, 'ISO-8859-1', 'UTF-8'), 0, 'J', false);
    }

    /**
     * Method ccontrol_create_pdf_callback
     *
     * @return void
     */
    public function ccontrol_create_pdf_callback()
    {
        if (isset($_POST['postid'])) {
            $postid = $_POST['postid'];
            $quote = get_post($postid);
        } else {
            $quote = esc_html__('Presupuesto', 'ccontrol');
        }

        $client_id = get_post_meta($postid, 'cliente_presupuesto', true);
        $client = get_post($client_id);
        $months = self::ccontrol_get_months_array();

        $arr_data = [
            'logo' => get_option('ccontrol_logo'),
            'title' => $quote->post_title,
            'desc' => $quote->post_content,
            'client' => $client->post_title,
            'currency' => get_post_meta($postid, 'moneda_presupuesto', true),
            'offering' => get_post_meta($postid, 'elem_ofrecer_presupuesto', true),
            'elements' => get_post_meta($postid, 'elem_items_presupuesto', true),
            'price_bs' => get_post_meta($postid, 'precio_bs', true),
            'price_usd' => get_post_meta($postid, 'precio_usd', true),
            'estimate' => get_post_meta($postid, 'tiempo_presupuesto', true),
            'current_date' => $months[gmdate('n') - 1] . ' ' . gmdate('Y')
        ];

        self::cc_create_pdf_sequence($quote, $arr_data, 'I');

        wp_die();
    }

    /**
     * Method cc_create_pdf_sequence
     *
     * @param object $quote [Current Quote]
     * @param array $arr_data [Current Quote data]
     * @param string $output [Signal to export]
     *
     * @return void
     */
    public function cc_create_pdf_sequence($quote, $arr_data, $output = 'I')
    {
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
        if ($output === 'I') {
            $pdf->Output($output, mb_convert_encoding($quote->post_title, 'ISO-8859-1', 'UTF-8') . '.pdf');
        } else {
            $wp_upload_dir = wp_upload_dir();
            $pdf->Output($output, $uploadedfile = trailingslashit($wp_upload_dir['path']) . sanitize_title(mb_convert_encoding($quote->post_title, 'ISO-8859-1', 'UTF-8')) . '.pdf');
        }
    }
}
