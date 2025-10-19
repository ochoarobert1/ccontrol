<?php

/**
 * Define the internationalization functionality.
 *
 * @since      1.0.0
 * @package    Ccontrol
 * @subpackage Ccontrol/includes
 * @author     Robert Ochoa <ochoa.robert1@gmail.com>
 */

if (!defined('WPINC')) {
    die;
}

class Ccontrol_i18n
{
    /**
     * Method load_plugin_textdomain
     *
     * @return void
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'ccontrol',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
