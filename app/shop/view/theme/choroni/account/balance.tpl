<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<section id="maincontent" class="row">
    <?php include(DIR_TEMPLATE. $tpl ."/shared/page-start.tpl");?>

    <div class="filter-form simple-form">
        <input type="text" name="filter_order" id="filter_order" value="" placeholder="<?php echo $l('placeholder_search_by_id');?>"/>
        <select name="filter_status" id="filter_status">
            <option value=""><?php echo $l('text_option_all');?></option>
            <option value="0"><?php echo $l('text_option_pending');?></option>
            <option value="1"><?php echo $l('text_option_confirmed');?></option>
            <option value="-1"><?php echo $l('text_option_no_confirmed');?></option>
        </select>
        <select name="filter_limit" id="filter_limit">
            <option value="5"><?php echo $l('text_option_5_per_page');?></option>
            <option value="10"><?php echo $l('text_option_10_per_page');?></option>
            <option value="20"><?php echo $l('text_option_20_per_page');?></option>
            <option value="50"><?php echo $l('text_option_50_per_page');?></option>
        </select>
        <?php echo $text_sort; ?>
        <div class="btn btn-filter btn--primary" data-action="filter" aria-label="Sort">
            <a id="filter" href="#"><?php echo $l('text_filter');?></a>
        </div>
    </div>

    <div class="payment-form">
        <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="form">
            <?php if ($payments) { ?>
            <table>
                <thead>
                <tr>
                    <th><?php echo $l('table_head_payment_id');?></th>
                    <th><?php echo $l('table_head_order_id');?></th>
                    <th><?php echo $text_status; ?></th>
                    <th><?php echo $text_date_added; ?></th>
                    <th><?php echo $text_total; ?></th>
                    <th><?php echo $l('table_head_actions');?></th>
                </tr>
                </thead>
                        <?php foreach ($payments as $value) { ?>
                <tr id="pid_<?php echo $value['order_payment_id']; ?>">
                    <td><b>#<?php echo $value['order_payment_id']; ?></b></td>
                    <td><a href="<?php echo $Url::createUrl("account/invoice",array('order_id'=>$value['order_id'])); ?>"><?php echo $value['order_id']; ?></a></td>
                    <td><?php echo $value['status']; ?></td>
                    <td><?php echo $value['date_added'];?></td>
                    <td><?php echo $value['amount']; ?></td>
                    <td><a href="<?php echo $Url::createUrl("account/payment/receipt",array('payment_id'=>$value['order_payment_id'])); ?>" class="button"><?php echo $l('text_see_recipe');?></a></td>
                </tr>
                        <?php } ?>
            </table>
            <?php if ($pagination) { ?><div class="pagination"><?php echo $pagination; ?></div><?php } ?>
            <?php } else { ?>
                <div class="no-info"><?php echo $l('text_empty_page');?>&nbsp;<a class="suggestion-action" href="<?php echo $Url::createUrl("account/payment/register"); ?>"><?php echo $l('text_help');?></a></div>
            <?php } ?>
        </form>
    </div>
    
                        <?php if($widgets) { ?>
                            <ul class="columns-widgets widgets">
                            <?php foreach ($widgets as $widget) { ?>{%<?php echo $widget; ?>%}<?php } ?>
                            </ul>
                        <?php } ?>
                        
    <?php include(DIR_TEMPLATE. $tpl ."/shared/columns-end.tpl"); ?>
</section>
<script>
    (function () {
        window.deferjQuery(function () {
            function filterProducts() {
                var url = '';
                var subjectFilter = $('#filter_subject').val();
                var sortFilter = $('#filter_sort').val();
                var statusFilter = $('#filter_status').val();
                var limitFilter = $('#filter_limit').val();

                if (subjectFilter){
                    url += '&keyword=' + subjectFilter;
                }

                if (sortFilter){
                    url += '&sort=' + sortFilter;
                }

                if (statusFilter){
                    url += '&status=' + statusFilter;
                }

                if (limitFilter){
                    url += '&limit=' + limitFilter;
                }
                window.location.href = '<?php echo $Url::createUrl("account/order"); ?>' + url;
                return false;
            }
            $('#filter').on('click',function(e){
                filterProducts();
                return false;
            });
            $('#filter_customer_product').on('keydown',function(e) {
                if (e.keyCode == 13) {
                    filterProducts();
                }
                return false;
            });
        });
    })();
</script>
<?php echo $footer; ?>