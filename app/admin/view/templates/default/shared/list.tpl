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
    
    <div class="box">
        <div class="header">
            <h1><?php echo $l('heading_title'); ?></h1>
            <div class="buttons">
            <a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $l('button_insert'); ?></a>
            </div>
        </div>    
          
        <div class="clear"></div><br />
        
        <h3>Filtros<span id="filters">[ <?php echo $l('Show'); ?> ]</span></h3>
        <form action="<?php echo $search; ?>" method="post" enctype="multipart/form-data" id="formFilter">
            <div class="grid_11">

            <?php if (isset($this->filters) && is_array($this->filters) && !empty($this->filters)) { ?>
            <?php foreach($this->filters as $k => $v) { ?>

                <?php if (isset($v['type']) && $v['type'] == 'date') { ?>
                    <div class="row">
                        <label><?php echo $v['label'] ?? $v['name']; ?></label>
                        <input type="necoDate" name="<?php echo $v['name']; ?>" value="" />
                    </div>
                <?php } elseif (isset($v['type']) && $v['type'] == 'option') { ?>
                    <div class="row">
                        <label><?php echo $v['label'] ?? $v['name']; ?></label>
                        <select name="<?php echo $v['name']; ?>">
                            <option value="">Selecciona un campo</option>

                            <?php foreach($v['options'] as $option_name => $option_label) { ?>
                                <option value="<?php echo $option_name; ?>"><?php echo $option_label ?? $option_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <label><?php echo $v['label'] ?? $v['name']; ?></label>
                        <input type="<?php echo $v['type']; ?>" name="<?php echo $v['name']; ?>" value="" />
                    </div>
                <?php } //end if ?>

            <?php } //end foreach ?>
            <?php } //end if ?>
                
            </div>
                        
            <div class="clear"></div><br />
        </form>
    </div>
    
    <div class="clear"></div>
    
    <div class="box">
        <div id="gridPreloader"></div>
        <div id="gridWrapper"></div>
    </div>
</div>
<?php echo $footer; ?>