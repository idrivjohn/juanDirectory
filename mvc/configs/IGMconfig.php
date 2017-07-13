<?
/**
 * JUANdirectory PHP Model-View-Controller Setup
 *
 * Master IG config loader V1.0
 *
 * Author/Contributor : John Virdi V. Alfonso
 * Date   : 27 July 2015
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
(!defined('ROOTDIR'))?die('ILLEGAL ACCESS OF FILE'):'';

class IGMConfig{
  private $IGdata;
  public function __construct(){
	// default settings for IG theidealab developer 
	$this -> IGdata['IGid'] = '';
	$this -> IGdata['IGsecret'] = '';
	$this -> IGdata['IGgrantType'] = 'authorization_code'; //is currently the only supported value
	$this -> IGdata['scope'] = 'basic'; //granted by default aditional scope permissions are comments, relationships, likes
  }
  
  protected function igConfig(){
	$config = array();
    $config['id'] = $this -> IGdata['IGid'];
	$config['secret'] = $this -> IGdata['IGssecret'];
	$config['grantType'] = $this -> IGdata['IGgrantType'];
	$config['scope'] = $this -> IGdata['scope'];
	//check if app has local database config file.
	//$this -> config ['apiHost'].'v1/tags/'.$hashTag.'/media/recent?client_id='.$this -> config['id'];
	$localFile = ROOTDIR.APPLICATIONFOLDERDIR.DS.'configs/IGconfig.php';
	
	if(is_file($localFile)){
	  // load and replace config file if needed.
	  require_once($localFile);
	  $localConfig = new \IGappConfig();
	  $newConfigs = $localConfig -> config();
	  if(isset($newConfigs['id']))
		unset($config['id']);
	  if(isset($newConfigs['secret']))
		unset($config['secret']);
	  if(isset($newConfigs['scope']))
		unset($config['scope']);
	  $config = array_merge($config, $newConfigs);
	}
	return $config;
  }
  
  public function config(){
	return $this -> igConfig();
  }
 
}
?>