<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ccontrol
 * @subpackage Ccontrol/includes
 * @author     Robert Ochoa <ochoa.robert1@gmail.com>
 */

if (!defined('WPINC')) {
    die;
}

class Ccontrol_Deactivator
{
    /**
     * Method deactivate
     *
     * @return void
     */
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
