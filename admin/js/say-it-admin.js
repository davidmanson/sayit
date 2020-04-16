(function( $ ) {
	'use strict';

	$(function() {

		/*--- Change Display according to mode ---*/
		let $modeselect = $('#say-it-mode');

		let sayit_admin_display = function(){
			$('.sayit_admin_wrapper').removeClass('active');
			$('#sayit_'+$modeselect.val()+'_wrapper').addClass('active');
			console.log('should change to '+ '#sayit_'+$modeselect.val()+'_wrapper');
		}

		// Trigger on load & on change
		if($modeselect.length > 0){
			sayit_admin_display();
			$modeselect.on('change', sayit_admin_display);
		}


		/* Show Voices list according to choises */
		let $langSelector = $('#say-it-google_language');
		let $genderSelector = $('#say-it-google_gender');

		if($langSelector.length > 0 && $genderSelector.length > 0){
			let filter_voices = function(){
				let language = $langSelector.val();
				let gender = $genderSelector.val().toUpperCase();
				$("#say-it-google_custom_voice").find('option').hide();
				$("#say-it-google_custom_voice").find(`[data-lang='${language}']`).filter(`[data-gender='${gender}']`).show();
			}
			$langSelector.on('change', filter_voices);
			$genderSelector.on('change', filter_voices);
			filter_voices();
		}


		
		/*--- Tabs system ---*/
		$('.say-it-tabs a').on('click', function(e){
			e.preventDefault();
			
			// Show proper tab
			$('.say-it-tab').removeClass('active');
			$($(this).attr('href')).addClass('active');

			// incicate proper tab
			$(this).siblings().removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active')

			// init TTS Area
			setTimeout(function(){
				refreashTTSArea();
			}, 200)
		})


		/*--- Json editor init ---*/
		var $tts_area = $('.google_tts_area');
		var myCodeMirror = null;
		function initGoogleTTSArea(){
			if($tts_area.length > 0){
				myCodeMirror = wp.codeEditor.initialize($tts_area, cm_settings);
				myCodeMirror.codemirror.setSize("100%", '100px');
			}
		}
		initGoogleTTSArea();

		function refreashTTSArea(){
			if(myCodeMirror){
				myCodeMirror.codemirror.refresh();
			}
		}
	});
	
})( jQuery );
