<div class="pagination"><?php echo $pagination; ?></div>
<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
    <table id="list">
        <thead>
            <tr>
                <th><?php echo $l('column_name'); ?></th>
                <th><?php echo $l('column_action'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if ($modules) { ?>
            <?php foreach ($modules as $module) { ?>
                <td><?php echo $module['name']; ?></td>
                <td>
                <?php foreach ($module['action'] as $action) { ?>
                <?php 
                    if ($action['action'] == "install") {
                        $href = "href='" . $action['href'] ."'";
                        $jsfunction = "";
                    } elseif ($action['action'] == "activate") {
                        $jsfunction = "activate(". $module['extension_id'] .")";
                        $href = $action['href'];
                    }  elseif ($action['action'] == "deactivate") {
                        $jsfunction = "deactivate(". $module['extension_id'] .")";
                        $href = $action['href'];
                    } elseif ($action['action'] == "edit") {
                        $href = "href='" . $action['href'] ."'";
                        $jsfunction = "";
                    }  
                ?>
                <a title="<?php echo $action['text']; ?>" <?php echo $href; ?> onclick="<?php echo $jsfunction; ?>"><img id="img_<?php echo $module['extension_id']; ?>" src="<?php echo $action['img']; ?>" alt="<?php echo $action['text']; ?>" /></a>
                <?php } ?>
                </td>
            </tr>
            <?php } ?>
        <?php } else { ?>
            <tr><td colspan="8" style="text-align:center"><?php echo $l('text_no_results'); ?></td></tr>
        <?php } ?>
        </tbody>
    </table>
</form>
<div class="pagination"><?php echo $pagination; ?></div>