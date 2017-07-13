<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-48081338-1', 'auto');
  ga('send', 'pageview');
</script>
<?
/*echo '<!-- ';
  $this -> trace($data);
echo ' -->';*/

echo ($data['appname'])? "\n".'<div id="fb-root"></div>'."\n":'';
?>
<script src="js/vendor/jquery.min.js"></script>
<script src="js/vendor/modernizr.min.js"></script>
<script src="js/vendor/ios-orientationchange-fix.js"></script>
<?
 $isJSfile = $this -> checkFile(array(
   'name' => '/js/js-'.$data['projectnamespace'].'.js'
  )); 
  if($isJSfile)
    echo '<script src="js/js-'.$data['projectnamespace'].'.js"></script>';
?>
<? if(isset($data['appname'])){
	$appPage = ($data['isPageApp'])?'true':'false';
	echo '<script src="js/script-jva-facebook.js"></script>'."\n";
	echo "<script>\n  var FBapp = {'AppID':'".$data['appid']."','AppPermissions':'".$data['permissions']."', 'AppPage':".$appPage."};\n  facebook = new FACEBOOK({'FBappID':FBapp.AppID,'FBappPermissions':FBapp.AppPermissions});\n  facebook.load();\n</script>\n";
}?>