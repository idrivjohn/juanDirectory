<?
/**
 * JUANdirectory PHP Model-View-Controller Setup
 *
 * class.staticpage.php V1.01
 *
 * Author/Contributor : John Virdi V. Alfonso
 * Date   : 02 July 2015
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

/*
  
  require will produce a fatal error (E_COMPILE_ERROR) and stop the script
  include will only produce a warning (E_WARNING) and the script will continue

*/
class Staticpage extends Application{
  public $MVCdata;
  function __construct($data){
	/*$data['device'];
    $data['requests'];
    $data['paths'];
	$data['params'];
	$data['pageMeta']; loaded by applications/router.php
  */
    if(isset($data['appMeta'])){
	  unset($data['pageMeta']['isPageApp']);
	  unset($data['appMeta']);
	  unset($data['appSecret']);
	}
	
	
	$this -> MVCdata = $data;
 }
 
  public function main(){
	$isFile = $this -> checkFile(array(
	  'name' => strtolower($this -> MVCdata['page'].".php"),
	  'path' => APPLICATIONFOLDERDIR.DS.'views'.DS
	));
	if($isFile){
	  
	  $this -> loadFile(array(
		'name' => strtolower($this -> MVCdata['page'].".php"),
		'path' => APPLICATIONFOLDERDIR.DS.'views'.DS
	  ));
	}else{
	  die("Cannot load static page. Error 404!");
	}
  }
  
}
?>
