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
class Ccontrol_CPT
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
     * Method ccontrol_clientes_cpt
     *
     * Register Custom Post Type
     * @return void
     */
    public function ccontrol_clientes_cpt()
    {
        $labels = array(
            'name'                  => _x('Clientes', 'Post Type General Name', 'ccontrol'),
            'singular_name'         => _x('Cliente', 'Post Type Singular Name', 'ccontrol'),
            'menu_name'             => __('Clientes', 'ccontrol'),
            'name_admin_bar'        => __('Clientes', 'ccontrol'),
            'archives'              => __('Archivo de Clientes', 'ccontrol'),
            'attributes'            => __('Atributos de Cliente', 'ccontrol'),
            'parent_item_colon'     => __('Cliente Padre:', 'ccontrol'),
            'all_items'             => __('Clientes', 'ccontrol'),
            'add_new_item'          => __('Agregar nuevo Cliente', 'ccontrol'),
            'add_new'               => __('Agregar Nuevo', 'ccontrol'),
            'new_item'              => __('Nuevo Cliente', 'ccontrol'),
            'edit_item'             => __('Editar Cliente', 'ccontrol'),
            'update_item'           => __('Actualizar Cliente', 'ccontrol'),
            'view_item'             => __('Ver Cliente', 'ccontrol'),
            'view_items'            => __('Ver Clientes', 'ccontrol'),
            'search_items'          => __('Buscar Cliente', 'ccontrol'),
            'not_found'             => __('No hay resultados', 'ccontrol'),
            'not_found_in_trash'    => __('No hay resultados en Papelera', 'ccontrol'),
            'featured_image'        => __('Logo del Cliente', 'ccontrol'),
            'set_featured_image'    => __('Colocar Logo del Cliente', 'ccontrol'),
            'remove_featured_image' => __('Remover Logo del Cliente', 'ccontrol'),
            'use_featured_image'    => __('Usar como Logo del Cliente', 'ccontrol'),
            'insert_into_item'      => __('Insertar en Cliente', 'ccontrol'),
            'uploaded_to_this_item' => __('Cargado a este Cliente', 'ccontrol'),
            'items_list'            => __('Listado de Clientes', 'ccontrol'),
            'items_list_navigation' => __('Nav. del Listado de Clientes', 'ccontrol'),
            'filter_items_list'     => __('Filtro del Listado de Clientes', 'ccontrol'),
        );
        $args = array(
            'label'                 => __('Cliente', 'ccontrol'),
            'description'           => __('Clientes', 'ccontrol'),
            'labels'                => $labels,
            'supports'              => array( 'title', 'thumbnail' ),
            'taxonomies'            => array( 'tipo-cliente' ),
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

        
    /**
     * Method cc_clientes_custom_columns
     *
     * @param $columns $columns [explicite description]
     *
     * @return void
     */
    public function cc_clientes_custom_columns($columns)
    {
        unset($columns['date']);
        $columns['logo_cliente'] = __('Logo', 'ccontrol');
        $columns['tipo_cliente'] = __('Tipo de Cliente', 'ccontrol');
        $columns['nombre_cliente'] = __('Contacto', 'ccontrol');
        $columns['correo_cliente'] = __('Correo', 'ccontrol');
        $columns['telf_cliente'] = __('Teléfono', 'ccontrol');
        $columns['date'] = __('Date', 'wordpress');
        return $columns;
    }

    
    /**
     * Method cc_clientes_promo_column_content
     *
     * @param $column_name $column_name [explicite description]
     * @param $post_id $post_id [explicite description]
     *
     * @return void
     */
    public function cc_clientes_promo_column_content($column_name, $post_id)
    {
        if ('logo_cliente' == $column_name) {
            $value = get_the_post_thumbnail_url($post_id, array(100, 100));
            if ($value != '') {
                echo '<img src="' . $value . '" alt="Logo" class="cc_clientes_logo" />';
            } else {
                echo '<img src="https://via.placeholder.com/70" alt="Logo" class="cc_clientes_logo" />';
            }
        }

        if ('tipo_cliente' == $column_name) {
            $value = get_post_meta($post_id, 'tipo_cliente', true);
            echo $value;
        }

        if ('nombre_cliente' == $column_name) {
            $value = get_post_meta($post_id, 'nombre_cliente', true);
            echo $value;
        }

        if ('correo_cliente' == $column_name) {
            $value = get_post_meta($post_id, 'correo_cliente', true);
            echo '<a href="mailto:'. $value .'">' . $value . '</a>';
        }

        if ('telf_cliente' == $column_name) {
            $value = get_post_meta($post_id, 'telf_cliente', true);
            echo '<a href="tel:'. $value .'">' . $value . '</a>';
        }
    }
    
    /**
     * Method my_sortable_cc_clientes_column
     *
     * @param $columns $columns [explicite description]
     *
     * @return void
     */
    public function my_sortable_cc_clientes_column($columns)
    {
        $columns['tipo_cliente'] = 'Tipo de Cliente';
        return $columns;
    }

    /**
     * Method ccontrol_clientes_cpt
     *
     * Register Custom Post Type
     * @return void
     */
    public function ccontrol_presupuestos_cpt()
    {
        $labels = array(
            'name'                  => _x('Presupuestos', 'Post Type General Name', 'ccontrol'),
            'singular_name'         => _x('Presupuesto', 'Post Type Singular Name', 'ccontrol'),
            'menu_name'             => __('Presupuestos', 'ccontrol'),
            'name_admin_bar'        => __('Presupuestos', 'ccontrol'),
            'archives'              => __('Archivo de Presupuesto', 'ccontrol'),
            'attributes'            => __('Atributos de Presupuesto', 'ccontrol'),
            'parent_item_colon'     => __('Presupuesto Padre:', 'ccontrol'),
            'all_items'             => __('Presupuestos', 'ccontrol'),
            'add_new_item'          => __('Agregar nuevo Presupuesto', 'ccontrol'),
            'add_new'               => __('Agregar Nuevo', 'ccontrol'),
            'new_item'              => __('Nuevo Presupuesto', 'ccontrol'),
            'edit_item'             => __('Editar Presupuesto', 'ccontrol'),
            'update_item'           => __('Actualizar Presupuesto', 'ccontrol'),
            'view_item'             => __('Ver Presupuesto', 'ccontrol'),
            'view_items'            => __('Ver Presupuestos', 'ccontrol'),
            'search_items'          => __('Buscar Presupuesto', 'ccontrol'),
            'not_found'             => __('No hay resultados', 'ccontrol'),
            'not_found_in_trash'    => __('No hay resultados en Papelera', 'ccontrol'),
            'insert_into_item'      => __('Insertar en Presupuesto', 'ccontrol'),
            'uploaded_to_this_item' => __('Cargado a este Presupuesto', 'ccontrol'),
            'items_list'            => __('Listado de Presupuestos', 'ccontrol'),
            'items_list_navigation' => __('Nav. del Listado de Presupuestos', 'ccontrol'),
            'filter_items_list'     => __('Filtro del Listado de Presupuestos', 'ccontrol'),
        );
        $args = array(
            'label'                 => __('Presupuesto', 'ccontrol'),
            'description'           => __('Presupuestos', 'ccontrol'),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor' ),
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
}
