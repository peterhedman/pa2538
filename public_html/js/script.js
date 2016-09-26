$( document ).ready(function() {
	navigation();
	navigationWidth();
	confirmDialog();
	dateTimePicker();
});

//http://xdsoft.net/jqplugins/datetimepicker/
function dateTimePicker(){
	jQuery('#date').datetimepicker({
		 timepicker:false,
		 format:'Y-m-d'
		});
	
	jQuery('#time').datetimepicker({
	  datepicker:false,
	  format:'H:i'
	});
	
}


function confirmDialog(){
    $("input.confirm").click(function(e){
        if(!confirm('Are you sure?')){
            e.preventDefault();
            return false;
        }
        return true;
    });
}

function navigationWidth(){
	
	var nrTotLi = $('#nav ul li').length;
	var nrLiChildren = $("#nav ul li ul").children().length;
	var nrLi = nrTotLi - nrLiChildren;

	var liWidth = 100/nrLi;
	
	$('#nav > ul > li').css("width", liWidth+"%");
}

function navigation(){
	
	;(function( $, window, document, undefined )
	{
		$.fn.doubleTapToGo = function( params )
		{
			if( !( 'ontouchstart' in window ) &&
				!navigator.msMaxTouchPoints &&
				!navigator.userAgent.toLowerCase().match( /windows phone os 7/i ) ) return false;
	
			this.each( function()
			{
				var curItem = false;
	
				$( this ).on( 'click', function( e )
				{
					var item = $( this );
					if( item[ 0 ] != curItem[ 0 ] )
					{
						e.preventDefault();
						curItem = item;
					}
				});
	
				$( document ).on( 'click touchstart MSPointerDown', function( e )
				{
					var resetItem = true,
						parents	  = $( e.target ).parents();
	
					for( var i = 0; i < parents.length; i++ )
						if( parents[ i ] == curItem[ 0 ] )
							resetItem = false;
	
					if( resetItem )
						curItem = false;
				});
			});
			return this;
		};
	})( jQuery, window, document );
}