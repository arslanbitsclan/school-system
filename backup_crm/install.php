<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'backups')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "backups` (
  `id` int(11) NOT NULL,
  `backup_name` varchar(191) NOT NULL,
  `backup_location` varchar(191) NOT NULL,
  `created_date` timestamp NOT NULL,
 
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'backups`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}


