<?php
function drawManufacturers($manufacturers, $thumb_width, $thumb_height, $Url, $Image) {
    if (count($manufacturers)>0) {
        $tpl = '<ul>';
        foreach ($manufacturers as $manufacturer) {

            $href = $Url::createUrl('store/manufacturer', array('manufacturer_id'=>$manufacturer['manufacturer_id']));
            $image = $Image::resizeAndSave($manufacturer['mimage'], $thumb_width, $thumb_height);

            $tpl .= '<li>';

            $tpl .= '<figure class="picture">';
            $tpl .= '<a class="thumb" href="'. $href .'" title="'. $manufacturer['mname'] .'">';
            $tpl .= '<img src="'. $image .'" alt="'. $manufacturer['mname'].'" />';
            $tpl .= '</a>';
            $tpl .= '</figure>';

            $tpl .= '<div class="info">';
            $tpl .= '<a class="name" href="'. $href .'" title="'. $manufacturer['mname'] .'">'. $manufacturer['mname'] .'</a>';
            $tpl .= '</div>';

            $tpl .= '</li>';
        }
        $tpl .= '</ul>';
        return $tpl;
    }
}
?>

<ul class="catalog-grid">
    <?php echo drawManufacturers($manufacturers, $thumb_width, $thumb_height, $Url, $Image); ?>
</ul>