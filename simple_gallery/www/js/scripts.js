/**
 * delayTimer
 * 
 * Setup a timer for delaying the execution of certain events
 * @param 	delay 	the length of the timer
 */
function delayTimer (delay) {
	var timer;
	
	return function (fn) {
		timer = clearTimeout(timer);
		
		if (fn) {
			timer = setTimeout(function () {
				fn();
			}, delay);
		}
		
		return timer;
	}
};

/**
 * toggleControlsMenu
 * 
 * Toggle the state of the controls menu
 */
toggleControlsMenu = function () {
	$('#controls_menu').toggle('slide', { direction: "right" });
	$('#controls_state').attr('name', $('#controls_state').attr('name') ? '' : 'open');
	$('#controls_toggle').stop(true, true).switchClass($('#controls_state').attr('name') ? '' : 'active', $('#controls_state').attr('name') ? 'active' : '', 500);
};

/**
 * toggleControlPanel
 * 
 * Toggle the state of a control panel
 * @param 	panel_name 		the name of the panel to toggle
 */
toggleControlPanel = function (panel_name) {
	if ( ! panel_name)
		panel_name = $('#current_control').attr('name');
	
	// Determine whether the panel to be toggled is currently open
	var is_open = panel_name == $('#current_control').attr('name');
	
	$('#panel_' + panel_name).stop(true, true).toggle('slide', { direction: "up" });
	$('#tab_' + panel_name).stop(true, true).switchClass(is_open ? 'active' : '', is_open ? '' : 'active', 500);
	$('#current_control').attr('name', is_open ? '' : panel_name);
};

/**
 * isControlsOpen
 * 
 * Check whether the controls area is open
 */
isControlsOpen = function () {
	return $('#controls_state').attr('name');
};

/**
 * resetColor
 * 
 * Reset the color panel
 */
resetColor = function () {
	$('#current_color').val('None selected');
	$('#current_color').css('backgroundColor', '#fff');
	$('#current_color').css('color', '#000');
};

/**
 * resetTag
 * 
 * Reset a single tag input
 * 
 * @param 	tag 	the name of the tag group to reset
 * @param 	value 	the value of the tag to reset
 */
resetTag = function (tag, value) {
	$('.filter_tag').each(function () {
		if ($(this).siblings('span').attr('innerHTML') == value) {
			$(this).attr('checked', false);
			$('#cloud_tag_' + $(this).attr('id').split('_').pop()).removeClass('selected');
		}
	});
};

/**
 * resetTags
 * 
 * Reset all tag inputs
 */
resetTags = function () {
	$('.filter_tag').attr('checked', false);
	$('.cloud_tag').removeClass('selected');
};

/**
 * resetSearch
 * 
 * Reset the search input
 */
resetSearch = function () {
	$('#search_text').val('');
};

/**
 * resetOption
 * 
 * Reset a single option input
 * 
 * @param 	option 	the name of the option to reset
 */
resetOption = function (option) {
	$("select#" + option)[0].selectedIndex = 0;
};

/**
 * resetOptions
 * 
 * Reset all option input
 */
resetOptions = function () {
	resetOption('sort_by');
	resetOption('sort_direction');
};

/**
 * getFilterData
 * 
 * Form a POST string from the currently selected filters
 */
getFilterData = function () {
	var data = '';
	
	// Get info from the color input
	if ($('#current_color').val() != 'None selected')
		data += ('&color=' + $('#current_color').val().replace('#', ''));
	
	// Get info from the tag lists
	$('.filter_tag:checked').each(function () {
		if (this.value)
			data += ('&tags[]=' + $(this).attr('id').split('_').pop())
	});
	
	// Get info from the search box
	if ($('#search_text').val()) {
		data += ('&search_text=' + $('#search_text').val());
		$('.search_fields input:checked').each(function () {
			data += ('&search_fields[]=' + $(this).val())
		});
		data += ('&search_match=' + $('.search_match input:checked').val());
	}
	
	// Get info from the sort_by option
	if ($('#sort_by')[0].selectedIndex != 0 || $('#sort_direction')[0].selectedIndex != 0) {
		data += ('&sort_by=' + $('#sort_by').val());
		data += ('&sort_direction=' + $('#sort_direction').val());
	}
	
	return data.substring(1);
};

/**
 * galleryAJAX
 * 
 * Update the gallery through AJAX
 */
galleryAJAX = function () {
	// Get a POST string of the currently selected filters
	var filterData = getFilterData();
	
	// Send an AJAX request for the gallery contents
	$.ajax({
    	url: 'gallery/gallery_ajax',
    	type: 'post',
    	data: filterData,
    	success: function (result) {
    		$("#gallery").attr("innerHTML", result);
    	}
    });
    
    // Clear the list of current filters
    $("#current_filters li").remove();
    
    if (filterData) {
    	// Add an entry to the current filters list for each filter in effect
		jQuery.each(getFilterData().split('&'), function () {
			// filter[0] = filter's type, filter[1] = filter's name
			var filter = this.split('=');
			
			// Skip secondary filter data
			if (filter[0] == 'search_fields' || filter[0] == 'search_match' || filter[0] == 'sort_direction')
				return true;
			
			if (filter[0] == 'color')
				filter[0] = 'Color';
			
			if (filter[0] == 'tags[]') {
				filter[0] = $('#filter_tag_'+filter[1]).attr('name');
				filter[1] = $('#filter_tag_'+filter[1]).siblings('span').attr('innerHTML');
			}
			
			if (filter[0] == 'search_text') {
				filter[0] = 'Search';
			}
			
			if (filter[0] == 'sort_by') {
				filter[0] = 'Sort by';
				filter[1] = $('#sort_by :selected').text() + ' (' + $('#sort_direction :selected').val() + ')';
			}
			
			$('#current_filters').append('<li><a title="Click to remove this filter" href="#">x</a><span name="'+ filter[0] + ':' + filter[1] + '">' + filter[0].replace('[]', '') + ': ' + filter[1] + '</span></li>');
		});
		
		// Setup the click event for the close button for each entry in the list
		$('#current_filters li a').click(function (event) {
			event.preventDefault();
			
			// filter[0] = entry's name, filter[1] = entry's value
			var filter = $(this).siblings().filter(':first').attr('name').split(':');
			
			if (filter[0] == 'Color')
				resetColor();
			else if (filter[0] == 'Search')
				resetSearch();
			else if (filter[0] == 'Sort by') {
				resetOption('sort_by');
				resetOption('sort_direction');
			}
			else if (filter[0].indexOf('[]') != -1)
				resetTag(filter[0], filter[1]);
			else
				resetOption(filter[0]);
			
			$(this).parent().remove();
			
			galleryAJAX();
		});
	}
	else {
		$('#current_filters').append('<li class="empty">No Filters</li>');
	}
	
	if (filterData.split('&') != '')
		showStatus('current', filterData.split('&').length);
	else
		hideStatus('current');
};


$(document).ready(function () {
	
	// Set the timer for delayed events
	var inputDelay = delayTimer(800);
	
	// Hide the controls menu and all panels initially
	$('#controls_menu').hide();
	$('.control_panel').hide();
	
	// Open the controls menu when hovering over the controls_toggle link
	$('#controls_toggle').mouseover(function (event) {
		if (!isControlsOpen())
			toggleControlsMenu();
	});
	
	// Toggle the controls when clicking the controls_toggle
	$('#controls_toggle').click(function (event) {
		event.preventDefault();
		toggleControlsMenu();
		toggleControlPanel();
	});
	
	// Open the corresponding controls panel when clicking on a controls tab
	$("#controls_menu li a").click(function (event) {
		event.preventDefault();
				
		// Get the name of the tab that was clicked
		var tab_name = $(this).attr('id').split('_').pop();
		
		// If a different panel is currently open, close it
		if ($('#current_control').attr('name') && $('#current_control').attr('name') != tab_name)
			toggleControlPanel();
		
		// Toggle the corresponding panel
		toggleControlPanel(tab_name);
	});
	
	// Close the controls menu and current panel when clicking the panel tab
	$('.control_panel a.panel_tab').click(function (event) {
		event.preventDefault();
		toggleControlsMenu();
		toggleControlPanel();
	});
	
	// Reset the current panel's contents when clicking the panel reset
	$('.control_panel a.panel_reset').click(function (event) {
		event.preventDefault();
	});
	
	// Close the controls menu and current panel with a delay after leaving the controls area
	$('#controls').mouseleave(function (event) {
		if (isControlsOpen()) {
			inputDelay(function () {
				toggleControlPanel();
				toggleControlsMenu();
			});
		}
	});
	
	// If the curser returns to the controls before they close, cancel the timer
	$('#controls').mouseenter(function (event) {
		if (isControlsOpen())
			clearTimeout(inputDelay());
	});
	
	// Filters panel
	
	// Reset the filters panel when its panel reset tab is clicked
	$('.control_panel#panel_filters a.panel_reset').click(function (event) {
		event.preventDefault();
		resetColor();
		resetTags();
		resetSearch();
		resetOptions();
		galleryAJAX();
	});
	
	// Colors panel
	
	// Setup the color picker
	$('#panel_color #color_picker').farbtastic('#current_color');
	
	// Update the current color when the mouse button is released
	$('#color_picker').mouseup(function (event) {
		galleryAJAX();
	});
	
	// Reset the color panel when its panel reset tab is clicked
	$('.control_panel#panel_color a.panel_reset').click(function (event) {
		event.preventDefault();
		resetColor();
		galleryAJAX();
	});
	
	// Tags panel
	
	$('.cloud_tag').click(function (event) {
		event.preventDefault();
		
		$(this).toggleClass('selected');
		
		$('#filter_tag_'+$(this).attr('id').split('_').pop()).attr('checked', $(this).hasClass('selected'));
		
		galleryAJAX();
	});
	
	// Reset the tags panel when its panel reset tab is clicked
	$('.control_panel#panel_tags a.panel_reset').click(function (event) {
		event.preventDefault();
		resetTags();
		galleryAJAX();
	});
	
	// Search panel
	
	// Perform the search after a delay once typing has stopped
	$('#search_text').keyup(function () {
		inputDelay(function () {
			galleryAJAX();
		});
	});
	
	// Re-search when changing the search fields
	$('.search_fields input').change(function (event) {
		if ($('#search_text').val())
			galleryAJAX();
	});
	
	// Re-search when changing the search match
	$('.search_match input').change(function (event) {
		if ($('#search_text').val())
			galleryAJAX();
	});
	
	// Reset the search panel when its panel reset tab is clicked
	$('.control_panel#panel_search a.panel_reset').click(function (event) {
		event.preventDefault();
		resetSearch();
		galleryAJAX();
	});
	
	// Options panel
	
	// Update the sort order when either select in the sort_by group is changed
	$('.sort_by select').change(function (event) {
		galleryAJAX();
	});
	
	// Reset the options panel when its panel reset tab is clicked
	$('.control_panel#panel_options a.panel_reset').click(function (event) {
		event.preventDefault();
		resetOptions();
		galleryAJAX();
	});

});
