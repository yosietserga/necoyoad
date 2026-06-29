function lightBoxWindowResize(e) {
    var width  = (window.innerWidth - $(e).width() - 100) / 2;
    var height = (window.innerHeight - $(e).height() - 100) / 2;

    $(e).css({
        'marginTop': height +'px',
        'marginLeft': width +'px'
    });
}