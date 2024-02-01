<?php
/*
  * Author : UmaDevi
  * Powered by Shiva Software Solutions. to All rights reserved © vGlogs LLC
  * Service - Exception -> Write error log for webservice
  * Modified by : Suriya 
  * Modified Date  :  19-08-2019
  * Modified Function name : Put comments for each line
*/
include('log4php/Logger.php');
//Include configuration file 
Logger::configure('webserviceconfiguration.xml');

//Webservice VgLogs
class pavalam{
	private $log;
	public function __construct(){
		$this->log = Logger::getLogger(__CLASS__);

	}
	//write webservice log file 
	public function go($error_no, $error_msg,$errfile, $errline){ 
		 echo " Error Description: [$error_msg] ";
 		$this->log->error("Error number: [$error_no] \r\n Error Description: [$error_msg] \r\n Error File: [$errfile] \r\n Error Line: [$errline]");   
	} 
}

		 
function error_handler($error_no, $error_msg,$errfile, $errline)
{ 
    $ob = new pavalam();
	$ob->go($error_no, $error_msg,$errfile, $errline);
		
}
set_error_handler("error_handler");
//echo (5 / 0);
?>
 