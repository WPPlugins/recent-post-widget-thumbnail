/**
 * File rpwt.js.
 *
 * Plugin ui enhancement
 *
 */

( function( $ ) {

	//call the function
	excerpt_show_hide();

	function excerpt_show_hide() {
		$('input#widget-rpwt-2-show_excerpt:checkbox').click(function(){
			if ($('input#widget-rpwt-2-show_excerpt').is(':checked')) {
				$('.rpwt-toggle-excerpt-length').css('display','block');
				//alert('checked');
			}
			else {
				$('.rpwt-toggle-excerpt-length').css('display','none');
				//alert('unchecked');
			}
		});
	}

	//rebind function after ajax request
	$(document).ajaxStop(function() {
		excerpt_show_hide();
	});
} )( jQuery );
