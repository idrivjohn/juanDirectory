<?
/**
 * JUANdirectory PHP Model-View-Controller Setup
 *
 * class.loader.php V1.0
 *
 * Author/Contributor : John Virdi V. Alfonso
 * Date   : 01 October 2014
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
class Loader{
  public $ds = DIRECTORY_SEPARATOR;
  public function __construct()
  {
  }
 
  public function loadFile($obj){
	$fileName = $obj['name'];
	$filePath = 'public'.$this -> ds;
	$data = NULL;
	if(isset($obj['path'])) $filePath = $obj['path']; 
	if(isset($obj['data'])) $data = $obj['data']; 
	
	$isFile = $this -> checkFile(array(
	  'name' => $fileName,
	  'path' => $filePath
	));
	
	if($isFile){
	  $filePath = $filePath.$fileName;
	  $filePath = ROOTDIR.$filePath;
	  require_once($filePath);
	  return true;  
	}else{
	  return  false;
	}
  }
  
  public function checkFile($obj){
	$fileName = $obj['name'];
	$filePath = 'public'.$this -> ds;
	if(isset($obj['path'])) $filePath = $obj['path'];
	$filePath = $filePath.$fileName;
	if(is_file(ROOTDIR.$filePath)){
	  return true;
	}else{
	  return  false;
	}
  }
  
  public function checkDirectory($obj){
	$isDirectory = false;
	$directoryPath = 'public'.$this -> ds;
	if(isset($obj['path'])) $directoryPath = $obj['path'];
	$directories = glob(ROOTDIR.$directoryPath.'*',GLOB_ONLYDIR);
	
	foreach($directories as $directory){
	  $directoryName = str_replace(ROOTDIR.$directoryPath,'',$directory);
	  if (strtolower($directoryName) == $obj['name']){
		$isDirectory =  true;
		break;
	  }
	}
	return $isDirectory;
  }
  
}
?>
