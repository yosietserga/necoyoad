<?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>

<?php if (count($currencies)>1) { ?>
<select class="nts-dropdown">
    <?php foreach($currencies as $currency) { ?>
    <option 
        value="<?php echo $currency['code']; ?>"
        data-tpl="<span class='currency_symbol'><?php echo $currency['symbol_left'] ?? $currency['symbol_right']; ?>     </span><span class='currency_code'><?php echo $currency['code']; ?></span>"
        <?php echo isset($currency_selected) && $currency_selected==$currency['code'] ? ' selected="selected"' : ''; ?>>
        <?php echo $currency['code']; ?>
    </option>
    <?php } ?>
</select>

<script>
$(function(){
    $('#<?php echo $widgetName; ?> select').on('change', function(e){
        let href = window.location.href;
        window.location.href = href + '&cc=' + $(this).val();
    });
});
</script>
<?php } elseif (isset($currencies[0])) { ?>
<?php echo $currencies[0]['symbol_left'] ?? $currencies[0]['symbol_right']; ?> <?php echo $currencies[0]['code']; ?>
<?php } ?>