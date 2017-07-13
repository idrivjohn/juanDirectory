<?
  $this -> MVCdata['paths'][0] = '404';
  $this -> loadFile(array(
   'name' => 'header.php',
   'path' => 'templates'.DS,
   'data' => array_merge($this -> MVCdata['device'],$this -> MVCdata['pageMeta'])
  ));
/*echo '<!-- ';
$this -> trace($this -> MVCdata,'data');
echo ' -->';*/
?>
</head>
<body class="colorway0">
  <div id="canvas" class="gridcontainer grid1440">
    <div class="cellrow">
      <div class="cell3"><img src="img/templates/logo.png" alt="ipampanga.com" class="main-logo device-width"/></div>
      <div class="cell9">
        <div  id="mainnavs">
          <ul class="clean inline fs16">
            <li class="omega">Ooopps.<br />Sorry, something went wrong.<br />The page you are looking for either does not exist or has been removed.</li>
          </ul>
        </div><!-- mainnavs -->
      </div><!-- cell9 -->
    </div><!-- cellrow -->

  <?
    $this -> loadFile(array(
     'name' => 'footer.php',
     'path' => 'templates'.DS,
     'data' => array_merge($this -> MVCdata['device'],$this -> MVCdata['pageMeta'])
    ));
  ?>
</body>
</html>