jQuery( document ).ready( function( $ )
{	
	var $usersDropdown = $( '#temporary-access-user-list' );
	
	var $userRolesDropdown = $( '#temporary-access-user-role-list' );

	$usersDropdown.select2();
	
	$userRolesDropdown.select2();

	$usersDropdown.on( "select2:select", function ( e )
	{
		var userRole = $( e.params.data.element ).attr( 'data-userrole' );
		
		$userRolesDropdown.val( userRole.split( ',' ) ).trigger( 'change' );

		$( '.temporary-link' ).html( '' );
		
		$( '#temporary-access-time-limit-value' ).val( '' );
	} );

	$( '#temporary-access-form' ).submit( function( event )
	{
		event.preventDefault();

		event.stopImmediatePropagation();

		$( this ).find( '#submit' ).prop( 'disabled', true ).val( TA_TEMPORARY_ACCESS.generating_text );

		var data = {
			'action'						: 'ta_temporary_access_generate_link',
			'user'							: $( '#temporary-access-user-list' ).val(),
			'timeLimitVal'					: $( '#temporary-access-time-limit-value' ).val(),
			'timeLimitValType'				: $( '#temporary-access-time-limit' ).val(),
			'ta_temporary_access_settings'	: $( '#ta_temporary_access_settings' ).val(),
			'ta_temporary_access_enable'	: $( '#ta_temporary_access_enable' ).is( ':checked' ),
		};

		$.post( ajaxurl, data, function( response )
		{
			if ( response.success === false )
			{
				alert( TA_TEMPORARY_ACCESS.ajax_error_text );
			}
			else
			{
				$( '.temporary-link' ).html( `<code>${response.data.link}</code>` );
			}
		} );
		
		$( this ).find( '#submit' ).prop( 'disabled', false ).val( TA_TEMPORARY_ACCESS.submit_button_text );		

		return false;
	} );

	var ta_temporary_access_time_count = $( '#ta_temporary_access_time_count' );

	if ( ta_temporary_access_time_count.length )
	{
		var distance = ta_temporary_access_time_count.attr( 'data-time' );

		// Update the count down every 1 second
		var ta_temporary_timer = setInterval( function()
		{		    
		    // Time calculations for hours, minutes and seconds
		    var d, h, m, s;
			s = Math.floor( distance / 1000 );
			m = Math.floor( s / 60 );
			s = s % 60;
			h = Math.floor( m / 60 );
			m = m % 60;
			d = Math.floor( h / 24 );
			h = h % 24;
		    
		    // Output the result in an element
		    ta_temporary_access_time_count.html( d + "d " + h + "h " + m + "m " + s + "s" );
		    
		    // If the count down is over 
		    if ( distance < 0 )
		    {
		        clearInterval( ta_temporary_timer );

		        location.reload();
		    }

		    distance -= 1000;
		
		}, 1000 );
	}
} );