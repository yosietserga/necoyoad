<?php echo $header; ?>
<div id="maincontent">
<div class="grid_16" id="htmlWrapper"></div>
<div id="toolWrapper">
    <div class="container_16">
        
        <div class="grid_4">
            <?php if ($_GET['step'] == 1 || !isset($_GET['step'])) { ?>
                <?php $_GET['step'] = $_SESSION['step'] = $_POST['step'] = $_GET['step'] +1; ?>
                <?php /* ?>
                <h1>Vistas</h1>
                <select id="r">
                    <option value="common/home">Home</option>
                    <option value="store/product">Producto</option>
                    <option value="store/category">Categor&iacute;a</option>
                    <option value="store/search">B&uacute;squeda</option>
                    <option value="store/special">Ofertas</option>
                    <option value="information/information">Informaci&oacute;n</option>
                    <option value="information/contact">Contacto</option>
                    <option value="store/quote">Presupuesto</option>
                    <option value="store/product">Marcas y Fabricantes</option>
                </select>
                <?php */ ?>
                <h1>Herramientas</h1>
                <ul>
                    <li onclick="showCssTools('layouts')">Plantillas</li>
                    <li onclick="showCssTools('background')">Fondo</li>
                    <li onclick="showCssTools('font')">Fuentes</li>
                    <li onclick="showCssTools('border')">Bordes</li>
                    <li onclick="showCssTools('shadow')">Sombras</li>
                    <li onclick="showCssTools('margin')">Margen y Alineaci&oacute;n</li>
                </ul>
            <?php } ?>
        </div>
        
        <div class="grid_8 layouts" id="cssToolsWrapper">
        
            <div id="layoutsWrapper">
                <h1>Layout</h1>
                <p class="desc">Modifique el Layout que desea para su tienda en cada una de las vistas</p>
                <ul>
                    <li id="fullContent" class="fullContent" onclick="changeLayout('fullcontent')"></li>
                    <li id="oneColLeft" class="oneColLeft" onclick="changeLayout('onecolleft')"></li>
                    <li id="oneColRight" class="oneColRight" onclick="changeLayout('onecolright')"></li>
                    <li id="twoColsCenter" class="twoColsCenter" onclick="changeLayout('twocolscenter')"></li>
                    <li id="twoColsLeft" class="twoColsLeft" onclick="changeLayout('twocolsleft')"></li>
                    <li id="twoColsRight" class="twoColsRight" onclick="changeLayout('twocolsright')"></li>
                    <li id="fullContentFeatured" class="fullContentFeatured" onclick="changeLayout('fullcontentfeatured')"></li>
                    <li id="oneColLeftFeatured" class="oneColLeftFeatured" onclick="changeLayout('onecolleftfeatured')"></li>
                    <li id="oneColRightFeatured" class="oneColRightFeatured" onclick="changeLayout('onecolrightfeatured')"></li>
                    <li id="twoColsCenterFeatured" class="twoColsCenterFeatured" onclick="changeLayout('twocolscenterfeatured')"></li>
                    <li id="twoColsLeftFeatured" class="twoColsLeftFeatured" onclick="changeLayout('twocolsleftfeatured')"></li>
                    <li id="twoColsRightFeatured" class="twoColsRightFeatured" onclick="changeLayout('twocolsrightfeatured')"></li>
                </ul>
            </div>
            
            <div id="backgroundWrapper">
                <h1>Fondo</h1>
                <p class="desc">Modifique el fondo de la plantilla y los diferentes bloques</p>
                
                <div class="property">
                    <div style="float: left;margin-right:5px;">background:</div>
                    <input class="smallInput" type="text" id="background-value" name="background-value" value="0" /><div id="colorpicker" style="float: right;"></div>
                    <a class="showMore" onclick="$(this).parent().find('.more').slideToggle();if ($(this).text() == '+') {$(this).text('-') } else {$(this).text('+')}">+</a>
                    <div class="clear"></div>
                    <div class="more">
                        <hr />
                        <div style="float: left;margin-right:5px;">Top Left:</div>
                        <div id="background-radius-topleft-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="background-radius-topleft-value" name="background-radius-topleft-value" value="0" />px
                        <div class="clear"></div>
                        <div style="float: left;margin-right:5px;">Top Right:</div>
                        <div id="background-radius-topright-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="background-radius-topright-value" name="background-radius-topright-value" value="0" />px
                        <div class="clear"></div>
                        <div style="float: left;margin-right:5px;">bottom right:</div>
                        <div id="background-radius-bottomright-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="background-radius-bottomright-value" name="background-radius-bottomright-value" value="0" />px
                        <div class="clear"></div>
                        <div style="float: left;margin-right:5px;">bottom left:</div>
                        <div id="background-radius-bottomleft-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="background-radius-bottomleft-value" name="background-radius-bottomleft-value" value="0" />px
                        <input type="hidden" id="background-radius" name="background-radius" value="" />
                    </div>
                </div>
            </div>
            
            <div id="fontWrapper">
                <h1>Fuentes</h1>
                <p class="desc">Modifique el Layout que desea para su tienda en cada una de las vistas</p>
            </div>
            
            <div id="borderWrapper">
                <h1>Bordes</h1>
                <p class="desc">Modifique el Layout que desea para su tienda en cada una de las vistas</p>
            </div>
            
            <div id="shadowWrapper">
                <h1>Sombras</h1>
                <p class="desc">Modifique el Layout que desea para su tienda en cada una de las vistas</p>
            </div>
            
            <div id="marginWrapper">
                <h1>M&aacute;rgenes</h1>
                <p class="desc">Modifique el Layout que desea para su tienda en cada una de las vistas</p>
            </div>
            
        </div>
    </div>
    <div id="toolToggle"><img src="<?php echo HTTP_IMAGE; ?>down.png" /></div>
 </div>
<div id="widgetsWrapper">
    <div id="widgetsToggle"><img src="<?php echo HTTP_IMAGE; ?>down.png" /></div>
    <div class="widgetSearch">
        <input type="text" name="widget_search" value="" />
        <input type="button" name="widget_submit" value="Buscar" onclick="searchWidget()" />
    </div>
    <ul id="widgetsPanel" class="widget widgetsPanel">
        <li>
            <!--<img src="https://demo.crowdfavorite.com/favebusiness/wp/wp-content/themes/favebusiness/carrington-build/modules/heading/icon.png" />-->
            <h2>Social Buttons</h2>
            <p>Colecci&oacute;n de botones para compatir en social media</p>
        </li>
        <li>
            <img />
            <h2>Skype Me!</h2>
            <p>Colecci&oacute;n de botones para compatir en social media</p>
        </li>
        <li>
            <img />
            <h2>Productos Populares</h2>
            <p>Colecci&oacute;n de botones para compatir en social media</p>
        </li>
        <li>
            <img />
            <h2>Productos Populares</h2>
            <p>Colecci&oacute;n de botones para compatir en social media</p>
        </li>
        
        <?php foreach ($extensions as $extension) { ?>
        <li>
            <img />
            <h2><?php echo $extension['name']; ?></h2>
            <p>Colecci&oacute;n de botones para compatir en social media</p>
        </li>
		<?php } ?>
    </ul>
</div>
<div id="showWidgets" style="display: none;" title="Widgets">
    <ul class="widgetsPanel">
		<?php foreach ($extensions as $extension) { ?>
        <li>
            <img />
            <h2><?php echo $extension['name']; ?></h2>
            <p>Colecci&oacute;n de botones para compatir en social media</p>
        </li>
		<?php } ?>
    </ul>
</div>
<div id="blocksWrapper" style="display: none;" title="Bloques">
    <div class="layouts">
        <ul>
            <li id="_fullContent" class="fullContent" onclick="drawFullContent()"></li>
            <li id="_oneColLeft" class="oneColLeft" onclick="drawOneColLeft()"></li>
            <li id="_oneColRight" class="oneColRight" onclick="drawOneColRight()"></li>
            <li id="_twoColsCenter" class="twoColsCenter" onclick="drawTwoColsCenter()"></li>
            <li id="_twoColsLeft" class="twoColsLeft" onclick="drawTwoColsLeft()"></li>
            <li id="_twoColsRight" class="twoColsRight" onclick="drawTwoColsRight()"></li>
        </ul>
    </div>
</div>
<div id="panel">
    <ul>
        <li><a href="#style">Estilos</a></li>
        <li><a href="#advanced">Avanzado</a></li>
    </ul>
    <div id="style">
        <div style="float:left;width:50%;">
            <p><b>General</b></p>
            <hr />
            <div class="cssContainer">

                <div class="property">
                    <div style="float: left;margin-right:5px;">background:</div>
                    <input class="smallInput" type="text" id="background-value" name="background-value" value="0" /><div id="clorpicker" style="float: right;"></div>
                    <a class="showMore" onclick="$(this).parent().find('.more').slideToggle();if ($(this).text() == '+') {$(this).text('-') } else {$(this).text('+')}">+</a>
                    <div class="clear"></div>
                    <div class="more">
                        <hr />
                        <div style="float: left;margin-right:5px;">Top Left:</div>
                        <div id="background-radius-topleft-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="background-radius-topleft-value" name="background-radius-topleft-value" value="0" />px
                        <div class="clear"></div>
                        <div style="float: left;margin-right:5px;">Top Right:</div>
                        <div id="background-radius-topright-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="background-radius-topright-value" name="background-radius-topright-value" value="0" />px
                        <div class="clear"></div>
                        <div style="float: left;margin-right:5px;">bottom right:</div>
                        <div id="background-radius-bottomright-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="background-radius-bottomright-value" name="background-radius-bottomright-value" value="0" />px
                        <div class="clear"></div>
                        <div style="float: left;margin-right:5px;">bottom left:</div>
                        <div id="background-radius-bottomleft-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="background-radius-bottomleft-value" name="background-radius-bottomleft-value" value="0" />px
                        <input type="hidden" id="background-radius" name="background-radius" value="" />
                    </div>
                </div>
                <div class="property">
                    <div style="float: left;margin-right:5px;">border-radius:</div>
                    <input class="smallInput" type="text" id="border-radius-value" name="border-radius-value" value="0" />px
                    <div id="border-radius-slider" style="width: 45%;float: left;"></div>
                    <a class="showMore" onclick="$(this).parent().find('.more').slideToggle();if ($(this).text() == '+') {$(this).text('-') } else {$(this).text('+')}">+</a>
                    <div class="clear"></div>
                    <div class="more">
                        <hr />
                        <div style="float: left;margin-right:5px;">Top Left:</div>
                        <div id="border-radius-topleft-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="border-radius-topleft-value" name="border-radius-topleft-value" value="0" />px
                        <div class="clear"></div>
                        <div style="float: left;margin-right:5px;">Top Right:</div>
                        <div id="border-radius-topright-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="border-radius-topright-value" name="border-radius-topright-value" value="0" />px
                        <div class="clear"></div>
                        <div style="float: left;margin-right:5px;">bottom right:</div>
                        <div id="border-radius-bottomright-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="border-radius-bottomright-value" name="border-radius-bottomright-value" value="0" />px
                        <div class="clear"></div>
                        <div style="float: left;margin-right:5px;">bottom left:</div>
                        <div id="border-radius-bottomleft-slider" style="width: 45%;float: left;"></div>
                        <input class="smallInput" type="text" id="border-radius-bottomleft-value" name="border-radius-bottomleft-value" value="0" />px
                        <input type="hidden" id="border-radius" name="border-radius" value="" />
                    </div>
                </div>
                <div class="property">
                    <div style="float: left;margin-right:5px;">box-shadow:</div>
                    <input class="smallInput" type="text" id="box-shadow-value" name="box-shadow-value" value="0" />px
                    <div id="box-shadow-slider" style="width: 45%;float: left;"></div>
                    <a class="showMore" onclick="$(this).parent().find('.more').slideToggle();if ($(this).text() == '+') {$(this).text('-') } else {$(this).text('+')}">+</a>
                    <div class="clear"></div>
                    <div class="more">
                        <hr />
                            <div style="float: left;margin:5px;">Horizontal:
                            <div id="box-shadow-horizontal-slider" style="width: 45%;float: left;"></div>
                            <input class="smallInput" type="text" id="box-shadow-horizontal-value" name="box-shadow-horizontal-value" value="0" />px
                        </div>
                            <div style="float: left;margin:5px;">Vertical:
                            <div id="box-shadow-vertical-slider" style="width: 45%;float: left;"></div>
                            <input class="smallInput" type="text" id="box-shadow-vertical-value" name="box-shadow-vertical-value" value="0" />px
                        </div>
                        <div class="clear">
                        </div>
                            <div style="float: left;margin-right:5px;">Blur:
                            <div id="box-shadow-blur-slider" style="width: 45%;float: left;"></div>
                            <input class="smallInput" type="text" id="box-shadow-blur-value" name="box-shadow-blur-value" value="0" />px
                        </div>
                        <div style="float: left;margin-right:5px;">Spread:
                            <div id="box-shadow-spread-slider" style="width: 45%;float: left;"></div>
                            <input class="smallInput" type="text" id="box-shadow-spread-value" name="box-shadow-spread-value" value="0" />px
                        </div>
                        <div style="float: left;margin-right:5px;">Shadow:
                            <div id="box-shadow-color-slider" style="width: 45%;float: left;"></div>
                            <input class="smallInput" type="text" id="box-shadow-color-value" name="box-shadow-color-value" value="#000" />
                        </div>
                        <input type="hidden" id="box-shadow" name="box-shadow" value="" />
                    </div>
                </div>
                <div class="property">
                    <a class="" onclick="$(this).parent().find('.more').slideToggle()">mas</a>
                    <div class="more">
                        Hola
                    </div>
                </div>
                <div class="property">
                    <a class="" onclick="$(this).parent().find('.more').slideToggle()">mas</a>
                    <div class="more">
                        Hola
                    </div>
                </div>
                <div class="property">
                    <a class="" onclick="$(this).parent().find('.more').slideToggle()">mas</a>
                    <div class="more">
                        Hola
                    </div>
                </div>
            </div>
            <p><b>Fuentes</b></p>
            <hr />
        </div>
        <div id="stylePreview"><b style="padding:40%;">Preview</b><h1>T&iacute;tulo</h1><p>Texto contenido dentro de un parrafo</p></div>
    </div>
    <div id="advanced">
        <h2>configuraci&oacute;n Avanzada</h2>
        <hr />
        <label for="class">Clase CSS</label>
        <input type="text" name="class" id="class" value="" />
    </div>
</div>
<input type="hidden" name="section" id="section" value="" />
<input type="hidden" name="lastsection" id="lastSection" value="" />
<input type="hidden" name="inputWidgetId" id="inputWidgetId" value="" />
<input type="hidden" name="inputWidgetName" id="inputWidgetName" value="" />
<input type="hidden" name="inputWidgetDesc" id="inputWidgetDesc" value="" />
<input type="hidden" name="inputWidgetIcon" id="inputWidgetIcon" value="" />
<input type="text" name="inputWidgetIcon" id="color-picker-value" value="" />
 <script>
function showCssTools(css) {
    $("#cssToolsWrapper > div").each(function(){
        $(this).hide();
    });
    $("#" + css + "Wrapper").fadeIn();
}
function changeLayout(layout) {
    if (confirm('Al cambiar la plantilla se perder�n todos los cambios realizados, �Desea continuar?')) {
        if (layout=='fullcontent') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/fullcontent"); ?>');
        if (layout=='fullcontentfeatured') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/fullcontentfeatured"); ?>');
        if (layout=='onecolleft') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/onecolleft"); ?>');
        if (layout=='onecolleftfeatured') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/onecolleftfeatured"); ?>');
        if (layout=='onecolright') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/onecolright"); ?>');
        if (layout=='onecolrightfeatured') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/onecolrightfeatured"); ?>');
        if (layout=='twocolscenter') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/twocolscenter"); ?>');
        if (layout=='twocolscenterfeatured') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/twocolscenterfeatured"); ?>');
        if (layout=='twocolsleft') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/twocolsleft"); ?>');
        if (layout=='twocolsleftfeatured') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/twocolsleftfeatured"); ?>');
        if (layout=='twocolsright') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/twocolsright"); ?>');
        if (layout=='twocolsrightfeatured') $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/twocolsrightfeatured"); ?>');
    }
}
$(function(){
    /* $("#htmlWrapper").load('<?php echo $Url::createAdminUrl("layout/fullcontentfeatured"); ?>'); */
    $("#cssToolsWrapper > div").each(function(){ $(this).hide(); });
    $("#layoutsWrapper").fadeIn();
    
    $('#colorpicker').ntColorPicker({
        type: 'rgba',
        change:function(color){
            $("#stylePreview").css({backgroundColor:color});
        }
    });
    
    $('html').find('title').text("Layout Builder");
    $(".more").slideUp();
    
    $( "#box-shadow-horizontal-slider" ).slider({
			range: "min",
			min: 0,
			max: 25,
			slide:function(event, ui) {
                $("#box-shadow-horizontal-value").val(ui.value);
                horizontal = $("#box-shadow-horizontal-value").val();
                vertical = $("#box-shadow-vertical-value").val();
                blur = $("#box-shadow-blur-value").val();
                spread = $("#box-shadow-spread-value").val();
                color = $("#box-shadow-color-value").val();
				$("#stylePreview").css({'boxShadow':horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color});
                $("#box-shadow").val(horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color);
			}
	});
    $( "#box-shadow-vertical-slider" ).slider({
			range: "min",
			min: 0,
			max: 25,
			slide:function(event, ui) {
                $("#box-shadow-vertical-value").val(ui.value);
                horizontal = $("#box-shadow-horizontal-value").val();
                vertical = $("#box-shadow-vertical-value").val();
                blur = $("#box-shadow-blur-value").val();
                spread = $("#box-shadow-spread-value").val();
                color = $("#box-shadow-color-value").val();
				$("#stylePreview").css({'boxShadow':horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color});
                $("#box-shadow").val(horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color);
			}
	});
    $( "#box-shadow-spread-slider" ).slider({
			range: "min",
			min: 0,
			max: 25,
			slide:function(event, ui) {
                $("#box-shadow-spread-value").val(ui.value);
                horizontal = $("#box-shadow-horizontal-value").val();
                vertical = $("#box-shadow-vertical-value").val();
                blur = $("#box-shadow-blur-value").val();
                spread = $("#box-shadow-spread-value").val();
                color = $("#box-shadow-color-value").val();
				$("#stylePreview").css({'boxShadow':horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color});
                $("#box-shadow").val(horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color);
			}
	});
    $( "#box-shadow-blur-slider" ).slider({
			range: "min",
			min: 0,
			max: 25,
			slide:function(event, ui) {
                $("#box-shadow-blur-value").val(ui.value);
                horizontal = $("#box-shadow-horizontal-value").val();
                vertical = $("#box-shadow-vertical-value").val();
                blur = $("#box-shadow-blur-value").val();
                spread = $("#box-shadow-spread-value").val();
                color = $("#box-shadow-color-value").val();
				$("#stylePreview").css({'boxShadow':horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color});
                $("#box-shadow").val(horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color);
			}
	});
    $("#box-shadow-slider").slider({
			range: "min",
			min: 0,
			max: 25,
			slide: function(event, ui) {
                $("#box-shadow-value").val(ui.value);
                $("#box-shadow-blur-value").val(ui.value);
                $("#box-shadow-blur-slider").slider('option', 'value', parseInt(ui.value));
                horizontal = $("#box-shadow-horizontal-value").val();
                vertical = $("#box-shadow-vertical-value").val();
                blur = $("#box-shadow-blur-value").val();
                spread = $("#box-shadow-spread-value").val();
                color = $("#box-shadow-color-value").val();
				$("#stylePreview").css({'boxShadow':horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color});
                $("#box-shadow").val(horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color);
			}
	});
    
    $( "#border-radius-topleft-slider" ).slider({
			range: "min",
			min: 0,
			max: 250,
			slide:function(event, ui) {
                $("#border-radius-topleft-value").val(ui.value);
                $("#stylePreview").css({'borderRadius':$("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px'});
                $("#border-radius").val($("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px');
			}
	});
    $( "#border-radius-topright-slider" ).slider({
			range: "min",
			min: 0,
			max: 250,
			slide:function(event, ui) {
                $("#border-radius-topright-value").val(ui.value);
                $("#stylePreview").css({'borderRadius':$("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px'});
                $("#border-radius").val($("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px');
			}
	});
    $( "#border-radius-bottomleft-slider" ).slider({
			range: "min",
			min: 0,
			max: 250,
			slide:function(event, ui) {
                $("#border-radius-bottomleft-value").val(ui.value);
                $("#stylePreview").css({'borderRadius':$("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px'});
                $("#border-radius").val($("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px');
			}
	});
    $( "#border-radius-bottomright-slider" ).slider({
			range: "min",
			min: 0,
			max: 250,
			slide:function(event, ui) {
                $("#border-radius-bottomright-value").val(ui.value);
                $("#stylePreview").css({'borderRadius':$("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px'});
                $("#border-radius").val($("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px');
			}
	});
    $( "#border-radius-value" ).change(function(){
        $("#stylePreview").css({'borderRadius':this.value + 'px'});
        $("#border-radius-slider").slider('option', 'value', parseInt($(this).val()));
    });
    $( "#border-radius-topleft-value" ).change(function(){
        $("#stylePreview").css({'borderRadius':$("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px'});
        $("#border-radius-topleft-slider").slider('option', 'value', parseInt($(this).val()));
    });
    $( "#border-radius-topright-value" ).change(function(){
        $("#stylePreview").css({'borderRadius':$("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px'});
        $("#border-radius-topright-slider").slider('option', 'value', parseInt($(this).val()));
    });
    $( "#border-radius-bottomleft-value" ).change(function(){
        $("#stylePreview").css({'borderRadius':$("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px'});
        $("#border-radius-bottomleft-slider").slider('option', 'value', parseInt($(this).val()));
    });
    $( "#border-radius-bottomright-value" ).change(function(){
        $("#stylePreview").css({'borderRadius':$("#border-radius-topleft-value").val() + 'px ' + $("#border-radius-topright-value").val() + 'px ' + $("#border-radius-bottomleft-value").val() + 'px ' + $("#border-radius-bottomright-value").val() + 'px'});
        $("#border-radius-bottomright-slider").slider('option', 'value', parseInt($(this).val()));
    });
    $("#border-radius-slider").slider({
			range: "min",
			min: 0,
			max: 250,
			slide: function(event, ui) {
				$("#stylePreview").css({'borderRadius':ui.value + 'px'});
				$("#border-radius-value").val(ui.value);
                $("#border-radius-topleft-value").val(ui.value);
                $("#border-radius-topright-value").val(ui.value);
                $("#border-radius-bottomleft-value").val(ui.value);
                $("#border-radius-bottomright-value").val(ui.value);
                $("#border-radius-topleft-slider").slider('option', 'value', parseInt(ui.value));
                $("#border-radius-topright-slider").slider('option', 'value', parseInt(ui.value));
                $("#border-radius-bottomleft-slider").slider('option', 'value', parseInt(ui.value));
                $("#border-radius-bottomright-slider").slider('option', 'value', parseInt(ui.value));
                $("#border-radius").val(ui.value + 'px');
			}
	});
    $("#panel").dialog({
        width:'940px',
        modal: false,
        buttons:{
            'Guardar':function() {
                
            },
            'Cancelar': function() {
                $(this).dialog('close');
            }
        },
        create:function(event,ui) {
            $(this).find('.ui-dialog-titlebar').css({'display':'none'});
        }
        });
        $(".ui-dialog-titlebar").remove(); 
    $("#widgetsPanel li").draggable({
        revert: true, 
        helper: "clone",
        start: function(event,ui) {
            $("#inputWidgetId").val();
            $("#inputWidgetName").val($(this).find('h2').text());
            $("#inputWidgetDesc").val($(this).find('p').text());
            $("#inputWidgetIcon").val($(this).find('img').attr('src'));
        }
    });
    /******************** Begin Tools and Panels ******************************/
    $("#toolWrapper").addClass('down').animate({'height':'0px'});
    $("#toolToggle").find('img').attr('src','<?php echo HTTP_IMAGE; ?>up.png');
    $("#widgetsWrapper").addClass('down').animate({'marginLeft':'-200px'});
    $("#widgetsToggle").find('img').attr('src','<?php echo HTTP_IMAGE; ?>up.png');
    
    $("#toolToggle").click(function(){
        if ($("#toolWrapper").hasClass("down")) {
            $("#toolWrapper").removeClass('down').addClass('up').animate({'height':'180px'});
            $("#toolToggle").find('img').attr('src','<?php echo HTTP_IMAGE; ?>down.png');
        } else {
            $("#toolWrapper").removeClass('up').addClass('down').animate({'height':'0px'});
            $("#toolToggle").find('img').attr('src','<?php echo HTTP_IMAGE; ?>up.png');
        }
    });
    
    $("#widgetsToggle").click(function(){
        if ($("#widgetsWrapper").hasClass("down")) {
            $("#widgetsWrapper").removeClass('down').addClass('up').animate({'marginLeft':'0px'});
            $("#widgetsToggle").find('img').attr('src','<?php echo HTTP_IMAGE; ?>down.png');
        } else {
            $("#widgetsWrapper").removeClass('up').addClass('down').animate({'marginLeft':'-200px'});
            $("#widgetsToggle").find('img').attr('src','<?php echo HTTP_IMAGE; ?>up.png');
        }
    });
    
    $("#widgetsWrapper").mouseenter(function(){
        clearTimeout($(this).data('timeoutId'));
    }).mouseleave(function(){
        var e = this;
        var timeoutId = setTimeout(function(){
            if ($(e).hasClass("up")) {
                $("#widgetsWrapper").removeClass('up').addClass('down').animate({'marginLeft':'-200px'});
                $("#widgetsToggle").find('img').attr('src','<?php echo HTTP_IMAGE; ?>up.png');
            }
        }, 900);
        $(this).data('timeoutId', timeoutId); 
    });
    
    $("#toolWrapper").mouseenter(function(){
        clearTimeout($(this).data('timeoutId'));
    }).mouseleave(function(){
        var e = this;
        var timeoutId = setTimeout(function(){
            if ($(e).hasClass("up")) {
                $("#toolWrapper").removeClass('up').addClass('down').animate({'height':'0px'});
                $("#toolToggle").find('img').attr('src','<?php echo HTTP_IMAGE; ?>up.png');
            }
        }, 900);
        $(this).data('timeoutId', timeoutId); 
    });
    /******************** Begin Tools and Panels ******************************/
});
function deleteBlock(e) {
    if (confirm("\xbfEst\u00E1 seguro que desea eliminar este bloque?")) {
        var li = $(e).closest("li");
        var ul = li.parent();
        li.fadeOut(function(){
            li.remove();
            ul.find("li").each(function(){
                blockId = $(this).find("input").val();
                $("#" + blockId + "_position").val($(this).index());
            });
        });
    }
}
function lockBlock(e) {
    if (!$("#" + e).hasClass('blockLocked')) {
        $("#" + e).addClass('blockLocked');
        $("#" + e + "_lock").addClass('unlock').removeClass('lock');
        $("#" + e + "_delete").attr('onclick','');
        $("#" + e + "_edit").attr('onclick','');
        $("#" + e + " .widgetContainer").attr('ondrop','');
        $("#" + e + " .addWidget").attr('onclick','');
        $("#" + e + " .deleteWidget").attr('onclick','');
        $("#" + e + " .editWidget").attr('onclick','');
        editWidget(this);
        $("#" + e + " .moveWidget").removeClass('moveWidget').addClass('dontMoveWidget');
        /* $("#" + e + " > .blockContent > a").removeClass(''); */
    } else {
        $("#" + e).removeClass('blockLocked');
        $("#" + e + "_lock").addClass('lock').removeClass('unlock');
        $("#" + e + "_delete").attr('onclick','deleteBlock(this)');
        $("#" + e + "_edit").attr('onclick','editBlockStyle(\'' + e + '\')');
        $("#" + e + " .widgetContainer").attr('ondrop','dropWidget(this);');
        $("#" + e + " .addWidget").attr('onclick','showWidgets()');
        $("#" + e + " .deleteWidget").attr('onclick','deleteWidget(this)');
        $("#" + e + " .editWidget").attr('onclick','editWidget(this)');
        $("#" + e + " .dontMoveWidget").removeClass('dontMoveWidget').addClass('moveWidget');
        /* $("#" + e + " > .blockContent > a").removeClass(''); */
    }
}
function checkListWrapper(section) {
    var output = "";
    if (!$("#" + section + " ul").length) {
        ulElement = $(document.createElement("ul")).attr('id', section + "ListWidgets");
        $("#" + section + " h1").after(ulElement);
        output += '$(function(){';
        output += '$("#' + section + 'ListWidgets").sortable({';
        output += 'placeholder: "blockPlaceHolder",handle:".move",';
        if (section == 'header') {output += 'connectWith: "#featuredListWidgets,#contentListWidgets,#footerListWidgets",';}
        if (section == 'featured') {output += 'connectWith: "#headerListWidgets,#contentListWidgets,#footerListWidgets",';}
        if (section == 'content') {output += 'connectWith: "#featuredListWidgets,#headerListWidgets,#footerListWidgets",';}
        if (section == 'footer') {output += 'connectWith: "#featuredListWidgets,#contentListWidgets,#headerListWidgets",';}
        output += 'update:function(event,ui) {if (this === ui.item.parent()[0]) {setOrder(ui);}}';
        output += '});';
        output += '$("#' + section + 'ListWidgets").disableSelection();';
        output += '});';
        $(document.createElement("script")).text(output).appendTo("#" + section + "ListWidgets");
        return true;
    }
}
function setOrder(ui) {
    if (ui.sender) {
        ui.sender.find("li").each(function(){
            widgetId = $(this).find("input").val();
            $("#" + widgetId + "_target").val(ui.sender.attr("id"));
            $("#" + widgetId + "_position").val($(this).index());
        });
    }
    ul = ui.item.parent();
    ul.find("li").each(function(){
        widgetId = $(this).find("input").val();
        $("#" + widgetId + "_target").val(ul.attr("id"));
        $("#" + widgetId + "_position").val($(this).index());
    });
}
function getSectionActive() {
    return $("#section").val();
}
function getLastSectionActive() {
    return $("#lastSection").val();
}
function setSectionActive(_id) {
    $("#lastSection").val(getSectionActive());
    $("#section").val(_id);
}
function countUlChildrens(section) {
    var ulElement = $("#" + section + "ListWidgets");
    var counter = 0;
    $(ulElement).children().each(function(){
       counter = counter + 1 * 1;
    });
    return counter;
}
function showPanel() {
    
    $("#panel").dialog({
        width:'940px',
        modal: false,
        buttons:{
            'Guardar':function() {
                
            },
            'Cancelar': function() {
                $(this).dialog('close');
            }
        },
        create:function(event,ui) {
            console.log(event);
            $(this).find('.ui-dialog-titlebar').css({'display':'none'});
        }
        });
        $(".ui-dialog-titlebar").remove(); 
}
function showWidgets() {
    $("#showWidgets").dialog({
        buttons:{
            'Guardar':function() {
                
            },
            'Cancelar':function() {
                $(this).dialog("close");
            }
        }
    });
}
function dropWidget(e) {
    var widgetId = $("#inputWidgetId").val();
    var widgetName = $("#inputWidgetName").val();
    var widgetDesc = $("#inputWidgetDesc").val();
    var widgetIcon = $("#inputWidgetIcon").val();
    var widgetCount = 0;
    
    $("#" + e.id + " .widgetSet").each(function(){
       widgetCount = widgetCount + 1 * 1; 
    });
    
    /* se crea y asigna el contenedor de la lista */
    if ($("#" + e.id + " > ul").length < 1) {
        ulWidget = $(document.createElement("ul")).attr("id",e.id + "_ulWidget").addClass("widgetsPanel widgetsInGrid");
        $(e).append(ulWidget);
    } else {
        ulWidget = $("#" + e.id + " > ul");
    }
    
    /* se asigna el identificador del widget */
    var _id = "widget_" + widgetCount + "_" + rand();
    output  = '<div style="float:left">';
    output += '<img src="' + widgetIcon + '" />';
    output += '<h2>' + widgetName + '</h2>';
    output += '<p>' + widgetDesc + '</p>';
    output += '</div>';
    output += '<div style="float:right">';
    output += '<a class="moveWidget" style="margin-left:-170px;cursor:move">Mover</a>';
    output += '<a class="editWidget" onclick="editWidget(this)" style="margin-left:-120px">Editar</a>';
    output += '<a class="deleteWidget" onclick="deleteWidget(this)" style="margin-left:-70px">Eliminar</a>';
    output += '</div>';
    output += '<input type="hidden" name="Widget[' + _id + '][widget_id]" id="' + _id + '_widget_id" value="' + _id + '" />';
    output += '<input type="hidden" name="Widget[' + _id + '][target]" id="' + _id + '_target" value="' + ulWidget.id + '" />';
    output += '<input type="hidden" name="Widget[' + _id + '][position]" id="' + _id + '_position" value="' + widgetCount + '" />';
    
    var widget = $(document.createElement("li"))
        .attr("id",_id)
        .addClass("widgetSet")
        .css({"cursor":"default"})
        .html(output);
    $(ulWidget).append(widget);
    
    var div;
    $.getJSON('<?php echo Url::createAdminUrl("style/layouts/widget"); ?>&widget_id=1' + widgetId + '&callback=?',function(data){
        if (data.success) {
            div = $(document.createElement("div"))
                .attr({'id':'l' + widgetId,'title':data.widget})
                .css({'display':'none'})
                .addClass("widgetPanel")
                .appendTo(widget);
            $.each(data.params,function(key,param){
                $(document.createElement("label"))
                    .attr({'for':param.name})
                    .text(param.title)
                    .appendTo(div);
                $(document.createElement(param.input))
                    .attr({'id':param.name,'name':param.name,'title':param.title,'type':param.type})
                    .appendTo(div);
            });
            $(div).append('<input type="button" id="" onclick="' + widgetId + 'SaveWidget" value="Guardar" /><input type="button" id="" onclick="' + widgetId + 'CancelWidget" value="Cancelar" />');
        }
    });
    
    
    output = '<script>$(function(){';
    output += '$("#' + $(ulWidget).attr('id') + '").sortable({';
    output += 'placeholder: "widgetPlaceHolder",handle:".moveWidget",connectWith:".widgetsInGrid",';
    output += 'update:function(event,ui) {if (this === ui.item.parent()[0]) {setOrder(ui);}}';
    output += '});';
    output += '$("#' + $(ulWidget).attr('id') + '").disableSelection();';
    output += '});<\/script>';
    $(e).append(output);
}
function deleteWidget(e) {
    if (confirm("\xbfEst\u00E1 seguro que desea eliminar este widget?")) {
        var li = $(e).closest("li");
        var ul = li.parent();
        li.fadeOut(function(){
            li.remove();
            ul.find("li").each(function(){
                widgetId = $(this).find("input").val();
                $("#" + widgetId + "_position").val($(this).index());
            });
        });
    }
}
function editWidget(e) {
    widget = $(e).closest("li");
    showPanel();
    $(widget).find(".widgetPanel").dialog();
    
}
function getDropScript() {
    var htmlOutput = '';
    htmlOutput += '<script>';
    htmlOutput += '$(".widgetContainer").droppable({';
    htmlOutput += 'accept: "#widgetsPanel li",';
    htmlOutput += '});';
    htmlOutput += '<\/script>';
    return htmlOutput;
}
function rand (min, max) {
    if (!min && !max) {
        min = 0;
        max = 2147483647;
    }
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
function drawFullContent() {
    var htmlOutput = "";
    var section = "";
    section = getSectionActive();
    checkListWrapper(section);
    liCount = countUlChildrens(section);
    var _id = 'block_' + liCount + '_' + rand();
    htmlOutput += '<li class="block" id="' + _id + '">';
    htmlOutput += '<div class="move"><p>mover</p></div>';
    htmlOutput += '<div class="icons">';
    htmlOutput += '<a id="' + _id + '_delete" class="cerrar" title="Eliminar Este Bloque" onclick="deleteBlock(this)"></a>';
    htmlOutput += '<a id="' + _id + '_edit" class="edit" title="Editar Apariencia" onclick="editBlockStyle(\'' + _id + '\')"></a>';
    htmlOutput += '<a id="' + _id + '_lock" class="lock" title="Bloquear Propiedades" onclick="lockBlock(\'' + _id + '\')"></a>';
    htmlOutput += '</div>';
    htmlOutput += '<div class="blockContent">';
    htmlOutput += '<div class="_fullContent widgetContainer" id="' + _id + '_0_grid" ondrop="dropWidget(this);"><a class="addWidget" onclick="showWidgets()">Agregar Widget</a></div>';
    htmlOutput += '</div>';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][block_id]" id="' + _id + '_block_id" value="' + _id + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][target]" id="' + _id + '_target" value="' + section + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][position]" id="' + _id + '_position" value="' + liCount + '" />';
    htmlOutput += '</li>';
    htmlOutput += getDropScript();
    $("#" + section + "ListWidgets").append(htmlOutput);
}
function drawOneColLeft() {
    var htmlOutput = "";
    var section = "";
    section = getSectionActive();
    checkListWrapper(section);
    liCount = countUlChildrens(section);
    var _id = 'block_' + liCount + '_' + rand();
    htmlOutput += '<li class="block" id="' + _id + '">';
    htmlOutput += '<div class="move"><p>mover</p></div>';
    htmlOutput += '<div class="icons">';
    htmlOutput += '<a id="' + _id + '_delete" class="cerrar" title="Eliminar Este Bloque" onclick="deleteBlock(this)"></a>';
    htmlOutput += '<a id="' + _id + '_edit" class="edit" title="Editar Apariencia" onclick="editBlockStyle(\'' + _id + '\')"></a>';
    htmlOutput += '<a id="' + _id + '_lock" class="lock" title="Bloquear Propiedades" onclick="lockBlock(\'' + _id + '\')"></a>';
    htmlOutput += '</div>';
    htmlOutput += '<div class="blockContent">';
    htmlOutput += '<div class="_oneColLeft widgetContainer" id="' + _id + '_0_grid" ondrop="dropWidget(this);"><a class="addWidget">Agregar Widget</a></div>';
    htmlOutput += '<div class="_oneColLeft widgetContainer" id="' + _id + '_1_grid" ondrop="dropWidget(this);"><a class="addWidget">Agregar Widget</a></div>';
    htmlOutput += '</div>';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][block_id]" id="' + _id + '_block_id" value="' + _id + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][target]" id="' + _id + '_target" value="' + section + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][position]" id="' + _id + '_position" value="' + liCount + '" />';
    htmlOutput += '</li>';
    htmlOutput += getDropScript();
    $("#" + section + "ListWidgets").append(htmlOutput);
}
function drawOneColCenter() {
    var htmlOutput = "";
    var section = "";
    section = getSectionActive();
    checkListWrapper(section);
    liCount = countUlChildrens(section);
    var _id = 'block_' + liCount + '_' + rand();
    htmlOutput += '<li class="block" id="' + _id + '">';
    htmlOutput += '<div class="move"><p>mover</p></div>';
    htmlOutput += '<div class="icons">';
    htmlOutput += '<a id="' + _id + '_delete" class="cerrar" title="Eliminar Este Bloque" onclick="deleteBlock(this)"></a>';
    htmlOutput += '<a id="' + _id + '_edit" class="edit" title="Editar Apariencia" onclick="editBlockStyle(\'' + _id + '\')"></a>';
    htmlOutput += '<a id="' + _id + '_lock" class="lock" title="Bloquear Propiedades" onclick="lockBlock(\'' + _id + '\')"></a>';
    htmlOutput += '</div>';
    htmlOutput += '<div class="blockContent">';
    htmlOutput += '<div class="_oneColCenter widgetContainer" id="' + _id + '_0_grid" ondrop="dropWidget(this);"><a class="addWidget">Agregar Widget</a></div>';
    htmlOutput += '<div class="_oneColCenter widgetContainer" id="' + _id + '_1_grid" ondrop="dropWidget(this);"><a class="addWidget">Agregar Widget</a></div>';
    htmlOutput += '</div>';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][block_id]" id="' + _id + '_block_id" value="' + _id + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][target]" id="' + _id + '_target" value="' + section + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][position]" id="' + _id + '_position" value="' + liCount + '" />';
    htmlOutput += '</li>';
    htmlOutput += getDropScript();
    $("#" + section + "ListWidgets").append(htmlOutput);
}
function drawOneColRight() {
    var htmlOutput = "";
    var section = "";
    section = getSectionActive();
    checkListWrapper(section);
    liCount = countUlChildrens(section);
    var _id = 'block_' + liCount + '_' + rand();
    htmlOutput += '<li class="block" id="' + _id + '">';
    htmlOutput += '<div class="move"><p>mover</p></div>';
    htmlOutput += '<div class="icons">';
    htmlOutput += '<a id="' + _id + '_delete" class="cerrar" title="Eliminar Este Bloque" onclick="deleteBlock(this)"></a>';
    htmlOutput += '<a id="' + _id + '_edit" class="edit" title="Editar Apariencia" onclick="editBlockStyle(\'' + _id + '\')"></a>';
    htmlOutput += '<a id="' + _id + '_lock" class="lock" title="Bloquear Propiedades" onclick="lockBlock(\'' + _id + '\')"></a>';
    htmlOutput += '</div>';
    htmlOutput += '<div class="blockContent">';
    htmlOutput += '<div class="_oneColRight widgetContainer" id="' + _id + '_0_grid" ondrop="dropWidget(this);"><a class="addWidget">Agregar Widget</a></div>';
    htmlOutput += '<div class="_oneColRight widgetContainer" id="' + _id + '_1_grid" ondrop="dropWidget(this);"><a class="addWidget">Agregar Widget</a></div>';
    htmlOutput += '</div>';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][block_id]" id="' + _id + '_block_id" value="' + _id + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][target]" id="' + _id + '_target" value="' + section + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][position]" id="' + _id + '_position" value="' + liCount + '" />';
    htmlOutput += '</li>';
    htmlOutput += getDropScript();
    $("#" + section + "ListWidgets").append(htmlOutput);
}
function drawTwoColsCenter() {
    var htmlOutput = "";
    var section = "";
    section = getSectionActive();
    checkListWrapper(section);
    liCount = countUlChildrens(section);
    var _id = 'block_' + liCount + '_' + rand();
    htmlOutput += '<li class="block" id="' + _id + '">';
    htmlOutput += '<div class="move"><p>mover</p></div>';
    htmlOutput += '<div class="icons">';
    htmlOutput += '<a id="' + _id + '_delete" class="cerrar" title="Eliminar Este Bloque" onclick="deleteBlock(this)"></a>';
    htmlOutput += '<a id="' + _id + '_edit" class="edit" title="Editar Apariencia" onclick="editBlockStyle(\'' + _id + '\')"></a>';
    htmlOutput += '<a id="' + _id + '_lock" class="lock" title="Bloquear Propiedades" onclick="lockBlock(\'' + _id + '\')"></a>';
    htmlOutput += '</div>';
    htmlOutput += '<div class="blockContent">';
    htmlOutput += '<div class="_twoColsCenter widgetContainer" id="' + _id + '_0_grid" ondrop="dropWidget(this);"><a class="addWidget">Agregar Widget</a></div>';
    htmlOutput += '<div class="_twoColsCenter widgetContainer" id="' + _id + '_1_grid" ondrop="dropWidget(this);"><a class="addWidget">Agregar Widget</a></div>';
    htmlOutput += '<div class="_twoColsCenter widgetContainer" id="' + _id + '_2_grid" ondrop="dropWidget(this);"><a class="addWidget">Agregar Widget</a></div>';
    htmlOutput += '</div>';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][block_id]" id="' + _id + '_block_id" value="' + _id + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][target]" id="' + _id + '_target" value="' + section + '" />';
    htmlOutput += '<input type="hidden" name="Block[' + _id + '][position]" id="' + _id + '_position" value="' + liCount + '" />';
    htmlOutput += '</li>';
    htmlOutput += getDropScript();
    $("#" + section + "ListWidgets").append(htmlOutput);
}
 </script>
<?php echo $footer; ?> 