<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 

  <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

  <div class="content" id="<?php echo $widgetName; ?>Content">
    <?php if ($attributes_groups) { ?>
      <div class="simple-form">
        <h3><?php echo $attributes_groups["name"]; ?></h3>
         <form id="<?php echo $widgetName; ?>Form">
            <?php if (isset($attributes_groups["attributes"])) { 
            foreach ($attributes_groups["attributes"] as $key => $attribute) { 
               $name = ($attribute['type'] === "radio") ? $attribute['group'] : $attribute['name']; ?> 
               <div class="form-entry"> 
                  <label><?php echo $attribute['label']; ?></label>
                  <input id="Attributes_<?php echo ($key .'_'. $attribute['name']); ?>" 
                        name="<?php echo $name; ?>" 
                        type="<?php echo $attribute['type']; ?>" 
                        value="" 
                        placeholder="<?php echo $attribute['label']; ?>" 
                        quickhelp="off" />
               </div>
            <?php } //end foreach ?>
            <?php } //end if ?>
            <div class="btn btn-primary action-button action-accept" 
               id="<?php echo $widgetName; ?>Submit"
               style="margin-top: 0.875rem;">
            <a><?php echo $l('text_accept'); ?></a>
            </div>
         </form>
      </div>
      <?php } ?>
   </div>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>
<script>
  $(function () {
    if (!$.ui) {
      $(document.createElement('script')).attr({
        type: 'text/javascript',
        src: '<?php echo HTTP_JS; ?>vendor/jquery-ui.min.js'
      }).appendTo('body');

      $(document.createElement('link')).attr({
        rel: 'stylesheet',
        src: '<?php echo HTTP_CSS; ?>jquery-ui/jquery-ui.min.css'
      }).appendTo('head');
    }

    if (!$.ntForm) {
      $(document.createElement('script')).attr({
        type: 'text/javascript',
        src: '<?php echo HTTP_JS; ?>necojs/neco.form.js'
      }).appendTo('body');

      $(document.createElement('link')).attr({
        rel: 'stylesheet',
        src: '<?php echo HTTP_CSS; ?>neco.form.css'
      }).appendTo('head');
    }

    $('#<?php echo $widgetName; ?>Form').ntForm({
      lockButton: false,
      submitButton: false,
      cancelButton: false
    });
    $('#<?php echo $widgetName; ?>Form').on('submit', function () {
      moduleSearchFilters({
        baseUrl: '<?php echo $Url::createUrl("store/search"); ?>/',
        form: $('#<?php echo $widgetName; ?>Form')
      });
    });
    $('#<?php echo $widgetName; ?>Submit').on('click', function () {
      moduleSearchFilters({
        baseUrl: '<?php echo $Url::createUrl("store/search"); ?>/',
        form: $('#<?php echo $widgetName; ?>Form')
      });
    });

    $('#<?php echo $widgetName; ?>Keyword').on('keyup', function (e) {
      var code = e.keyCode || e.which;
      if ($(this).val().length > 0 && code === 13) {
        moduleSearchFilters({
          baseUrl: '<?php echo $Url::createUrl("store/search"); ?>/',
          form: $('#<?php echo $widgetName; ?>Form')
        });
      }
    });
  });
</script>
