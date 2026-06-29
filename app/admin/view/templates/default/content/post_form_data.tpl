<?php 
    require_once(dirname(__FILE__)."/../shared/form/data_image.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_allow_reviews.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_publish.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_date_start_end.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_view.tpl"); 

    $object_category = $post_category ?? $category;
    require_once(dirname(__FILE__)."/../shared/form/data_categories.tpl");
    
    $object_id = $post_id;
    require_once(dirname(__FILE__)."/../shared/form/data_customergroups.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_stores.tpl");
?>
            