/*****
IPASURI is a jquery plugin to validate forms
Created by: John Virdi V. Alfonso (jva.ipampanga@gmail.com)
Date: 05 May 2017

INSTANTIATION
	var validate= new IPASURI();
	validate.suriin({form: '{#taget-form}', button:'{#submit-button}', callback: '{submit callback function name}'); callback will return the jquery form
	
Optional form novalidate attribute to disable html5 form validation

REQUIRED FIELDS
	Just add the attribute required on the element. Element needs to have the name attribute.

REQUIRED OPTION/CHECKBOX GROUPS
	Put the required attribute on each element.
	
*****/
function IPASURI(){
	var mgaPinapasuri = {},
			attrID = 'validation-id',
			classEditingField = 'analyzing',
			classProcessing = 'processing',
			classTargetFormatting = '.status-indicators',
			classDirty = 'dirty',
			resback = false;
	console.log('JVA form validator loaded');
	this.suriin = function(detalye){ // myForm is the jquery form element to target, myButton is the button that will submit the form
		var papeles = typeof detalye.form !== typeof undefined ? $(detalye.form) : null,
				butonNaMahalaga = typeof detalye.button !== typeof undefined ? $(detalye.button) : null,
				resback = typeof detalye.callback !== typeof undefined ? detalye.callback : false;
		
		
		if(papeles !== null){
			classTargetFormatting = typeof detalye.formatter !== typeof undefined ? detalye.formatter : classTargetFormatting;
			classProcessing = typeof detalye.busy !== typeof undefined ? detalye.busy : classProcessing;
			classDirty = typeof detalye.dirty !== typeof undefined ? detalye.dirty : classDirty;
			classEditingField = typeof detalye.fieldStatus !== typeof undefined ? detalye.fieldStatus : classEditingField;
			var ngalan = Math.random().toString(16).slice(-5);
			papeles.attr(attrID, ngalan);
			mgaPinapasuri[ngalan] = {};
			
			var etiketa = papeles.find('label');
			etiketa.each(function(){
				$(this).addClass('untouched');
			});
			
			if(resback !== false){
				papeles.bind('submit', function (event) {
					event.preventDefault();
					window[resback]($(this));
				});
			}
			
			papeles.find('input[type="checkbox"], input[type="radio"]').on('click change',function(){
				pagtibayin($(this));
			});
			
			papeles.find('input, textarea, select').bind('input propertychange', function(e) { //input propertychange,keydown keypress keyup
				var pinindot = e.which || e.keyCode || e.charCode,
						katayuan = true,
						laman = $(this).prop('value');
				katayuan = paglilimbang($(this));

				if(pinindot == 8 || pinindot == 37 || pinindot == 38 || pinindot == 39 || pinindot == 40 || pinindot == 46) //keypress is not backspace, arrows and del
					katayuan = true;

				if(katayuan == false)
					e.preventDefault();
	
				return katayuan;
			});
			
		}else{
			console.log('Form to validate not found.');
		}
		
		if(butonNaMahalaga !== null){	
			butonNaMahalaga.on('click',pagpasyahan);
			//butonNaMahalaga.prop('disabled','disabled');
		}else{
			console.log('Validator button not found.');
		}
		
		if(butonNaMahalaga !== null && papeles !== null){
			console.log('Validator initialized');
		}
	}
	
	function paglilimbang(ito){
		var balido = true,
				nilalaman = ito.val(),
				tipo = ito.prop('type'),
				haba = ito.attr('length'),
				pangalan = ito.attr('name'),
				pinakaHaba = ito.attr('max-length'), //property maxlength not crossbrowser compatible
				tamangHaba = ito.attr('minlength'); //property minlength not crossbrowser compatible
	
		ito.siblings('label[for="'+ pangalan +'"]').addClass(classProcessing).removeClass('empty untouched');
		
		if(nilalaman !== null && typeof nilalaman !== typeof undefined && nilalaman !== ''){
			var pattern = ito.attr('pattern');
			
			if(tipo == 'email' || tipo == 'number' || tipo == 'tel'){
				switch(tipo){
					case 'email': pattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/; break;//'/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
					case 'number': pattern = /^\d*$/; break;
					default : pattern = /^\d*$/;
				}
			}
			
			if(typeof pattern !== typeof undefined){
				var  regexs = new RegExp (eval(pattern)); 	
				balido = regexs.test(nilalaman);
			}

			if(typeof haba !== typeof undefined || typeof pinakaHaba !== typeof undefined || typeof tamangHaba !== typeof undefined && balido){
				
				if(typeof haba !== typeof undefined){
					pinakaHaba = haba;
					tamangHaba = haba;
				}
				
				if(nilalaman.length > pinakaHaba || nilalaman.length < tamangHaba)
					balido = false;
			}
		}else if(typeof ito.prop('required') !== typeof undefined){
			balido = false;
			ito.siblings('label[for="'+ pangalan +'"]').addClass('empty').removeClass(classProcessing);
		}
		
		baguhinAngKlase(ito, balido);
		return balido;
	}

	function baguhinAngKlase(elemento, estado){
		var laman = elemento.val(),
				kailangan = elemento.prop('required');
		if(estado){
			if(elemento.parents(classTargetFormatting).length)
				elemento.parents(classTargetFormatting).removeClass('invalid').addClass('valid');

			elemento.removeClass('invalid');
			elemento.addClass('valid');
		}else{
			if(elemento.parents(classTargetFormatting).length)
				elemento.parents(classTargetFormatting).addClass('invalid').removeClass('valid');

			elemento.addClass('invalid').removeClass('valid');
		}
		if(laman == '' && !kailangan){
			if(elemento.parents(classTargetFormatting).length)
				elemento.parents(classTargetFormatting).removeClass('invalid').removeClass('valid');

			elemento.removeClass('invalid').removeClass('valid');
		}
	}

	function pagtibayin(ito){
		var kategorya = ito.prop('type'),
				pangalan = ito.prop('name'),
				nilalaman = [],
				balido = false;
		
		$('[name="'+pangalan+'"').each(function(){
			var elemento = $(this);
			if(elemento.prop('checked'))
				nilalaman.push(elemento.prop('value'));
		});
		
		if(nilalaman.length)
			balido = true;
		
		$('[name="'+pangalan+'"').each(function(){
			baguhinAngKlase($(this), balido);
		});
		
		return balido;
	}

	function pagpasyahan(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		var $form = $(this).parents('form'),
				datos = {};
		$form.addClass(classProcessing);
		$($form.prop('elements')).each(function(){
		//console.log($(this).prop('required'),$(this).prop('type'), $(this).prop('name'),$(this).prop('value'),$(this).prop('checked'));
			if($(this).prop('required') == true){
				var nilalaman = $(this).prop('value');
				var kategorya = $(this).prop('type');
				if(kategorya == 'radio' || kategorya == 'checkbox'){
					if($(this).prop('checked') || !($(this).prop('name') in datos))
						datos[$(this).prop('name')] = $(this).prop('checked') ? nilalaman : '';
				}else
					datos[$(this).prop('name')] = nilalaman;
			}
		});

		$($form.serializeArray()).each(function(index, value){
			if(!(value.name in datos) && (value.value))
				datos[value.name] = value.value;
		});

		for(var pangalan in datos){
			var elemento = $form.find('[name="' + pangalan + '"]'),
					kategorya = $(elemento).prop('type'),
					balido = false,
					laman = elemento.val();
			if(kategorya == 'radio' || kategorya == 'checkbox'){
				balido = pagtibayin(elemento);
			}else{
				balido = paglilimbang(elemento);
			}
			datos[pangalan] = balido ? datos[pangalan] : null;
		}
		
		mgaPinapasuri[$form.attr(attrID)] = datos;
		hulingPagsuri($form, $(this));
	};
	
	function hulingPagsuri(sinusuri, nagpasuri){
		var pantukoy = sinusuri.attr(attrID),
				mgaSusuriin = mgaPinapasuri[pantukoy],
				katayuan = true;
		if(typeof mgaSusuriin !== typeof undefined){
			sinusuri.addClass(classDirty);
			for(var elemento in mgaSusuriin){
				if(mgaSusuriin[elemento] == null){
					katayuan = false;
					nagpasuri.prop('disabled',false);
					sinusuri.removeClass(classProcessing);
					return katayuan;
				}
			}
		}
		
		sinusuri.removeClass(classDirty);
		//if(resback == false)
			sinusuri.submit();
		//else
		//	window[resback]();
	}
	
}// END IPASURI



