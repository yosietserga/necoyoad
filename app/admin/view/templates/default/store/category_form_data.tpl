<?php 
    require_once(dirname(__FILE__)."/../shared/form/data_image.tpl"); 
    require_once(dirname(__FILE__)."/../shared/form/data_view.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_parent_id.tpl"); 

    $object_id = $category_id;
    require_once(dirname(__FILE__)."/../shared/form/data_stores.tpl");
?>

<div class="clear"></div><br />

<div id="addsPanel" class="necoPanel"><b>Agregar / Eliminar Productos</b></div>
<div id="addsWrapper"><div id="gridPreloader"></div></div>

<div class="clear"></div><br />
            