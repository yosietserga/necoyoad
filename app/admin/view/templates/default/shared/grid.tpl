<?php if (isset($batch_available)) { ?>
<select id="batch">
    <option value="">Procesamiento en lote</option>
    <?php if (in_array('editAll', $batch_available)) { ?><option value="editAll"><?php echo $l('Edit'); ?></option><?php } ?>
    <?php if (in_array('copyAll', $batch_available)) { ?><option value="copyAll"><?php echo $l('Copy'); ?></option><?php } ?>
    <?php if (in_array('deleteAll', $batch_available)) { ?><option value="deleteAll"><?php echo $l('Delete'); ?></option><?php } ?>
</select>
<a href="#" title="Ejecutar acci&oacute;n por lote" onclick="if ($('#batch').val().length <= 0) { return false; } else { window[$('#batch').val()](); return false;}" style="margin-left: 10px;font-size: 10px;">[ Ejecutar ]</a>
<div class="clear"></div><br />
<?php } //end if ?>

<?php if (isset($pagination)) { ?><div class="pagination"><?php echo $pagination; ?></div><?php } ?>
<form method="post" enctype="multipart/form-data" id="form">
    <table id="list">
        <thead>
            <tr>
                <th><input title="Seleccionar Todos" type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                
                <?php if (isset($columns) && is_array($columns) && !empty($columns)) { ?>
                <?php foreach ($columns as $column) { ?>
                <th>
                    <?php if (isset($column['isSortable']) && $column['isSortable']) { ?>
                    <a onclick="$('#gridWrapper').load('<?php echo ${"sort_".$column['name']}; ?>')"<?php if ($sort == $column['name']) { ?> class="<?php echo strtolower($order); ?>" <?php } ?>>
                    <?php } //end if ?>
                    
                    <?php echo $l($column['label']); ?>
                    
                    <?php if (isset($column['isSortable']) && $column['isSortable']) { ?>
                    </a>
                    <?php } //end if ?>
                </th>
                <?php } //end foreach ?>
                <?php } //end if ?>

                <?php if (isset($this->public_methods) && in_array('sortable', $this->public_methods)) { ?>
                <th>Sort Order</td>
                <?php } ?>

                <th><?php echo $l('column_action'); ?></th>
            </tr>
        </thead>
        
        <?php if (isset($this->public_methods) && !in_array('nestedSortable', $this->public_methods)) { ?>
        <tbody>
                
            <?php if (isset($results) && is_array($results) && !empty($results)) { ?>
                <?php foreach ($results as $result) { ?>
                <tr id="tr_<?php echo $result['id']; ?>">
                    <td label="Select"><input title="Seleccionar para una acci&oacute;n" type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" <?php if ($result['selected']) { ?>checked="checked"<?php } ?>/></td>
                    
                    
                    <?php if (isset($columns) && is_array($columns) && !empty($columns)) { ?>
                    <?php foreach ($columns as $column) { ?>
                    <td label="<?php echo $column['label'] ?? $column['name']; ?>">
                        <?php echo isset($column['formatter']) && is_callable($column['formatter']) ? $column['formatter']($result) : $result[$column['name']]; ?>
                    </td>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                    
                    <?php if (isset($this->public_methods) && in_array('sortable', $this->public_methods)) { ?>
                    <td label="Move or Drag" class="move"><img src="<?php echo str_replace('%theme%',$Config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE) .'move.png'; ?>"" alt="Posicionar" title="Posicionar" style="text-align:center" /></td>
                    <?php } ?>

                    <td label="Actions">
                    <?php if (isset($result['actions']) && is_array($result['actions']) && !empty($result['actions'])) { ?>
                    <?php foreach ($result['actions'] as $action) { ?>
                    <?php 
                        if ($action['action'] == "activate") { 
                            $jsfunction = "activate(". $result['id'] .")";
                            $href = "";
                        } elseif ($action['action'] == "delete") {
                            $jsfunction = "eliminar(". $result['id'] .")";
                            $href = "";
                        } elseif ($action['action'] == "edit") {
                            $href = "href='" . $action['href'] ."'";
                            $jsfunction = "";
                        }
                    ?>
                    <a title="<?php echo $action['text']; ?>" <?php echo $href; ?> onclick="<?php echo $jsfunction; ?>"><img id="img_<?php echo $result['id']; ?>" src="<?php echo $action['img']; ?>" alt="<?php echo $action['text']; ?>" /></a>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                    </td>
                </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="8" style="text-align:center"><?php echo $l('text_no_results'); ?></td></tr>
            <?php } ?>
        </tbody>
        <?php } //end if ?>

    </table>
    
    <?php if (isset($this->public_methods) && in_array('nestedSortable', $this->public_methods)) { ?>
        <?php if (isset($categories)) { ?>
            <?php echo $categories; ?>
        <?php } else { ?>
            <?php echo $l('text_no_results'); ?>
        <?php } ?>
    <?php } //end if ?>
</form>
<?php if (isset($pagination)) { ?><div class="pagination"><?php echo $pagination; ?></div><?php } ?>