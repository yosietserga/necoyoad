<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<!--footerContainer -->
<div id="footerContainer" nt-editable>

    <!--footer -->
    <div id="footer" nt-editable>
        <?php $position = 'footer'; ?>
        <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-rows.tpl");?>
    </div>
    <!--/footer -->

    <!-- terms -->
    <div id="copyright" nt-editable>
        <div class="row">
            <div class="medium-12 column">
                <?php echo $text_powered_by; ?>
            </div>
        </div>
    </div>
    <!-- /terms -->

<!--/footerContainer -->
</div>

<?php include_once(DIR_TEMPLATE. $tpl . "/shared/fragment/footer-start.tpl"); ?>

<!--/mainContainer -->
</div><!--closing tag for <div id="mainContainer"> in header.tpl-->
</body>
</html>