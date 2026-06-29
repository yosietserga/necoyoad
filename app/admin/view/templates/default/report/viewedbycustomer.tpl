<?php echo $header; ?>
<?php if (isset($success) && $success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 style="background-image: url('image/report.png');"><?php echo $l('heading_title'); ?></h1>
    <div class="buttons"><a href="<?php echo $back; ?>" class="button"><span><?php echo $l('button_back; ?></span></a></div>  
  </div>
  <div class="content">  
            <label>Fecha Inicial:</label><input type="necoDate" name="filter_sdate" value="<?php echo $filter_sdate; ?>" id="sdate">
            <label>Fecha Final:</label><input type="necoDate" name="filter_fdate" value="<?php echo $filter_fdate; ?>" id="fdate">
    <table class="list">
    <thead>
          <tr>
            <td class="left"><?php if ($sort == 'email') { ?>
              <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $l('column_email; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_email; ?>"><?php echo $l('column_email; ?></a>
              <?php } ?></td>
            <td class="left"><?php if ($sort == 'name') { ?>
              <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $l('column_name'); ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_name; ?>"><?php echo $l('column_name'); ?></a>
              <?php } ?></td>
            <td class="left"><?php if ($sort == 'viewed') { ?>
              <a href="<?php echo $sort_viewed; ?>" class="<?php echo strtolower($order); ?>"><?php echo $l('column_tviewed; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_viewed; ?>"><?php echo $l('column_tviewed; ?></a>
              <?php } ?></td>
          <td class="right"><?php echo $l('column_percent; ?></td>
          </tr>
        </thead>
      <tbody>
      <tr class="filter">
            <td><input type="text" name="filter_email" value="<?php echo $filter_email; ?>"></td>
            <td><input type="text" name="filter_name" value="<?php echo $filter_name; ?>"></td>
            <td></td>
            <td align="right"><a onclick="filter();" class="button"><span><?php echo $l('button_filter; ?></span></a></td>
          </tr>
        <?php if ($products) { ?>
        <?php foreach ($products as $product) { ?>
        <?php if (empty($product['name'])) continue; ?>    
        <?php if ($product['percent'] < 1) { ?>            
           <tr style="background: #FFF0F0;">
          <?php } else { ?>            
          <tr style="font-weight:bold;background: #F4FFF0">
          <?php } ?>     
          <td class="left"><a href="<?php echo HTTP_HOME."index.php?r=sale/customer/update&token=".$_GET['token']."&customer_id=".$product['customer_id']; ?>"><?php echo $product['email']; ?></a></td>
           <td class="left"><?php echo $product['name']; ?></td>
          <td class="right"><?php echo $product['tviewed']; ?></td>
          <?php if ($product['percent'] == '0%') { ?>            
          <td class="right"><?php echo $product['percent']; ?></td>
          <?php } else { ?>            
          <td class="right"><b><?php echo $product['percent']; ?></b></td>
          <?php } ?>
        </tr>        
        <?php } ?>  
        <?php } else { ?>
        <tr>
          <td class="center" colspan="5"><?php echo $l('text_no_results'); ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <div class="pagination"><?php echo $pagination; ?></div>
    <h1>Informaci&oacute;n Detallada</h1>
    <table class="list">
    <thead>
          <tr>
            <td class="left"><?php if ($sort == 'email') { ?>
              <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $l('column_email; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_email; ?>"><?php echo $l('column_email; ?></a>
              <?php } ?></td>
            <td class="left"><?php if ($sort == 'name') { ?>
              <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $l('column_name'); ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_name; ?>"><?php echo $l('column_name'); ?></a>
              <?php } ?></td>
            <td class="left"><?php if ($sort == 'store_name') { ?>
              <a href="<?php echo $sort_store_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $l('column_store_name; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_store_name; ?>"><?php echo $l('column_store_name; ?></a>
              <?php } ?></td>
            <td class="left"><?php if ($sort == 'ip') { ?>
              <a href="<?php echo $sort_ip; ?>" class="<?php echo strtolower($order); ?>"><?php echo $l('column_ip; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_ip; ?>"><?php echo $l('column_ip; ?></a>
              <?php } ?></td>
            <td class="left"><?php echo $l('column_added; ?></td>
          </tr>
        </thead>
      <tbody>
        <?php if ($products['detail']) {  ?>
        <?php foreach ($products['detail'] as $detail) { ?>
        <tr>    
          <td class="left"><?php echo $detail['email']; ?></td>
          <td class="left">
          <?php if (!empty($detail['name'])) { ?>
                    <?php echo $detail['name']; ?>
           <?php     } else { ?>
                    Visitante
            <?php   } ?>
          </td>
          <td class="left"><a href="https://<?php echo $detail['store_url']; ?>" target="_blank"><?php echo $detail['store_name']; ?></a></td>          
          <td class="left"><?php echo $detail['ip']; ?></td>   
          <td class="left"><?php echo $detail['added']; ?></td>
        </tr>        
        <?php } ?> 
        <?php } else { ?>
        <tr>
          <td class="center" colspan="5"><?php echo $l('text_no_results'); ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
<form action="index.php?r=tool/excel&token=<?php echo $_GET['token']; ?>" name="excel_form" id="excel_form" method="post">
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
<script type="text/javascript">
function filter() {
	url = 'index.php?r=report/viewedbycustomer&token=<?php echo $_GET["token"]; ?>&product_id=<?php echo $_GET["product_id"]; ?>';
	
	var filter_name = $('input[name=\'filter_name\']').attr('value');
	
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
    
	var filter_email = $('input[name=\'filter_email\']').attr('value');
	
	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}
    
	var filter_sdate = $('input[name=\'filter_sdate\']').attr('value');
	
	if (filter_sdate) {
		url += '&filter_sdate=' + encodeURIComponent(filter_sdate);
	}
    
	var filter_fdate = $('input[name=\'filter_fdate\']').attr('value');
	
	if (filter_fdate) {
		url += '&filter_fdate=' + encodeURIComponent(filter_fdate);
	}
		
	location = url;
}
</script>
<script type="text/javascript">
$(function() {    
    $('#sdate').datepicker({dateFormat: 'yy-mm-dd'});
    $("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
})
</script>
<?php echo $footer; ?>