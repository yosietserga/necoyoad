<div>
    <h2><?php echo $l('heading_select_language'); ?></h2>
    <ul>
        <?php foreach ($languages as $language) { ?>
        <li>
            <a title="<?php echo $language['name']; ?>" href="<?php echo $redirect . '&hl='. $language['code']; ?>">
                <img src="<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" />&nbsp;&nbsp;
                <?php echo $language['name']; ?>
            </a>
        </li>
        <?php } ?>
    </ul>
</div>