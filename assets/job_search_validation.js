function validation(pageurl) {	
	var job_keyword			= jQuery('#job_keyword').val();
	var job_location		= jQuery('#job_location').val();
	var number_of_post		= jQuery('#number_of_post').val();
	var submitform 			= 'yes';
	if(job_keyword == '') {
		jQuery("#job_keyword").addClass('error');
		submitform 			= 'no';
	} else {
		jQuery("#job_keyword").removeClass('error');
	}
	if(job_location == '') {
		jQuery("#job_location").addClass('error');
		submitform = 'no';
	} else {
		jQuery("#job_location").removeClass('error');
	}
	if(submitform == 'no'){
		return false ;
	} else {
		jQuery('#imagereload').show();
		jQuery.post(pageurl, {
							job_keyword		: job_keyword, 
							job_location	: job_location, 
							number_of_post	: number_of_post
						},
					function(data) {
							jQuery('#reponse').html(data);
							jQuery('#imagereload').hide();	
					}
		);
		return false;
	}
}