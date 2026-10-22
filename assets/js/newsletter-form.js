// Submits newsletter forms to MailerLite in the background and shows the result
// in place, instead of navigating to MailerLite's JSON reply.
( function () {
	document.querySelectorAll( '.john-newsletter-form' ).forEach( function ( form ) {
		var message = form.querySelector( '.john-newsletter-form__message' );
		var button = form.querySelector( 'button' );

		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			button.disabled = true;
			message.textContent = '';
			form.classList.remove( 'has-error' );

			fetch( form.action, { method: 'POST', body: new FormData( form ) } )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( result ) {
					if ( result.success ) {
						form.classList.add( 'is-subscribed' );
						message.textContent = form.dataset.success;
						return;
					}
					var errors = result.errors && result.errors.fields && result.errors.fields.email;
					throw { fromMailerLite: errors ? errors[ 0 ] : null };
				} )
				.catch( function ( error ) {
					form.classList.add( 'has-error' );
					message.textContent = ( error && error.fromMailerLite ) || form.dataset.error;
					button.disabled = false;
				} );
		} );
	} );
} )();
