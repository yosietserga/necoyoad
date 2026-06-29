<div>
    <h2><?php echo $l('heading_select_currency'); ?></h2>
    <ul>
    <?php foreach ($currencies as $currency) { ?>
        <li>
            <a title="<?php echo $currency['title']; ?>" href="<?php echo $redirect . '&cc='. $currency['code']; ?>">
                <?php echo $currency['title']; ?>&nbsp;( <?php echo $currency['code']; ?> )
            </a>
        </li>
    <?php } ?>
    </ul>
</div>