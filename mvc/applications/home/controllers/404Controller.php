<?
class _404 extends MVC\Application{
 public $MVCdata;
 function __construct($data){
  /*$data['device'];
    $data['requests'];
    $data['paths'];
	$data['params'];
	$data['pageMeta'];
  */
  $this -> MVCdata = $data;
  $this -> loadFile(array(
	'name' => 'pageConfig.php',
	'path' => APPLICATIONFOLDERDIR.DS.'configs'.DS
  ));
 }
 
 public function main(){
  $this -> loadFile(array(
	'name' => '404.php',
	'path' => APPLICATIONFOLDERDIR.DS.'views'.DS
  ));
 }
}

?>