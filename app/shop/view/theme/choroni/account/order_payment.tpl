<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-order-payment" nt-editable>

    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured.tpl");?>

    <!--mainContentContainer -->
    <div id="mainContentContainer" nt-editable>
        <div class="row">

            <!-- left-column -->
            <div class="large-3 medium-3 small-12">
                <div id="columnLeft" nt-editable>
                    <?php echo $account_column_left; ?>
                    <?php if ($column_left) { echo $column_left; } ?>
                </div>
            </div>
            <!--/left-column -->

            <!--center-column -->
            <?php if ($column_left && $column_right) { ?>
            <div class="large-6 medium-6 small-12">
            <?php } else { ?>
            <div class="large-9 medium-9 small-12">
            <?php } ?>
                        <div id="columnCenter" nt-editable>


                            <div class="form-entry">
                                <input type="text" name="filter_order" id="filter_order" value="" placeholder="Buscar Pedido..." />
                            </div>
                            <?php echo $text_sort; ?>
                            <div class="btn btn-filter btn--primary" data-action="filter" role="button" aria-label="Sort">
                                <a href="#" id="filter"><?php echo $l('text_filter');?></a>
                            </div>

                            <ul id="paymentMethods" class="nt-editable payment-methods">
                                <?php foreach ($payment_methods as $payment_method) { ?>
                                <li data-action="payment">{%<?php echo $payment_method['id']; ?>%}</li>
                                <?php } ?>
                            </ul>



                        </div>
                    </div>
                    <!--/center-column -->

                    <!-- right-column -->
                    <?php if ($column_right) { ?>
                    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-column-right.tpl");?>
                    <?php } ?>
                    <!--/right-column -->

                </div>
            </div>
            <!--/mainContentContainer -->

            <!--featuredFooterContainer -->
            <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured-footer.tpl");?>
            <!--/featuredFooterContainer -->

        </div>
        <!--/contentContainer -->

        <?php echo $footer; ?>