/*

 */
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