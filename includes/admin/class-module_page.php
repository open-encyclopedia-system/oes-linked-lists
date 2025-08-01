<?php

namespace OES\Linked_Lists;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('\OES\Admin\Module_Page')) oes_include('admin/pages/class-module_page.php');

if (!class_exists('Linked_Lists_Module_Page')) :

    class Linked_Lists_Module_Page extends \OES\Admin\Module_Page
    {
        //@oesDevelopment: add help tabs
    }

    new Linked_Lists_Module_Page([
        'name' => 'Linked Lists',
        'schema_enabled' => false,
        'file' => (__DIR__ . '/views/view-settings-linked-lists.php')
    ]);

endif;