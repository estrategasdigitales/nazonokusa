$(function(){
	var opts = {
		lines: 13, // The number of lines to draw
		length: 25, // The length of each line
		width: 10, // The line thickness
		radius: 35, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 0, // The rotation offset
		direction: 1, // 1: clockwise, -1: counterclockwise
		color: '#f48120', // #rgb or #rrggbb or array of colors
		speed: 1, // Rounds per second
		trail: 100, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spinner', // The CSS class to assign to the spinner
		zIndex: 2e9, // The z-index (defaults to 2000000000)
		top: '50%', // Top position relative to parent in px
		left: '50%' // Left position relative to parent in px
	};
	var target = document.getElementById('foo');

	$('.btn-toggle').click(function(){
		$(this).find('.btn').toggleClass('active');
		if ( $(this).find('.btn-danger').size() > 0 ){
			$(this).find('.btn').toggleClass('btn-default');
		} 

		if ( $(this).find('.btn-success').size() > 0 ){
			$(this).find('.btn').toggleClass('btn-default');
		}

		$(this).find('.btn').toggleClass('btn-default');
	});

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
					window.location.href = '../usuarios';
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

	$('#form_categoria_nueva').submit(function(){
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
					window.location.href = 'categorias';
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

function handlerProgramm(status, uidjob){
	var opts = {
		lines: 13, // The number of lines to draw
		length: 25, // The length of each line
		width: 10, // The line thickness
		radius: 35, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 0, // The rotation offset
		direction: 1, // 1: clockwise, -1: counterclockwise
		color: '#f48120', // #rgb or #rrggbb or array of colors
		speed: 1, // Rounds per second
		trail: 100, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spinner', // The CSS class to assign to the spinner
		zIndex: 2e9, // The z-index (defaults to 2000000000)
		top: '50%', // Top position relative to parent in px
		left: '50%' // Left position relative to parent in px
	};

	var target = document.getElementById('foo');

	$('#foo').css('display','block');
	var spinner = new Spinner(opts).spin(target);
	$.post('cms/job_process', {status: status, uidjob: uidjob}, function(data){
		if(data != true){
			spinner.stop();
			$('#foo').css('display','none');
			$('#messages').css('display','block');
			$('#messages').addClass('alert-danger');
			$('#messages').html(data);
		}else{
			spinner.stop();
			$('#foo').css('display','none');
		}
	});
}

function deleteJob(uidjob){
	var opts = {
		lines: 13, // The number of lines to draw
		length: 25, // The length of each line
		width: 10, // The line thickness
		radius: 35, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 0, // The rotation offset
		direction: 1, // 1: clockwise, -1: counterclockwise
		color: '#f48120', // #rgb or #rrggbb or array of colors
		speed: 1, // Rounds per second
		trail: 100, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spinner', // The CSS class to assign to the spinner
		zIndex: 2e9, // The z-index (defaults to 2000000000)
		top: '50%', // Top position relative to parent in px
		left: '50%' // Left position relative to parent in px
	};

	var target = document.getElementById('foo');

	$('#foo').css('display','block');
	var spinner = new Spinner(opts).spin(target);
	$.post('eliminar_trabajo', {uidjob: uidjob}, function(data){
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
	});
}

function cargar_campos(){
	var opts = {
		lines: 13, // The number of lines to draw
		length: 25, // The length of each line
		width: 10, // The line thickness
		radius: 35, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 0, // The rotation offset
		direction: 1, // 1: clockwise, -1: counterclockwise
		color: '#f48120', // #rgb or #rrggbb or array of colors
		speed: 1, // Rounds per second
		trail: 100, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spinner', // The CSS class to assign to the spinner
		zIndex: 2e9, // The z-index (defaults to 2000000000)
		top: '50%', // Top position relative to parent in px
		left: '50%' // Left position relative to parent in px
	};
	var target = document.getElementById('foo');

	$('#foo').css('display','block');
	var spinner = new Spinner(opts).spin(target);
	$('#tipo_archivo').html('');
	$('#jsonLocation').html('');
	$('#campos-feed').html('');
	$('#messages').css('display','none');
	if( $('#url-origen').val() != ''){
		$.ajax({
			url: 'nucleo/detectar_campos',
			type: 'POST',
			dataType: 'json',
			data: { url: $('#url-origen').val() },
			success: function(data){
				spinner.stop();
				$('#foo').css('display','none');
			 	$('#tipo_archivo').html( "Tipo de Archivo: " + data.feed_type );
			 	
			 	$('.campos-feed').slideDown();
			},
			error: function(){
				spinner.stop();
				data = '<span class="error">Ocurrió un problema al intentar detectar los campos del feed.</span>';
				$('#foo').css('display','none');
				$('#messages').css('display','block');
				$('#messages').addClass('alert-danger');
				$('#messages').html(data);
			}
		});
	}else{
		spinner.stop();
		data = '<span class="error">Es necesario escribir alguna <b>URL</b> de un feed para detectar los campos.</span>';
		$('#foo').css('display','none');
		$('#messages').css('display','block');
		$('#messages').addClass('alert-danger');
		$('#messages').html(data);
	}
}

function desplegar(item){
	if( $('.' + item).css( 'display' ) === 'none' ){
		$('.'+item).slideDown();
	}
}

function desplega(element){
	if ( ! $(element).is( ":checked" ) ){
		$(element).parent().parent().children('div').slideUp(300, function (){
			$.each($(element).parent().parent()
			.children("div").children('label')
			.children('input'), function(){
				$(this).prop( 'checked', false );
				if ( $(this).parent().parent().children('div').size()>0 ){
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