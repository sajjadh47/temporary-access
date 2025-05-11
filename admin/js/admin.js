jQuery( document ).ready( function( $ )
{
	var ta_temporary_access_time_count = $( '#ta_temporary_access_time_count' );

	if ( ta_temporary_access_time_count.length )
	{
		var distance = ta_temporary_access_time_count.attr( 'data-time' );

		// Update the count down every 1 second
		var ta_temporary_timer = setInterval( function()
		{
			// If the count down is over 
			if ( distance < 0 )
			{
				clearInterval( ta_temporary_timer );

				window.location.reload();
			}
			
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

			distance -= 1000;
		
		}, 1000 );
	}

	$( document ).on( 'click', '.temporary-link .copy-to-clipboard', function( event )
	{
		var codeElement	= $( this ).closest( '.temporary-link' ).find( 'code' )[0];

		var range = document.createRange();

		range.selectNodeContents( codeElement );

		var selection = window.getSelection();

		selection.removeAllRanges();

		selection.addRange( range );

		try
		{
			document.execCommand( 'copy' );

			$( this ).fadeOut( 'slow', function()
			{
				$( '.temporary-link .code-copied' ).fadeIn( 'fast' );
			} );
		}
		catch ( err )
		{
			console.error( 'Failed to copy:', err );
		}

		selection.removeAllRanges();
	} );
} );