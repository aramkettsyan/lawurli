$(document).ready(function() {
	
	//////////////////////
	/// jQuery UI TABS ///
	//////////////////////
    $( ".tabs" ).tabs();


	//////////////////////////
	/// Drop Down on Click ///
	//////////////////////////
	$('.dropDnBtn').on('click', function(e){
    	e.preventDefault();
    	e.stopPropagation();
    	var thisDD = $(this);
        $('.dropDn').not(thisDD.parent('.dropDn')).removeClass('dropDnOpened');
        $('.dropDn').not(thisDD.parent('.dropDn')).children('.dropDnSub').hide();
    	thisDD.next('.dropDnSub').fadeToggle();
    	thisDD.parent('.dropDn').toggleClass('dropDnOpened');
    });
    if ($('.dropDn').hasClass('dropDnOpened')) {
    	$(this).next('.dropDnSub').fadeToggle();
 console.log($(this))
	}; 
    $('.dropDnSub').on('click', function(e){
        e.stopPropagation();
    })
    $('body').on('click', function(){
        $('.dropDnSub').fadeOut();
        $('.dropDn').removeClass('dropDnOpened');
    })


    //////////////////////
    /// magnific popup ///
    //////////////////////
    $('.popupBtn').magnificPopup({
        type: 'inline',
        preloader: false,
        focus: '#name',
        mainClass: 'popupAnim',
        removalDelay: 300
    });


    ///////////////////////
    /// advanced search ///
    ///////////////////////
    // $('.openAdvSrch').on('click', function(){
    //     $('.advSrchCont').slideToggle();
    // });

    ////////////////////////
    /// mCustomScrollBar ///
    ////////////////////////
    $('.customScroll').mCustomScrollbar({
        scrollbarPosition: "outside"
    })

}); /* end document ready */








