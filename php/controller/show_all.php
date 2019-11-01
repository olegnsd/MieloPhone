<?php
 
/*
 * Example PHP implementation used for the index.html example
 */
 
// DataTables PHP library
// include $_SESSION['CONF']['DIRS']['LIB'].'DataTables/DataTables.php';//'../lib/DataTables/DataTables.php';
// /var/www/call.holding.home/php/lib/DataTables
// /var/www/call.holding.home/php/lib/DataTables/DataTables.php
 
// Alias Editor classes so they are easy to use

 use   DataTables\Editor;
 use   DataTables\Editor\Field;
 use   DataTables\Editor\Format;
 use   DataTables\Editor\Mjoin;
 use   DataTables\Editor\Options;
 use  DataTables\Editor\Upload;
 use  DataTables\Editor\Validate;
 
// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'datatables_demo' )
    ->fields(
        Field::inst( 'email' )
    )
    ->process( $_POST )
    ->json();