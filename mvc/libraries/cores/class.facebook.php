<?
/**
 *
 * class.facebook.php V1.1
 *
 * Author/Contributor : John Virdi V. Alfonso
 * Date   : 03 August 2015
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

class Facebook extends Application{
  protected $FBconfig;
  protected $configs;
  private $appSecret;
  private $pageURL;
  private $pageRequests;
  protected $signedRequest;
  public $app_data;
  public function __construct($data)
  {
	$this -> loadFile(array(
	    'name' => 'FBconfig.php',
		'path' => APPLICATIONFOLDERDIR.DS.'configs'.DS
	));	
	$this -> FBconfig =  new \FBappConfig();
	$this -> device = $this -> getDevicePlatform();
	$config = $this -> FBconfig -> appConfig();
	$page = $this -> FBconfig -> pageConfig();
	$this -> appSecret = $this -> FBconfig -> appSecret();
	$defaultParams = $this -> getDefaultParams();
	$this -> configs = array('appconfig' => $config, 'pageconfig' =>$page);
	$arrayURL = $this -> getURL();
	$urlData = $this -> getURLQueries();
	$pageURL = $arrayURL['protocol'].$arrayURL['server'].$arrayURL['request'].$this -> configs['appconfig']['appnamespace'].DS;
	$appData = NULL;
	
	if($urlData){
	  foreach($urlData as $key => $value){
		$appData = ($appData === NULL)?'':"$appData|";
		$appData = $appData."$key=$value";
	  }
	}
	
	$appData = ($appData===NULL)?'':"$appData";
    
	if($this -> device['type'] === 'Desktop' && $this -> configs['appconfig']['isPageApp']){
	  $this -> pageRequests = $urlData;
	  $pageURL = $arrayURL['protocol'].'www.facebook.com/'.$this -> configs['appconfig']['pageid'].'?sk=app_'.$this -> configs['appconfig']['appid'];
	  $pageURL = ($appData === NULL)?$pageURL:"$pageURL&$appData";
	  $this -> pageURL = $pageURL;
	}else{
	  $this -> pageURL = $pageURL;
	}
	
	if(isset($config['appstart']) && isset($config['append'])){
	  $now = strtotime('now');
	  if($now < $config['appstart'] || $now > $config ['append']){
		$msg = ($now < $config ['appstart'])?'Sorry, the contest has yet to begin!':'Sorry, the contest has ended!';
		$this -> configs['teaser'] = array('message' => $msg);
		if(isset($_SESSION['fb_'.$config['appid'].'_oauth_token'])){
		  unset($_SESSION['fb_'.$config['appid'].'_oauth_token']);
		}
	    if($this -> generatedController !== $this -> defaultController){
		  $this -> redirect(BASEHREF.CURRENTFOLDER);
		}
	  }
	  
	}
	
	if(isset($_SESSION['fb_'.$config['appid'].'_oauth_token'])){
	  $decryptedToken = $this -> decodeToken($_SESSION['fb_'.$config['appid'].'_oauth_token']);
	  if(isset($decryptedToken -> error)){
		if(isset($_SESSION['fb_'.$config['appid'].'_oauth_token']))
		  unset ($_SESSION['fb_'.$config['appid'].'_oauth_token']);
		  
		if(isset($_SESSION['FB'.$config['appid'].'signedRequest']))
		  unset ($_SESSION['FB'.$config['appid'].'signedRequest']);

		echo "Error: ".$decryptedToken -> error -> message;
		echo "<br /> Redirecting in seconds.";
		echo "\n<script>window.setTimeout(function() { window.top.location.href = '".$this -> pageURL."' },5000);</script>";
		exit;
	  }else{
		if(isset($decryptedToken -> id))
		  $this -> configs['userid'] = $decryptedToken -> id;
	  }
	}
	
	if(isset($_POST) && isset($_POST['oauth_token'])){
	  $this -> setOauthToken($_POST['oauth_token']);
	  exit;
	}
	//$_SERVER['HTTP_USER_AGENT']

	$agent = $_SERVER['HTTP_USER_AGENT'];
	if(stristr($agent, 'FacebookExternalHit')){
		//Facebook User-Agent
		$this -> initPage();
	}else{
	  if($this -> device['type'] === 'Desktop' && $this -> configs['appconfig']['isPageApp']){
		$this -> initCanvasPage();
	  }else{
		if(isset($_REQUEST['app_data'])) $this -> appdata = $_REQUEST['app_data'];
		if(isset($_REQUEST['app_data']))
		  $this -> configs['app_data'] = $_REQUEST['app_data'];
		$this -> initPage();
	  }
	}
	
  }
  
  public function decodeToken($token){
    $response = $this -> getUrlContents("https://graph.facebook.com/me?access_token=$token");
	return $response;
  }
  
  protected function initPage(){
	$appData = NULL;
	
	if($this -> pageRequests){
	  foreach($this -> pageRequests as $key => $value){
		if($key == 'app_data'){
		  $appData = "$key=$value";
		}
	  }
	}

	$pageURL = $this -> pageURL;

	if(isset($_REQUEST['code']) && !isset($_SESSION['fb_'.$this -> configs['appconfig']['appid'].'_oauth_token'])){
	  $code =  $this -> exchangeCodeToToken($_REQUEST['code']);
	  if($this -> device['type'] === 'Desktop'){
		$pageURL = $this -> pageURL;
		$pageURL = ($appData==NULL)?$pageURL:"$pageURL?$appData";
	  }else{
		$pageURL = "https://apps.facebook.com/".$this -> configs['appconfig']['appnamespace'];
		$pageURL = ($appData==NULL)?$pageURL:"$pageURL?$appData";
	  }
	  if(isset($code ->error)){
		echo "Error: ".$code -> error -> message;
		if($this -> device['type'] === 'Desktop'){
		  $this -> redirect($pageURL,5);
		}else{
		   $this -> redirect($pageURL);
		}
	  }else{
		$code = explode('=',$code);
		if($code[0]=='access_token'){
		  $this -> setOauthToken($code[1]);
		}
	  }
	}
  }
  
  protected function initCanvasPage(){
	$this -> signedRequest = $this -> getSignedRequest();
	if($this -> signedRequest === NULL){
	  $this -> redirect($this -> pageURL);
	}else{
	  if(isset($this -> signedRequest['oauth_token']))
		$_SESSION['fb_'.$this -> configs['appconfig']['appid'].'_oauth_token'] = $this -> signedRequest['oauth_token'];
	  if(isset($this -> app_data))
		$this -> configs['app_data'] = $this -> app_data;
	  if(isset($this -> signedRequest['user_id']))
		$this -> configs['userid'] = $this -> signedRequest['user_id'];
	  if(isset($this -> signedRequest['page'])){
		if($this -> signedRequest['page']['id']!=$this -> configs['appconfig']['pageid'])
		$this -> redirectJS($this -> pageURL);
	  }else{
		$this -> redirectJS($this -> pageURL);
	  }
	}
  }
  
  protected function getSignedRequest(){
	$objData = NULL;
	$signedRequest = NULL;
	if(isset($_POST['signed_request']) || isset($_REQUEST['signed_request'])){
	  if(isset($_REQUEST['signed_request'])){
		$signedRequest = $_REQUEST['signed_request'];
	  }elseif(isset($_POST['signed_request'])){
		$signedRequest = $_POST['signed_request'];
	  }
	}elseif(isset($_SESSION['FB'.$this -> configs['appconfig']['appid'].'signedRequest'])){
	  $signedRequest = $_SESSION['FB'.$this -> configs['appconfig']['appid'].'signedRequest'];
	}
	
	if($signedRequest){
	  $objData = $this -> parseSignedRequest($signedRequest);
	  if($objData){
		$_SESSION['FB'.$this -> configs['appconfig']['appid'].'signedRequest'] =  $signedRequest;
		return $objData;
	  }else{
		return NULL;
	  }
	}else{
	  unset($_SESSION['FB'.$this -> configs['appconfig']['appid'].'signedRequest']);
	  return NULL;
	}
  }
  
  private function parseSignedRequest($signedRequest=NULL){
    list($encoded_sig, $payload) = explode('.', $signedRequest, 2); 
	
	$sig = $this -> base64_url_decode($encoded_sig);
	$data = json_decode($this -> base64_url_decode($payload), true);
  
	$expected_sig = hash_hmac('sha256', $payload, $this -> appSecret , $raw = true);
	if ($sig !== $expected_sig) {
	  if(isset($_SESSION['FB'.$this -> configs['appconfig']['appid'].'signedRequest']))
		unset($_SESSION['FB'.$this -> configs['appconfig']['appid'].'signedRequest']);
	  return null;
	}
	if(isset($data['app_data']))
	  $this -> configs['app_data'] = $data['app_data'];
	  
	return $data;
  }
  
  private function base64_url_decode($input=NULL) {
	return base64_decode(strtr($input, '-_', '+/'));
  }
  
  private function exchangeCodeToToken($code=NULL){
	$redirectURI = $this -> pageURL;

	$tokenURL = 'https://graph.facebook.com/oauth/access_token?client_id='.$this -> configs['appconfig']['appid'].'&redirect_uri='.urlencode($redirectURI) .'&client_secret='.$this -> appSecret.'&code='.$code;

	return $this -> getUrlContents($tokenURL);

  }
  
  private function getUrlContents($URL){
	if (!function_exists('curl_init')){ 
	  $response = @file_get_contents($URL);//
    }else{
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $URL);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  $response = curl_exec($ch);
	  curl_close($ch);
	}
	if(json_decode($response)){
	  return json_decode($response);
	}else{
	  return $response;
	}
  }
  
  private function setOauthToken($token){
	$response = $this -> getUrlContents("https://graph.facebook.com/me?access_token=$token");
	if(!isset($response -> error)){
	  $_SESSION['fb_'.$this -> configs['appconfig']['appid'].'_oauth_token'] = $token;
	  return true;
	}else{
	  unset($_SESSION['fb_'.$this -> configs['appconfig']['appid'].'_oauth_token']);
	  return false;
	}
  }
  
}
?>