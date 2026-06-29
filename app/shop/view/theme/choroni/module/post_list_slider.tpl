<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>
    <?php include(DIR_TEMPLATE. $tpl ."/shared/sort.tpl"); ?>
    <?php include(DIR_TEMPLATE. $tpl ."/shared/pagination.tpl"); ?>
    <?php include(DIR_TEMPLATE. $tpl ."/shared/blockgrid-start.tpl"); ?>

    <?php foreach($posts as $post) { ?>
    <li class="catalog-item">
        <?php if (isset($post['thumb'])) include(DIR_TEMPLATE. $tpl ."/shared/post-picture.tpl"); ?>
        <?php include(DIR_TEMPLATE. $tpl ."/shared/post-info.tpl"); ?>
    </li>
    <?php } ?>

    <?php include(DIR_TEMPLATE. $tpl ."/shared/blockgrid-end.tpl"); ?>
    <?php include(DIR_TEMPLATE. $tpl ."/shared/pagination.tpl"); ?>