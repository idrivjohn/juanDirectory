function mobilecheck() {
  var check = false;
  (function(a){if(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
}

function hyperlinkMe(url,targetWindow){
  if(typeof targetWindow == 'undefined'){
	window.location.href=url;
  }else{
	target = '_'+targetWindow;
	window.open(url,target);
  }
}

function showMe(object){
  closeChild = ''
  element = (object.hasOwnProperty('element'))?object.element:'';
  child = (object.hasOwnProperty('child'))?object.child:'';
  callback = (object.hasOwnProperty('callback'))?object.callback:'';
  if(child){
	if($.isArray(child)){
	  for(n=0;n<=child.length;n++){
		$('#'+child[n]).show();
		closeChild = (n==0)?', Array("'+child[n]+'"':closeChild+', "'+child[n]+'"';
		closeChild = (n==child.length)?closeChild+')':closeChild;
	  }
	}else{
	  $('#'+child).show();
	  closeChild = child;
	}
  }
  $('#'+element).fadeIn(function(){
	 pos = $(this).offset();
	 $(document).scrollTop(pos.top);
	$('.xbutton').attr('onClick',"hideMe({'element':'"+element+"','child':'"+child+"','callback':'"+callback+"'});");
  });
  
}

function hideMe(object){
  element = (object.hasOwnProperty('element'))?object.element:'';
  child = (object.hasOwnProperty('child'))?object.child:'';
  callback = (object.hasOwnProperty('callback'))?object.callback:'';
  $('#'+element).fadeOut(function(){
    if(child){
	  if($.isArray(child)){
		for(n=0;n<=child.length;n++){
		  $('#'+child[n]).css('display','none');
		}
	  }else{
		$('#'+child).css('display','none');
	  }
	}
	if(callback !== ''){
	  enforceCheckVar(callback,'function');	
	}
  });
}

function enforceCheckVar(varName,varType,varData){
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

function runAjax(form){
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
	  timeout: 300000,
	  success: function(response){
	  $("body").css("cursor", "default");
	  try{
		jsonData = JSON.parse(response);
	  }catch(e){	
		jsonData = response;
	  }
	  enforceCheckVar(CALLBACK,'function',jsonData);
	  },
	  error: function(jqXHR, textStatus, errorThrown){
		enforceCheckVar(CALLBACK,'function',{'error':handleError(jqXHR, textStatus, errorThrown)});
		$("body").css("cursor", "default");
		return false;
	  }
	});
}

function submitForm(form){
  URL = (form.hasOwnProperty('URL'))?form.URL:'';
  DATA = (form.hasOwnProperty('DATA'))?form.DATA:'';
  if(URL!=='' && DATA !==''){
	formData='';
	$.each(DATA, function(key,value){
		formData+='<input type="text" name="' + key + '" value="' + value + '" />';
	});
	$('<form style="display:none" method="POST"></form>').attr('action',URL).html(formData).appendTo(document.body).submit();
	/*$('<form style="display:none">',{
	  'action':URL,
	  'html':formData
	}).appendTo(document.body);
	trace(formData);*/
	
  }
  //CALLBACK = (form.hasOwnProperty('CALLBACK'))?form.CALLBACK:'';
  //$.post(URL, DATA);
  /*
  , function(data){
    enforceCheckVar(CALLBACK,'function',data);
  }
  */
}
function getObjects(obj, key) {
  var objects = new Array;
  for (var i in obj) {
	if(i===key){
	  objects.push(obj);
	}else{
	  if (typeof obj[i] == 'object') {
		objects = objects.concat(getObjects(obj[i], key));
	  }
	}
  }
  if(objects.length>0){
	objects = (objects.length>1)?objects:objects[0];
	return objects;
  }
}

function handleError(jqXHR, textStatus, errorThrown){
  result ={'status':true,'message':'Connection status "' + textStatus + '".'};
  return result; //{'error':result};
   
}

function trace(msg){
  try {
	if (window.console && window.console.log) {
	  console.log('Console:', msg);
	}
  } catch (e) {
	return false;
  }
}