/*

facebook API V2.1 JS SDK wrapper
Author: John Virdi V. Alfonso
Email: jva.ipampanga@gmail.com
Date: 11 September 2014
Version: 1.02

*/
function FACEBOOK(FBsetings){
  FBappID = (FBsetings.hasOwnProperty('FBappID'))?FBsetings.FBappID:'';
  FBlocale = (FBsetings.hasOwnProperty('FBlocale'))?FBsetings.FBlocale:'en_US';
  FBappPermissions = (FBsetings.hasOwnProperty('FBappPermissions'))?FBsetings.FBappPermissions:'';
  FBpageID = (FBsetings.hasOwnProperty('FBpageID'))?FBsetings.FBpageID:'';
  APIversion = 'v2.4';
  this.settings ={
	appID:FBappID,
	locale: FBlocale,
	permissions: FBappPermissions
  };
  
  this.loaded = false;
  
  this.user={
	authResponse:'',
	me:'',
	permission:'',
	status:''	
  };
  
  var parent = this;
  
  init = function(vars){
	if(typeof FB === 'undefined'){
	  FB.init({
		appId      : vars.appID,
		cookie	 : true,
		xfbml      : true,
		version    : APIversion
	  });
	}
	
	//parent.updateUserStatus();
	FB.XFBML.parse();
	FB.Canvas.setAutoGrow();
	// FB.Canvas.scrollTo(0,0);
	FB.Event.subscribe('edge.create', function(response) {
	  enforceCheckVar('FBedgeCreate','function');
	});
	FB.Event.subscribe('edge.remove', function(response) {
	  enforceCheckVar('FBedgeRemove','function');
	});
	parent.loaded = true;	
	$(document).trigger('facebook.loaded'); 
  };

  function load(){
	window.fbAsyncInit = function() {
	  init(parent.settings);
	};
	
	/*(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/"+parent.settings.locale+"/sdk.js#xfbml=1&appId="+parent.settings.appID+"&version=v2.1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));*/
	
	(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/"+parent.settings.locale+"/sdk.js#xfbml=1&appId="+parent.settings.appID+"&version="+APIversion;
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	//console.log(APIversion);
  };
  
  function isLoaded(){
	return parent.loaded;
  };
  
  function updateUserStatus(callback){
	FB.getLoginStatus(function(response) {
	  arrayGoals = new Array();
	  goals = 4;
	  parent.user.status = response.status;
	  arrayGoals.push(response.status);
	  setGoal();
	  if(response.authResponse){
		parent.user.authResponse = response.authResponse;
		arrayGoals.push(response.authResponse);
		FB.api('/me', function (response) {
		  parent.user.me = response;
		  arrayGoals.push(response);
		  setGoal();
		});
		FB.api('/me/permissions', function (response) {
		  parent.user.permission = response.data[0];
		  arrayGoals.push(response);
		  setGoal();
		});
	  }else{
		enforceCheckVar(callback,'function',parent.user);	
	  }
	});
	
	function setGoal(){
	  if(goals == arrayGoals.length){
		if(typeof callback !== 'undefined'){
		  enforceCheckVar(callback,'function',parent.user);	
		}
	  }
	}
	
  };
  
  function login(){
	$("body").css("cursor", "progress");
	if($('.notifications').length>0){
	  $('.notifications').html('connecting to facebook...');
	}
	if($('.fb-login-button').length>0){
	  $('.fb-login-button').hide();
	}
	if(parent.user.status!=='connected'){
	  FB.login(function(response) {
		if (response.authResponse) {
		  parent.user.authResponse = response.authResponse;
		  parent.user.status = response.status;
		  enforceCheckVar('FBendorsed','function',parent.user);	
		} else {
		  enforceCheckVar('FBdeclined','function');
		}
		if($('.fb-login-button').length>0){
		  $('.fb-login-button').show();
		}
		$("body").css("cursor", "default");
	  },{scope: parent.settings.permissions});
	}else{
	  enforceCheckVar('FBendorsed','function',parent.user);	
	}
  }
  
  function saveOauthToken(FBuser, baseURL, callback){
	var data = {'oauth_token':FBuser.authResponse.accessToken};
	form = { URL:baseURL, DATA:data, CALLBACK:callback	}
	runAjax(form);
  }
  
  function UIrequest(settings, type,baseurl) {
	FB.api('/me', function(response) {
	  var obj = settings;
	  recipients = '';
	  function callback(response) {  
	    trace(response);
		if(typeof response!== 'undefined'){
		  if ((typeof response.post_id != 'undefined') || (typeof response.request != 'undefined')) {
			ajaxPost(type, response,baseurl);
		  } else {
			trace('Facebook request was not successful.');
		  }
		}else {
		  trace('Facebook request was not successful.');
		}
	  }
	  
	  try {
		FB.ui(obj, callback);
	  } catch (err) {
		
		trace(err);
		// TODO handle payment error
	  }
	  
	  
	});
  }
  
  function UIlike(pageID) {
	var page = pageID || FBpageID;
	var windowHeight = $(window).height();
	var windowWidth = $(window).width();
	var frameHeight = 160;
	var frameWidth = 325;
	var marginTop =(windowHeight - frameHeight) / 2;
	if($('#likeLBx').length === 0){
	  $('<div/>',{
		  'id': 'likeLbx',
		  'css':{'height':windowHeight, 'width':windowWidth, 'background':'url(img/transparentBlack.png)','position':'fixed', 'top':0, 'left':0, 'z-index':999999, 'display':'none'}
	  }).appendTo('body');
	  $('<div/>',{
		  'id': 'likeHolder',
		  'css':{'height':frameHeight, 'width':(frameWidth), 'background-color':'#E9EAED', 'opacity':1, 'margin': marginTop + 'px auto 0','overflow':'hidden', 'border':'5px solid #E9EAED', 'position':'relative'}
	  }).appendTo('#likeLbx');
	   
		$('#likeHolder').append('<a href="javascript:void(0)" onClick="hideLike(\'likeLbx\')" title="close" style="background:#E9EAED; color:#4E5565; padding:5px; display:inline-block; position:absolute; right:0; top:0; z-index:2">CLOSE</a>');
	  $('#likeLbx').fadeIn();
	  $('<div />', {
		  'data-href': 'https://www.facebook.com/' + page,
		  'class' :'fb-page',
		  'data-small-header':'true',
		  'data-adapt-container-width':'true',
		  'data-width' : frameWidth,
		  'data-hide-cover':'false',
		  'data-show-facepile':'true',
		  'data-show-posts':'false',
		  'hide_cta' : 'true'
	  }).appendTo('#likeHolder');
	  FB.XFBML.parse();
	}else{
	  $('#likeLbx').fadeIn();
	}
	
  }
  
  function ajaxPost(shareType,shareData,baseurl) {
	  var fb_uid = '';
	  FB.api('/me', function(response) {
		if (response.id){
		  fb_uid = response.id;
		}
		form = {'form_action':'processform','form_method':'insert-log_'+shareType,'app_id':parent.settings.appID,'fbuid':fb_uid,'device':''};
		for (key in shareData) {
		  if (shareData.hasOwnProperty(key)) {
			if(key==='to'){
			  form['recipient'] =shareData[key];
			}else{
			  form[key] =shareData[key];
			}
		  }
		}
		runAjax({DATA:form, URL:baseurl+'ajaxify/sharing', CALLBACK:'post'+shareType});
	  });
	}
  
  this.load = load;
  this.isLoaded = isLoaded;
  this.updateUserStatus = updateUserStatus;
  this.login = login;
  this.saveOauthToken = saveOauthToken;
  this.UIrequest = UIrequest;
  this.UIlike = UIlike;
};

$(document).bind('facebook.loaded', function() {
  enforceCheckVar('FBonLoaded','function');
});

function hideLike(element){
	$('#' + element).fadeOut();
}

if(typeof window['enforceCheckVar'] === 'undefined' ){
  window.enforceCheckVar = function (varName,varType,varData){
	if(typeof window[varName] === varType ){
	  if(varType ==='function'){
		var fn = window[varName];
		fn(varData);
	  }else{
		window[varName] = varData;
	  }
	}else{
	  trace(varName+' is not a '+varType);
	}
  }
}

if(typeof window['runAjax'] === 'undefined' ){
  window.runAjax = function(form){
	URL = (form.hasOwnProperty('URL'))?form.URL:'';
	TYPE = (form.hasOwnProperty('TYPE'))?form.TYPE:'POST';
	DATA = (form.hasOwnProperty('DATA'))?form.DATA:'';
	CALLBACK = (form.hasOwnProperty('CALLBACK'))?form.CALLBACK:'';
	$("body").css("cursor", "progress");
	$.ajax({
	  url : URL,
	  type: TYPE,
	  data :DATA,
	  datatype:'json',
	  async: true,
	  timeout: 15000,
	  success: function(response){
	  $("body").css("cursor", "default");
	  try{
		jsonData = JSON.parse(response);
	  }catch(e){	
		jsonData = response;
	  }
	  enforceCheckVar(CALLBACK,'function',jsonData);
	  },
	  error: function(){
		handleError;
		$("body").css("cursor", "default");
		return false;
	  }
	});
  }
}
if(typeof window['trace'] === 'undefined' ){
  window.trace = function(msg){
	try {
	  if (window.console && window.console.log) {
		console.log('Console:', msg);
	  }
	} catch (e) {
	  return false;
	}
  }
}
