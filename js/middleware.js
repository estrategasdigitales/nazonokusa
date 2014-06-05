$(function(){
	var opts = {
		lines: 13, // The number of lines to draw
		length: 35, // The length of each line
		width: 10, // The line thickness
		radius: 30, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 0, // The rotation offset
		direction: 1, // 1: clockwise, -1: counterclockwise
		color: '#ffffff', // #rgb or #rrggbb or array of colors
		speed: 1.5, // Rounds per second
		trail: 50, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spinner', // The CSS class to assign to the spinner
		zIndex: 2e9, // The z-index (defaults to 2000000000)
		top: 'auto', // Top position relative to parent in px
		left: 'auto' // Left position relative to parent in px
	};
	var target = document.getElementById('foo');

	// $('#form_vertical_nueva').validate({
	// 	rules: {
	// 		nombre: {
	// 			required: true,
	// 			minlength: 3
	// 		},
	// 	},
	// 	messages: {
	// 		nombre: {
	// 			required: "Por favor, ingresa el Nombre de la Vertical.",
	// 			minlength: "Este campo debe ser de al menos 3 caracteres."
	// 		},
	// 	}
	// });

	// $('#form_categoria_nueva').validate({
	// 	rules: {
	// 		nombre: {
	// 			required: true,
	// 			minlength: 3
	// 		},
	// 	},
	// 	messages: {
	// 		nombre: {
	// 			required: "Por favor, ingresa el Nombre de la Categoría.",
	// 			minlength: "Este campo debe ser de al menos 3 caracteres."
	// 		},
	// 	}
	// });

	// $("#form_trabajo_nuevo1").validate({
	// 	rules: {
	// 		nombre: {
	// 			required: true,
	// 			minlength: 3
	// 		},
	// 		"url-origen": {
	// 			required: true
	// 		},
	// 		"destino-local": {
	// 			required: true
	// 		},
	// 		"destino-net": {
	// 			required: true
	// 		},
	// 		"formato[]": {
	// 			required: true
	// 		},
	// 	},
	// 	messages: {
	// 		nombre: {
	// 			required: "Por favor, ingresa un nombre para el trabajo",
	// 			minlength: "Este campo debe ser de al menos 3 caracteres."
	// 		},
	// 		"url-origen": {
	// 			required: "Por favor, ingresa la URL del feed"
	// 		},
	// 		"destino-local": {
	// 			required: "Por favor, ingresa un destino para el feed creado"
	// 		},
	// 		"destino-net": {
	// 			required: "Por favor, ingresa un destino para el feed creado"
	// 		},
	// 		"formato[]": "Selecciona un formato de salida",
	// 	}
	// });

	$('#read_feed_form').submit(function(){
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			beforeSubmit: function(){
				$('#foo').css('display','block');
			},
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messages').html(data);
					// $('#messages').hide().slideDown("slow");
					// $("#messages").delay(2500).slideUp(800, function(){
					// $("#messages").html("");
					// });
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					window.location.reload();
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messages').html(data);
			}
		});
		return false;
	});

	$('#form_usuario_nuevo').submit(function(){
		$('#foo').css('display','block');
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messages').css('display','block');
					$('#messages').addClass('alert-danger');
					$('#messages').html(data);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					window.location.href = 'usuarios';
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messages').css('display','block');
				$('#messages').addClass('alert-danger');
				$('#messages').html(data);
			}
		});
		return false;
	});

	$('#form_login').submit(function(){
		$('#foo').css('display','block');
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messages').css('display','block');
					$('#messages').addClass('alert-danger');
					$('#messages').html(data);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					window.location.reload();
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messages').css('display','block');
				$('#messages').addClass('alert-danger');
				$('#messages').html(data);
			}
		});
		return false;
	});

	$('#form_trabajo_nuevo').submit(function(){
		$('#foo').css('display','block');
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messages').css('display','block');
					$('#messages').addClass('alert-danger');
					$('#messages').html(data);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					window.location.href = 'trabajos';
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messages').css('display','block');
				$('#messages').addClass('alert-danger');
				$('#messages').html(data);
			}
		});
		return false;
	});
});

function desplegar(item){
	if($('.'+item).css('display')==='none'){
		$('.'+item).slideDown();
	}
}

function desplega(element){
	if(!$(element).is(":checked")){
		$(element).parent().parent().children("div").slideUp(300,function (){
			$.each($(element).parent().parent()
			.children("div").children('label')
			.children('input'),function(){
				$(this).prop("checked",false);
				if($(this).parent().parent().children("div").size()>0){
					desplega(this);
				}
			});
		});	
	}else{		
		$(element).parent().parent().children("div").slideDown(300,function (){
			$.each($(element).parent().parent()
			.children("div").children('label')
			.children('input'),function(){
				$(this).prop("checked",true);
				if($(this).parent().parent().children("div").size()>0){
					desplega(this);
				}
			});
		});			
	}
}

function datosAdicionales(check){
	if($(check).prop("checked")==true){
		if($(check).val()==="rss2"){
			$(".campos_rss").slideDown();
			$("#channel_title").val("");
			$("#channel_link").val("");
			$("#channel_description").val("");
		}
		if($(check).val()==="jsonp"){
			$(".campos_jsonp").slideDown();
			$("#nom_funcion").val("");
		}
	}else{
		if($(check).val()==="rss2"){
			$(".campos_rss").slideUp();
			$("#channel_title").val("");
			$("#channel_link").val("");
			$("#channel_description").val("");
		}
		if($(check).val()==="jsonp"){
			$(".campos_jsonp").slideUp();
			$("#nom_funcion").val("");
		}
	}
}

function ShowDialog(url,nombre) {
    $('#spanMessage').html('¿Está seguro(a) que desea eliminar el usuario: '+nombre+'?');
    $("#dialogConfirm").dialog({
        resizable: false,
        height: 200,
        width: 400,
        modal: true,
        title: 'Eliminar',
        buttons: {
            'Aceptar': function () {
            	window.location.href = url;
            },
                'Cancelar': function () {
                $(this).dialog("close");
            }
        }
    });
}

function ShowDialogT(url,nombre) {
    $('#spanMessage').html('¿Está seguro(a) que desea eliminar el trabajo: '+nombre+'?');
    $("#dialogConfirm").dialog({
        resizable: false,
        height: 200,
        width: 400,
        modal: true,
        title: 'Eliminar',
        buttons: {
            'Aceptar': function () {
            	window.location.href = url;
            },
                'Cancelar': function () {
                $(this).dialog("close");
            }
        }
    });
}

function ShowDialog2(url,nombre) {
    $('#spanMessage').html('¿Está seguro(a) que desea eliminar la categoría: '+nombre+'?');
    $("#dialogConfirm").dialog({
        resizable: false,
        height: 200,
        width: 400,
        modal: true,
        title: 'Eliminar',
        buttons: {
            'Aceptar': function () {
            	window.location.href = url;
            },
                'Cancelar': function () {
                $(this).dialog("close");
            }
        }
    });
}

function ShowDialog3(url,nombre) {
    $('#spanMessage').html('¿Está seguro(a) que desea eliminar la vertical: '+nombre+'?');
    $("#dialogConfirm").dialog({
        resizable: false,
        height: 200,
        width: 400,
        modal: true,
        title: 'Eliminar',
        buttons: {
            'Aceptar': function () {
            	window.location.href = url;
            },
                'Cancelar': function () {
                $(this).dialog("close");
            }
        }
    });
}

function ShowDialog4() {
	$("#agregarCampo #nuevo_nombre").val("");
    $("#agregarCampo").dialog({
        resizable: false,
        height: 200,
        width: 400,
        modal: true,
        title: 'Nuevo Campo',
        buttons: {
            'Aceptar': function () {
            	$(".agregar_campo").before('<div class="form-group">'+
            		'<label for="channel_description" class="col-sm-3 col-md-2 control-label">'+$("#agregarCampo #nuevo_nombre").val()+'</label>'+
            		'<div class="col-sm-9 col-md-10">'+
            		'<input type="hidden" name="claves_rss[]" value="'+ $("#agregarCampo #nuevo_nombre").val().toLowerCase()+'">'+
            		'<input type="text" class="form-control" id="channel_description" name="valores_rss[]">'+
            		'</div></div>');            	
                $(this).dialog("close");
            },
                'Cancelar': function () {
                $(this).dialog("close");
            }
        }
    });
}