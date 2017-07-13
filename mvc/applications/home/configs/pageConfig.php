<?
(!defined('ROOTDIR'))?die('ILLEGAL ACCESS OF FILE'):'';
class PAGEConfig {
  public $config;
  private $pageData;
  public function __construct(){
	  $this -> pageData['PROJECTNAMESPACE'] = 'project-namespace'; //optional
	  $this -> pageData['DISPLAYNAME'] = 'displayname';//title page
	  $this -> pageData['TAGLINE'] = 'short description';
	  $this -> pageData['DESCRIPTION'] = 'full description';
	  $this -> pageData['HTTPSPROTOCOL'] = false; //force to use https protocol true/false
  }
  
  public function returnConfig(){
	$this -> config = array();
	$this -> config['title'] = $this -> pageData['DISPLAYNAME'];
	$this -> config['tagline'] = $this -> pageData['TAGLINE'];
	$this -> config['description'] = $this -> pageData['DESCRIPTION'];
	$this -> config['projectname'] = $this -> pageData['DISPLAYNAME'];
	$this -> config['projectnamespace'] = $this -> pageData['PROJECTNAMESPACE'];
	return $this -> config;
  }
  
  public function appConfig(){
	$this -> config = array();
    $this -> config['appname'] = $this -> pageData['DISPLAYNAME'];
	$this -> config['appid'] = $this -> pageData['APPID'];
	$this -> config['appnamespace'] = $this -> pageData['NAMESPACE'];
	$this -> config['pagename'] = $this -> pageData['PAGENAME'];
	$this -> config['pageid'] = $this -> pageData['PAGEID'];
	$this -> config['isPageApp'] = isset($this -> pageData['PAGEAPP'])?$this -> pageData['PAGEAPP']:false;
	if(isset($this -> pageData['PERMISSIONS'])){
	  $this -> config['permissions'] = $this -> pageData['PERMISSIONS'];
	}
	if(isset($this -> pageData['APPSTART']) && isset($this -> pageData['APPEND'])){
	  $this -> config['append'] = $this -> pageData['APPEND'];
	  $this -> config['appstart'] = $this -> pageData['APPSTART'];
	}
	$this -> config['projectname'] = $this -> pageData['PROJECTNAMESPACE'];
	return $this -> config;
  }
  
  public function appSecret(){
	return $this -> pageData['APPSECRET'];
  }
}
?>
