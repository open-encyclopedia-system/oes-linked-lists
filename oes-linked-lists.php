<?php

namespace OES\Linked_Lists;

/**
 * OES Linked Lists (OES Core Module)
 *
 * @wordpress-plugin
 * Plugin Name:        OES Linked Lists (OES Core Module)
 * Plugin URI:         https://www.open-encyclopedia-system.org/
 * Description:        Display a collection of objects as linked lists, based on the design of Marian Dörk. Requires OES Core to function.
 * Version:            1.1.0
 * Author:             Maren Welterlich-Strobl, Freie Universität Berlin, FUB-IT, Digitale Forschungsinfrastrukturen
 * Author URI:         https://www.fu-berlin.de/
 * Requires at least:  6.5
 * Tested up to:       7.1
 * Requires PHP:       8.1
 * License:            GPLv2 or later
 * License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:        oes-linked-lists
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('oes/plugins_loaded', function () {

    if (!OES()->initialized) {
        return;
    }

    if(is_admin()){
        include_once __DIR__ . '/includes/admin/class-module_page.php';
    }

    include_once __DIR__ . '/includes/functions.php';
    include_once __DIR__ . '/includes/class-linked_list.php';

    add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts', 9);

    add_shortcode('oes_linked_lists', __NAMESPACE__ . '\\html');

    do_action('oes/linked_lists_plugin_loaded');
}, 12);