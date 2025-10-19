<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ccontrol
 * @subpackage Ccontrol/includes
 * @author     Robert Ochoa <ochoa.robert1@gmail.com>
 */
class Ccontrol_Activator
{
    /**
     * Method activate
     *
     * @return void
     */
    public static function activate()
    {
        flush_rewrite_rules();
    }
}
