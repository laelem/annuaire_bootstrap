
function toggle_radio(name, val){
	$(this).button('toggle');
	$('input[name="'+name+'"]').val(val);
}

$(function () {


	$('.action a').tooltip();
	$('.triable a').tooltip();
	$('.pagination li').tooltip();
	
	if($('.chosen').length){ 
		$('.chosen').chosen({
			no_results_text: "Aucun résultat ne correspond à ",
			allow_single_deselect: true
		});
	}
	
	if($('.colorbox').length){ 
		$('.colorbox').colorbox({
			rel:'nofollow', 
			transition:"none", 
			width:"70%", 
			height:"70%"
		});
	}
	
	$('#open_modal_suppr').click(function(){
		$('#modal_suppr').modal();
	});
	
	$('#filtre_recherche_nom').click(function(){
		$('input[name="filtre_recherche_type"]').val('nom');
		$('input[name="filtre_lettre_val"]').val('');
		$('#form_contact_filtre').submit();
	});

	$('#filtre_recherche_prenom').click(function(){
		$('input[name="filtre_recherche_type"]').val('prenom');
		$('input[name="filtre_lettre_val"]').val('');
		$('#form_contact_filtre').submit();
	});

	$('#filtre_recherche_del').click(function(){
		$('input[name="filtre_recherche_val"]').val('');
		$('input[name="filtre_lettre_val"]').val('');
		$('#form_contact_filtre').submit();
	});

	$('.filtre_lettre_val').click(function(){
		$('input[name="filtre_recherche_type"]').val('');
		$('input[name="filtre_recherche_val"]').val('');
		var val = $(this).text();
		$(this).button('toggle');
		$('input[name="filtre_lettre_val"]').val(val);
		$('#form_contact_filtre').submit();
	});

	$('#filtre_lettre_del').click(function(){
		$('input[name="filtre_lettre_val"]').val('');
		$('input[name="filtre_recherche_val"]').val('');
		$('#form_contact_filtre').submit();
	});
	
});