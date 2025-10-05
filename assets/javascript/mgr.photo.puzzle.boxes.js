var router = '../assets/includes/photo.puzzle.inc.php';

$('#modalScatole').on('show.bs.modal', function (event) {
	
	var button = $(event.relatedTarget) // Button that triggered the modal
	var groupId = button.data('group-id') // Extract info from data-* attributes
	var modal = $(this)
	//console.log(recipient)
	$.ajax({
  		method: "POST",
  		url: router,
  		dataType: "json",
  		data: { f: "mgrGetBoxes", groupId: groupId }
	}).done(function( result ) {
		
		table = "<table class='table table-striped'>";
		//table += "<thead><tr><td>id</td><td>Modello</td><td>Azione</td><td></td></tr></thead>";
		table += "<thead><tr><td>id</td><td>Modello</td><td>Azione</td></tr></thead>";
		$.each( result, function( key, json ) {
  			table += "<tr><td>"+json.boxId+"</td><td>"+json.nome+"</td><td>";
  			table += "<form method='POST' action='' style='display: inline-block !important;'>";
			table += "<input type='hidden' name='function' value='box_mod_menu' >";
			table += "<input type='hidden' name='group_id' value='"+groupId+"'>";
			table += "<input type='hidden' name='box_id' value='"+json.boxId+"'>";
			table += "<button type='submit' class='btn btn-default btn-sm' style='display: inline-block !important; margin-right: 15px;'>Modifica Scatola</button>";
			table += "</form>";
			
  			table += "<form method='POST' action='' style='display: inline-block !important;'>";
			table += "<input type='hidden' name='function' value='box_del' >";
			table += "<input type='hidden' name='group_id' value='"+groupId+"'>";
			table += "<input type='hidden' name='box_id' value='"+json.boxId+"'>";
			table += "<button type='submit' class='btn btn-default btn-sm' style='display: inline-block !important; margin-right: 15px;' onclick='return confirm(\"sicuro ?\")'>Cancella Scatola</button>";
			table += "</form>";

  			table += "</td></tr>";
  			//table += "<td><button class='btn btn-default btn-sm' style='display: inline-block !important; margin-right: 15px;' data-function='generaPNG' data-group-id='"+groupId+"' data-box-id='"+json.boxId+"'>Genera PNG</button></td></tr>";
		});	
		table += "</table>";
		modal.find('.modal-body').html(table);
    	//alert( "Data Saved: " + msg );
  	});
  	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.

	//modal.find('.modal-title').text('New message to ' + recipient)

})


var cntPng = 0;
var totPng = 0;
var running = false;
$('#modalGenerazionePNG').on('show.bs.modal', function (event) {
	
	//var cnt = 0;
	//var tot = 0;
	var modal = $(this)
	
	if(!running) {
		running = true;
		$.post(
			router, { f : 'mgrGetBoxesList' },
				function(res){
					//console.log(res)
					totPng = res.tot;
					//totPng = 1;
					//$('#pngTotale').text(totPng);
					//tot--;
					
					draw(res.jobs);
				},
			"json"
		);
	}

})



function draw(obj){

	$('#progresso').html('elaboro scatola n: '+(cntPng+1)+' di ' + totPng + ' - '+obj[cntPng].filename);
	//console.log(obj[cntPng].filename);
	$.post(router, { f : 'mgrDrawBox', filename : obj[cntPng].filename, obj : obj[cntPng].obj, bFoto : obj[cntPng].bFoto, pezzi : obj[cntPng].pezzi, dimensioni : obj[cntPng].dimensioni },
		function(){
			
			cntPng++;
			if (cntPng == totPng) {
				running = false;
				cntPng = 0;
				totPng = 0;
				$('#progresso').html('operazione completata!');
				return;
			}
			else draw(obj);
	
		}
	);

}