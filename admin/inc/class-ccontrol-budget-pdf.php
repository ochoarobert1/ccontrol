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
class Ccontrol_Admin_Budget_PDF
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function ccontrol_create_pdf_send_callback()
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        if (isset($_POST['postid'])) {
            $postid = $_POST['postid'];
            $presupuesto = get_post($postid);
        } else {
            $presupuesto = 'presupuesto';
        }

        $client_id = get_post_meta($postid, 'cliente_presupuesto', true);
        $cliente_correo = get_post_meta($client_id, 'correo_cliente', true);
        $cliente = get_post($client_id);

        $arr_data = array(
            'logo' => 'https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg',
            'title' => $presupuesto->post_title,
            'desc' => $presupuesto->post_content,
            'client' => $cliente->post_title,
            'currency' => get_post_meta($postid, 'moneda_presupuesto', true),
            'offering' => get_post_meta($postid, 'elem_ofrecer_presupuesto', true),
            'elements' => get_post_meta($postid, 'elem_items_presupuesto', true),
            'price_bs' => get_post_meta($postid, 'precio_bs', true),
            'price_usd' => get_post_meta($postid, 'precio_usd', true),
            'estimate' => get_post_meta($postid, 'tiempo_presupuesto', true),
            'current_date' => $meses[date('n') - 1] . ' ' . date('Y')
        );

        $wp_upload_dir = wp_upload_dir();
        $pdfdoc = self::cc_create_pdf_sequence($presupuesto, $arr_data, 'F');
        $uploadedfile = trailingslashit($wp_upload_dir['path']) . sanitize_title(utf8_decode($presupuesto->post_title)) . '.pdf';

        $attachment = array(
            $uploadedfile
        );

        $subject = utf8_decode($presupuesto->post_title);
        ob_start();
        require_once plugin_dir_path(__FILE__) . 'partials/ccontrol-email-budget.php';
        $body = ob_get_clean();

        //$to = $cliente_correo;
        //$to = 'test@mailhog.local';
        $to = 'ochoa.robert1@gmail.com';

        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . esc_html(get_bloginfo('name')) . ' <noreply@' . strtolower($_SERVER['SERVER_NAME']) . '>';
        $headers[] = 'Reply-To: Robert Ochoa <ochoa.robert1@gmail.com>';
        $sent = wp_mail($to, $subject, $body, $headers, $attachment);

        var_dump($sent);

        wp_send_json_success($sent, 200);
        wp_die();
    }

    public function ccontrol_pdf_first_page($arr_data, $pdf)
    {
        $pdf->Image('https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg', 90, 115, -150);
        $pdf->SetXY(0, 155);
        $pdf->SetFont('Helvetica', '', 32);
        $pdf->Cell(0, 0, utf8_decode('PRESUPUESTO WEB'), 0, 1, 'C');
        $pdf->SetXY(0, 170);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 0, $arr_data['current_date'], 0, 1, 'C');
    }

    public function ccontrol_pdf_top_page($arr_data, $pdf)
    {
        $pdf->SetXY(0, 10);
        $pdf->Image('https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg', 190, 5, -350);
        $pdf->SetXY(155, 11);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Cell(0, 0, utf8_decode('PRESUPUESTO WEB'), 0, 1, 'L');
    }

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
        $pdf->MultiCell(165, 8, utf8_decode('Y por último pero no menos importante: estoy entregándote un sitio con un diseño que se mantendrá actualizado que tendrá todas las cualidades necesarias para que tu marca / empresa tenga una grandiosa presencia en la Internet.'), 0, 'C', false);
    }

    public function ccontrol_pdf_third_page($arr_data, $pdf)
    {
        $header = array('Descripción', 'Costo');
        $data = explode(PHP_EOL, $arr_data['elements']);

        if ($arr_data['currency'] == 'Dolares') {
            $text_currency = '(Valuado en Dólares)';
            $value = '$ ' . number_format($arr_data['price_usd'], 2, ',', '.');
        }
        if ($arr_data['currency'] == 'Bolivares') {
            $text_currency = '(Valuado en Bolivares)';
            $value = 'Bs ' . number_format($arr_data['price_bs'], 2, ',', '.');
        }
        if ($arr_data['currency'] == 'Ambos') {
            $text_currency = '(Valuado en Bolívares / Dólares)';
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
        for ($i = 0; $i < count($header); $i++) {
            $pdf->Cell($w[$i], 10, utf8_decode($header[$i]), 1, 0, 'C', true);
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
        $pdf->Cell(0, 0, utf8_decode('Condiciones del Proyecto'), 0, 1, 'L');

        $pdf->SetXY(10, 40);
        $pdf->SetFont('Helvetica', '', 11);
        $pdf->MultiCell(185, 8, utf8_decode($data), 0, 'J', false);
    }

    public function ccontrol_create_pdf_callback()
    {
        /*
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        */

        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        if (isset($_GET['postid'])) {
            $postid = $_GET['postid'];
            $presupuesto = get_post($postid);
        } else {
            $presupuesto = 'presupuesto';
        }

        $client_id = get_post_meta($postid, 'cliente_presupuesto', true);
        $cliente = get_post($client_id);

        $arr_data = array(
            'logo' => 'https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg',
            'title' => $presupuesto->post_title,
            'desc' => $presupuesto->post_content,
            'client' => $cliente->post_title,
            'currency' => get_post_meta($postid, 'moneda_presupuesto', true),
            'offering' => get_post_meta($postid, 'elem_ofrecer_presupuesto', true),
            'elements' => get_post_meta($postid, 'elem_items_presupuesto', true),
            'price_bs' => get_post_meta($postid, 'precio_bs', true),
            'price_usd' => get_post_meta($postid, 'precio_usd', true),
            'estimate' => get_post_meta($postid, 'tiempo_presupuesto', true),
            'current_date' => $meses[date('n') - 1] . ' ' . date('Y')
        );

        self::cc_create_pdf_sequence($presupuesto, $arr_data, 'I');

        wp_die();
    }

    public function cc_create_pdf_sequence($presupuesto, $arr_data, $output = 'I')
    {
        require_once __DIR__ . '/../../vendor/autoload.php';

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
            $pdf->Output($output, utf8_decode($presupuesto->post_title) . '.pdf');
        } else {
            $wp_upload_dir = wp_upload_dir();
            $pdf->Output($output, $uploadedfile = trailingslashit($wp_upload_dir['path']) . sanitize_title(utf8_decode($presupuesto->post_title)) . '.pdf');
        }
    }
}