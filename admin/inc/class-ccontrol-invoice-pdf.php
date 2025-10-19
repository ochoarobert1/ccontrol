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

use function Ramsey\Uuid\v1;

if (!defined('WPINC')) {
    die;
}

class Ccontrol_Admin_Invoice_PDF
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
     * Method invoice_header
     *
     * @param array $arr_data [Current Data Array]
     * @param object $pdf [PDF Object Constructor]
     *
     * @return void
     */
    public function invoice_header($arr_data, $pdf)
    {
        $pdf->SetXY(-1, 10);
        $pdf->Image('https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg', 10, 10, -170);
        $pdf->SetXY(10, 14);
        $pdf->SetFont('Helvetica', '', 32);
        $pdf->Cell(190, 0, utf8_decode('INVOICE'), 0, 1, 'R');

        $pdf->SetXY(10, 25);
        $pdf->SetFont('Helvetica', '', 13);
        $pdf->Cell(190, 0, utf8_decode('# ' . $arr_data['number_invoice']), 0, 1, 'R');


        $pdf->SetXY(120, 40);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->SetLineWidth(.1);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(40, 5, utf8_decode('Date:'), 1, 0, 'R', true);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(40, 5, utf8_decode(date('M D, Y')), 1, 0, 'L', true);
        $pdf->Ln();
        $pdf->SetXY(120, 45);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(40, 5, utf8_decode('Payment Method:'), 1, 0, 'R', true);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(40, 5, utf8_decode($arr_data['currency_name']), 1, 0, 'L', true);
        $pdf->Ln();
        $pdf->SetXY(120, 50);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(40, 5, utf8_decode('Due Date:'), 1, 0, 'R', true);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(40, 5, utf8_decode($arr_data['due_date']), 1, 0, 'L', true);
        $pdf->Ln();


        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetXY(10, 40);
        $pdf->Cell(90, 0, utf8_decode(get_option('ccontrol_name')), 0, 1, 'L');
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetXY(10, 45);
        $pdf->Cell(90, 0, utf8_decode('Full Stack Developer'), 0, 1, 'L');

        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetXY(10, 60);
        $pdf->Cell(90, 0, utf8_decode('Bill to:'), 0, 1, 'L');
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetXY(10, 65);
        $pdf->Cell(90, 0, utf8_decode($arr_data['client_name']), 0, 1, 'L');
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetXY(10, 70);
        $pdf->Cell(90, 0, utf8_decode($arr_data['client_address']), 0, 1, 'L');

        /*
        $pdf->SetXY(10, 24);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->MultiCell(150, 8, utf8_decode(get_option('ccontrol_address')), 0, 'L', false);
        */
    }

    /**
     * Method invoice_table
     *
     * @param array $arr_data [Current Data Array]
     * @param object $pdf [PDF Object Constructor]
     *
     * @return void
     */
    public function invoice_table($arr_data, $pdf)
    {
        $header = array('  Description', 'Qty', 'Total');
        $data = $arr_data['elements'];
        $price = 0;

        $pdf->SetXY(10, 80);
        $pdf->SetFillColor(30, 30, 30);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(30, 30, 30);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 12);
        // Header
        $w = array(120, 35, 35);
        for ($i = 0; $i < count($header); $i++) {
            $alignment = ($i == 0) ? 'L' : 'C';
            $pdf->Cell($w[$i], 10, utf8_decode($header[$i]), 1, 0, $alignment, true);
        }
        $pdf->Ln();
        // Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Helvetica', '', 10);
        // Data
        $fill = false;
        foreach ($data as $item) {
            $price = $price + ($item['item_factura_qty'] * $item['item_factura_price']);
            $pdf->SetX(10);
            $pdf->Cell($w[0], 9, '  ' . utf8_decode($item['item_factura_name']), 'LR', 0, 'L', $fill);
            $pdf->Cell($w[1], 9, '  ' . utf8_decode($item['item_factura_qty']), 'LR', 0, 'C', $fill);
            $pdf->Cell($w[2], 9, '  ' . utf8_decode($arr_data['currency'] . number_format($item['item_factura_price'], 2, ',', '.')), 'LR', 0, 'C', $fill);
            $pdf->Ln();
            $fill = !$fill;
        }
        $pdf->SetX(10);
        $pdf->Cell($w[0], 9, '', 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 9, '', 'LR', 0, 'L', $fill);
        $pdf->Cell($w[2], 9, '', 'LR', 0, 'L', $fill);
        $fill = !$fill;
        $pdf->Ln();
        $pdf->SetX(10);
        $pdf->SetFont('Helvetica', 'B', 13);
        $pdf->Cell($w[0], 9, '  ' . utf8_decode('Total'), 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 9, '', 'LR', 0, 'L', $fill);
        $pdf->Cell($w[2], 9, utf8_decode($arr_data['currency'] . number_format($price, 2, ',', '.')), 'LR', 0, 'C', $fill);
        $pdf->Ln();
        // Closing line
        $pdf->SetX(10);
        $pdf->Cell(array_sum($w), 0, '', 'T');
    }

    /**
     * Method invoice_terms
     *
     * @param array $arr_data [Current Data Array]
     * @param object $pdf [PDF Object Constructor]
     *
     * @return void
     */
    public function invoice_terms($arr_data, $pdf)
    {
        $pdf->SetXY(10, 170);
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(90, 0, utf8_decode('Notes:'), 0, 1, 'L');

        $pdf->SetXY(10, 178);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->MultiCell(150, 4, utf8_decode($arr_data['terms_conditions']), 0, 'L', false);

        $pdf->SetXY(10, 186);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->MultiCell(150, 4, utf8_decode($arr_data['payment_instructions']), 0, 'L', false);
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
            $pdf->Output($output, utf8_decode($arr_data['number_invoice'] . ' ' . $arr_data['name']) . '.pdf');
        } else {
            $wp_upload_dir = wp_upload_dir();
            $pdf->Output($output, $uploadedfile = trailingslashit($wp_upload_dir['path']) . sanitize_title(utf8_decode($arr_data['number_invoice'] . ' ' . $arr_data['name'])) . '.pdf');
        }
    }

    /**
     * Method ccontrol_create_pdf_callback
     *
     * @return void
     */
    public function ccontrol_create_pdf_callback()
    {
        $meses = [
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

        if (isset($_GET['postid'])) {
            $postid = $_GET['postid'];
            $invoice = get_post($postid);
        } else {
            $invoice = 'Invoice';
        }

        // GET CLIENT INFO
        $client_id = get_post_meta($postid, 'cliente_factura', true);
        $client_post = get_post($client_id);
        $client_address = get_post_meta($client_id, 'direccion_cliente', true);

        $qty_invoices = wp_count_posts('cc_invoices');
        $fixed_number_invoice = get_post_meta($postid, 'numero_factura', true);
        $numero_factura = ($fixed_number_invoice !== '') ? $fixed_number_invoice : (int) $qty_invoices + 1;

        $plataforma_pago = get_post_meta($postid, 'plataforma_pago', true);
        switch ($plataforma_pago) {
            case 'usd':
                $currency = '$ ';
                $currency_name = 'Bank Transfer';
                $payment_instructions = get_option('ccontrol_invoice_accounts_usa');
                break;
            case 'bs':
                $currency = 'BS. ';
                $currency_name = 'Transferencia Bancaria';
                $payment_instructions = get_option('ccontrol_invoice_accounts_venezuela');
                break;
            default:
                $currency = '$ ';
                $currency_name = 'PayPal';
                $payment_instructions = get_option('ccontrol_invoice_accounts_paypal');
                break;
        };

        $fixed_terms_conditions = get_option('ccontrol_invoice_conditions');
        $terms_conditions = $fixed_number_invoice !== '' ? $fixed_terms_conditions : get_post_meta($postid, 'terminos_condiciones', true);

        $arr_data = array(
            'logo' => 'https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg',
            'number_invoice' => $numero_factura,
            'name' => $invoice->post_title . ' - ' . $client_post->post_title,
            'dir' => $invoice->post_content,
            'client_name' => $client_post->post_title,
            'client_address' => $client_address,
            'currency' => $currency,
            'currency_name' => $currency_name,
            'elements' => get_post_meta($postid, 'items_factura', true),
            'price_bs' => get_post_meta($postid, 'precio_bs', true),
            'price_usd' => get_post_meta($postid, 'precio_usd', true),
            'due_date' => get_post_meta($postid, 'fecha_factura', true),
            'payment_instructions' => $payment_instructions,
            'terms_conditions' => $terms_conditions,
            'current_date' => $meses[date('n') - 1] . ' ' . date('Y')
        );

        self::cc_create_pdf_sequence($invoice, $arr_data, 'I');

        wp_die();
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

        $meses = [
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

        if (isset($_POST['postid'])) {
            $postid = $_POST['postid'];
            $invoice = get_post($postid);
        } else {
            $invoice = 'invoice';
        }

        $client_id = get_post_meta($postid, 'cliente_invoice', true);
        $cliente_correo = get_post_meta($client_id, 'correo_cliente', true);
        $cliente = get_post($client_id);

        $arr_data = [
            'logo' => 'https://robertochoaweb.com/wp-content/uploads/2022/10/logo-black.jpg',
            'title' => $invoice->post_title . ' - ' . $cliente->post_title,
            'desc' => $invoice->post_content,
            'client' => $cliente->post_title,
            'currency' => get_post_meta($postid, 'moneda_invoice', true),
            'offering' => get_post_meta($postid, 'elem_ofrecer_invoice', true),
            'elements' => get_post_meta($postid, 'elem_items_invoice', true),
            'price_bs' => get_post_meta($postid, 'precio_bs', true),
            'price_usd' => get_post_meta($postid, 'precio_usd', true),
            'estimate' => get_post_meta($postid, 'tiempo_invoice', true),
            'current_date' => $meses[gmdate('n') - 1] . ' ' . gmdate('Y')
        ];

        $wp_upload_dir = wp_upload_dir();
        $pdfdoc = self::cc_create_pdf_sequence($invoice, $arr_data, 'F');
        $uploadedfile = trailingslashit($wp_upload_dir['path']) . sanitize_title(mb_convert_encoding($invoice->post_title, 'ISO-8859-1', 'UTF-8')) . '.pdf';

        $attachment = [
            $uploadedfile
        ];

        $subject = mb_convert_encoding($invoice->post_title, 'ISO-8859-1', 'UTF-8');
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
        $headers[] = 'From: ' . esc_html(get_bloginfo('name')) . ' <noreply@' . strtolower(isset($_SERVER['SERVER_NAME']) ?? $_SERVER['SERVER_NAME']) . '>';
        $headers[] = 'Reply-To: Robert Ochoa <ochoa.robert1@gmail.com>';
        $sent = wp_mail($to, $subject, $body, $headers, $attachment);

        wp_send_json_success($sent, 200);
        wp_die();
    }
}
