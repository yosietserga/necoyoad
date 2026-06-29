$(function(){
    initiWidgetVars();
    initWidgetUI();
    rowSortableUI();
    colSortableUI();
    initDragNDrop();

    var height = $(window).height() * 1.9;
    var width = $(window).width() * 0.9;

    $(window['widgetConfig'].containers.widgets +" .advanced").fancybox({
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
});