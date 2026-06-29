<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<!--contentContainer -->
<div id="contentContainer" class="tpl-page" nt-editable>

    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-common.tpl"); ?>
    
</div>

    <div class="svg-container-01" style="width: 2918px; height: 1306px;">
        <?php include("landing_page_necotienda_svg_01.tpl");?>
    </div>

    <div class="svg-container-02" style="width: 2374px; height: 681px;">
        <?php include("landing_page_necotienda_svg_02.tpl");?>
    </div>

    <div class="svg-container-03" style="width: 2388px; height: 667px;">
        <?php include("landing_page_necotienda_svg_03.tpl");?>
    </div>

    <div class="svg-container-04" style="width: 2374px; height: 681px;">
        <?php include("landing_page_necotienda_svg_02.tpl");?>
    </div>

    <div class="svg-container-05" style="width: 2374px; height: 681px;">
        <?php include("landing_page_necotienda_svg_02.tpl");?>
    </div>

<script>
let style = $(document.createElement('style'));
let windowHeight = $(window).height();
let containerWidth = $('#mainContainer').width();
$('.svg-container-01').width( containerWidth );
style.html(
    '#headerContainer {'
        +'position:relative;'
        +'z-index:200;'
    +'}'
    
    +'#featuredContentContainer, #contentContainer {'
        +'position:relative;'
        +'z-index:2;'
    +'}'
    
    +'.svg-container-01, .svg-container-02, .svg-container-03, .svg-container-04, .svg-container-05 {'
        +'position:absolute;'
        +'z-index:1;'
        +'overflow-x:hidden;'
        +`width:${containerWidth}px !important;`
    +'}'
    
    +'.svg-container-01 {'
        +`top:${windowHeight-1306}px !important;`
    +'}'
    
    +'.svg-container-02 {'
        +`top:${Math.abs(windowHeight/2-1306).toFixed(2)}px !important;`
    +'}'
    
    +'.svg-container-03 {'
        +`top:${Math.abs(windowHeight+1306).toFixed(2)}px !important;`
    +'}'
    
    +'.svg-container-04 {'
        +`top:${Math.abs(windowHeight+3000).toFixed(2)}px !important;`
        +`width:${containerWidth+600}px !important;`
        +'left:-70%'
    +'}'
    
    +'.svg-container-05 {'
        +`top:${Math.abs(windowHeight+4000).toFixed(2)}px !important;`
    +'}'
    
    +'#header_widgetRow_dc19cb628508 {'
        +'background: rgba(0,0,0,0.1);'
        +'box-shadow: rgba(0,0,0,0.3) 0 0 18px;'
        +'transition:all 0.4s ease-out;'
    +'}'
    
    +'#header_widgetRow_dc19cb628508.sticky {'
        +`width:${containerWidth-20}px !important;`
        +'background: rgba(229,19,255,1);'
    +'}'
).attr({
    id:'lading_page_necotienda'
}).appendTo('head');

$(window).resize(function() {
    let containerWidth = $('#contentContainer').width();
    $('.svg-container-01').width( containerWidth );
    $('#header_widgetRow_dc19cb628508.sticky').width( containerWidth-20 );
});
</script>
    
<!--/contentContainer -->
<?php echo $footer; ?>