// Select2 JS
jQuery(document).ready(function() {
	if(jQuery(".settings-role-select").length){
		jQuery(".settings-role-select").select2({
			multiple: true,
			placeholder: " Choose roles to display",
		});
	}	
	
	if(jQuery("#role_ragister_list").length){
		jQuery('#role_ragister_list').DataTable({
				paging: true,
				searching: true,
				ordering: true,
				responsive: true
		});
	}
});


		
		