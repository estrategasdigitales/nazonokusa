tvs(function($){
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
					$('html,body').animate({
						'scrollTop': $('#messages').offset().top
					}, 1000);
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
				$('html, body').animate({
					'scrollTop': $('#messages').offset().top
				}, 1000);
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
					$('html,body').animate({
						'scrollTop': $('#messages').offset().top
					}, 1000);
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
				$('html,body').animate({
					'scrollTop': $('#messages').offset().top
				}, 1000);
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
					$('html,body').animate({
						'scrollTop': $('#messages').offset().top
					}, 1000);
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
				$('html,body').animate({
					'scrollTop': $('#messages').offset().top
				}, 1000);
			}
		});
		return false;
	});

	$('#form_estructura_nuevo').submit(function(){
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
					$('html,body').animate({
						'scrollTop': $('#messages').offset().top
					}, 1000);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					window.location.href = 'estructuras';
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messages').css('display','block');
				$('#messages').addClass('alert-danger');
				$('#messages').html(data);
				$('html,body').animate({
					'scrollTop': $('#messages').offset().top
				}, 1000);
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
					$('html,body').animate({
						'scrollTop': $('#messages').offset().top
					}, 1000);
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
				$('html,body').animate({
					'scrollTop': $('#messages').offset().top
				}, 1000);
			}
		});
		return false;
	});

	$('#form_vertical_nueva').submit(function(){
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
					$('html,body').animate({
						'scrollTop': $('#messages').offset().top
					}, 1000);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					window.location.href = 'verticales';
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messages').css('display','block');
				$('#messages').addClass('alert-danger');
				$('#messages').html(data);
				$('html,body').animate({
					'scrollTop': $('#messages').offset().top
				}, 1000);
			}
		});
		return false;
	});

	$('#form_actualizar_perfil').submit(function(){
		$('#foo').css('display','block');
		$('#messages').removeClass('alert-danger');
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messages').css('display','block');
					$('#messages').addClass('alert-danger');
					$('#messages').html(data);
					$('html,body').animate({
						'scrollTop': $('#messages').offset().top
					}, 1000);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					$('#messages').css('display','block');
					$('#messages').addClass('alert-success');
					data = '<span class="success">Datos actualizados satisfactoriamente.</span>';
					$('#messages').html(data);
					window.location.reload();
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messages').css('display','block');
				$('#messages').addClass('alert-danger');
				$('#messages').html(data);
				$('html,body').animate({
					'scrollTop': $('#messages').offset().top
				}, 1000);
			}
		});
		return false;
	});

	$('#form_recupera_contrasena').submit(function(){
		$('#foo').css('display','block');
		$('#messages').removeClass('alert-danger');
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messages').css('display','block');
					$('#messages').addClass('alert-danger');
					$('#forgot_email').val('');
					$('#messages').html(data);
					$('html,body').animate({
						'scrollTop': $('#messages').offset().top
					}, 1000);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					$('#messages').css('display','block');
					$('#messages').addClass('alert-success');
					data = '<span class="success">Acabamos de enviarte un correo para que recuperes tu contraseña.</span>';
					$('#forgot_email').val('');
					$('#messages').html(data);
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messages').css('display','block');
				$('#messages').addClass('alert-danger');
				$('#forgot_email').val('');
				$('#messages').html(data);
				$('html,body').animate({
					'scrollTop': $('#messages').offset().top
				}, 1000);
			}
		});
		return false;
	});

	$('#form_eliminar_usuario').submit(function(){
		$('#foo').css('display','block');
		$('#messagesModal').removeClass('alert-danger');
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messagesModal').css('display','block');
					$('#messagesModal').addClass('alert-danger');
					$('#messagesModal').html(data);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					$('#messagesModal').css('display','block');
					$('#modalMessage').modal('hide');
					$('#modalMessage').on('hidden.bs.modal');
					window.location.reload();
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messagesModal').css('display','block');
				$('#messagesModal').addClass('alert-danger');
				$('#messagesModal').html(data);
			}
		});
		return false;
	});

	$('#form_eliminar_categoria').submit(function(){
		$('#foo').css('display','block');
		$('#messagesModal').removeClass('alert-danger');
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messagesModal').css('display','block');
					$('#messagesModal').addClass('alert-danger');
					$('#messagesModal').html(data);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					$('#messagesModal').css('display','block');
					$('#modalMessage').modal('hide');
					$('#modalMessage').on('hidden.bs.modal');
					window.location.reload();
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messagesModal').css('display','block');
				$('#messagesModal').addClass('alert-danger');
				$('#messagesModal').html(data);
			}
		});
		return false;
	});

	$('#form_eliminar_vertical').submit(function(){
		$('#foo').css('display','block');
		$('#messagesModal').removeClass('alert-danger');
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messagesModal').css('display','block');
					$('#messagesModal').addClass('alert-danger');
					$('#messagesModal').html(data);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					$('#messagesModal').css('display','block');
					$('#modalMessage').modal('hide');
					$('#modalMessage').on('hidden.bs.modal');
					window.location.reload();
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messagesModal').css('display','block');
				$('#messagesModal').addClass('alert-danger');
				$('#messagesModal').html(data);
			}
		});
		return false;
	});

	$('#form_eliminar_estructura').submit(function(){
		$('#foo').css('display','block');
		$('#messagesModal').removeClass('alert-danger');
		var spinner = new Spinner(opts).spin(target);
		$(this).ajaxSubmit({
			success: function(data){
				if(data != true){
					spinner.stop();
					$('#foo').css('display','none');
					$('#messagesModal').css('display','block');
					$('#messagesModal').addClass('alert-danger');
					$('#messagesModal').html(data);
				}else{
					spinner.stop();
					$('#foo').css('display','none');
					$('#messagesModal').css('display','block');
					$('#modalMessage').modal('hide');
					$('#modalMessage').on('hidden.bs.modal');
					window.location.reload();
				}
			},
			error: function(data){
				spinner.stop();
				$('#foo').css('display','none');
				$('#messagesModal').css('display','block');
				$('#messagesModal').addClass('alert-danger');
				$('#messagesModal').html(data);
			}
		});
		return false;
	});

	$('#tipo_salida').change(function(){
		var tipo = $(this).val();
		switch( tipo ){
			case '1':
				$('#formatos_especificos').addClass('hide');
				$('#formatos_estandar').removeClass('hide');
			break;
			case '2':
				$('#formatos_estandar').addClass('hide');
				$('#formatos_especificos').removeClass('hide');
			break;
			default:
				$('#formatos_estandar').addClass('hide');
				$('#formatos_especificos').addClass('hide');
			break;
		}
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
	$('#claves').val('');
	$('#tipo_archivo').html('');
	$('#jsonLocation').html('');
	$('#campos-feed').html('');
	$('#messages').css('display','none');
	if( $('#url-origen').val() != ''){
		$.ajax({
			url: 'nucleo/detectar_campos',
			type: 'POST',
			dataType: 'html',
			data: { url: $('#url-origen').val() },
			success: function(data){
				spinner.stop();
				$('#foo').css('display','none');
			 	$('#campos-feed').html(data);
			 	$('.campos-feed').slideDown();
			},
			error: function(){
				spinner.stop();
				data = '<span class="error">Ocurrió un problema al intentar detectar los campos del feed.</span>';
				$('#foo').css('display','none');
				$('#messages').css('display','block');
				$('#messages').addClass('alert-danger');
				$('#messages').html(data);
				$('.campos-feed').slideUp();
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

function activarTrabajo(uid, flag){
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
	$.post('nucleo/job_process', {status: flag, uidjob: uid}, function(data){
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

function desplegar(item){
	if( $('.' + item).css( 'display' ) === 'none' ){
		$('.' + item).slideDown();
	}
}

function desplega(element){
	if ( ! $(element).is( ":checked" ) ){
		$.each($(element).parent().parent()
		.children("div").children('label')
		.children('input'), function(){
			$(this).prop( 'checked', false );
			if ( $(this).parent().parent().children('div').size() > 0 ){
				desplega(this);
			}
		});
	}else{
		$.each($(element).parent().parent()
		.children("div").children('label')
		.children('input'),function(){
			$(this).prop("checked",true);
			if( $(this).parent().parent().children("div").size() > 0 ){
				desplega(this);
			}
		});
	}
}

function datosAdicionales(check){
	if($(check).prop("checked")==true){
		if($(check).val()==="rss"){
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
		if($(check).val()==="rss"){
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