<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 

  <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

  <div class="widget-content" id="<?php echo $widgetName; ?>Content">
    <div class="group group--btn">
      
      <?php if (isset($settings['google_client_id'])) { ?>
      <div class="action-button action-google">
        <a href="<?php echo $Url::createUrl("api/google",array('redirect'=>'invitefriends')); ?>">
           <?php echo $l('text_google_invite'); ?>
        </a>
      </div>
      <?php } ?>

      <?php if (isset($settings['live_client_id'])) { ?>
      <div class="action-button action-live">
        <a href="<?php echo $Url::createUrl("api/live",array('redirect'=>'invitefriends')); ?>">
          <?php echo $l('text_live_invite'); ?>
        </a>
      </div>
      <?php } ?>

    </div>
  </div>

<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>