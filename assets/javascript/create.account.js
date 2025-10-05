$(function()
{	
	// Gestione perdita focus campo email per controllo duplicati
	$('#email').blur(function()
	{
		$('.formErrorMessage').remove();
		$('*').removeClass('formError');
		checkEmail();
	});
	
	// Gestione submit form - CORREZIONE PRINCIPALE
	$('#createAccountForm').submit(function(event)
	{
		event.preventDefault(); // Previeni submit default

		console.log('Form submit triggered'); // DEBUG
		
		// Esegui validazione
		if(submitAccountForm()) {
			// Validazione passata - invia il form
			console.log('Validazione completata con successo. Invio del form...');
			// Rimuovi l'event listener per evitare loop infinito
			$(this).off('submit').submit();
		} else {
			console.log('Errori di validazione trovati. Form non inviato.');
		}
	});
});

/*
* Funzione principale di validazione del form
*/
function submitAccountForm()
{
	// Pulisci errori precedenti
	$('.formErrorMessage').remove();
	$('*').removeClass('formError');
	
	var hasErrors = false;
	
	// 1. Controlla tutti i campi obbligatori
	hasErrors = checkRequired() || hasErrors;
	
	// 2. Validazione specifica email
	var email = $('#email').val().trim();
	if(email && !isValidEmail(email)) {
		displayFormError('#email', 'Inserisci un indirizzo email valido');
		hasErrors = true;
	}
	
	// 3. Validazione password
	var password = $('#password').val();
	var vpassword = $('#vpassword').val();
	
	if(password && vpassword && password !== vpassword) {
		displayFormError('#password', 'Le password non corrispondono');
		displayFormError('#vpassword', 'Le password non corrispondono');
		hasErrors = true;
	}
	
	if(password && password.length < 6) {
		displayFormError('#password', 'La password deve contenere almeno 6 caratteri');
		hasErrors = true;
	}
	
	// 4. Validazione campo stato - SEMPLIFICATA
	// Il campo stato è ora un normale input text, gestito automaticamente da checkRequired()
	
	// 5. Validazione checkbox accordi se presente e obbligatorio
	var $agreementCheckbox = $('#signupAgreement');
	if($agreementCheckbox.length && $agreementCheckbox.attr('require') === 'require') {
		if(!$agreementCheckbox.is(':checked')) {
			displayFormError('#signupAgreement', 'Devi accettare i termini e condizioni');
			hasErrors = true;
		}
	}
	
	return !hasErrors;
}

/*
* Controllo campi obbligatori generici
*/
function checkRequired()
{
	var hasErrors = false;
	
	$('[require="require"]').each(function() {
		var $field = $(this);
		var value = $field.val();
		var fieldType = $field.attr('type');
		var tagName = $field.prop('tagName').toLowerCase();
		
		// Gestione diversa per diversi tipi di campo
		var isEmpty = false;
		
		if(fieldType === 'checkbox') {
			isEmpty = !$field.is(':checked');
		} else if(tagName === 'select') {
			isEmpty = !value || value === '' || value === '0';
		} else {
			// Per tutti gli input text (incluso il nuovo campo stato)
			isEmpty = !value || value.trim() === '';
		}
		
		if(isEmpty) {
			var errorMessage = $field.attr('errorMessage') || 'Questo campo è obbligatorio';
			displayFormError('#' + $field.attr('id'), errorMessage);
			hasErrors = true;
		}
	});
	
	return hasErrors;
}

/*
* Visualizzazione errori sui campi
*/
function displayFormError(fieldSelector, message)
{
	var $field = $(fieldSelector);
	
	if(!$field.length) {
		console.warn('Campo non trovato:', fieldSelector);
		return;
	}
	
	// Rimuovi errori precedenti per questo campo
	$field.removeClass('formError');
	$field.siblings('.formErrorMessage').remove();
	$field.next('.formErrorMessage').remove();
	
	// Aggiungi classe errore
	$field.addClass('formError');
	
	// Determina il messaggio da mostrare
	var errorMsg = message;
	if(typeof message === 'number') {
		// Compatibilità con vecchio sistema - usa attributi errorMessage
		var attrName = 'errorMessage' + (message > 1 ? message : '');
		errorMsg = $field.attr(attrName) || 'Errore di validazione';
	}
	
	// Aggiungi messaggio di errore visibile
	if(errorMsg) {
		var $errorDiv = $('<div class="formErrorMessage" style="color: #d32f2f; font-size: 12px; margin-top: 3px; font-weight: normal;">' + errorMsg + '</div>');
		
		// Per checkbox, posiziona l'errore dopo il label
		if($field.attr('type') === 'checkbox') {
			var $label = $('label[for="' + $field.attr('id') + '"]');
			if($label.length) {
				$label.after($errorDiv);
			} else {
				$field.parent().append($errorDiv);
			}
		} else {
			$field.after($errorDiv);
		}
	}
	
	// Scroll al primo errore se è il primo campo con errore
	if($('.formError').first().is($field)) {
		$('html, body').animate({
			scrollTop: $field.offset().top - 100
		}, 500);
	}
}

/*
* Validazione formato email
*/
function isValidEmail(email) 
{
	var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	return emailRegex.test(email);
}

/*
* Controllo esistenza email nel database
*/
function checkEmail()
{
	var email = $('#email').val().trim();
	
	if(!email || !isValidEmail(email)) {
		return;
	}
	
	// Mostra indicatore di caricamento
	$('#email').after('<span class="email-check-loading" style="color: #666; font-size: 12px; margin-left: 5px;">Controllo disponibilità...</span>');
	
	$.ajax({
		type: 'POST',
		url: 'actions.php',
		data: { 
			"action": "checkEmail",
			"email": email 
		},
		dataType: 'json',
		timeout: 5000,
		success: function(data) {
			$('.email-check-loading').remove();
			
			if(data && data.emailsReturned > 0) {
				displayFormError('#email', 'Questa email è già registrata nel sistema');
			}
		},
		error: function(xhr, status, error) {
			$('.email-check-loading').remove();
			console.warn('Errore controllo email:', error);
			// Non mostrare errore all'utente, è solo un controllo preventivo
		}
	});	
}