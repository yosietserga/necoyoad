<!-- catalog-latest -->
<?php if($tabs) { ?>
    <div class="product-tabs large-12 medium-12 small-12 columns" id="<?php echo 'tabs_wrapper_'. $k .'_'. $widgetName; ?>" nt-editable>
        <ul class="tabs" id="<?php echo 'tabs_'. $k .'_'. $widgetName; ?>" nt-editable>
            <?php foreach($tabs as $k => $tab) { ?>
            <li class="tab" id="<?php echo 'tab_'. $k .'_'. $widgetName; ?>">
                <span class="tab-item"><?php echo $tab['name']; ?></span>
            </li>
            <?php } ?>
        </ul>

        <?php foreach($tabs as $k => $tab) { ?>
        <div id="_<?php echo 'tab_'. $k .'_'. $widgetName; ?>" nt-editable>
            <?php include("product_tabs_home_". $tab['view'] .".tpl"); ?>
        </div>
        <?php } ?>
    </div>

<script data-script="tabs">
    (function () {
        window.deferjQuery(function () {
            var $tabs = $('.tab');
            $tabs.each(function(){
                $(this).removeClass('active');
                $('#_' + this.id).hide();
            });
            $tabs.on('click',function() {
                $tabs.each(function(){
                    $(this).removeClass('active');
                    $('#_' + this.id).hide();
                });
                $(this).addClass('active');
                $('#_' + this.id).show();
            });

            $("#<?php echo 'tab_0_'. $widgetName; ?>").addClass('active');
            $('#_<?php echo 'tab_0_'. $widgetName; ?>').show();
        });
    })();
</script>

<?php } ?>
