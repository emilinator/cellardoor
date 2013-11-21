<?php

function autoload($className){
	include_once "classes/$className.class.php";
}

spl_autoload_register("autoload");