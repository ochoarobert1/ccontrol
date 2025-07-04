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

class Ccontrol_CPT
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function formatPhoneNumber($phoneNumber)
    {
        if (strpos($phoneNumber, '+1') === 0) {
            $countryCode = substr($phoneNumber, 0, 3);
        } else {
            $countryCode = substr($phoneNumber, 0, 2);
        }
        $areaCode = substr($phoneNumber, 2, 3);
        $number = substr($phoneNumber, 6);
        $formattedNumber = "+" . $countryCode . "-" . $areaCode . "-" . $number;
        return $formattedNumber;
    }

    public function ccontrol_clientes_cpt()
    {
        $labels = array(
            'name'                  => esc_attr_x('Clientes', 'Post Type General Name', 'ccontrol'),
            'singular_name'         => esc_attr_x('Cliente', 'Post Type Singular Name', 'ccontrol'),
            'menu_name'             => esc_attr__('Clientes', 'ccontrol'),
            'name_admin_bar'        => esc_attr__('Clientes', 'ccontrol'),
            'archives'              => esc_attr__('Archivo de Clientes', 'ccontrol'),
            'attributes'            => esc_attr__('Atributos de Cliente', 'ccontrol'),
            'parent_item_colon'     => esc_attr__('Cliente Padre:', 'ccontrol'),
            'all_items'             => esc_attr__('Clientes', 'ccontrol'),
            'add_new_item'          => esc_attr__('Agregar nuevo Cliente', 'ccontrol'),
            'add_new'               => esc_attr__('Agregar Nuevo', 'ccontrol'),
            'new_item'              => esc_attr__('Nuevo Cliente', 'ccontrol'),
            'edit_item'             => esc_attr__('Editar Cliente', 'ccontrol'),
            'update_item'           => esc_attr__('Actualizar Cliente', 'ccontrol'),
            'view_item'             => esc_attr__('Ver Cliente', 'ccontrol'),
            'view_items'            => esc_attr__('Ver Clientes', 'ccontrol'),
            'search_items'          => esc_attr__('Buscar Cliente', 'ccontrol'),
            'not_found'             => esc_attr__('No hay resultados', 'ccontrol'),
            'not_found_in_trash'    => esc_attr__('No hay resultados en Papelera', 'ccontrol'),
            'featured_image'        => esc_attr__('Logo del Cliente', 'ccontrol'),
            'set_featured_image'    => esc_attr__('Colocar Logo del Cliente', 'ccontrol'),
            'remove_featured_image' => esc_attr__('Remover Logo del Cliente', 'ccontrol'),
            'use_featured_image'    => esc_attr__('Usar como Logo del Cliente', 'ccontrol'),
            'insert_into_item'      => esc_attr__('Insertar en Cliente', 'ccontrol'),
            'uploaded_to_this_item' => esc_attr__('Cargado a este Cliente', 'ccontrol'),
            'items_list'            => esc_attr__('Listado de Clientes', 'ccontrol'),
            'items_list_navigation' => esc_attr__('Nav. del Listado de Clientes', 'ccontrol'),
            'filter_items_list'     => esc_attr__('Filtro del Listado de Clientes', 'ccontrol'),
        );
        $args = array(
            'label'                 => esc_attr__('Cliente', 'ccontrol'),
            'description'           => esc_attr__('Clientes', 'ccontrol'),
            'labels'                => $labels,
            'supports'              => array('title', 'thumbnail'),
            'taxonomies'            => array('tipo-cliente'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'ccontrol-dashboard',
            'menu_position'         => 19,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
        );
        register_post_type('cc_clientes', $args);
    }

    public function cc_clientes_custom_columns($columns)
    {
        unset($columns['date']);
        $columns['logo_cliente'] = esc_attr__('Logo', 'ccontrol');
        $columns['tipo_cliente'] = esc_attr__('Tipo de Cliente', 'ccontrol');
        $columns['nombre_cliente'] = esc_attr__('Contacto', 'ccontrol');
        $columns['correo_cliente'] = esc_attr__('Correo', 'ccontrol');
        $columns['telf_cliente'] = esc_attr__('Teléfono', 'ccontrol');
        $columns['date'] = esc_attr__('Date', 'wordpress');
        return $columns;
    }

    public function cc_clientes_promo_column_content($column_name, $post_id)
    {
        if ('logo_cliente' == $column_name) {
            $value = get_the_post_thumbnail_url($post_id, array(100, 100));
            if ($value != '') {
                echo wp_kses_post('<img src="' . $value . '" alt="Logo" class="cc_clientes_logo" />');
            } else {
                echo wp_kses_post('<img src="https://via.placeholder.com/70" alt="Logo" class="cc_clientes_logo" />');
            }
        }

        if ('tipo_cliente' == $column_name) {
            $value = get_post_meta($post_id, 'tipo_cliente', true);
            if ($value !== '') {
                echo esc_html($value);
            } else {
                esc_html_e('No hay tipo de cliente seleccionado', 'ccontrol');
            }

        }

        if ('nombre_cliente' == $column_name) {
            $value = get_post_meta($post_id, 'nombre_cliente', true);
            if ($value !== '') {
                echo esc_html($value);
            } else {
                esc_html_e('No hay nombre ingresado', 'ccontrol');
            }
        }

        if ('correo_cliente' == $column_name) {
            $value = get_post_meta($post_id, 'correo_cliente', true);
            if ($value !== '') {
                echo wp_kses_post('<a href="mailto:' . $value . '">' . $value . '</a>');
            } else {
                esc_html_e('No hay correo ingresado', 'ccontrol');
            }
        }

        if ('telf_cliente' == $column_name) {
            $value = get_post_meta($post_id, 'telf_cliente', true);
            $value = preg_replace('/\D/', '', $value);
            if ($value !== '') {
                echo wp_kses_post('<a href="tel:' . $value . '">' . $this->formatPhoneNumber($value) . '</a>');
            } else {
                esc_html_e('No hay telefono ingresado', 'ccontrol');
            }
        }
    }

    public function my_sortable_cc_clientes_column($columns)
    {
        $columns['tipo_cliente'] = esc_attr__('Tipo de Cliente', 'control');
        return $columns;
    }

    public function ccontrol_presupuestos_cpt()
    {
        $labels = array(
            'name'                  => esc_attr_x('Presupuestos', 'Post Type General Name', 'ccontrol'),
            'singular_name'         => esc_attr_x('Presupuesto', 'Post Type Singular Name', 'ccontrol'),
            'menu_name'             => esc_attr__('Presupuestos', 'ccontrol'),
            'name_admin_bar'        => esc_attr__('Presupuestos', 'ccontrol'),
            'archives'              => esc_attr__('Archivo de Presupuesto', 'ccontrol'),
            'attributes'            => esc_attr__('Atributos de Presupuesto', 'ccontrol'),
            'parent_item_colon'     => esc_attr__('Presupuesto Padre:', 'ccontrol'),
            'all_items'             => esc_attr__('Presupuestos', 'ccontrol'),
            'add_new_item'          => esc_attr__('Agregar nuevo Presupuesto', 'ccontrol'),
            'add_new'               => esc_attr__('Agregar Nuevo', 'ccontrol'),
            'new_item'              => esc_attr__('Nuevo Presupuesto', 'ccontrol'),
            'edit_item'             => esc_attr__('Editar Presupuesto', 'ccontrol'),
            'update_item'           => esc_attr__('Actualizar Presupuesto', 'ccontrol'),
            'view_item'             => esc_attr__('Ver Presupuesto', 'ccontrol'),
            'view_items'            => esc_attr__('Ver Presupuestos', 'ccontrol'),
            'search_items'          => esc_attr__('Buscar Presupuesto', 'ccontrol'),
            'not_found'             => esc_attr__('No hay resultados', 'ccontrol'),
            'not_found_in_trash'    => esc_attr__('No hay resultados en Papelera', 'ccontrol'),
            'insert_into_item'      => esc_attr__('Insertar en Presupuesto', 'ccontrol'),
            'uploaded_to_this_item' => esc_attr__('Cargado a este Presupuesto', 'ccontrol'),
            'items_list'            => esc_attr__('Listado de Presupuestos', 'ccontrol'),
            'items_list_navigation' => esc_attr__('Nav. del Listado de Presupuestos', 'ccontrol'),
            'filter_items_list'     => esc_attr__('Filtro del Listado de Presupuestos', 'ccontrol'),
        );
        $args = array(
            'label'                 => esc_attr__('Presupuesto', 'ccontrol'),
            'description'           => esc_attr__('Presupuestos', 'ccontrol'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'ccontrol-dashboard',
            'menu_position'         => 20,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
        );
        register_post_type('cc_presupuestos', $args);
    }

    public function cc_presupuestos_custom_columns($columns)
    {
        unset($columns['date']);
        $columns['client'] = esc_attr__('Cliente', 'ccontrol');
        $columns['estimation'] = esc_attr__('Tiempo de Estimación', 'ccontrol');
        $columns['price'] = esc_attr__('Precio', 'ccontrol');
        $columns['status'] = esc_attr__('Estatus', 'ccontrol');
        $columns['date'] = esc_attr__('Date', 'wordpress');
        return $columns;
    }

    public function cc_presupuestos_promo_column_content($column_name, $post_id)
    {
        if ('client' == $column_name) {
            $client_id = get_post_meta($post_id, 'cliente_presupuesto', true);
            $client = get_post($client_id);
            if ($client) {
                echo esc_html($client->post_title);
            } else {
                esc_html_e('No hay cliente seleccionado', 'ccontrol');
            }
        }

        if ('estimation' == $column_name) {
            $estimation = get_post_meta($post_id, 'tiempo_presupuesto', true);
            if ($estimation !== '') {
                echo esc_html($estimation);
            } else {
                esc_html_e('No hay tiempo de estimación seleccionado', 'ccontrol');
            }
        }

        if ('price' == $column_name) {
            $precio_bs = get_post_meta($post_id, 'precio_bs', true);
            $precio_usd = get_post_meta($post_id, 'precio_usd', true);
            if ($precio_bs !== '') {
                echo esc_html('Bs. ' . number_format($precio_bs, 2, ',', '.'));
            } elseif ($precio_usd !== '') {
                echo esc_html('$ ' . number_format($precio_usd, 2, ',', '.'));
            } else {
                esc_html_e('No hay precio seleccionado', 'ccontrol');
            }
        }

        if ('status' == $column_name) {
            $arr_status = [
                'sent' => esc_attr__('Enviado', 'ccontrol'),
                'accepted' => esc_attr__('Aceptado', 'ccontrol'),
                'rejected' => esc_attr__('Rechazado', 'ccontrol')
            ];
            $status = get_post_meta($post_id, 'status_presupuesto', true);
            if ($status !== '') {
                echo wp_kses_post('<span class="ccontrol-status ccontrol-status-' . $status . '">' . $arr_status[$status] . '</span>');
            } else {
                esc_html_e('No hay estatus seleccionado', 'ccontrol');
            }
        }
    }

    public function ccontrol_invoices_cpt()
    {
        $labels = array(
            'name'                  => esc_attr_x('Facturas', 'Post Type General Name', 'ccontrol'),
            'singular_name'         => esc_attr_x('Factura', 'Post Type Singular Name', 'ccontrol'),
            'menu_name'             => esc_attr__('Facturas', 'ccontrol'),
            'name_admin_bar'        => esc_attr__('Facturas', 'ccontrol'),
            'archives'              => esc_attr__('Archivo de Facturas', 'ccontrol'),
            'attributes'            => esc_attr__('Atributos de Factura', 'ccontrol'),
            'parent_item_colon'     => esc_attr__('Factura Padre:', 'ccontrol'),
            'all_items'             => esc_attr__('Facturas', 'ccontrol'),
            'add_new_item'          => esc_attr__('Agregar nuevo Factura', 'ccontrol'),
            'add_new'               => esc_attr__('Agregar Nuevo', 'ccontrol'),
            'new_item'              => esc_attr__('Nuevo Factura', 'ccontrol'),
            'edit_item'             => esc_attr__('Editar Factura', 'ccontrol'),
            'update_item'           => esc_attr__('Actualizar Factura', 'ccontrol'),
            'view_item'             => esc_attr__('Ver Factura', 'ccontrol'),
            'view_items'            => esc_attr__('Ver Facturas', 'ccontrol'),
            'search_items'          => esc_attr__('Buscar Factura', 'ccontrol'),
            'not_found'             => esc_attr__('No hay resultados', 'ccontrol'),
            'not_found_in_trash'    => esc_attr__('No hay resultados en Papelera', 'ccontrol'),
            'featured_image'        => esc_attr__('Logo de Factura', 'ccontrol'),
            'set_featured_image'    => esc_attr__('Colocar Logo de Factura', 'ccontrol'),
            'remove_featured_image' => esc_attr__('Remover Logo de Factura', 'ccontrol'),
            'use_featured_image'    => esc_attr__('Usar como Logo de Factura', 'ccontrol'),
            'insert_into_item'      => esc_attr__('Insertar en Factura', 'ccontrol'),
            'uploaded_to_this_item' => esc_attr__('Cargado a este Factura', 'ccontrol'),
            'items_list'            => esc_attr__('Listado de Facturas', 'ccontrol'),
            'items_list_navigation' => esc_attr__('Nav. del Listado de Facturas', 'ccontrol'),
            'filter_items_list'     => esc_attr__('Filtro del Listado de Facturas', 'ccontrol'),
        );
        $args = array(
            'label'                 => esc_attr__('Factura', 'ccontrol'),
            'description'           => esc_attr__('Facturas', 'ccontrol'),
            'labels'                => $labels,
            'supports'              => array('title'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'ccontrol-dashboard',
            'menu_position'         => 19,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
        );
        register_post_type('cc_invoices', $args);
    }

    public function cc_invoices_custom_columns($columns)
    {
        unset($columns['date']);
        $columns['invoice'] = esc_attr__('Factura', 'ccontrol');
        $columns['client'] = esc_attr__('Cliente', 'ccontrol');
        $columns['price'] = esc_attr__('Precio', 'ccontrol');
        $columns['status'] = esc_attr__('Status', 'ccontrol');
        $columns['due_date'] = esc_attr__('Vencimiento', 'ccontrol');
        $columns['date'] = esc_attr__('Date', 'wordpress');
        return $columns;
    }

    public function cc_invoices_promo_column_content($column_name, $post_id)
    {
        if ('invoice' == $column_name) {
            $invoice = get_post_meta($post_id, 'numero_factura', true);
            if ($invoice !== '') {
                echo esc_html($invoice);
            } else {
                esc_html_e('No hay numero de invoice seleccionado', 'ccontrol');
            }
        }

        if ('client' == $column_name) {
            $client_id = get_post_meta($post_id, 'cliente_factura', true);
            $client = get_post($client_id);
            if ($client) {
                echo esc_html($client->post_title);
            } else {
                esc_html_e('No hay cliente seleccionado', 'ccontrol');
            }
        }

        if ('price' == $column_name) {
            $metodo_pago = get_post_meta($post_id, 'metodo_pago', true);
            if ($metodo_pago == 'bs') {
                $metodo_pago = 'Bs.';
            } elseif ($metodo_pago == 'usd') {
                $metodo_pago = '$';
            } else {
                $metodo_pago = '';
            }
            $price = get_post_meta($post_id, 'price', true);
            if ($price !== '') {
                echo esc_html($metodo_pago . ' ' . number_format($price, 2, ',', '.'));
            } else {
                esc_html_e('No hay precio seleccionado', 'ccontrol');
            }
        }

        if ('status' == $column_name) {
            $arr_status = [
                'sent' => esc_attr__('Enviado', 'ccontrol'),
                'accepted' => esc_attr__('Aceptado', 'ccontrol'),
                'rejected' => esc_attr__('Rechazado', 'ccontrol'),
                'paid' => esc_attr__('Pagado', 'ccontrol')
            ];
            $status = get_post_meta($post_id, 'status_factura', true);
            if ($status !== '') {
                echo wp_kses_post('<span class="ccontrol-status ccontrol-status-' . $status . '">' . $arr_status[$status] . '</span>');
            } else {
                esc_html_e('No hay estatus seleccionado', 'ccontrol');
            }
        }

        if ('due_date' == $column_name) {
            $due_date = get_post_meta($post_id, 'fecha_factura', true);
            if ($due_date !== '') {
                echo esc_html($due_date);
            } else {
                esc_html_e('No hay fecha de vencimiento seleccionada', 'ccontrol');
            }
        }
    }
}
