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
class Ccontrol_Dashboard
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

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
