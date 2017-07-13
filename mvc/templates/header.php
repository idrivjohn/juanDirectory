<?
/*echo ' <!--';
  $this -> trace ($data);	
echo ' -->';*/

  $viewport = $data['viewport'] ? $data['viewport'] : 'device-width'; 
  if(!empty($data['projectnamespace'])){
	$ogIMG =  $data['projectnamespace'];
  }
  $defaultParams = $this -> getDefaultParams();
  $defaultFolder = 'applications'.DS.$defaultParams['app'];
  $shortlink = SHORTLINK;
 /* if(strtolower(CURRENTFOLDER) === strtolower($defaultParams['app'])){
    $ogURL = BASEHREF; 
	//$ogIMG =  'template';
  }else
    $shortlink .= CURRENTFOLDER.DS; 
	
	
	if(strtolower($data['currentPage']) !== strtolower($defaultParams['classController']))
    $shortlink .= $data['currentPage'].DS; 
	
  */
  
  
  $data['title'] = strip_tags($data['title']);
  $data['description'] = strip_tags($data['description']);
?>
<!DOCTYPE html>
<html lang="en">
<!--[if IEMobile 7 ]> <html dir="ltr" lang="en-US"class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html dir="ltr" lang="en-US" class="no-js ie6 oldie"> <![endif]-->
<!--[if IE 7 ]>    <html dir="ltr" lang="en-US" class="no-js ie7 oldie"> <![endif]-->
<!--[if IE 8 ]>    <html dir="ltr" lang="en-US" class="no-js ie8 oldie"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!--><html dir="ltr" lang="en-US" class="no-js"><!--<![endif]-->
<html xmlns:fb="http://ogp.me/ns/fb#">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<meta charset="utf-8">
<title><? echo $data['title']?></title>
<base href="<? echo BASEHREF ?>">
<link rel="canonical" href="<? echo $shortlink  ?>" />
<link rel="shortlink" href="<? echo $shortlink  ?>" />
<link rel="icon" type="image/png" href="img/favicon.png">
<meta name="viewport" content="width=<? echo $viewport ?>">
<meta property="og:site_name" content="<? echo $data['projectname']?>" />
<meta name="description" content="<? echo $data['description']?>">
<meta property="og:title" content="<? echo $data['title']?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<? echo SHORTLINK ?>">
<meta property="og:image" content="img/<? echo isset($ogIMG) ? $ogIMG.'/' : '' ?>fbicon_200x200.png">
<meta property="og:description" content="<? echo $data['description'] ?>">

<!--[if lt IE 9]>
<script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?
  $appPage = ($data['isPageApp'])?'true':'false';
  if($data['type']=='Desktop' && $data['isPageApp']){
	$redirectURI = "https://apps.facebook.com/".$data['appnamespace'];
	if(empty($_POST['signed_request'])){
	 echo '<script>if(window.name == "" || self == top) {top.location=\''.$redirectURI.'\';}</script>';
	}
  }

  $isCSS = $this -> checkFile(array(
   'name' => '/css/style-'.$data['projectnamespace'].'.css'
  )); 
  if($isCSS)
	echo '<link href="css/style-'.$data['projectnamespace'].'.css" rel="stylesheet" type="text/css" />';
?>
