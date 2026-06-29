<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 
    <div class="container">
        <ul id="slides">
            <li class="slide showing"></li>
            <li class="slide"></li>
            <li class="slide"></li>
        </ul>
        <div class="buttons">
            <button class="controls" id="previous">&lt;</button>

            <button class="controls" id="pause">&#10074;&#10074;</button>

            <button class="controls" id="next">&gt;</button>
        </div>
    </div>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>