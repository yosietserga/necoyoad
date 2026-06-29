var pages = [];
var languages = [];

function makeSortable() {
    
    $("ol.items").nestedSortable({
      forcePlaceholderSize: true,
      handle: "div.item",
      helper: "clone",
      items: "li",
      maxLevels: 3,
      opacity: 0.6,
      placeholder: "placeholder",
      revert: 250,
      tabSize: 25,
      tolerance: "pointer",
      toleranceElement: "> div.item",
      update: function (event, ui) {
        $("ol.items li").each(function () {
          parentIndex = "";
          idIndex = "";
          parent = $(this).parents("li");
          if (parent.index() >= 0) {
            principal = $(parent).parents("li");
            if (principal.index() >= 0) {
              parent = $(this).parents("li:eq(0)");
              parentIndex =
                "[" +
                $(principal).index() +
                "_" +
                $(parent).index() +
                "_" +
                $(this).index() +
                "]";
              idIndex =
                $(principal).index() +
                "_" +
                $(parent).index() +
                "_" +
                $(this).index();
            } else {
              parentIndex =
                "[" + $(parent).index() + "_" + $(this).index() + "]";
              idIndex = $(parent).index() + "_" + $(this).index();
            }
          } else {
            parentIndex = "[" + $(this).index() + "]";
            idIndex = $(this).index();
          }

          $(this)
            .find(".menu_link")
            .attr({
              id: "link." + idIndex + ".link",
              name: "link" + parentIndex + "[link]",
            });
          $(this)
            .find(".menu_tag")
            .attr({
              id: "link." + idIndex + ".tag",
              name: "link" + parentIndex + "[tag]",
            });
        });
      },
    });
};

$(function() {
    $('.htabs2 .htab2').on('click', function() {
        $('.htab2').each(function() {
            $($(this).attr('tab')).hide();
            $(this).removeClass('selected');
        });
        $(this).addClass('selected');
        $($(this).attr('tab')).show();
    });
    $('.htabs2 .htab2:first-child').trigger('click');

    $.getJSON(createAdminUrl('api/v1/pages'))
        .done(function(data) {
            pages = data.payload.results;
        });

    $.getJSON(createAdminUrl('api/v1/languages'))
        .done(function(data) {
            languages = data.payload.results;
        });

    $('#_name').on('change', function() {
        $('#name').val($(this).val());
    });
    
    makeSortable();

    let cssIconsPath = window.nt.http_catalog + 'assets/css/icons/';

    if ($('link[href="' + cssIconsPath + 'elusive-icons.css' + '"]').length == 0) {
        $('<link>').attr({
            href: cssIconsPath + 'elusive-icons.css',
            rel: 'stylesheet'
        }).appendTo('head');
    }

    if ($('link[href="' + cssIconsPath + 'fontawesome-icons.css' + '"]').length == 0) {
        $('<link>').attr({
            href: cssIconsPath + 'fontawesome-icons.css',
            rel: 'stylesheet'
        }).appendTo('head');
    }

    if ($('link[href="' + cssIconsPath + 'foundation-icons.css' + '"]').length == 0) {
        $('<link>').attr({
            href: cssIconsPath + 'foundation-icons.css',
            rel: 'stylesheet'
        }).appendTo('head');
    }

    if ($('link[href="' + cssIconsPath + 'materialdesign-icons.css' + '"]').length == 0) {
        $('<link>').attr({
            href: cssIconsPath + 'materialdesign-icons.css',
            rel: 'stylesheet'
        }).appendTo('head');
    }
});

function getLastItemIndex() {
    return $('ol.items li').length;
}

function getNextItemIndex() {
    return (getLastItemIndex() + 1);
}

function addLink(token) {
    const k = getNextItemIndex();
    const link = $('#external_link').val();
    const tag = $('#external_tag').val();
    const li = __addItem(k, link, tag, '');

    $.getJSON(createAdminUrl('common/home/slug'), {
        slug: tag
    }).done(function(resp) {
        $('#link_' + k + '_keyword').val(resp.slug);
        makeSortable();
    });
}

function addPage(token) {

    if (!$('#pagesWrapper :checkbox').is(':checked')) {
        alert('Debe seleccionar al menos una p\u00E1gina');
        return false;
    }

    $.post(createAdminUrl('content/menu/page'), $('#pagesWrapper :checkbox:checked').serialize()).done(function(response) {
        var data = $.parseJSON(response);
        $.each(data, function(i, item) {
            const k = getNextItemIndex();
            const link = item.href;
            const tag = item.title;
            const keyword = item.keyword;
            const li = __addItem(k, link, tag, keyword);
        });
        
        $('#pagesWrapper :checkbox').removeAttr('checked');
        
        makeSortable();
    });
}

function addCategory(token) {
    if (!$('#categoriesWrapper :checkbox').is(':checked')) {
        alert('Debe seleccionar al menos una categor\u00EDa de productos');
        return false;
    }

    $.post('index.php?r=content/menu/category&token=' + token, $('#categoriesWrapper :checkbox:checked').serialize(), function(response) {
        var data = $.parseJSON(response);
        $.each(data, function(i, item) {
            const k = getNextItemIndex();
            const link = item.href;
            const tag = item.title;
            const keyword = item.keyword;
            const li = __addItem(k, link, tag, keyword);
        });
        $('#categoriesWrapper :checkbox').removeAttr('checked');
        
        makeSortable();
    });
}

function addPostCategory(token) {
    if (!$('#post_categoriesWrapper :checkbox').is(':checked')) {
        alert('Debe seleccionar al menos una categor\u00EDa de productos');
        return false;
    }

    $.post(createAdminUrl('content/menu/postcategory'), $('#post_categoriesWrapper :checkbox:checked').serialize()).done(function(response) {
        var data = $.parseJSON(response);
        $.each(data, function(i, item) {
            const k = getNextItemIndex();
            const link = item.href;
            const tag = item.title;
            const keyword = item.keyword;
            const li = __addItem(k, link, tag, keyword);
        });
        $('#post_categoriesWrapper :checkbox').removeAttr('checked');
        
        makeSortable();
    });
}

function __addLabel(k, name, text) {
    return $(document.createElement('label'))
        .attr({
            'for': 'link_' + k + '_' + name
        })
        .addClass('neco-label')
        .text(text);
}

function __addInput(k, name, value) {
    return $(document.createElement('input'))
        .addClass('menu_' + name)
        .css({
            width: '60%'
        })
        .attr({
            value: value,
            type: 'text',
            name: 'link[' + k + '][' + name + ']',
            id: 'link_' + k + '_' + name
        });
}

function __addItem(k, link, tag, keyword) {

    const selectSubMenuType = '<select id="link_' + k + '_submenu_type" name="link[' + k + '][submenu_type]" style="width:40%" onchange="' +
        'if (this.value===\'links\') { $(\'#link_row_' + k + '_page_id\').hide(); $(\'#link_row_' + k + '_html_content\').hide(); }' +
        'if (this.value===\'page_id\') { $(\'#link_row_' + k + '_html_content\').hide(); $(\'#link_row_' + k + '_page_id\').show(); }' +
        'if (this.value===\'html_content\') { $(\'#link_row_' + k + '_page_id\').hide(); $(\'#link_row_' + k + '_html_content\').show(); }' +
        '">' +
        '<option value="links">Sub-Links</option>' +
        '<option value="page_id">A Page</option>' +
        '<option value="html_content">HTML Content</option>' +
        '</select>';

    inputHtmlOptions = () => {
        html = '<option value="">Seleccione Contenido</option>';
        $.each(pages, (i, item) => {
            html += '<option value="' + item.id + '">' + item.title + '</option>';
        });
        return html;
    };

    const selectPageId = $(document.createElement('select'))
        .addClass('menu_html')
        .css({
            width: '60%'
        })
        .attr({
            name: 'link[' + k + '][page_id]',
            id: 'link_' + k + '_page_id'
        }).append(inputHtmlOptions());

    let $html = '<div id="languages_' + k + '" class="htabs2">';
    let $html2 = '';

    $.each(languages, (i, language) => {
        let $i = language.id + k;
        $html += '<a tab="#language' + $i + '" class="htab2">';
        $html += '<img src="images/flags/' + language.image + '" title="' + language.name + '" /> ' + language.name;
        $html += '</a>';

        $html2 += '<div id="language' + $i + '">';
        $html2 += '<textarea name="link[' + k + '][descriptions][' + language.id + '][description]" id="description' + $i + '"></textarea>';
        $html2 += '</div>';
    });

    $html += '</div>';
    $html += $html2;

    const itemOptions = __addItemOptions(k,
        [{
            name: 'icons',
            label: __addLabel(k, 'icon', 'Select An Icon:'),
            input: __drawFontIcons(k)
        },
        {
            name: 'link',
            label: __addLabel(k, 'link', 'Url:'),
            input: __addInput(k, 'link', link)
        },
        {
            name: 'tag',
            label: __addLabel(k, 'tag', 'Etiqueta:'),
            input: __addInput(k, 'tag', tag)
        },
        {
            name: 'keyword',
            label: __addLabel(k, 'keyword', 'Slug:'),
            input: __addInput(k, 'keyword', keyword)
        },
        {
            name: 'class_css',
            label: __addLabel(k, 'class_css', 'Clases CSS:'),
            input: __addInput(k, 'class_css')
        },
        {
            name: 'submenu_type',
            label: __addLabel(k, 'submenu_type', 'Select Sub-Menu Type:'),
            input: selectSubMenuType
        },
        {
            name: 'page_id',
            label: __addLabel(k, 'page_id', 'Page As Sub-Menu:'),
            input: selectPageId
        },
        {
            name: 'html_content',
            label: __addLabel(k, 'html_content', 'Contenido HTML:'),
            input: $html
        }]);

    const div = $(document.createElement('div'))
        .addClass('item')
        .append('<b>' + tag + '</b><a class="showOptions" onclick="$(\'#linkOptions' + k + '\').slideToggle(\'fast\')">&darr;</a>');

    const li = $(document.createElement('li'))
        .attr({
            id: 'li_' + k
        })
        .append(div)
        .append(itemOptions)
        .appendTo('.items');

    $('#link_row_' + k + '_page_id').hide();
    $('#link_row_' + k + '_html_content').hide();

    if (typeof cssEditorConfig != 'undefined') {
        $.each(languages, (i, language) => {
            let $i = language.id + k;
            CKEDITOR.replace('description' + $i, cssEditorConfig);
        });
    }

    $('#link_row_' + k + '_icons li').on('click', function(){
        $('#link_row_' + k + '_icons li[data-icon]').removeClass('selected');
        let v = $(this).addClass('selected').find('span').attr('class');
        $('#link_' + k + '_icon').val( v );
    });

    return li;
}

function __addItemOptions(k, options) {
    let div = $(document.createElement('div'))
        .addClass('itemOptions')
        .attr({
            id: 'linkOptions' + k,
        });

    div.append('<a style="float:right;font-size:10px;" onclick="$(\'#li_' + k + '\').remove()">[ Eliminar ]</a>');

    $.each(options, (i, opt) => {
        div.append(__addRow(k, opt)).append('<div class="clear"></div>');
    });
    div.append('<hr />');

    return div;
}

function __addRow(k, opt) {
    return $(document.createElement('div'))
        .attr({
            id: 'link_row_' + k + '_' + opt.name
        })
        .addClass('row')
        .append(opt.label)
        .append(opt.input);
}

let divIcons = null;
function __drawFontIcons(k) {
    if (!divIcons) {
        divIcons = $('<div>')
        .css({
            width:'90%',
            height:'200px',
            overflow:'scroll'
        })
        .append('<hr />')
        .append( __drawIconsList() );
    }

    return divIcons.clone()
        .append('<input type="hidden" id="link_' + k + '_icon" name="link[' + k + '][icon]" value="" />')
        .prepend( __drawSearchIcons(k) );
}

let divSearchIcons = null;
function __drawSearchIcons(k) {
    if (!divSearchIcons) {
        divSearchIcons = $('<input>')
        .css({
            width:'96%',
            margin:0
        })
        .attr({
            placeholder:'Search icon ...',
            type: 'text',
            id: 'link_' + k + '_searchicon'
        });
    }

    return divSearchIcons
        .clone()
        .on('keyup', e => {
            __searchIcon(k);
        });
}

function __searchIcon(k) {
    let t = $('#link_' + k + '_searchicon').val().toLowerCase();
    if (t.trim().length > 0) {
        $('#link_row_' + k + '_icons li[data-icon]').each(function(){
            if ($(this).attr('data-icon').indexOf( t ) == -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    } else {
        $('#link_row_' + k + '_icons').find('li[data-icon]').show();
    }
}

function showIcons(index, icon=null){
    if ($('#link_row_'+ index +'_icons li').length == 0) {
        $('#link_row_'+ index +'_icons').html( __drawFontIcons(index) );

        $('#link_row_'+ index +'_icons li').on('click', function(){
            $('#link_row_'+ index +'_icons li[data-icon]').removeClass('selected');
            let v = $(this).addClass('selected').find('span').attr('class');
            $('#link_'+ index +'_icon').val( v );
        });
    }

    if (icon) {
        var s = $('#link_row_'+ index +'_icons li[data-icon="'+ icon +'"]').addClass('selected').find('span').attr('class'); 
        $('#link_'+ index +'_icon').val( s );
    }
}

let divFontIcons = null;
function __drawIconsList() {
    if (divFontIcons) return divFontIcons;
    const fas = __getFontAwesomeSolidListIcons();
    const far = __getFontAwesomeRegularListIcons();
    const fab = __getFontAwesomeBrandsListIcons();
    const ul = $('<ul>');
    
    fas.forEach((v,k) => {
        ul.append('<li data-icon="'+ v +'"><small>'+ v +'</small><span class="fa-'+ v +' fas"></span></li>');
    });

    far.forEach((v,k) => {
        ul.append('<li data-icon="'+ v +'"><small>'+ v +'</small><span class="fa-'+ v +' far"></span></li>');
    });

    fab.forEach((v,k) => {
        ul.append('<li data-icon="'+ v +'"><small>'+ v +'</small><span class="fa-'+ v +' fab"></span></li>');
    });
    divFontIcons = ul;
    return divFontIcons;
}

function __getFontAwesomeSolidListIcons() {
    return ["ad","address-book","address-card","adjust","air-freshener","align-center","align-justify","align-left","align-right","allergies","ambulance","american-sign-language-interpreting","anchor","angle-double-down","angle-double-left","angle-double-right","angle-double-up","angle-down","angle-left","angle-right","angle-up","angry","ankh","apple-alt","archive","archway","arrow-alt-circle-down","arrow-alt-circle-left","arrow-alt-circle-right","arrow-alt-circle-up","arrow-circle-down","arrow-circle-left","arrow-circle-right","arrow-circle-up","arrow-down","arrow-left","arrow-right","arrow-up","arrows-alt","arrows-alt-h","arrows-alt-v","assistive-listening-systems","asterisk","at","atlas","atom","audio-description","award","backspace","backward","balance-scale","ban","band-aid","barcode","bars","baseball-ball","basketball-ball","bath","battery-empty","battery-full","battery-half","battery-quarter","battery-three-quarters","bed","beer","bell","bell-slash","bezier-curve","bible","bicycle","binoculars","birthday-cake","blender","blender-phone","blind","bold","bolt","bomb","bone","bong","book","book-dead","book-open","book-reader","bookmark","bowling-ball","box","box-open","boxes","braille","brain","briefcase","briefcase-medical","broadcast-tower","broom","brush","bug","building","bullhorn","bullseye","burn","bus","bus-alt","business-time","calculator","calendar","calendar-alt","calendar-check","calendar-minus","calendar-plus","calendar-times","camera","camera-retro","campground","cannabis","capsules","car","car-alt","car-battery","car-crash","car-side","caret-down","caret-left","caret-right","caret-square-down","caret-square-left","caret-square-right","caret-square-up","caret-up","cart-arrow-down","cart-plus","cat","certificate","chair","chalkboard","chalkboard-teacher","charging-station","chart-area","chart-bar","chart-line","chart-pie","check","check-circle","check-double","check-square","chess","chess-bishop","chess-board","chess-king","chess-knight","chess-pawn","chess-queen","chess-rook","chevron-circle-down","chevron-circle-left","chevron-circle-right","chevron-circle-up","chevron-down","chevron-left","chevron-right","chevron-up","child","church","circle","circle-notch","city","clipboard","clipboard-check","clipboard-list","clock","clone","closed-captioning","cloud","cloud-download-alt","cloud-meatball","cloud-moon","cloud-moon-rain","cloud-rain","cloud-showers-heavy","cloud-sun","cloud-sun-rain","cloud-upload-alt","cocktail","code","code-branch","coffee","cog","cogs","coins","columns","comment","comment-alt","comment-dollar","comment-dots","comment-slash","comments","comments-dollar","compact-disc","compass","compress","concierge-bell","cookie","cookie-bite","copy","copyright","couch","credit-card","crop","crop-alt","cross","crosshairs","crow","crown","cube","cubes","cut","database","deaf","democrat","desktop","dharmachakra","diagnoses","dice","dice-d20","dice-d6","dice-five","dice-four","dice-one","dice-six","dice-three","dice-two","digital-tachograph","directions","divide","dizzy","dna","dog","dollar-sign","dolly","dolly-flatbed","donate","door-closed","door-open","dot-circle","dove","download","drafting-compass","dragon","draw-polygon","drum","drum-steelpan","drumstick-bite","dumbbell","dungeon","edit","eject","ellipsis-h","ellipsis-v","envelope","envelope-open","envelope-open-text","envelope-square","equals","eraser","euro-sign","exchange-alt","exclamation","exclamation-circle","exclamation-triangle","expand","expand-arrows-alt","external-link-alt","external-link-square-alt","eye","eye-dropper","eye-slash","fast-backward","fast-forward","fax","feather","feather-alt","female","fighter-jet","file","file-alt","file-archive","file-audio","file-code","file-contract","file-csv","file-download","file-excel","file-export","file-image","file-import","file-invoice","file-invoice-dollar","file-medical","file-medical-alt","file-pdf","file-powerpoint","file-prescription","file-signature","file-upload","file-video","file-word","fill","fill-drip","film","filter","fingerprint","fire","fire-extinguisher","first-aid","fish","fist-raised","flag","flag-checkered","flag-usa","flask","flushed","folder","folder-minus","folder-open","folder-plus","font","football-ball","forward","frog","frown","frown-open","funnel-dollar","futbol","gamepad","gas-pump","gavel","gem","genderless","ghost","gift","glass-martini","glass-martini-alt","glasses","globe","globe-africa","globe-americas","globe-asia","golf-ball","gopuram","graduation-cap","greater-than","greater-than-equal","grimace","grin","grin-alt","grin-beam","grin-beam-sweat","grin-hearts","grin-squint","grin-squint-tears","grin-stars","grin-tears","grin-tongue","grin-tongue-squint","grin-tongue-wink","grin-wink","grip-horizontal","grip-vertical","h-square","hammer","hamsa","hand-holding","hand-holding-heart","hand-holding-usd","hand-lizard","hand-paper","hand-peace","hand-point-down","hand-point-left","hand-point-right","hand-point-up","hand-pointer","hand-rock","hand-scissors","hand-spock","hands","hands-helping","handshake","hanukiah","hashtag","hat-wizard","haykal","hdd","heading","headphones","headphones-alt","headset","heart","heartbeat","helicopter","highlighter","hiking","hippo","history","hockey-puck","home","horse","hospital","hospital-alt","hospital-symbol","hot-tub","hotel","hourglass","hourglass-end","hourglass-half","hourglass-start","house-damage","hryvnia","i-cursor","id-badge","id-card","id-card-alt","image","images","inbox","indent","industry","infinity","info","info-circle","italic","jedi","joint","journal-whills","kaaba","key","keyboard","khanda","kiss","kiss-beam","kiss-wink-heart","kiwi-bird","landmark","language","laptop","laptop-code","laugh","laugh-beam","laugh-squint","laugh-wink","layer-group","leaf","lemon","less-than","less-than-equal","level-down-alt","level-up-alt","life-ring","lightbulb","link","lira-sign","list","list-alt","list-ol","list-ul","location-arrow","lock","lock-open","long-arrow-alt-down","long-arrow-alt-left","long-arrow-alt-right","long-arrow-alt-up","low-vision","luggage-cart","magic","magnet","mail-bulk","male","map","map-marked","map-marked-alt","map-marker","map-marker-alt","map-pin","map-signs","marker","mars","mars-double","mars-stroke","mars-stroke-h","mars-stroke-v","mask","medal","medkit","meh","meh-blank","meh-rolling-eyes","memory","menorah","mercury","meteor","microchip","microphone","microphone-alt","microphone-alt-slash","microphone-slash","microscope","minus","minus-circle","minus-square","mobile","mobile-alt","money-bill","money-bill-alt","money-bill-wave","money-bill-wave-alt","money-check","money-check-alt","monument","moon","mortar-pestle","mosque","motorcycle","mountain","mouse-pointer","music","network-wired","neuter","newspaper","not-equal","notes-medical","object-group","object-ungroup","oil-can","om","otter","outdent","paint-brush","paint-roller","palette","pallet","paper-plane","paperclip","parachute-box","paragraph","parking","passport","pastafarianism","paste","pause","pause-circle","paw","peace","pen","pen-alt","pen-fancy","pen-nib","pen-square","pencil-alt","pencil-ruler","people-carry","percent","percentage","person-booth","phone","phone-slash","phone-square","phone-volume","piggy-bank","pills","place-of-worship","plane","plane-arrival","plane-departure","play","play-circle","plug","plus","plus-circle","plus-square","podcast","poll","poll-h","poo","poo-storm","poop","portrait","pound-sign","power-off","pray","praying-hands","prescription","prescription-bottle","prescription-bottle-alt","print","procedures","project-diagram","puzzle-piece","qrcode","question","question-circle","quidditch","quote-left","quote-right","quran","rainbow","random","receipt","recycle","redo","redo-alt","registered","reply","reply-all","republican","retweet","ribbon","ring","road","robot","rocket","route","rss","rss-square","ruble-sign","ruler","ruler-combined","ruler-horizontal","ruler-vertical","running","rupee-sign","sad-cry","sad-tear","save","school","screwdriver","scroll","search","search-dollar","search-location","search-minus","search-plus","seedling","server","shapes","share","share-alt","share-alt-square","share-square","shekel-sign","shield-alt","ship","shipping-fast","shoe-prints","shopping-bag","shopping-basket","shopping-cart","shower","shuttle-van","sign","sign-in-alt","sign-language","sign-out-alt","signal","signature","sitemap","skull","skull-crossbones","slash","sliders-h","smile","smile-beam","smile-wink","smog","smoking","smoking-ban","snowflake","socks","solar-panel","sort","sort-alpha-down","sort-alpha-up","sort-amount-down","sort-amount-up","sort-down","sort-numeric-down","sort-numeric-up","sort-up","spa","space-shuttle","spider","spinner","splotch","spray-can","square","square-full","square-root-alt","stamp","star","star-and-crescent","star-half","star-half-alt","star-of-david","star-of-life","step-backward","step-forward","stethoscope","sticky-note","stop","stop-circle","stopwatch","store","store-alt","stream","street-view","strikethrough","stroopwafel","subscript","subway","suitcase","suitcase-rolling","sun","superscript","surprise","swatchbook","swimmer","swimming-pool","synagogue","sync","sync-alt","syringe","table","table-tennis","tablet","tablet-alt","tablets","tachometer-alt","tag","tags","tape","tasks","taxi","teeth","teeth-open","temperature-high","temperature-low","terminal","text-height","text-width","th","th-large","th-list","theater-masks","thermometer","thermometer-empty","thermometer-full","thermometer-half","thermometer-quarter","thermometer-three-quarters","thumbs-down","thumbs-up","thumbtack","ticket-alt","times","times-circle","tint","tint-slash","tired","toggle-off","toggle-on","toilet-paper","toolbox","tooth","torah","torii-gate","tractor","trademark","traffic-light","train","transgender","transgender-alt","trash","trash-alt","tree","trophy","truck","truck-loading","truck-monster","truck-moving","truck-pickup","tshirt","tty","tv","umbrella","umbrella-beach","underline","undo","undo-alt","universal-access","university","unlink","unlock","unlock-alt","upload","user","user-alt","user-alt-slash","user-astronaut","user-check","user-circle","user-clock","user-cog","user-edit","user-friends","user-graduate","user-injured","user-lock","user-md","user-minus","user-ninja","user-plus","user-secret","user-shield","user-slash","user-tag","user-tie","user-times","users","users-cog","utensil-spoon","utensils","vector-square","venus","venus-double","venus-mars","vial","vials","video","video-slash","vihara","volleyball-ball","volume-down","volume-mute","volume-off","volume-up","vote-yea","vr-cardboard","walking","wallet","warehouse","water","weight","weight-hanging","wheelchair","wifi","wind","window-close","window-maximize","window-minimize","window-restore","wine-bottle","wine-glass","wine-glass-alt","won-sign","wrench","x-ray","yen-sign","yin-yang"];
}

function __getFontAwesomeRegularListIcons() {
    return ["address-book","address-card","angry","arrow-alt-circle-down","arrow-alt-circle-left","arrow-alt-circle-right","arrow-alt-circle-up","bell","bell-slash","bookmark","building","calendar","calendar-alt","calendar-check","calendar-minus","calendar-plus","calendar-times","caret-square-down","caret-square-left","caret-square-right","caret-square-up","chart-bar","check-circle","check-square","circle","clipboard","clock","clone","closed-captioning","comment","comment-alt","comment-dots","comments","compass","copy","copyright","credit-card","dizzy","dot-circle","edit","envelope","envelope-open","eye","eye-slash","file","file-alt","file-archive","file-audio","file-code","file-excel","file-image","file-pdf","file-powerpoint","file-video","file-word","flag","flushed","folder","folder-open","frown","frown-open","futbol","gem","grimace","grin","grin-alt","grin-beam","grin-beam-sweat","grin-hearts","grin-squint","grin-squint-tears","grin-stars","grin-tears","grin-tongue","grin-tongue-squint","grin-tongue-wink","grin-wink","hand-lizard","hand-paper","hand-peace","hand-point-down","hand-point-left","hand-point-right","hand-point-up","hand-pointer","hand-rock","hand-scissors","hand-spock","handshake","hdd","heart","hospital","hourglass","id-badge","id-card","image","images","keyboard","kiss","kiss-beam","kiss-wink-heart","laugh","laugh-beam","laugh-squint","laugh-wink","lemon","life-ring","lightbulb","list-alt","map","meh","meh-blank","meh-rolling-eyes","minus-square","money-bill-alt","moon","newspaper","object-group","object-ungroup","paper-plane","pause-circle","play-circle","plus-square","question-circle","registered","sad-cry","sad-tear","save","share-square","smile","smile-beam","smile-wink","snowflake","square","star","star-half","sticky-note","stop-circle","sun","surprise","thumbs-down","thumbs-up","times-circle","tired","trash-alt","user","user-circle","window-close","window-maximize","window-minimize","window-restore"];
}

function __getFontAwesomeBrandsListIcons() {
    return ["500px","accessible-icon","accusoft","acquisitions-incorporated","adn","adversal","affiliatetheme","algolia","alipay","amazon","amazon-pay","amilia","android","angellist","angrycreative","angular","app-store","app-store-ios","apper","apple","apple-pay","asymmetrik","audible","autoprefixer","avianex","aviato","aws","bandcamp","behance","behance-square","bimobject","bitbucket","bitcoin","bity","black-tie","blackberry","blogger","blogger-b","bluetooth","bluetooth-b","btc","buromobelexperte","buysellads","cc-amazon-pay","cc-amex","cc-apple-pay","cc-diners-club","cc-discover","cc-jcb","cc-mastercard","cc-paypal","cc-stripe","cc-visa","centercode","chrome","cloudscale","cloudsmith","cloudversify","codepen","codiepie","connectdevelop","contao","cpanel","creative-commons","creative-commons-by","creative-commons-nc","creative-commons-nc-eu","creative-commons-nc-jp","creative-commons-nd","creative-commons-pd","creative-commons-pd-alt","creative-commons-remix","creative-commons-sa","creative-commons-sampling","creative-commons-sampling-plus","creative-commons-share","creative-commons-zero","critical-role","css3","css3-alt","cuttlefish","d-and-d","d-and-d-beyond","dashcube","delicious","deploydog","deskpro","dev","deviantart","digg","digital-ocean","discord","discourse","dochub","docker","draft2digital","dribbble","dribbble-square","dropbox","drupal","dyalog","earlybirds","ebay","edge","elementor","ello","ember","empire","envira","erlang","ethereum","etsy","expeditedssl","facebook","facebook-f","facebook-messenger","facebook-square","fantasy-flight-games","firefox","first-order","first-order-alt","firstdraft","flickr","flipboard","fly","font-awesome","font-awesome-alt","font-awesome-flag","fonticons","fonticons-fi","fort-awesome","fort-awesome-alt","forumbee","foursquare","free-code-camp","freebsd","fulcrum","galactic-republic","galactic-senate","get-pocket","gg","gg-circle","git","git-square","github","github-alt","github-square","gitkraken","gitlab","gitter","glide","glide-g","gofore","goodreads","goodreads-g","google","google-drive","google-play","google-plus","google-plus-g","google-plus-square","google-wallet","gratipay","grav","gripfire","grunt","gulp","hacker-news","hacker-news-square","hackerrank","hips","hire-a-helper","hooli","hornbill","hotjar","houzz","html5","hubspot","imdb","instagram","internet-explorer","ioxhost","itunes","itunes-note","java","jedi-order","jenkins","joget","joomla","js","js-square","jsfiddle","kaggle","keybase","keycdn","kickstarter","kickstarter-k","korvue","laravel","lastfm","lastfm-square","leanpub","less","line","linkedin","linkedin-in","linode","linux","lyft","magento","mailchimp","mandalorian","markdown","mastodon","maxcdn","medapps","medium","medium-m","medrt","meetup","megaport","microsoft","mix","mixcloud","mizuni","modx","monero","napster","neos","nimblr","nintendo-switch","node","node-js","npm","ns8","nutritionix","odnoklassniki","odnoklassniki-square","old-republic","necotienda","openid","opera","optin-monster","osi","page4","pagelines","palfed","patreon","paypal","penny-arcade","periscope","phabricator","phoenix-framework","phoenix-squadron","php","pied-piper","pied-piper-alt","pied-piper-hat","pied-piper-pp","pinterest","pinterest-p","pinterest-square","playstation","product-hunt","pushed","python","qq","quinscape","quora","r-project","ravelry","react","reacteurope","readme","rebel","red-river","reddit","reddit-alien","reddit-square","renren","replyd","researchgate","resolving","rev","rocketchat","rockrms","safari","sass","schlix","scribd","searchengin","sellcast","sellsy","servicestack","shirtsinbulk","shopware","simplybuilt","sistrix","sith","skyatlas","skype","slack","slack-hash","slideshare","snapchat","snapchat-ghost","snapchat-square","soundcloud","speakap","spotify","squarespace","stack-exchange","stack-overflow","staylinked","steam","steam-square","steam-symbol","sticker-mule","strava","stripe","stripe-s","studiovinari","stumbleupon","stumbleupon-circle","superpowers","supple","teamspeak","telegram","telegram-plane","tencent-weibo","the-red-yeti","themeco","themeisle","think-peaks","trade-federation","trello","tripadvisor","tumblr","tumblr-square","twitch","twitter","twitter-square","typo3","uber","uikit","uniregistry","untappd","usb","ussunnah","vaadin","viacoin","viadeo","viadeo-square","viber","vimeo","vimeo-square","vimeo-v","vine","vk","vnv","vuejs","weebly","weibo","weixin","whatsapp","whatsapp-square","whmcs","wikipedia-w","windows","wix","wizards-of-the-coast","wolf-pack-battalion","wordpress","wordpress-simple","wpbeginner","wpexplorer","wpforms","wpressr","xbox","xing","xing-square","y-combinator","yahoo","yandex","yandex-international","yelp","yoast","youtube","youtube-square","zhihu"];
}