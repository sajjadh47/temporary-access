jQuery( document ).ready( function( $ )
{	
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