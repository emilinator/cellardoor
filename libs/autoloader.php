<?php

function autoload( $className ){
	include_once "models/$className.class.php";
}

//tell PHP to call function autoload() whenever it is required
	spl_autoload_register( "autoload" );