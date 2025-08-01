<?php

/**
 * OES Linked Lists (OES Core Module)
 *
 * @wordpress-plugin
 * Plugin Name:        OES Linked Lists (OES Core Module)
 * Plugin URI:         https://www.open-encyclopedia-system.org/
 * Description:        Display a collection of objects as linked lists, based on the design of Marian Dörk. Requires OES Core to function.
 * Version:            1.0.0
 * Author:             Maren Welterlich-Strobl, Freie Universität Berlin, FUB-IT
 * Author URI:         https://www.it.fu-berlin.de/die-fub-it/mitarbeitende/mstrobl.html
 * Requires at least:  6.5
 * Tested up to:       6.8.2
 * Requires PHP:       8.1
 * Tags:               oes, linked-lists, visualization, data-display, plugin-addon, encyclopedia
 * License:            GPLv2 or later
 * License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('oes/plugins_loaded', function () {

    if (!function_exists('OES')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>' .
                __('The OES Core Plugin is not active.', 'oes') . '</p></div>';
        });
    } else {

        $oes = OES();
        if (!$oes->initialized) return;

        if(is_admin()){
            include_once __DIR__ . '/includes/admin/class-module_page.php';
        }

        include_once __DIR__ . '/includes/functions.php';
        include_once __DIR__ . '/includes/class-linked_list.php';

        add_action('wp_enqueue_scripts', '\OES\Linked_Lists\enqueue_scripts', 9);

        add_shortcode('oes_linked_lists', 'OES\Linked_Lists\html');

        do_action('oes/linked_lists_plugin_loaded');
    }
}, 12);