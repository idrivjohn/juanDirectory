<?
/**
 * JUANdirectory PHP Model-View-Controller Setup
 *
 * class.application.php V1.02
 *
 * Author/Contributor : John Virdi V. Alfonso
 * Update   : 02 July 2015
 * Email  : jva.ipampanga@gmail.com
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
**/

namespace MVC;

(!defined('ROOTDIR'))?die('ILLEGAL ACCESS OF FILE'):'';

class Application extends Loader{
  private $fallback = array('app'=>'home','controller' => 'home','method' => 'main');
  //public $derivedData;
  function __construct(){
	/*if(!defined('BASEHREF')){
	  define('BASEHREF',$this -> getBaseHref());
	}*/
	/*$data = NULL;
	$data['path'] = $this -> parseURL();
	$data['device'] = $this -> getDevicePlatform();
	$arrayRequest = $this -> getURLQueries();
	
	$data['defaultParams'] = $this -> getDefaultParams();
	if(gettype($arrayRequest) == 'array')
	  $data['requests'] = $arrayRequest;
	

	
	$this -> derivedData = $data;
	*/
  }
  
/*  public function loadRouter(){
	$this -> loadFile(array(
	  'name' => 'router.php',
	  'path' => 'applications'.DS,
	 // 'data' => $this -> derivedData
	));
  }*/
  
  public function getDefaultParams(){
	$isDconfigFile = $this -> checkFile(array(
	  'name' => 'MVCconfig.php',
	  'path' => 'configs'.DS
	));
	if($isDconfigFile){
	  $this -> loadFile(array(
		'name' => 'MVCconfig.php',
		'path' => 'configs'.DS
	  ));
	  $MVCconfig = new MVCconfig();
	  $defaultMVC = $MVCconfig -> config();
	  $this -> fallback['app'] = $defaultMVC['defaultApplication'];
	  $this -> fallback['controller'] = $defaultMVC['defaultController'];
	  $this -> fallback['method'] = $defaultMVC['defaultFunction'];
	}
    $defaults = array('app' => $this -> fallback['app'], 'classController' => $this -> fallback['controller'], 'method' => $this -> fallback['method']);
	return $defaults;
  }
  
  public function getBaseHref(){
	$pageName = basename($_SERVER['SCRIPT_FILENAME']);
	$publicFolder = basename(dirname($_SERVER['PHP_SELF']));
	$mvcPath = trim(str_replace($publicFolder,'',dirname($_SERVER['PHP_SELF'])),DS);
	$folders = explode(DS, $mvcPath);
	if($folders[count($folders) - 1] === 'mvc')
	  array_pop($folders);
	$tempHref = DS;
	foreach($folders as $folder){
	  $tempHref .= $folder.DS;
	}
	if (substr($tempHref, -1) !== DS)
	  $tempHref.=DS;
	  	
	$tempBASE = $this -> getURL();
	return ($tempBASE['protocol'].$tempBASE['server'].$tempHref);
  }
  
  public function parseURL($URLgetVars =  false){
	$URLarray = $this -> getURL();
	$index = strlen($_SERVER['DOCUMENT_ROOT']);//dirname(dirname(dirname(__FILE__))).DS);
	$path = substr(dirname(dirname(dirname(__DIR__))), $index); //ROOTDIR, $index
	//$this -> jsonPrettyPrint(array(ROOTDIR, $_SERVER['DOCUMENT_ROOT'], $path, dirname(dirname(dirname(__DIR__)))));
	//exit;
	if(strlen($URLarray['request'])>0){
	  $params = str_replace('index.php','',$URLarray['request']);
	  $params = str_replace($path,'',$params);
	  $params = trim($params,DS);
	  $params = (!$URLgetVars)?preg_replace('/\\?.*/', '', $params):$params;
	  //$this -> jsonPrettyPrint($params);
	  if(!$URLgetVars)
	    $params = rtrim($params,DS);
	  $params = explode(DS,$params);
	  foreach($params as $key => $value){
	    $value = trim(urldecode($value));
		if(strlen($value) == 0){
		  $params[$key] = NULL;
		}else{
		  $params[$key] = $value;
		}
	  }
	  return (strlen($params[0])==0) ? array_shift($params):$params;
	}else{
	  return null;
	}
  }
  
  public function getURL(){
	$URLprotocol = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	$URLserver = $_SERVER["SERVER_NAME"];
	$URLrequest = filter_var(trim($_SERVER["REQUEST_URI"]),\FILTER_SANITIZE_URL);
	return array('protocol'=>$URLprotocol,'server'=>$URLserver,'request'=>$URLrequest);
  }
  
  public function getURLQueries(){
	$queries = NULL;
	$URLrequest = $_GET;
	unset($URLrequest['url']);
	if(count($URLrequest)>=1){
	  $queries = $URLrequest;
	}
	return $queries;
  }
  
  public function getDevicePlatform(){
	$browsers = array(
	  '/msie/i'       =>  'Internet Explorer',
	  '/firefox/i'    =>  'Firefox',
	  '/chrome/i'     =>  'Chrome', 
	  '/opera/i'      =>  'Opera',
	  '/netscape/i'   =>  'Netscape',
	  '/maxthon/i'    =>  'Maxthon',
	  '/konqueror/i'  =>  'Konqueror',
	  '/safari/i'     =>  'Safari'	,
	  '/mobile/i'     =>  'Handheld Browser'  
	);
	
	$desktopOS = array(
	  '/windows nt 6.2/i'     =>  'Windows 8',
	  '/windows nt 6.1/i'     =>  'Windows 7',
	  '/windows nt 6.0/i'     =>  'Windows Vista',
	  '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
	  '/windows nt 5.1/i'     =>  'Windows XP',
	  '/windows xp/i'         =>  'Windows XP',
	  '/windows nt 5.0/i'     =>  'Windows 2000',
	  '/windows me/i'         =>  'Windows ME',
	  '/win98/i'              =>  'Windows 98',
	  '/win95/i'              =>  'Windows 95',
	  '/win16/i'              =>  'Windows 3.11',
	  '/macintosh|mac os x/i' =>  'Mac OS X',
	  '/mac_powerpc/i'        =>  'Mac OS 9',
	  '/linux/i'              =>  'Linux',
	  '/ubuntu/i'             =>  'Ubuntu',
	  '/iphone/i'             =>  'iPhone',
	  '/ipod/i'               =>  'iPod',
	  '/ipad/i'               =>  'iPad',
	  '/android/i'            =>  'Android',
	  '/blackberry/i'         =>  'BlackBerry',
	  '/webos/i'              =>  'Mobile'
	);
	$detect = new Mobile_Detect;
	$device= array();
	$device['browser'] = 'Generic';
	$device['type'] = 'Desktop';
	$device['system'] = 'Unlisted';
	
	$mobileOS = $detect -> getOperatingSystems();
	
	if($detect -> isTablet()){
	  $device['type'] = 'Tablet';
	}else if($detect -> isMobile()){
	  $device['type'] = 'Mobile';
	}
	
	if($device['type'] == 'Desktop'){
	  foreach($desktopOS as $osKey=> $osName){
		if(preg_match($osKey, $_SERVER['HTTP_USER_AGENT'])){
		  $device['system'] = $osName;
		  break;
		}
	  }
	}else{
	  foreach($mobileOS as $osName=> $osKey){
		if(preg_match('/('.preg_quote($osKey,'/').')/i', $_SERVER['HTTP_USER_AGENT'])){
		  $device['system'] = $osName;
		  break;
		}
	  }
	}
	
	foreach($browsers as $browserKey => $browserOS){
	  if(preg_match($browserKey, $_SERVER['HTTP_USER_AGENT'])){
	    $device['browser'] = $browserOS;
		break;
	  }
	}  
	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
	  if (array_key_exists($key, $_SERVER) === true) {
		foreach (explode(',', $_SERVER[$key]) as $ip) {
		  if (filter_var($ip, \FILTER_VALIDATE_IP) !== false) {
			$device['ip'] = $ip;
		  }
		}
	  }
	}
	
	$device['timestamp'] = date('Y-m-d H:i:s');
	return $device;
  }
  
  public function redirect($url,$refresh=0){
	if (headers_sent() ){
	  if($refresh>=1){
		echo "<br /> Redirecting in $refresh sec(s).";
		$refresh = $refresh*1000;
	  }
	  echo "\n<script>window.setTimeout(function() { self.location.href = '$url' },".$refresh.");</script>";
	  exit;
	}else{
	  header( "Location: $url" );
	  exit;
	}
	
  }
  
  public function redirectJS($url, $target='top') {
	echo "<script type='text/javascript'>".$target.".location.href = '".$url."';</script>";
	exit();
  }
  
  public function curlData($obj){
	$result = array();
	$url = isset($obj['url']) ? $obj['url'] : BASEHREF;
	$data = $obj['data'];
	$method = isset($obj['method']) ? $obj['method'] : 'POST';
	$header = isset($obj['header']) ? $obj['header'] : array('Content-Type: application/json', 'Content-Length: ' . count($data));
	
	$ch = curl_init();

	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $this->_agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $this->_cookie_file_path);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_cookie_file_path);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	
	
	if($method !== 'POST')
	  curl_setopt($ch,CURLOPT_CUSTOMREQUEST, strtoupper($method));
	  
	curl_setopt($ch,CURLOPT_HTTPHEADER, http_build_query($header));
	if(isset($obj['header']))
	  curl_setopt($ch,CURLOPT_POST, count($data));
	curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
	
	//execute post
	$result = trim(curl_exec($ch));
	
	if(curl_errno($ch)) {
      $result['error'] = curl_error($ch);
    }else{
      curl_close($ch);
    }

	return $result;
  }
  
  public function trace($data, $name = NULL){
	echo "<br /><pre>";
	if($name) echo "<strong>".$name."</strong><br />";	
	var_dump($data);
	echo "</pre><br />";
  }
  
  public function jsonPrettyPrint($data){
    //$data can be an array or object
	header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
  }
}
?>