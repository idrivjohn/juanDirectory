<?
/**
 * JUANdirectory PHP Model-View-Controller Setup
 *
 * router.php V1.03
 *
 * Author/Contributor : John Virdi V. Alfonso
 * Update   : 19 August 2015
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
  
 
  $MVCparams = array() ;
  $MVCparams['device'] = $this -> getDevicePlatform();
    
  if(gettype( $this -> getURLQueries()) == 'array')
	  $MVCparams['URLqueries'] = $this -> getURLQueries();
  
  $defaultParams = $this -> getDefaultParams();
  $params = $this -> parseURL();
  //echo json_encode($params);
  if($params)
    $MVCparams['paths'] = array();
	
  $MVCparams['params'] = array();
  
  if($params)
	$tempApp = strtolower(array_shift($params));
  else
    $tempApp = strtolower($defaultParams['app']);

  
  
  $isDirectory = $this -> checkDirectory(array(
	'name' => strtolower($tempApp),
	'path' => 'applications'.DS
  ));
  
  $appClass =  strtolower($defaultParams['classController']);
    
 
  if($isDirectory){
	$appFolder =  $tempApp; 
	if($params){
	  $appClass = array_shift($params);
	}
	
    $isPageConfig = $this -> checkFile(array(
	  'name' => 'pageConfig.php',
	  'path' => $appFolderDIR.DS.'configs'.DS
	));
	
	$isFBConfig = $this -> checkFile(array(
	  'name' => 'FBconfig.php',
	  'path' => $appFolderDIR.DS.'configs'.DS
	));
	
  }else{
    $appFolder =  strtolower($defaultParams['app']);
	$appClass = $tempApp;
  }
  
  $MVCparams['params']['folder'] = $appFolder;
  
  $currentFolder = $appFolder;
  $appFolderDIR = 'applications'.DS.$currentFolder;
  $defaultFolderDIR = 'applications'.DS.strtolower($defaultParams['app']);
  
  $isPageConfig = $this -> checkFile(array(
	'name' => 'pageConfig.php',
	'path' => $appFolderDIR.DS.'configs'.DS
  ));
  
  $isFBConfig = $this -> checkFile(array(
	'name' => 'FBconfig.php',
	'path' => $appFolderDIR.DS.'configs'.DS
  ));
  
  $pageConfigFolder = '';
  if($isPageConfig){
	$pageConfigFolder = $appFolderDIR.DS.'configs'.DS;
  }else{
	$isPageConfig = $this -> checkFile(array(
	  'name' => 'pageConfig.php',
	  'path' => $defaultFolderDIR.DS.'configs'.DS
	));
    if($isPageConfig)
	  $pageConfigFolder = $defaultFolderDIR.DS.'configs'.DS;
  }
  if($pageConfigFolder){
	$this -> loadFile(array(
	  'name' => 'pageConfig.php',
	  'path' => $pageConfigFolder
	));
	$defaultPageConfigs = new PAGEConfig(); 
	$MVCparams['pageMeta'] = $defaultPageConfigs -> returnConfig();
  }
  $FBconfigFolder = '';
  
  if($isFBConfig){
	$FBconfigFolder = $appFolderDIR.DS.'configs'.DS;
  }else{
	$isFBConfig = $this -> checkFile(array(
	  'name' => 'FBconfig.php',
	  'path' => $defaultFolderDIR.DS.'configs'.DS
	));
	if($isFBConfig)
	  $FBconfigFolder = $defaultFolderDIR.DS.'configs'.DS;
  }
  
  if($FBconfigFolder){
    $this -> loadFile(array(
	  'name' => 'FBconfig.php',
	  'path' => $FBconfigFolder
	));
	$defaultPageConfigs = new FBappConfig(); 
	$MVCparams['pageMeta'] = $defaultPageConfigs -> pageConfig();
	$MVCparams['appMeta'] = $defaultPageConfigs -> appConfig();
	$MVCparams['appSecret'] = $defaultPageConfigs -> appSecret();
  }
  
  
  $newClassController = setNewName(strtolower($appClass), 'class');
  $newFileController = setNewName(strtolower($appClass), 'file');
  
  $isController = $this -> checkFile(array(
	'name' => $newFileController.'Controller.php',
	'path' => $appFolderDIR.DS.'controllers'.DS
  ));  
  
  if($isController){
	
	if(!defined('CURRENTFOLDER')){
	  define('CURRENTFOLDER',$currentFolder); 
	}
	$MVCparams['pageMeta']['currentPage'] = $appClass;
	array_push($MVCparams['paths'], $appClass);
	
	  
	$MVCparams['params']['controller'] = $appClass;
	//$classController = new $newClassController($MVCparams);
	
  }else{//if($isController){ 
	  
	  array_push($MVCparams['paths'], $appClass);
	  $isStatic = $this -> checkFile(array(
		'name' => strtolower($appClass.'.php'),
		'path' => $appFolderDIR.DS.'views'.DS
	  ));
	  
	  if(!$isStatic)
		  $isDefaultFileController =  $this -> checkFile(array(
			'name' => setNewName($defaultParams['classController'], 'file').'Controller.php',
			'path' => $appFolderDIR.DS.'controllers'.DS
		  ));
	  
	  if($isDefaultFileController){
		if(!defined('CURRENTFOLDER'))
		  define('CURRENTFOLDER',$currentFolder);
		  
		$newClassController = setNewName($defaultParams['classController'], 'class');
		//$classController = new $tempClassController($MVCparams);

		if(method_exists($newClassController,$appClass)){
		   array_unshift($params, $appClass);
		   
		}else{
		  $is404 = $this -> checkFile(array(
			'name' => '404Controller.php',
			'path' => $appFolderDIR.DS.'controllers'.DS
		  ));
		  if($is404)
		    $newClassController = '_404';
			//$classController = new _404($MVCparams);
		}
				

	  }else{ //if($isDefaultFileController){
  
	   $is404 = $this -> checkFile(array(
		  'name' => '404Controller.php',
		  'path' => $appFolderDIR.DS.'controllers'.DS
		));
	  }////// if($isDefaultFileController){
  }///// if($isController){ 

    if($is404 || $isStatic){
	  if(!defined('CURRENTFOLDER'))
		define('CURRENTFOLDER',$currentFolder); 
	}		
	
	if($isStatic){
	  $MVCparams['pageMeta']['currentPage'] = $appClass;
	  $MVCparams['params']['controller'] = 'staticpage';
	  $MVCparams['page'] = $appClass;
	  $newClassController = 'MVC\Staticpage';
	 // $classController = new MVC\Staticpage($MVCparams);
	}elseif($is404){ //else if($isStatic)
	 if($is404){
		$MVCparams['params']['controller'] = '_404';
		$newClassController = '_404';
		//$classController = new _404($MVCparams);
	  }else{
		$newClassController = '';
	  }
	}
  if(!$newClassController){//$classController){
	$currentFolder = strtolower($defaultParams['app']); 
  }
  
  if(!defined('CURRENTFOLDER'))
    define('CURRENTFOLDER',$currentFolder); 
/* $currentFolder = $appFolder;
  $appFolderDIR = 'applications'.DS.$currentFolder;
  */  
  if(!defined('APPLICATIONFOLDERDIR'))
    define('APPLICATIONFOLDERDIR','applications'.DS.$currentFolder);
	
  if(!defined('BASEHREF')){
	$basehref = $this -> getBaseHref();
	define('BASEHREF',$basehref);
	
	if($currentFolder !== strtolower($defaultParams['app']) && $currentFolder !== ''){
	  $basehref .= $currentFolder . DS;
    }
	define('SHORTLINK',$basehref);
  }
  
 
  
  $defaultMethod = str_replace('-','_',strtolower($defaultParams['method'])); 
  if($params)
	$appMethod = array_shift($params);
  else
    $appMethod = $defaultMethod;
  //$this -> jsonPrettyPrint(array($MVCparams, $appClass, $appFolderDIR, $isController));
  if(!$newClassController)//$classController)
	$newClassController = '_404';
	//$classController = new _404($MVCparams);

  if($MVCparams['pageMeta']['currentPage'] === $MVCparams['paths'][0])
	array_shift($MVCparams['paths']);  
	
  if(!method_exists($newClassController,str_replace('-','_',strtolower($appMethod)))){
	array_push($MVCparams['paths'], $appMethod);
	$appMethod = $defaultMethod;
  }
  
  $MVCparams['params']['method'] = $appMethod;
  
  if($params)
	   $MVCparams['paths'] = array_merge($MVCparams['paths'], $params);
  
  if($MVCparams['params']['method'] === $MVCparams['paths'][0])
    array_shift($MVCparams['paths']);
  
  $classController = new $newClassController($MVCparams);
 /* echo "Controller <br />";
   var_dump($newClassController);
  echo "<br />Method <br />";
  exit;*/
  

  
  
  /*echo json_encode(array($params, $MVCparams['paths']));
  exit;*/
  call_user_func(array($classController,$appMethod));
   
  function setNewName($string, $type='file'){
	$temp = explode('-',strtolower($string));
	$newString ='';
	$separator = $type === 'file' ? '-' : '_';
	foreach($temp as $word){
	  $word = ucfirst(strtolower($word));
	  $newString .= $newString === '' ? $word : $separator.$word;
	}
	if('type' === 'class'){
	  if(strlen($newString) > 0 && ctype_digit(substr($newString, 0, 1)))
	  $newString = '_' . $newString;
	}
	return $newString;
  }
?>