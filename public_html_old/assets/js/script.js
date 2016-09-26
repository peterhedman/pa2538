$( document ).ready(function() {
    loginForm();
	navigation();
	navigationWidth();
	confirmDialog();
});


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

function loginForm(){
	
	//$(function(){

	var form = $('#login-register');

	form.on('submit', function(e){

		if(form.is('.loading, .loggedIn')){
			return false;
		}

		var email = form.find('input').val(),
			messageHolder = form.find('span');
			
		

		e.preventDefault();

		$.post(this.action, {email: email}, function(m){
			
			if(m.error){
				form.addClass('error');
				messageHolder.text(m.message);
				
				if(m.message == "You're logging in..."){
					 window.location = "/protected.php";
				}
			}
			else{
				form.removeClass('error').addClass('loggedIn');
				messageHolder.text(m.message);
				
				
			}
		});

	});

		$(document).ajaxStart(function(){
			form.addClass('loading');
		});
	
		$(document).ajaxComplete(function(){
			form.removeClass('loading');
		});
	//});
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