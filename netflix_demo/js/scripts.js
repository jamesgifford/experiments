/**
 * A delay timer for the input field
 * @access 	public
 * @return 	void
 */
var delay = (function ()
{
	var timer = 0;
	
	return function (callback, ms)
	{
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();

$(document).ready(function ()
{

	// Connect the ajax events to the keyup event for the input field
	$('#terms').keyup(function ()
	{
		delay(function ()
		{
			// First, load the loading graphic
			$.ajax({
				url: "views/loader.php",
				success: function (data)
				{
					$('#content').html(data);
				}
			});
			
			// Then send the request to the server
			$.ajax({
				url: "views/chart.php",
				type: "post",
				data: "terms="+$('#terms').val(),
				success: function (data)
				{
					$('#content').html(data);
				}
			});
		
		}, 1000 );
	});

});