<?php echo $header; ?>
<?php echo $navigation; ?>
<div class="container">
    
    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)) { ?>
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    
    <?php if (isset($success) && $success) { ?><div class="grid_12"><div class="message success"><?php echo $success; ?></div></div><?php } ?>
    <?php if ((isset($msg) && $msg) || (isset($error_warning) && $error_warning)) { ?><div class="grid_12"><div class="message warning"><?php echo $msg ?? $error_warning; ?></div></div><?php } ?>
    <?php if (isset($error) && $error) { ?><div class="grid_12"><div class="message error"><?php echo $error; ?></div></div><?php } ?>
    <div class="grid_12" id="msg"></div>
    
    <div class="grid_12">
        <div class="box">
            <h1><?php echo $l('heading_title'); ?></h1>

            <div class="buttons">
                <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
                <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
                <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
            </div>

            <div class="clear"></div><br />

            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <div class="row">
                    <input style="width:100%;height:40px;font-size:24px;" name="iframe_url" value="<?php echo isset($iframe_url) ? $iframe_url : ''; ?>" />
                </div>
			</form>

        </div>
    </div>
</div>
<div id="toolbar-options" class="hidden">
   <a href="#"><i class="fa fa-plus-square"></i></a>
   <a href="#"><i class="fa fa-external-link"></i></a>
</div>
<script>
	$(function(){
		$('input[name=iframe_url]').on('change', function(e){
			__loadUrl($(this).val());
		});
	});

	function guid() {
	  	function s4() {
	    	return Math.floor((1 + Math.random()) * 0x10000)
	      	.toString(16)
	      	.substring(1);
	  	}
	  	return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
	}

	function __loadUrl(url) {
			let that = this;
			let iframe = $('#crawlerWrapper');

			if (iframe.length === 0) {
				iframe = $('<iframe>').attr({
					id:'crawlerWrapper',
					src:'<?php echo $Url::createAdminUrl("common/home/loadiframe"); ?>&url='+ encodeURIComponent( url ),
					width:'100%',
					height:'600px'
				}).appendTo('#form');
			} else {
				$('#crawlerWrapper').attr({
					src:'<?php echo $Url::createAdminUrl("common/home/loadiframe"); ?>&url='+ encodeURIComponent( url )
				});
			}

			iframe.on('load', function(e){
				iframe.contents().find('script').each(function(){
					if ($(this).attr('src')) {
						$(this).attr('src', null);
					}
				})
				iframe.contents().on('mouseover', function(e){
	                let el = e.target;
	                if (!el.id) {
	                	el.id = guid();
	                }
	                console.log(el);
	                $(el).css({
	                    border:'dashed 1px red'
	                })

	                if ($('#toolbar'+ el.id).length === 0) {
		                $(el).toolbar({
		                	id:'toolbar'+ el.id,
							content: '#toolbar-options',
							event: 'click',
							hideOnClick: true
						});
	            	}
	            }).on('mouseout', function(e){
	                let el = e.target;
	                console.log(el);
	                $(el).css({
	                    border:'none'
	                });
	            }).on('click', function(e){
	                e.preventDefault();
	                let el = e.target;
	                //TODO: show submenu 
	                // save selector reference 
	                // open link 
	                $('.tool-container').not('#toolbar'+ el.id).hide();
	                console.log('el', el);
	                console.log('parents', $(el).parents());
	                console.log('children', $(el).children());
	                return false;
	            }).on('dblclick', function(e){
	            	e.preventDefault();
	            	console.log( 'double clicked!' )
	            	let href = $(this).attr('href');
	            	if (href) __loadUrl(href);
	            });
            });
	}
</script>
<?php echo $footer; ?>