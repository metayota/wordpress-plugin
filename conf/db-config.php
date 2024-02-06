<?php
   require_once( realpath( dirname( __FILE__ ) . '/../../../../wp-config.php' ) );

   $GLOBALS['DB_USERNAME'] = DB_USER;
   $GLOBALS['DB_PASSWORD'] = DB_PASSWORD;
   $GLOBALS['DB_HOST'] = DB_HOST;
   $GLOBALS['DB_NAME'] = DB_NAME;
?>
