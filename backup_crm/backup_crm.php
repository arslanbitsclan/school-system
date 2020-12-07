<?php

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: backup crm
Description: backup crm module description |Bitsclan.
Author – module author name
Version: 2.3.0| Bitsclan
Requires at least: 2.3.*
*/
define('BACKUPCRM_MODULE_NAME', 'BACKUP_CRM');

/**
* Register activation module hook
*/
register_activation_hook(BACKUPCRM_MODULE_NAME, 'backup_crm_module_activation_hook');

function backup_crm_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(BACKUPCRM_MODULE_NAME, [BACKUPCRM_MODULE_NAME]);

/**
 * Init users module menu items in setup in admin_init hook
 * @return null
 */


hooks()->add_action('admin_init', 'custom_module_init_menu_items');

function custom_module_init_menu_items(){
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item(uniqid(), [
        'name'     => 'Backup crm', // The name if the item
        'href'     => admin_url('Backup_crm'),  // URL of the item
        'position' => 1, // The menu position, see below for default positions.
        'icon'     => 'fa fa-question-circle', // Font awesome icon
    ]);
}


hooks()->add_action('admin_init', 'backup_crm_permissions');

function backup_crm_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('backup_crm', $capabilities, _l('backup_crm'));
}


