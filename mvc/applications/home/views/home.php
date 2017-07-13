<?
  $this -> loadFile(array(
   'name' => 'header.php',
   'path' => 'templates'.DS,
   'data' => array_merge($this -> MVCdata['device'],$this -> MVCdata['pageMeta'])
  ));
  
/*echo '<!-- ';
$this -> trace($this -> MVCdata,'data');
echo ' -->';*/
?>
<style>
	form{
		position: relative;
	}
	form .notifications{
		color: #fff;
    padding: 8px 5px;
    text-align: center;
    background: #ed473b;
		display: none;
	}
	form.dirty .notifications{
		display: block;
	}
	.loader{
		position: absolute;
		z-index: 999;
    width: 100%;
    height: 100%;
		background: rgba(69,79,79,0.8);
		display: none;
	}
.loader > span,
.loader > span:after {
  border-radius: 50%;
  width: 10em;
  height: 10em;
}
.loader > span {
	display: block;
	margin: 60px auto;
  font-size: 6px;
  position: relative;
  text-indent: -9999em;
  border-top: 1.1em solid rgba(255, 255, 255, 0.2);
  border-right: 1.1em solid rgba(255, 255, 255, 0.2);
  border-bottom: 1.1em solid rgba(255, 255, 255, 0.2);
  border-left: 1.1em solid #ffffff;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation: load8 1.1s infinite linear;
  animation: load8 1.1s infinite linear;
}
	form.processing .loader{
		display: block;
	}
@-webkit-keyframes load8 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes load8 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
</style>
</head>
<body>
  <div id='browser'>
  <div id='browser-bar'>
    <div class='circles'></div>
    <div class='circles'></div>
    <div class='circles'></div>
    <p>videoLab</p>
    <span class='arrow entypo-resize-full'></span>
  </div>
  <div id='content'>
    <div id='left'>
      <div id='container'>
      <!-- START MAIN CONTENTS -->
      <!-- END MAIN CONTENTS -->
      </div>
			<p id="footer" class="other">© 2017 The Idea Laboratory Pte Ltd. All Rights Reserved</p>
    </div>
    <div id='right'>
      <form action="login" method="POST" id="frm-login" novalidate>
				<div class="loader"><span></span></div>
        <p>Login</p>
        <input placeholder="Email" type="email" name="email" required>
        <input placeholder="Password" type="password" name="password" required>
        <input name="form-signature" type="hidden" value="<?= $_SESSION[$this -> MVCdata['formSignature']] ?>" />
				<p class="notifications">Sorry invalid login.</p>
        <input value="Sign me in" type="submit" id="btn-login" >
      </form>
      <p>TIL PRO-duction</p>
      <p class='other entypo-location'>33 Ubi Avenue 3, Vertex #04-74 Singapore 408868</p>
      <p class='other entypo-phone'>(+65) 6745 4332</p>
    </div>
  </div>
</div>
  <?
    $this -> loadFile(array(
     'name' => 'footer.php',
     'path' => 'templates'.DS,
     'data' => array_merge($this -> MVCdata['device'],$this -> MVCdata['pageMeta'])
    ));
  ?>
  <script src="js/form-validator.js"></script>
  <script>
		var validate= new IPASURI();
		$(document).ready(function(){
			validate.suriin({form:'#frm-login',button:'#btn-login', callback:'login'});
		});
		function login($form){
			var URI = $form.attr('action'),
					method = $form.attr('method'),
					data = $form.serializeArray();
			$.ajax({
				type: method,
				dataType: 'JSON',
				url: URI,
				data: data,
				success: function(response) {
					$form.removeClass('processing');//.addClass('dirty')
					console.log(response);
				},
				error: function(event, jqXHR, ajaxSettings, thrownError) {
					alert('[event:' + JSON.stringify(event) + '], [jqXHR:' + jqXHR + '], [ajaxSettings:' + ajaxSettings + '], [thrownError:' + thrownError + '])');
					$form.removeClass('processing');
				}
			});
		};
	</script>
</body>
</html>
