<?php
if (!is_callable('__drawCategories')) {
function __drawCategories($categories, $thumb_width, $thumb_height, $Url, $Image) {
    if (isset($categories[0])) {
        $tpl = '<ul>';
        foreach ($categories as $category) {

            $path = isset($category['parent_id']) && !empty($category['parent_id']) ?
                $category['parent_id'] .'_'. $category['category_id'] : $category['category_id'];

            $href = $Url::createUrl('store/category', array('path'=>$path));
            $image = $Image::resizeAndSave($category['cimage'], $thumb_width, $thumb_height);

            $tpl .= '<li'. ($category['children'] ? ' class="hasCategories"' : '') .'>';

            $tpl .= '<figure class="picture">';
            $tpl .= '<a class="thumb" href="'. $href .'" title="'. $category['title'] .'">';
            $tpl .= '<img src="'. $image .'" alt="'. $category['title'].'" />';
            $tpl .= '</a>';
            $tpl .= '</figure>';

            $tpl .= '<div class="info">';
            $tpl .= '<a class="name" href="'. $href .'" title="'. $category['title'] .'">'. $category['title'] .'</a>';

            if (isset($category['meta_description'])) {
                $tpl .= '<p class="overview">'. $category['meta_description'] .'</p>';
            }

            $tpl .= '</div>';

            if (isset($category['children'])) {
                $tpl .= __drawCategories($category['children'], $thumb_width, $thumb_height, $Url, $Image);
            }
            $tpl .= '</li>';
        }
        $tpl .= '</ul>';
        return $tpl;
    }
}
}
?>

<ul class="catalog-grid">
    <?php echo __drawCategories($categories, $settings['thumb_width'], $settings['thumb_height'], $Url, $Image); ?>
</ul>