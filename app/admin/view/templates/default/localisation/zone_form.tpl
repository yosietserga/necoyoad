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
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $l('entry_name'); ?><a title="Ingrese el nombre del estado, la provincia o el departamento"> (?)</a></td>
          <td><input title="Ingrese el nombre del estado, la provincia o el departamento" type="text" name="name" value="<?php echo $name; ?>">
            <?php if ($error_name) { ?>
            <span class="error"><?php echo $error_name; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $l('entry_code'); ?><a title="Si lo posee, ingrese el c&oacute;digo ISO del estado, la provincia o el departamento sino, ingrese las dos primeras letras del nombre del estado siempre y cuando no se repitan para el mismo pa&iacute;s"> (?)</a></td>
          <td><input title="Si lo posee, ingrese el c&oacute;digo ISO del estado, la provincia o el departamento sino, ingrese las dos primeras letras del nombre del estado siempre y cuando no se repitan para el mismo pa&iacute;s" type="text" name="code" value="<?php echo $code; ?>"></td>
        </tr>
        <tr>
          <td><?php echo $l('entry_country'); ?><a title="Seleccione el pa&iacute;s al cual pertenece"> (?)</a></td>
          <td><select title="Seleccione el pa&iacute;s al cual pertenece" name="country_id">
              <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
		<tr>
          <td><?php echo $l('entry_status'); ?><a title="Seleccione el status del estado, la provincia o el departamento"> (?)</a></td>
          <td><select title="Seleccione el status del estado, la provincia o el departamento" name="status">
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