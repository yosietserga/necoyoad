<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 style="background-image: url('view/image/country.png');"><?php echo $l('heading_title'); ?></h1>
    <div class="buttons"><a title="" onClick="$('#form').submit();" class="button"><span><?php echo $l('button_save'); ?></span></a><a title="" onClick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $l('button_cancel'); ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
    
      <?php require_once(dirname(__FILE__)."/../shared/form/general_descriptions.tpl"); ?>
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $l('entry_name'); ?><a title="Ingrese le nombre del pa&iacute;s"> (?)</a></td>
          <td><input title="Ingrese le nombre del pa&iacute;s" type="text" name="name" value="<?php echo $name; ?>">
            <?php if ($error_name) { ?>
            <span class="error"><?php echo $error_name; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $l('entry_iso_code_2'); ?><a title="Ingrese le C&oacute;digo ISO de dos caracteres"> (?)</a></td>
          <td><input title="Ingrese le C&oacute;digo ISO de dos caracteres" type="text" name="iso_code_2" value="<?php echo $iso_code_2; ?>"></td>
        </tr>
        <tr>
          <td><?php echo $l('entry_iso_code_3'); ?><a title="Ingrese le C&oacute;digo ISO de tres caracteres"> (?)</a></td>
          <td><input title="Ingrese le C&oacute;digo ISO de tres caracteres" type="text" name="iso_code_3" value="<?php echo $iso_code_3; ?>"></td>
        </tr>
        <tr>
          <td><?php echo $l('entry_address_format'); ?><a title="Ingrese el formato de direcci&oacute;n del pa&iacute;s, si no sabe cu&aacute;l es, deje este campo vac&iacute;o"> (?)</a></td>
          <td><textarea title="Ingrese el formato de direcci&oacute;n del pa&iacute;s, si no sabe cu&aacute;l es, deje este campo vac&iacute;o" name="address_format" cols="40" rows="5"><?php echo $address_format; ?></textarea></td>
        </tr>
		<tr>
          <td><?php echo $l('entry_status'); ?><a title="Selecione el estado del pa&iacute;s"> (?)</a></td>
          <td><select name="status">
              <?php if ($status) { ?>
              <option value="1" selected="selected"><?php echo $l('text_enabled'); ?></option>
              <option value="0"><?php echo $l('text_disabled'); ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $l('text_enabled'); ?></option>
              <option value="0" selected="selected"><?php echo $l('text_disabled'); ?></option>
              <?php } ?>
            </select></td>
        </tr>
      </table>
      
    </form>
  </div>
</div>
<?php echo $footer; ?>