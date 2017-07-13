<?
  define('DS', DIRECTORY_SEPARATOR);
  define('ROOTDIR',dirname(dirname(__FILE__)).DS);
  spl_autoload_register(function($className){
	$coreDirectories = array ('libraries', 'libraries'.DS.'cores', 'libraries'.DS.'vendors');
	$directories = array ('controllers', 'models');
	$parentDirectory = ROOTDIR.'applications'.DS;
	$fileName = str_replace('_','-',$className);
	$isFile = '';
	$prefix = 'MVC\\';
	$len = strlen($prefix);
	if (strncmp($prefix, $fileName, $len) == 0) { // 0 means prefix is found in the className string
	  $fileName = str_replace($prefix,"",$fileName);
	  $fileName = 'class.'.strtolower($fileName).'.php';
	  $directories = $coreDirectories;
	  $parentDirectory = ROOTDIR;
	}else{	  
	  foreach($directories as $directory){
	    $affix = ucfirst(strtolower(rtrim($directory,'s')));
			$newName = trim($fileName,'-').$affix.'.php';
			$activeDirectory = defined('CURRENTFOLDER') ? $parentDirectory.CURRENTFOLDER.DS : $parentDirectory;
			$isFile = checkClassFile(array(
				'fileName' => $newName,
				'directories' => array($directory),
				'parentDirectory' => $activeDirectory
			));
			if($isFile){
				break;
			}
	  }
	}
    
	if(!$isFile){
	  $isFile = checkClassFile(array(
	    'fileName' => $fileName,
			'directories' => $directories,
			'parentDirectory' => $parentDirectory
	  ));
	}
	
	if($isFile){
	  require $isFile;
	  return;
	}else{
	  echo $fileName. ' ' . $directories . ' ' .$parentDirectory;
	  $activeDirectory = defined(CURRENTFOLDER) ? $parentDirectory.CURRENTFOLDER.DS : $parentDirectory;
	  exit('Sorry "'.$activeDirectory.$fileName.'" file could not be loaded'); 
	}
  
  });
  
  function checkClassFile($classFile){
		$filePath = NULL;
	
	foreach($classFile['directories'] as $directory){
	  $path = $classFile['parentDirectory'].$directory.DS.$classFile['fileName'];
	  if(file_exists($path)){
			$filePath = $path;
			break;
	  }
	}
	return $filePath;
}
?>