/* TO DO */
/*
 * verificare classe enabled disabled sulle tab delle scatole (disabled rimane sempre)
 * 
 * 
 * 
 */

var router = 'assets/includes/photo.puzzle.inc.php';
var orientamento = 'landscape';
var puzzleSelected = false;
/*step1*/
manageSession();

$('#buttonFile').click(function(e) {
	e.preventDefault();
	$('#inputFile').click();
});



$('#uploaded').on('click', 'img', function() {
	$.post(router, { f : 'getRestore', data : $(this).attr('data-file') }, function(res){
		//var orientation = res.orientation;
		//$('#img-container').html('<img id="img" src="' + res.file + '">');
		$('#img-container').html('<img id="img" class="img-responsive" style="display: block; margin-left: auto; margin-right: auto;" src="' + res.file + '">');
		
		var htmlTab = '<table class="table table-hover">';
		htmlTab += '<thead><tr><th>Pezzi</th><th>Formato</th><th>Prezzo</th></tr></thead>';
		htmlTab += '<tbody>';
		$.each(res.puzzles, function(k, formato) {
			if(formato.shape != null) htmlTab += '<tr style="cursor: pointer" id=' + formato.id + ' data=' + formato.initSelectArea + ' shape=' + formato.shape.toLowerCase() + '><td>' + formato.pezzi + '</td><td>' + formato.dimensioni + ' - ' + formato.shape + '</td><td><strong>€' + formato.prezzo + '</strong></td>';
			else htmlTab += '<tr style="cursor: pointer" id=' + formato.id + ' data=' + formato.initSelectArea + ' shape=' + formato.shape + '><td>' + formato.pezzi + '</td><td>' + formato.dimensioni + '</td><td><strong>€' + formato.prezzo + '</strong></td>';
		})
		htmlTab += '</tbody>';
		htmlTab += '</table>';

		$("#img").one("load", function() {
			if(this.complete){
				// hide
				$('#steps').hide();
				$('#banner-container').hide();
				$('#uploaded').hide();
				$('#progress-bar-container').hide();
				$('#upload').hide();

				// show			
				$('#labels').show( 400, function(){setOrientation(orientamento)}); //?								
				$('#labels').show(); //?
				$('#puzzlesList').html(htmlTab);
				$('#img-container').show();
				$('#navButtons').show();
				$('#puzzlesList').show();

				$('#buttonFile').removeClass('disabled');
				$('#inputFile').removeAttr('disabled');
			}
		})
	},
	'json'
	)// /post
});



$('#banner-container').on('click', 'button[data-show="steps"] ',function(){$('#banner-container').empty();$('#steps').show();})



function manageSession(){
	$.ajax({ 
		type: 'POST', 
		url: router, 
		data: {f : 'manageSession'}, 
		success: function(res){
			if(res.restore) {
				var html = '<h6>oppure puoi usare una delle immagini inviate precedentemente!</h6>';
				$.each(res.restore, function(k, value){
					html += '<img class="restored" src="'+value+'" data-file="'+res.files[k]+'">';
				});
				$('#uploaded').html(html).show();
			}
			$('#maxFileSize').text(res.limit / (1024 * 1024));
			$("#drop-area-div").dmUploader({
				url : router,
				dataType : 'json',
				maxFileSize : res.limit,
				onBeforeUpload : function(data) {
					$('#buttonFile').addClass('disabled', 'disabled');
					$('#inputFile').attr('disabled', 'disabled');
					$('#steps').hide();
					$('#uploaded').hide();
					$('#banner-container').hide();
					$('.progress-bar').css("width", '0%');
					$('#progress-bar-container').show();
				},
				onUploadProgress : function(id, percent) {
					$('.progress-bar').css("width", percent + '%');
					$('.progress-bar').css("aria-valuenow", percent);
					$('.progress-bar').text(percent + '%');
				},
				onUploadSuccess : function(id, res) {
					if (res.res == 'ok') {
						$('#labels').show( 400, function(){setOrientation(orientamento)});
						var imageBoxWidth = $('#menu-left').width();
						//$('#img-container').html('<img id="img" src="' + res.file + '">');
						$('#img-container').html('<img id="img" class="img-responsive" style="display: block; margin-left: auto; margin-right: auto;" src="' + res.file + '">');

						var htmlTab = '<table class="table table-hover">';
						htmlTab += '<thead><tr><th>Pezzi</th><th>Formato</th><th>Prezzo</th></tr></thead>';
						htmlTab += '<tbody>';
						$.each(res.puzzles, function(k, formato) {
							if (formato.shape) htmlTab += '<tr style="cursor: pointer" id=' + formato.id + ' data=' + formato.initSelectArea + ' shape='+formato.shape.toLowerCase()+'><td>' + formato.pezzi + '</td><td>' + formato.dimensioni + ' - ' + formato.shape +'</td><td><strong>€' + formato.prezzo + '</strong></td>'; 
							else htmlTab += '<tr style="cursor: pointer" id=' + formato.id + ' data=' + formato.initSelectArea + ' shape='+formato.shape+'><td>' + formato.pezzi + '</td><td>' + formato.dimensioni + '</td><td><strong>€' + formato.prezzo + '</strong></td>';
						})
						htmlTab += '</tbody>';
						htmlTab += '</table>';
		
						$("#img").one("load", function() {
							if(this.complete){
								$('#labels').show();
								$('#img-container').show();
								$('#navButtons').show();
								$('#progress-bar-container').hide();
								$('#upload').hide();
								$('#uploaded').hide();
								$('#puzzlesList').html(htmlTab);
								$('#puzzlesList').show();
								$('#buttonFile').removeClass('disabled');
								$('#inputFile').removeAttr('disabled');
							}
						})
					} else {
						var alert = '<div class="alert alert-danger alert-dismissible" role="alert">';
						alert += '<button type="button" class="close" data-show="steps" data-dismiss="alert";><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
						alert += res.detail;
						alert += '</div>';
						$('#progress-bar-container').hide();
						$('#banner-container').html(alert);
						$('#banner-container').show();
						$('#buttonFile').removeClass('disabled');
						$('#inputFile').removeAttr('disabled');
					}
				},
				onFileSizeError : function() {
					$('#steps').hide();
					var alert = '<div class="alert alert-danger alert-dismissible" role="alert">';
					alert += '<button type="button" class="close" data-show="steps" data-dismiss="alert";><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
					alert += '<strong>attenzione! </strong>il file è troppo grande e supera i limiti indicati.</div>';
					$('#banner-container').html(alert);
					$('#banner-container').show();
				}
			});
		},
		dataType: 'json'
	});
}
/* /step1 */

/*step 2*/
function setOrientation(orientamento) {
	if (orientamento == 'landscape') {
		$("#landscape").removeClass().addClass('label label-info');
		$("#portrait").removeClass().addClass('label label-default');
	} else {
		$("#landscape").removeClass().addClass('label label-default');
		$("#portrait").removeClass().addClass('label label-info');
	}  
	
}



function drawSelectArea(data, shape) {

	w = data[orientamento].w;
	h = data[orientamento].h;
	x = data[orientamento].x;
	y = data[orientamento].y;
	x2 = data[orientamento].x2;
	y2 = data[orientamento].y2;

	$('#img').Jcrop({
		addClass : 'jcrop-centered',
		trueSize : [w, h],
		setSelect : [x, y, x2, y2],
		aspectRatio : data[orientamento].ratio,
		bgOpacity : 0.2,
		mask : shape,
		orientamento: orientamento,
		onSelect : function(c) {
			$('#navNext').hide();
			$('#quality').empty();
			$.post(router, {
				f : 'getQuality',
				data : {
					sW : c.w,
					sH : c.h,
					pW : data[orientamento].printW,
					pH : data[orientamento].printH,
					oR : orientamento,
					c : c,
					s : data[orientamento].shape
				}
			}, function(res) {
				if (res == 4) {
					$('#quality').html('<span id="quality" class="text-success"><strong>&nbsp; ottima   &nbsp;&nbsp;&nbsp;</strong></span><img src="assets/images/icons/4stars_4.gif"></strong></span>');
					$('#navNext').show();
				} else if (res == 3) {
					$('#quality').html('<span class="text-info"><strong>&nbsp; buona   &nbsp;&nbsp;&nbsp;</strong></span><img src="assets/images/icons/4stars_3.gif"></strong></span>');
					$('#navNext').show();
				} else if (res == 2) {
					$('#quality').html('<span class="text-info"><strong>&nbsp; discreta   &nbsp;&nbsp;&nbsp;</strong></span><img src="assets/images/icons/4stars_2.gif"></strong></span>');
					$('#navNext').show();
				} else if (res == 1) {
					$('#quality').html('<span class="text-warning"><strong>&nbsp; scarsa  &nbsp;&nbsp;&nbsp;</strong></span><img src="assets/images/icons/4stars_1.gif"> &nbsp;&nbsp;&nbsp;<img src="assets/images/icons/attenzione.gif">');
					$('#navNext').show();
				} else if (res == 0)
					$('#quality').html('<span class="text-danger"><strong>&nbsp; insufficiente  &nbsp;&nbsp;&nbsp;</strong></span><img src="assets/images/icons/attenzione.gif">');
			});
		},
		onRelease : function() {
			$('#navNext').hide();
			$('#quality').empty();
		}
	});

}



$('#puzzlesList').on('click', 'table tbody tr', function() {
	
	$('#puzzlesList table tbody tr').css('background-color', '');
	$(this).css('background-color', '#FFFF00');
	
	var id = $(this).attr('id');
	var shape = $(this).attr('shape');
	var data = $.parseJSON($(this).attr('data'));
	puzzleSelected = id;

	$.post(router, {
		f : 'updateSession',
		data : { campo : 'products_id', value : id }
		}, function(){drawSelectArea(data, shape)}
	)
	
});



$("#labels").on('click', 'span', function(){
	orientamento = this.id;
	setOrientation(orientamento);
	if(puzzleSelected) {
		var id = $('#'+puzzleSelected).attr('id');
		var shape = $('#'+puzzleSelected).attr('shape');
		var data = $.parseJSON($('#'+puzzleSelected).attr('data'));
		drawSelectArea(data, shape);
	} 
})


$('#navPrev').click(function(e) {
	e.preventDefault();
	resetView();
	manageSession();
});



$('#navNext').click(function(e) {
	e.preventDefault();
	/*
	 * se il ppuzzle selezionato ha altre opzione oltre alla scatola, faccio apparire il modal di selezione, altrimento proseguo
	 */

	verificaOpzioni();
	/*POPUP SCELTA OPZIONE PRODOTTO */
	//$('#modalOpzione').modal('show');

	/*
	$('#labels').hide();
	$('#img-container').hide();
	$('#navButtons').hide();
	$('#puzzlesList').hide();

	loadBoxesCategories();
	*/
});



function verificaOpzioni() {
	$.post(	router,
			{ f : 'getOpzioniProdotto'},
			function(res){
				if(res == false) { //il prodotto non ha opzioni
					$('#labels').hide();
					$('#img-container').hide();
					$('#navButtons').hide();
					$('#puzzlesList').hide();
					
					$('#left-box').removeClass("col-md-8").addClass("col-md-6");
					$('#right-box').removeClass("col-md-4").addClass("col-md-6");
					loadBoxesCategories();
				} else {
					$('#modalOpzione .modal-body').html(res);
					$('#modalOpzione').modal('show');
				} 
				
			}
	)
};



$('#modalOpzione').on('click', 'tr', function(e){
	e.preventDefault();
	if($(this).attr('opzione') == "scatola_personalizzata") {
		
		$('#modalOpzione').modal('hide');
		$('#labels').hide();
		$('#img-container').hide();
		$('#navButtons').hide();
		$('#puzzlesList').hide();

		loadBoxesCategories();

	} else if ($(this).attr('opzione') == "preassemblato") {
		/*genero il puzzle e faccio apparire il popup di conferma ordine per il carrello*/
		$('body').css('cursor', 'wait', 'important');
		$('#modalOpzioneFootNote').toggle();
		$('#modalOpzioneFootWaitMsg').toggle();
		//$(this).css('cursor', 'wait');
		$.post(	router,
			{ f : 'getOrderDesc', boxed : 0, optionId : $(this).attr('idOpzione'), optionIdValue : $(this).attr('idOptionValue') },
			function(res){
				
				$('#modalOpzione').modal('hide');
				$('body').css('cursor', 'default');
				$('#modalOpzioneFootNote').toggle();
				$('#modalOpzioneFootWaitMsg').toggle();
				$('#orderConfBoxedHeader').hide();
				//$.post(router, { f : 'getOrderDesc' }, function(res){
				$('#orderConf .modal-body').html(res.body);
				$('#modalForm').html(res.footer);
				
				$('#orderConf').modal('show');
				//});
			}, 'json'
		)
	}
	//if(!$('#consenso').prop('checked'))	$('#modal-warning').show();
	//else $(this).closest( "form" ).submit(); 
})
/* /step2 */

/* step3 */
$('#navPrevBox').click(function(e) {
	e.preventDefault();
	$('#banner-container').empty().hide();
	$('#img-box-container').empty();
	$('#boxsList').empty();
	$('#navButtonsBox').hide();
	$('#navNextBox').hide();
	
	$('#left-box').removeClass("col-md-6").addClass("col-md-8");
	$('#right-box').removeClass("col-md-6").addClass("col-md-4");
	
	$('#labels').show();
	$('#img-container').show();
	$('#navButtons').show();
	$('#puzzlesList').show();
});



function resetView() {
	
	orientamento = 'landscape';
	puzzleSelected = false;

	$('#labels').hide();
	$('#quality').empty();
	$('#img-container').empty();
	$('#img-container').hide();
	$('#steps').show();
	$('#puzzlesList').empty();
	$('#puzzlesList').hide();
	$('#upload').show();
	$('#navButtons').hide();
	$('#navNext').hide();

}



function loadBoxesCategories(){

	$.ajax({
  		type: 'POST',
  		url: router,
  		data: { f : 'getBoxesList' },
	  		beforeSend: function(){
	  			$('body').css('cursor', 'wait', 'important');
	  			$('#banner-container').html('<p class="bg-primary" style="padding: 15px; margin-bottom: 240px;">Stiamo generando il tuo puzzle. Attendi per favore, possono essere necessari diversi secondi a seconda della pesantezza del file inviato. Al termine, seleziona una scatola per avere l\'anteprima definitiva...</p>').show();
			},
  		success: function(res){
  			$('body').css('cursor', 'default');
  			$('#banner-container').html('<p class="bg-primary" style="padding: 15px; margin-bottom: 240px;">seleziona una scatola per avere l\'anteprima definitiva...</p>').show();
			$('#boxsList').html(res).show();
			$('#navButtonsBox').show();
		}
	});
	
}

/*
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  e.target // newly activated tab
  e.relatedTarget // previous active tab
})*/
//$('#boxsList').on('click', 'a[role="tab"]', function(e){
//$('#boxsList').on('click', '#menu-right > a[data-toggle="tab"]', function(e){
$('#boxsList').on('click', 'a[data-toggle="tab"]', function(e){	

	e.preventDefault()

	//console.log(e.target) // newly activated tab
	//console.log(e.relatedTarget) // previous active tab

	$('#singleText').hide();
	$('#extendedText').hide();
	$(this).tab('show');
	//console.log(this)
	//$(e.target).tab('show');
});



var loading = false;
var indice = 0;
var boxId = 0;
$('#boxsList').on('click', 'div div img', function (e) {

	e.preventDefault();
	if ( loading == true ) return;
	
	loading = true;
	$('#navButtonsBox').hide();
	$('#img-box-container').empty();
	$('#banner-container').html('<p class="bg-primary" style="padding: 15px;">sto elaborando la scatola, attendi prego...</p>').show();
	var toggle = $('ul[role="tablist"] > li.active > a').attr('text-toggle');


	boxId = $(this).attr('boxId');
	indice = $(this).attr('indice');
	var elaborated = $(this).attr('data-elaborated');
	$.post(	router,
			{ f : 'getBoxes', boxId : boxId, indice : indice, redraw : false } ,
			function() {

				var d = new Date();
				var n = d.getTime();
				$('#img-box-container').html('<img src="'+elaborated+'?cache='+n+'">').show();
	  			$('#banner-container').empty().hide();
				$('#navButtonsBox').show();
				$('#navNextBox').show();
				$('#'+toggle).show();
				loading = false;
			
			}
	);
})



$('#boxsList').on('paste copy cut keyup keydown', 'div input', function(e){
	
	valuesSpan = $(this).parent('div').find('.limit');
	var aValues = valuesSpan.text().split("/");
	var currentChars = parseInt($(this).val().length);
	var limit = parseInt(aValues[1]);
	var currentValues = currentChars + '/' + limit;
	
	$(valuesSpan).text(currentChars + '/' + limit);
	if(currentChars <= limit) {
		if($(this).parent().hasClass('has-error')) $(this).parent().removeClass('has-error');
	}
	else {
		$(this).parent().addClass('has-error');
	} 
	
});



$('#boxsList').on('click', '.write', function(){
	
	if(loading) return;

	loading = true; 
	$('#navButtonsBox').hide();
	$('#img-box-container').hide();
	$('#banner-container').html('<p class="bg-primary" style="padding: 15px;">sto elaborando la scatola, attendi prego...</p>').show();

	var form = $(this).closest('form');
	var active = $('#boxsList').find('li.active a[data-toggle="tab"]');
	$(form).submit(function(e){
		e.preventDefault();
		$.post(router, { f : 'submitForm', form: $(this).attr('id'), data : $(this).serializeArray() },

			function () {
				$.post(	router, { f : 'getBoxes', boxId : boxId, indice : indice, redraw : false },
					function() {
						$('#banner-container').hide();
						var d = new Date();
						var n = d.getTime();
						var currentImg = $('#img-box-container > img').attr('src');
						var img = currentImg.substr(0, currentImg.indexOf('?'));
						$('#img-box-container').html('<img src="'+img+'?cache='+n+'">').show();
						$('#navButtonsBox').show();
						loading = false;
					}
				)
			}	
		);
		$(this).off();
	})
	
	$(form).submit();

})



$('#navNextBox').click(function(e){
	e.preventDefault();
	$.post(router, { f : 'getOrderDesc', boxed : 1, optionId : null, optionIdValue :  null}, function(res){
		//console.log(res)
		$('#orderConf .modal-body').html(res.body);
		$('#orderConfBoxedHeader').show();
		$('#modalForm').html(res.footer);
		$('#orderConf').modal('show');
	}, 'json');
});



$('#orderConf').on('click', 'form button[type=submit]', function(e){
	e.preventDefault();
	if(!$('#consenso').prop('checked'))	$('#modal-warning').show();
	else $(this).closest( "form" ).submit(); 
})



$('#orderConf').on('hide.bs.modal', function (e) {
  $("#consenso").prop("checked", false);
  $('#modal-warning').hide();
})



$("#boxsList").on("submit", "form", function (e) {     
	 e.preventDefault();
});
/* /step 3 */