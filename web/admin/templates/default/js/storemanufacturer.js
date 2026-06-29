$(function(){
    $('.htabs2 .htab2').on('click',function() {
        $('.htab2').each(function(){
           $($(this).attr('tab')).hide();
           $(this).removeClass('selected'); 
        });
        $(this).addClass('selected');
        $($(this).attr('tab')).show(); 
    });
    $('.htabs2 .htab2:first-child').trigger('click');
    
    loadFormWidgets();
    
    if (typeof window.isAForm != 'undefined') {
        if (!$.fn.fancybox) {
            $(document.createElement('script')).attr({
                src:'js/vendor/jquery.fancybox.pack.js',
                type:'text/javascript'
            }).appendTo('body');
        }
        
        var height = $(window).height() * 0.8;
        var width = $(window).width() * 0.8;
        
        $(".filemanager").fancybox({
            maxWidth	: width,
            maxHeight	: height,
            fitToView	: false,
            width	: '90%',
            height	: '90%',
            autoSize	: false,
            closeClick	: false,
            openEffect	: 'none',
            closeEffect	: 'none'
        });
    }
});

function loadFormWidgets() {
    if (window.oid) $('#widgetsFormWrapper').html('<img src="'+ window.imageFolderUrl +'loader.gif" alt="Cargando" />');
    $('#widgetsFormWrapper').load(window.widgetsLoadUrl);
}