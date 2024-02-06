<?php
   global $metayota_config;
   global $wpdb;
   $metayota_config = [
     'wordpress' => true,
     'db_table_prefix' => $wpdb->prefix.'metayota_'
   ];
   $GLOBALS['allowed_referers'] = ['https:/'];
   $GLOBALS['no_referer_check'] = [];
?>
