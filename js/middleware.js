/*

*/

function desplegar(item){
	if($('.'+item).css('display')==='none'){
		$('.'+item).slideDown();
	}
}
$(function(){
	$('#form_vertical_nueva').validate({
		rules: {
			nombre: {
				required: true,
				minlength: 3
			},
		},
		messages: {
			nombre: {
				required: "Por favor, ingresa el Nombre de la Categoría.",
				minlength: "Este campo debe ser de al menos 3 caracteres."
			},
		}
	});
	$('#form_categoria_nueva').validate({
		rules: {
			nombre: {
				required: true,
				minlength: 3
			},
		},
		messages: {
			nombre: {
				required: "Por favor, ingresa el Nombre de la Categoría.",
				minlength: "Este campo debe ser de al menos 3 caracteres."
			},
		}
	});
	$("#form_usuario_nuevo").validate({
		rules: {
			nombre: {
				required: true,
				minlength: 5
			},
			apellidos: {
				required: true
			},
			password: {
				required: true,
				minlength: 5
			},
			password_2: {
				required: true,
				equalTo: "#password"
			},
			email: {
				required: true,
				email: true
			},
			'categoria[]': {
				required: true, 
				minlength: 1 
			},
			'vertical[]': {
				required: true, 
				minlength: 1 
			},
		},
		messages: {
			nombre: {
				required: "Por favor, ingresa un Nombre de Usuario",
				minlength: "Este campo debe ser de al menos 5 caracteres."
			},
			apellidos: {
				required: "Por favor, ingresa un Apellido"
			},
			password: {
				required: "Por favor, ingresa una clave de usuario.",
				minlength: "Tu clave debe ser de al menos 5 caracteres de longitud o mas."
			},
			password_2: {
				required: "Por favor, vuelve a indicar tu clave.",
				equalTo: "No coincide con tu clave, por favor, verifica."
			},
			email: "Por favor, ingresa un correo valido.",
			'categoria[]': "Debe seleccionar al menos una categoria",
			'vertical[]': "Debe seleccionar al menos una vertical",
		}
	});
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
});

function ShowDialog(url,nombre) {
    $('#spanMessage').html('¿Está seguro(a) que desea eliminar el usuario: '+nombre+'?');
    $("#dialogConfirm").dialog({
        resizable: false,
        height: 200,
        width: 400,
        modal: true,
        title: 'Mensaje Modal',
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
        title: 'Mensaje Modal',
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
        title: 'Mensaje Modal',
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