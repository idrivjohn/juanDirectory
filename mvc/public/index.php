<?
  header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
  ini_set('display_errors',1);
  //error_reporting(E_ALL|E_STRICT); //show all errors
  error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); // do not show notice and warnings
  //ini_set('error_log','script_errors.log');
  //ini_set('log_errors','On');
  $condition = (int)str_replace('.',PHP_VERSION) < 540 ? session_id() === '' : session_status() === PHP_SESSION_NONE;

  if($condition){
		//session_status() === PHP_SESSION_NONE for PHP >= 5.4.0
		//and  session_id() === '' PHP < 5.4.0
		session_start();
  }

  require_once('../libraries/bootstrap.php');
  $APP = new MVC\Application;
  
  $APP -> loadFile(array(
		'name' => 'router.php',
		'path' => 'applications'.DS/*\,
		'data' => $APP -> getDefaultParams()*/
  ));
?>
