<?
/**
 * JUANdirectory PHP Model-View-Controller Setup
 *
 * class.databasea.php V1.0
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

namespace MVC;

(!defined('ROOTDIR'))?die('ILLEGAL ACCESS OF FILE'):'';

class Instagram extends Application{
  private $config;
  private $APIhost;
  private $validTokenURL;
  public function __construct()
  {
	parent :: __construct();
	$this -> APIhost = 'https://api.instagram.com/v1/';
	$this -> loadFile(array(
	  'name' => 'IGMconfig.php',
	  'path' => 'configs'.DS
	));
	
	
	
	$IG = new \IGMconfig();
	
	$this -> config = 	$IG -> config();
	
	$this -> validTokenURL = 'client_id='.$this -> config['id'];  //future update will use access_token=ACCESS-TOKEN or client_id
  }
  
  public function searchForTags($tag){
	$useUrl = $this -> APIhost.'tags/search?q='.$tag. $this -> validTokenURL;
	$tags = json_decode($this -> callInstagram($useUrl),true);
	return $tags;
  }
  
  public function grabPhotosByHash($hashTag, $maxtagID=NULL){
	$useUrl = $this -> APIhost.'tags/'.$hashTag.'/media/recent?'. $this -> validTokenURL;
	if($maxtagID !== NULL){
	  $useUrl.='&max_id='.$maxtagID;
	}
	$photosIG = json_decode($this -> callInstagram($useUrl),true);
	$photosIG['hashtag']=$hashTag;
	return $photosIG;
  }
 
  public function grabPhotosByUser($user, $isID = false, $maxtagID = NULL){
	$params = ($maxtagID !== NULL) ? $this -> validTokenURL . '&max_id=' . $maxtagID : $this -> validTokenURL;
	  
	if($isID){
	  $useUrl = $this -> APIhost.'users/'.$user.'/media/recent/?'. $params;
	}else{
	  $users = $this -> getUserID($user);
	  $photosIG['owner'] = $users[0];
	  if($users[0]){
		$useUrl = $this -> APIhost.'users/'.$users[0]['id'].'/media/recent/?'. $params;
	  }
	  if(count($users) > 1)
		$photosIG['users']=$users;
	}
	
	$photosIG = json_decode($this -> callInstagram($useUrl),true);	  
	return $photosIG;

  }
  
  private function getUserID($username){
	$useUrl = $this -> APIhost.'users/search?q='.$username.'&'. $this -> validTokenURL;
	$results= json_decode($this -> callInstagram($useUrl),true);
	return $results['data'];
  }
  
  private function callInstagram($url)
  {
	$ch = curl_init();
	curl_setopt_array($ch, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_SSL_VERIFYPEER => false,
	  CURLOPT_SSL_VERIFYHOST => 2
	));
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
  }
}
?>
