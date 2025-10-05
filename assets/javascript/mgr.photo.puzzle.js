var router = '../assets/includes/photo.puzzle.inc.php';

function gestisciOpzioniBox (checkbox) {
	
	//$j("#wbbody").css('cursor', 'wait', 'important');
	//$j(checkbox).closest('span').text('aspetta...');
	//var opId = (!$j(checkbox).attr("value")) ? $j(checkbox).attr("value") : null;
	var opId = $j(checkbox).attr("value");
	var optValId = $j(checkbox).closest('div.prodoptionrow').attr('options-values-id');
	var optGrpId = $j(checkbox).closest('div.prodoptionrow').attr('option-group-id');
	var optName = $j(checkbox).closest('div.prodoptionrow').attr('option-name');


	if ($j(checkbox).is( ':checked' )) var action = 1 //aggiungo
	else var action = 0 //elimino
	
	$j.post(router, {
				f : 'gestisciOpzioniBox',
				data : {
					opId : opId,
					optValId : optValId,
					action : action,
					optGrpId: optGrpId,
					optName: optName
				}
	}).done();

}
