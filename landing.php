<?php
	include 'settings.php';
	include 'htmlprocessing.php';
	include 'logging.php';
	//Включение отладочной информации
	ini_set('display_errors','1'); 
	ini_set('display_startup_errors', 1); 
	error_reporting(E_ALL);
	//Конец включения отладочной информации
	
	//добавляем в лог факт пробива проклы
	$prelanding = isset($_COOKIE['prelanding'])?$_COOKIE['prelanding']:'';
	$subid = isset($_COOKIE['subid'])?$_COOKIE['subid']:'';
	write_lpctr_to_log($subid,$prelanding);
	
	$l=isset($_GET['l'])?$_GET['l']:-1;
	//A-B тестирование лендингов
	$landings = explode(",", $black_land_folder_name);
	if ($l<count($landings) && $l>=0)
		echo load_landing($landings[$l]);
	else{
		$r = rand(0, count($landings) - 1);
		echo load_landing($landings[$r]);
	}
?>