<?php
function drawCategories($categories, $thumb_width, $thumb_height, $Url, $Image) {
    if (count($categories)>0) {
        $tpl = '<ul>';
        foreach ($categories as $category) {

            $path = isset($category['parent_id']) && !empty($category['parent_id']) ?
                $category['parent_id'] .'_'. $category['category_id'] : $category['category_id'];

            $href = $Url::createUrl('store/category', array('path'=>$path));
            $image = $Image::resizeAndSave($category['cimage'], $thumb_width, $thumb_height);

            $tpl .= '<li'. ($category['children'] ? ' class="hasCategories"' : '') .'>';

            $tpl .= '<figure class="picture">';
            $tpl .= '<a class="thumb" href="'. $href .'" title="'. $category['cname'] .'">';
            $tpl .= '<img src="'. $image .'" alt="'. $category['cname'].'" />';
            $tpl .= '</a>';
            $tpl .= '</figure>';

            $tpl .= '<div class="info">';
            $tpl .= '<a class="name" href="'. $href .'" title="'. $category['cname'] .'">'. $category['cname'] .'</a>';
            $tpl .= '</div>';

            if (isset($category['children'])) {
                $tpl .= drawCategories($category['children'], $thumb_width, $thumb_height, $Url, $Image);
            }
            $tpl .= '</li>';
        }
        $tpl .= '</ul>';
        return $tpl;
    }
}
?>

<ul class="catalog-grid">
    <?php echo drawCategories($categories, $thumb_width, $thumb_height, $Url, $Image); ?>
</ul>