<?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>
<?php $flags_path = HTTP_IMAGE . '/flags/'; ?>

<?php if (count($languages)>1) { ?>
<select class="nts-dropdown">
    <?php foreach($languages as $language) { ?>
    <option 
        value="<?php echo $language['code']; ?>"
        data-tpl="<img width='16' src='<?php echo $flags_path . $language['code'] . '.png'; ?>' alt='<?php echo $language['code']; ?>' /><span><?php echo $language['name']; ?></span>"
        <?php echo isset($language_selected) && $language_selected==$language['code'] ? ' selected="selected"' : ''; ?>>
        <?php echo $language['name']; ?>
    </option>
    <?php } ?>
</select>

<script>
$(function(){
    $('#<?php echo $widgetName; ?> select').on('change', function(e){
        let href = window.location.href;
        window.location.href = href + '&hl=' + $(this).val();
    });
});
</script>
<?php } elseif (isset($languages[0])) { ?>
<img width='16' src='<?php echo $flags_path . $languages[0]['code'] . '.png'; ?>' alt='<?php echo $languages[0]['code']; ?>' /> <?php echo $languages[0]['name']; ?>
<?php } ?>