<?
/**
 * JUANdirectory PHP Model-View-Controller Setup
 *
 * Master DB config loader V1.1
 *
 * Author/Contributor : John Virdi V. Alfonso
 * Date   : 9 July 2015
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

class DBMConfig{
  private $DBdata;
  public function __construct(){
	$this -> DBdata['DBuser'] = 'database user';
	$this -> DBdata['DBpwd'] = 'password';
	$this -> DBdata['DBname'] = 'database name';
	$this -> DBdata['DBkey'] = '32characterslongkeyforencryption';
  }
  
  protected function dbConfig(){
	$config = array();
    $config['user'] = $this -> DBdata['DBuser'];
	$config['password'] = $this -> DBdata['DBpwd'];
	$config['database'] = $this -> DBdata['DBname'];
	$config['pkey'] = $this -> DBdata['DBkey'];
	
	//check if app has local database config file.
	$localFile = ROOTDIR.APPLICATIONFOLDERDIR.DS.'configs/DBconfig.php';
	
	if(is_file($localFile)){
	  // load and replace config file if needed.
	  require_once($localFile);
	  $localConfig = new \DBappConfig();
	  $newConfigs = $localConfig -> config();
	  if(isset($newConfigs['user']))
		unset($config['user']);
	  if(isset($newConfigs['password']))
		unset($config['password']);
	  if(isset($newConfigs['database']))
		unset($config['database']);
	  if(isset($newConfigs['pkey']))
		unset($config['pkey']);
	  $config = array_merge($config, $newConfigs);
	}
	return $config;
  }
  
  public function config(){
	return $this -> dbConfig();
  }
 
}
?>
