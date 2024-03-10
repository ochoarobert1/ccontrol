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

class Ccontrol_Admin_Invoice_PDF
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function invoice_header($arr_data, $pdf)
    {
        $pdf->SetXY(0, 10);
        $pdf->Image('https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg', 178, 10, -200);
        $pdf->SetXY(10, 14);
        $pdf->SetFont('Helvetica', '', 16);
        $pdf->Cell(15, 0, utf8_decode(get_option('ccontrol_name')), 0, 1, 'L');

        $pdf->SetXY(10, 24);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->MultiCell(70, 8, utf8_decode(get_option('ccontrol_address')), 0, 'L', false);

        // Line
        $pdf->SetDrawColor(223, 2, 9);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, 55, 200, 55);

        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);
    }

    public function invoice_table($arr_data, $pdf)
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

    public function invoice_terms($arr_data, $pdf)
    {
        $pdf->SetXY(0, 10);
        $pdf->Image('https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg', 178, 10, -200);
        $pdf->SetXY(10, 14);
        $pdf->SetFont('Helvetica', '', 16);
        $pdf->Cell(15, 0, utf8_decode('Robert Ochoa'), 0, 1, 'L');

        $pdf->SetXY(10, 24);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->MultiCell(70, 8, utf8_decode('Parque Urbanizacion Colinas de Carrizal, Av El Lago, Municipio Carrizal 1203, Miranda'), 0, 'L', false);

        // Line
        $pdf->SetDrawColor(223, 2, 9);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, 55, 200, 55);

        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);
    }

    public function cc_create_pdf_sequence($invoice, $arr_data, $output = 'I')
    {
        require_once __DIR__ . '/../../vendor/autoload.php';

        $pdf = new tFPDF();
        $pdf->AddPage();
        $pdf->SetMargins(3, 2.5);

        $this->invoice_header($arr_data, $pdf);
        $this->invoice_table($arr_data, $pdf);
        $this->invoice_terms($arr_data, $pdf);

        if ($output === 'I') {
            $pdf->Output($output, utf8_decode($invoice->post_title) . '.pdf');
        } else {
            $wp_upload_dir = wp_upload_dir();
            $pdf->Output($output, $uploadedfile = trailingslashit($wp_upload_dir['path']) . sanitize_title(utf8_decode($invoice->post_title)) . '.pdf');
        }
    }

    public function ccontrol_create_pdf_callback()
    {
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        if (isset($_GET['postid'])) {
            $postid = $_GET['postid'];
            $invoice = get_post($postid);
        } else {
            $invoice = 'Invoice';
        }

        $client_id = get_post_meta($postid, 'cliente_invoice', true);
        $cliente = get_post($client_id);

        $arr_data = array(
            'logo' => 'https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg',
            'name' => $invoice->post_title,
            'dir' => $invoice->post_content,
            'client' => $cliente->post_title,
            'currency' => get_post_meta($postid, 'moneda_invoice', true),

            'elements' => get_post_meta($postid, 'elem_items_invoice', true),
            'price_bs' => get_post_meta($postid, 'precio_bs', true),
            'price_usd' => get_post_meta($postid, 'precio_usd', true),
            'current_date' => $meses[date('n') - 1] . ' ' . date('Y')
        );

        self::cc_create_pdf_sequence($invoice, $arr_data, 'I');

        wp_die();
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
            $invoice = get_post($postid);
        } else {
            $invoice = 'invoice';
        }

        $client_id = get_post_meta($postid, 'cliente_invoice', true);
        $cliente_correo = get_post_meta($client_id, 'correo_cliente', true);
        $cliente = get_post($client_id);

        $arr_data = array(
            'logo' => 'https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg',
            'title' => $invoice->post_title,
            'desc' => $invoice->post_content,
            'client' => $cliente->post_title,
            'currency' => get_post_meta($postid, 'moneda_invoice', true),
            'offering' => get_post_meta($postid, 'elem_ofrecer_invoice', true),
            'elements' => get_post_meta($postid, 'elem_items_invoice', true),
            'price_bs' => get_post_meta($postid, 'precio_bs', true),
            'price_usd' => get_post_meta($postid, 'precio_usd', true),
            'estimate' => get_post_meta($postid, 'tiempo_invoice', true),
            'current_date' => $meses[date('n') - 1] . ' ' . date('Y')
        );

        $wp_upload_dir = wp_upload_dir();
        $pdfdoc = self::cc_create_pdf_sequence($invoice, $arr_data, 'F');
        $uploadedfile = trailingslashit($wp_upload_dir['path']) . sanitize_title(utf8_decode($invoice->post_title)) . '.pdf';

        $attachment = array(
            $uploadedfile
        );

        $subject = utf8_decode($invoice->post_title);
        ob_start();
        if (defined('CCONTROL_PLUGIN_DIR')) {
            $plugin_dir_path = CCONTROL_PLUGIN_DIR;
        }
        require_once $plugin_dir_path . 'partials/ccontrol-email-budget.php';
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
}
