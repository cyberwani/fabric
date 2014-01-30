// Holds the status of whether or not the rest of the code should be run
var cstmzr_multicat_js_run = true;
var packages = '';

(function($) {
	$(document).ready( function() {

		// Prevents code from running twice due to live preview window.load firing in addition to the main customizer window.
		if( true == cstmzr_multicat_js_run ) {
			cstmzr_multicat_js_run = false;
		} else {
			return;
		}

		var api = wp.customize;

		// Loops through each instance of the category checkboxes control.
		function checkActive() {

			var id = $('.cstmzr-hidden-packages').prop('id');
			var categoryString = api.instance(id).get();
			var categoryArray = categoryString.split(',');

			// Checks/unchecks category checkboxes based on saved data.
			$(categoryArray).each(function( index, plugin ) {
				$('[id="'+plugin+'"]').prop('checked', true);
			});
		}

		// Sets listeners for plugins
		$(document).on('change', '.cstmzr-package-plugin', function(){

			var fieldID = $('.cstmzr-hidden-packages').prop('id');
			var pluginID = $(this).prop('id');

			if( $(this).prop('checked' ) == true ) {
				addPlugin(pluginID, fieldID);
			} else {
				removePlugin(pluginID, fieldID);
			}

		});

		// Adds category ID to hidden input.
		function addPlugin( pluginID, fieldID ) {

			var categoryString = api.instance(fieldID).get();
			var categoryArray = categoryString.split(',');

			if ( '' == categoryString ) {
				var delimiter = '';
			} else {
				var delimiter = ',';
			}

			// Updates hidden field value.
			if( $.inArray( pluginID, categoryArray ) < 0 ) {
				api.instance(fieldID).set( categoryString + delimiter + pluginID );
			}
		}

		// Removes category ID from hidden input.
		function removePlugin( pluginID, fieldID ) {

			var categoryString = api.instance(fieldID).get();
			var categoryArray = categoryString.split(',');
			var catIndex = $.inArray( pluginID, categoryArray );

			if( catIndex >= 0 ) {

				// Removes element from array.
				categoryArray.splice(catIndex, 1);

				// Creates new category string based on remaining array elements.
				var newCategoryString = '';
				$.each( categoryArray, function() {
					if ( '' == newCategoryString ) {
						var delimiter = '';
					} else {
						var delimiter = ',';
					}
					newCategoryString = newCategoryString + delimiter + this;
				});

				// Updates hidden field value.
				api.instance(fieldID).set( newCategoryString );
			}
		}

		$(document).on( 'click', 'i.cstmzr-package', function() {
			if($(this).hasClass('closed')) {
				$(this).removeClass('closed');
				$(this).removeClass('dashicons-arrow-right');
				$(this).addClass('dashicons-arrow-down');
				var package_slug = $(this).attr('data-package');
				$('tr.' + package_slug).css('display', 'table-row');
			} else {
				$(this).addClass('closed');
				$(this).addClass('dashicons-arrow-right');
				$(this).removeClass('dashicons-arrow-down');
				var package_slug = $(this).attr('data-package');
				$('tr.' + package_slug).css('display', 'none');
			}
		});

		$(document).on( 'click', 'label.cstmzr-package-label', function() {
			$(this).closest('tr').find('td.first i').trigger('click');
		});

		$(document).on( 'change', '#choose_a_package', function() {
			$.post(
				ajaxurl,
				{
					// wp ajax action
					action : 'fabric_read_package_ajax',

					// send the nonce along with the request
					fabric_package : $(this).val()
				},
				function( response ) {
					packages = buildConfigurator(response);
					$('table#packages').html(packages);
					checkActive();
				}
			);
		});
	});

	function buildConfigurator(packages) {
		packages = JSON.parse(packages);
		var html = '<tbody>';

		$.each(packages, function(package_name, package_details) {
			var package_slug = convertToSlug(package_name);
			html += '<tr>';
				html += '<td class="first">';
					html += '<i class="dashicons dashicons-arrow-down cstmzr-package" data-package="' + package_slug + '" id="' + package_slug + '"></i>';
				html += '</td>';
				html += '<td>';
					html += '<label class="cstmzr-package-label" for="' + package_slug + '"> ' + package_name + '</label>';
				html += '</td>';
				html += '<td>';
					html += '<span>' + this.description + '</span>';
				html += '</td>';
			html += '</tr>';
			
			$.each(this.plugins, function(plugin_name, plugin_details) {
				var plugin_source = plugin_details.source;
				var slug = baseName( plugin_source );
				var file_ext = plugin_source.substr( (plugin_source.lastIndexOf('.') +1) );
				if( "zip" == file_ext ) {
					slug = 'local_' + plugin_source;
				}

				html += '<tr class="sub ' + package_slug + '">';
					html += '<td>';
						html += '<input type="checkbox" class="cstmzr-package-plugin" id="' + slug + '" value="true" />';
					html += '</td>';
					html += '<td>';
						if( "zip" == file_ext ) {
							html += '<span>' + plugin_name + '</span>';
						} else {
							html += '<span><a href="' + this.source + '" target="_blank">' + plugin_name + '</a></span>';
						}
					html += '</td>';
					html += '<td>';
						html += '<span>' + this.description + '</span>';
					html += '</td>';
				html += '</tr>';
			});
		});

		html += '</tbody>';

		return html;

	}
	function convertToSlug(Text)
	{
		Text = 'package-' + Text;
		return Text
			.toLowerCase()
			.replace(/ /g,'-')
			.replace(/[^\w-]+/g,'')
			;
	}
	function baseName(str)
	{
		str = str.replace(/\/$/, "");
		var base = new String(str).substring(str.lastIndexOf('/') + 1); 
		if(base.lastIndexOf(".") != -1)
			base = base.substring(0, base.lastIndexOf("."));
		return base;
	}
})(jQuery);