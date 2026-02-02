<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings for tool_forced_settings.
 *
 * @package    tool_forced_settings
 * @author     Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright  2026 onwards to Universitat Rovira i Virgili <https://www.urv.cat>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('tool_forced_settings', get_string('pluginname', 'tool_forced_settings'));

    // Get tool_forced_settings metadata from $CFG if available.
    $configfile = '';
    $loader = '';
    $overriddensettings = '';

    if (isset($CFG->forced_plugin_settings['tool_forced_settings'])) {
        $metadata = $CFG->forced_plugin_settings['tool_forced_settings'];

        if (isset($metadata['configfile'])) {
            $configfile = $metadata['configfile'];
        }

        if (isset($metadata['loader'])) {
            $loader = $metadata['loader'];
        }
    }

    // Build human-readable list of overridden settings.
    if (!empty($configfile) && !empty($loader)) {
        try {
            // Load settings on-demand only when viewing this settings page.
            // This avoids performance overhead on every Moodle request.

            // Instantiate the loader class.
            $loaderinstance = new $loader();
            $alloverrides = $loaderinstance->load($configfile);

            // Use config_formatter to format the output.
            $overriddensettings = \tool_forced_settings\local\config_formatter::format($alloverrides);
        } catch (Exception $e) {
            $overriddensettings = "Error loading configuration: " . $e->getMessage();
        }
    }

    // Configuration file (read-only).
    $settings->add(new admin_setting_description(
        'tool_forced_settings/configfile',
        get_string('configfile', 'tool_forced_settings'),
        get_string('configfile_desc', 'tool_forced_settings', $configfile ?: get_string('no_config', 'tool_forced_settings'))
    ));

    // Loader used (read-only).
    $settings->add(new admin_setting_description(
        'tool_forced_settings/loader',
        get_string('loader', 'tool_forced_settings'),
        get_string('loader_desc', 'tool_forced_settings', $loader ?: get_string('no_config', 'tool_forced_settings'))
    ));

    // Overridden settings (read-only).
    if (!empty($overriddensettings)) {
        $settings->add(new admin_setting_description(
            'tool_forced_settings/overridden_settings',
            get_string('overridden_settings', 'tool_forced_settings'),
            get_string('overridden_settings_desc', 'tool_forced_settings', s($overriddensettings))
        ));
    }

    $ADMIN->add('tools', $settings);
}
