jQuery(document).ready(function() {
	jQuery('#bdShortCode').val('[quotopia quotepack="Select Language Pack!"]');

	jQuery('#quotopiaPack').change(function(e){
		jQuery('#quotopiaTextQP').val(' quotepack="' + jQuery('#quotopiaPack').children("option:selected").val() + '"');
		jQuery('#bdShortCode').val('[quotopia' + jQuery('#quotopiaTextQP').val() + jQuery('#quotopiaTextAuthor').val() + jQuery('#quotopiaTextCycle').val() + jQuery('#quotopiaTextDisplay').val() + jQuery('#quotopiaCycleFormat').val() + ']');
	});

	jQuery('#quotopiashowhideAuthor').change(function(e) {
		var authValue = jQuery('#quotopiashowhideAuthor').children("option:selected").val();
		if(authValue == 1){
			if(jQuery('.quotopiaAuthor').hasClass('bdHide')) {
				jQuery('.quotopiaAuthor').toggleClass('bdHide');
			}
			jQuery('#quotopiaTextAuthor').val('');
		} else {
			if(!jQuery('.quotopiaAuthor').hasClass('bdHide')) {
				jQuery('.quotopiaAuthor').toggleClass('bdHide');
			}
			jQuery('#quotopiaTextAuthor').val(' hideauthor="yes"');
		}
		jQuery('#bdShortCode').val('[quotopia' + jQuery('#quotopiaTextQP').val() + jQuery('#quotopiaTextAuthor').val() + jQuery('#quotopiaTextCycle').val() + jQuery('#quotopiaTextDisplay').val() + jQuery('#quotopiaCycleFormat').val() + ']');
	});

	jQuery("#quotopiacSpeed").on('input', function(){
		var theCSpeed = jQuery("#quotopiacSpeed").val();
		if(theCSpeed > 500){
			jQuery('#quotopiaTextCycle').val(' cyclespeed="' + theCSpeed + '"');
		} else if(theCSpeed < 500) {
			jQuery('#quotopiaTextCycle').val(' cyclespeed="' + theCSpeed + '"');
		} else {
			jQuery('#quotopiaTextCycle').val('');
		}
		jQuery('#bdShortCode').val('[quotopia' + jQuery('#quotopiaTextQP').val() + jQuery('#quotopiaTextAuthor').val() + jQuery('#quotopiaTextCycle').val() + jQuery('#quotopiaTextDisplay').val() + jQuery('#quotopiaCycleFormat').val() + ']');
	});

	jQuery("#quotopiadSpeed").on('input', function(){
		var thedSpeed = jQuery("#quotopiadSpeed").val();
		if(thedSpeed > 6000){
			jQuery('#quotopiaTextDisplay').val(' displayspeed="' + thedSpeed + '"');
		} else if(thedSpeed < 6000) {
			jQuery('#quotopiaTextDisplay').val(' displayspeed="' + thedSpeed + '"');
		} else {
			jQuery('#quotopiaTextDisplay').val('');
		}
		jQuery('#bdShortCode').val('[quotopia' + jQuery('#quotopiaTextQP').val() + jQuery('#quotopiaTextAuthor').val() + jQuery('#quotopiaTextCycle').val() + jQuery('#quotopiaTextDisplay').val() + jQuery('#quotopiaCycleFormat').val() + ']');
	});

/***
 * Demo version of cycling...
 */
	jQuery(".quotopiaCycle").on("click", function() {
		var selectedEffect = jQuery('.quotopiaCycle:checked').val();
		var options = {};
		if(selectedEffect === "bounce") {
			options = {times: 3};
		} else if(selectedEffect === "blind") {
			options = {direction: "down"};
		} else if(selectedEffect === "drop") {
			options = {direction: "up"};
		} else if(selectedEffect === "explode") {
			options = {pieces: 16};
		} else if(selectedEffect === "shake") {
			options = {times: 4};
		} else if(selectedEffect === "slide") {
			options = {direction: "down"};
		} else if(selectedEffect === "fade") {
			options = {};
		}

		if(selectedEffect == "fade") {
			jQuery('#quotopiaCycleFormat').val('');
		} else {
			jQuery('#quotopiaCycleFormat').val(' cycle="' + selectedEffect + '"');
		}
		jQuery('#bdSCcontainer div div').effect(selectedEffect, options, 500, callback);
	});

	function runEffect() {
	};

	function callback() {
		setTimeout(function() {
			jQuery('#bdSCcontainer div div').removeAttr( "style" ).hide().fadeIn();
		}, 500 );
		jQuery('#bdShortCode').val('[quotopia' + jQuery('#quotopiaTextQP').val() + jQuery('#quotopiaTextAuthor').val() + jQuery('#quotopiaTextCycle').val() + jQuery('#quotopiaTextDisplay').val() + jQuery('#quotopiaCycleFormat').val() + ']');
	};

});