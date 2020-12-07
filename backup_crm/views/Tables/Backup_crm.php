<?php

defined('BASEPATH') or exit('No direct script access allowed');




$this->ci->db->query("SET sql_mode = ''");

$aColumns = [
    
    'id',
    'backup_name',
    'backup_location',
    'created_date',
   
   
   
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'backups';
$where        = [];
// Add blank where all filter can be stored
$filter = [];

$join = [];


$aColumns = hooks()->apply_filters('customers_table_sql_columns', $aColumns);
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    // User id
    $row[] = $aRow['id'];
    $company  = $aRow['backup_name'];
    $url = admin_url('clients/client/' . $aRow['id']);
    $company = '<a href="' . $url . '">' . $company . '</a>';
    $company .= '<div class="row-options">';
    $company .= '<a href="' . admin_url('backup_crm/restorebackup/' . $aRow['id']) . '">' . _l('Restore Backup') . '</a>';
    $company .= ' | <a href="' . admin_url('backup_crm/deletebackup/' . $aRow['id']) . '" class="text-danger delete">' . _l('delete') . '</a>';
    $company .= '</div>';
     $row[] = $company;
    $row[] = $aRow['backup_location'];
    $row[] = $aRow['created_date'];
    
    

    
    $output['aaData'][] = $row;
}



