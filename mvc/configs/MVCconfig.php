<?
namespace MVC;
(!defined('ROOTDIR'))?die('ILLEGAL ACCESS OF FILE'):'';


class MVCConfig{
  private $MVCdata;
  public function __construct(){
	$this -> MVCdata['defaultApplication'] = 'home'; // offline
	$this -> MVCdata['defaultController'] = 'home';
	$this -> MVCdata['defaultFunction'] = 'main';
  }
  
  
  protected function mvcConfig(){
	$config = array();
    $config['defaultApplication'] = $this -> MVCdata['defaultApplication'];
	$config['defaultController'] = $this -> MVCdata['defaultController'];
	$config['defaultFunction'] = $this -> MVCdata['defaultFunction'];
	return $config;
  }
  
  public function config(){
	return $this -> mvcConfig();
  }
 
}
?>