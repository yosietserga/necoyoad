<?php echo $header; ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 style="background-image: url('image/report.png');"><?php echo $l('heading_title'); ?></h1>
  </div>
  <div class="content">
    <table class="list">
      <thead>
        <tr>
          <td class="left"><?php echo $l('column_name'); ?></td>
          <td class="right"><?php echo $l('column_quantity; ?></td>
          <td class="right"><?php echo $l('column_total; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php if ($products) { ?>
        <?php foreach ($products as $product) { ?>
        <tr>
          <td class="left"><?php echo $product['mname']; ?></td>
          <td class="right"><?php echo $product['quantity']; ?></td>
          <td class="right"><?php echo $product['total']; ?></td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
          <td class="center" colspan="4"><?php echo $l('text_no_results'); ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <div class="pagination"><?php echo $pagination; ?></div>
  </div>
</div><form action="index.php?r=tool/excel&token=<?php echo $_GET['token']; ?>" name="excel_form" id="excel_form" method="post">
	<input type='hidden' value='<?php echo $content_excel; ?>' name='excel_data' id='excel_data'>
</form>
<form action="index.php?r=tool/csv&token=<?php echo $_GET['token']; ?>" name="csv_form" id="csv_form" method="post">
	<input type='hidden' value='<?php echo $content_csv; ?>' name='csv_data' id='csv_data'>
</form>
<script>
$(function(){    
	jQuery('#pdf_button img').attr('src','image/menu/pdf_off.png');
})
</script>
<script type="text/javascript">
function excel() {
	jQuery('#excel_form').submit();
}
function csv() {
	jQuery('#csv_form').submit();
}
</script>
<?php echo $footer; ?>