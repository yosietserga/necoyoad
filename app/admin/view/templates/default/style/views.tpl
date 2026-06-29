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
        <h1><?php echo $l('heading_title'); ?></h1>
        <div class="buttons">
            <a onclick="$('#form').submit();" class="button"><?php echo $l('button_save'); ?></a>
        </div>

        <div class="clear"></div>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <h2><?php echo $l('text_general'); ?></h2>
            <table class="form">
                <tr>
                    <td><?php echo $l('entry_default_view_home'); ?></td>
                    <td>
                        <select name="default_view_home">
                            <option value=""<?php if (empty($default_view_home)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_home==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_maintenance'); ?></td>
                    <td>
                        <select name="default_view_maintenance">
                            <option value=""<?php if (empty($default_view_maintenance)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_maintenance==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_contact'); ?></td>
                    <td>
                        <select name="default_view_contact">
                            <option value=""<?php if (empty($default_view_contact)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_contact==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_sitemap'); ?></td>
                    <td>
                        <select name="default_view_sitemap">
                            <option value=""<?php if (empty($default_view_sitemap)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_sitemap==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_search'); ?></td>
                    <td>
                        <select name="default_view_search">
                            <option value=""<?php if (empty($default_view_search)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_search==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_not_found'); ?></td>
                    <td>
                        <select name="default_view_not_found">
                            <option value=""<?php if (empty($default_view_not_found)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_not_found==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>

            <h2><?php echo $l('text_content'); ?></h2>
            <table class="form">
                <tr>
                    <td><?php echo $l('entry_default_view_page'); ?></td>
                    <td>
                        <select name="default_view_page">
                            <option value=""<?php if (empty($default_view_page)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_page==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_page_all'); ?></td>
                    <td>
                        <select name="default_view_page_all">
                            <option value=""<?php if (empty($default_view_page_all)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_page_all==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_page_error'); ?></td>
                    <td>
                        <select name="default_view_page_error">
                            <option value=""<?php if (empty($default_view_page_error)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_page_error==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_page_review'); ?></td>
                    <td>
                        <select name="default_view_page_review">
                            <option value=""<?php if (empty($default_view_page_review)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_page_review==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_page_comment'); ?></td>
                    <td>
                        <select name="default_view_page_comment">
                            <option value=""<?php if (empty($default_view_page_comment)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_page_comment==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_post'); ?></td>
                    <td>
                        <select name="default_view_post">
                            <option value=""<?php if (empty($default_view_post)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_post==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_post_all'); ?></td>
                    <td>
                        <select name="default_view_post_all">
                            <option value=""<?php if (empty($default_view_post_all)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_post_all==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_post_error'); ?></td>
                    <td>
                        <select name="default_view_post_error">
                            <option value=""<?php if (empty($default_view_post_error)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_post_error==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_post_review'); ?></td>
                    <td>
                        <select name="default_view_post_review">
                            <option value=""<?php if (empty($default_view_post_review)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_post_review==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_post_comment'); ?></td>
                    <td>
                        <select name="default_view_post_comment">
                            <option value=""<?php if (empty($default_view_post_comment)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_post_comment==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_post_category'); ?></td>
                    <td>
                        <select name="default_view_post_category">
                            <option value=""<?php if (empty($default_view_post_category)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_post_category==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>

            <h2><?php echo $l('text_catalog'); ?></h2>
            <table class="form">
                <tr>
                    <td><?php echo $l('entry_default_view_special'); ?></td>
                    <td>
                        <select name="default_view_special">
                            <option value=""<?php if (empty($default_view_special)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_special==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_special_home'); ?></td>
                    <td>
                        <select name="default_view_special_home">
                            <option value=""<?php if (empty($default_view_special_home)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_special_home==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_special_error'); ?></td>
                    <td>
                        <select name="default_view_special_error">
                            <option value=""<?php if (empty($default_view_special_error)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_special_error==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product_category'); ?></td>
                    <td>
                        <select name="default_view_product_category">
                            <option value=""<?php if (empty($default_view_product_category)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product_category==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product_category_all'); ?></td>
                    <td>
                        <select name="default_view_product_category_all">
                            <option value=""<?php if (empty($default_view_product_category_all)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product_category_all==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product_category_home'); ?></td>
                    <td>
                        <select name="default_view_product_category_home">
                            <option value=""<?php if (empty($default_view_product_category_home)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product_category_home==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product_category_error'); ?></td>
                    <td>
                        <select name="default_view_product_category_error">
                            <option value=""<?php if (empty($default_view_product_category_error)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product_category_error==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product'); ?></td>
                    <td>
                        <select name="default_view_product">
                            <option value=""<?php if (empty($default_view_product)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product_all'); ?></td>
                    <td>
                        <select name="default_view_product_all">
                            <option value=""<?php if (empty($default_view_product_all)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product_all==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product_error'); ?></td>
                    <td>
                        <select name="default_view_product_error">
                            <option value=""<?php if (empty($default_view_product_error)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product_error==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product_review'); ?></td>
                    <td>
                        <select name="default_view_product_review">
                            <option value=""<?php if (empty($default_view_product_review)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product_review==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product_comment'); ?></td>
                    <td>
                        <select name="default_view_product_comment">
                            <option value=""<?php if (empty($default_view_product_comment)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product_comment==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_product_related'); ?></td>
                    <td>
                        <select name="default_view_product_related">
                            <option value=""<?php if (empty($default_view_product_related)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_product_related==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_manufacturer'); ?></td>
                    <td>
                        <select name="default_view_manufacturer">
                            <option value=""<?php if (empty($default_view_manufacturer)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_manufacturer==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_manufacturer_all'); ?></td>
                    <td>
                        <select name="default_view_manufacturer_all">
                            <option value=""<?php if (empty($default_view_manufacturer_all)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_manufacturer_all==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_manufacturer_home'); ?></td>
                    <td>
                        <select name="default_view_manufacturer_home">
                            <option value=""<?php if (empty($default_view_manufacturer_home)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_manufacturer_home==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_manufacturer_error'); ?></td>
                    <td>
                        <select name="default_view_manufacturer_error">
                            <option value=""<?php if (empty($default_view_manufacturer_error)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_manufacturer_error==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>

            <h2><?php echo $l('text_account'); ?></h2>
            <table class="form">
                <tr>
                    <td><?php echo $l('entry_default_view_account_login'); ?></td>
                    <td>
                        <select name="default_view_account_login">
                            <option value=""<?php if (empty($default_view_account_login)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_login==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_logout'); ?></td>
                    <td>
                        <select name="default_view_account_logout">
                            <option value=""<?php if (empty($default_view_account_logout)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_logout==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_message'); ?></td>
                    <td>
                        <select name="default_view_account_message">
                            <option value=""<?php if (empty($default_view_account_message)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_message==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_message_sent'); ?></td>
                    <td>
                        <select name="default_view_account_message_sent">
                            <option value=""<?php if (empty($default_view_account_message_sent)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_message_sent==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_message_create'); ?></td>
                    <td>
                        <select name="default_view_account_message_create">
                            <option value=""<?php if (empty($default_view_account_message_create)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_message_create==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_message_read'); ?></td>
                    <td>
                        <select name="default_view_account_message_read">
                            <option value=""<?php if (empty($default_view_account_message_read)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_message_read==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_addresses'); ?></td>
                    <td>
                        <select name="default_view_account_addresses">
                            <option value=""<?php if (empty($default_view_account_addresses)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_addresses==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_address'); ?></td>
                    <td>
                        <select name="default_view_account_address">
                            <option value=""<?php if (empty($default_view_account_address)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_address==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_balance'); ?></td>
                    <td>
                        <select name="default_view_account_balance">
                            <option value=""<?php if (empty($default_view_account_balance)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_balance==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_balance_receipt'); ?></td>
                    <td>
                        <select name="default_view_account_balance_receipt">
                            <option value=""<?php if (empty($default_view_account_balance_receipt)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_balance_receipt==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_order_balance'); ?></td>
                    <td>
                        <select name="default_view_account_order_balance">
                            <option value=""<?php if (empty($default_view_account_order_balance)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_order_balance==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_download'); ?></td>
                    <td>
                        <select name="default_view_account_download">
                            <option value=""<?php if (empty($default_view_account_download)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_download==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_edit'); ?></td>
                    <td>
                        <select name="default_view_account_edit">
                            <option value=""<?php if (empty($default_view_account_edit)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_edit==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_forgotten'); ?></td>
                    <td>
                        <select name="default_view_account_forgotten">
                            <option value=""<?php if (empty($default_view_account_forgotten)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_forgotten==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <!--
                <tr>
                    <td><?php echo $l('entry_default_view_account_history'); ?></td>
                    <td>
                        <select name="default_view_account_history">
                            <option value=""<?php if (empty($default_view_account_history)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_history==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_history_error'); ?></td>
                    <td>
                        <select name="default_view_account_history_error">
                            <option value=""<?php if (empty($default_view_account_history_error)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_history_error==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <td><?php echo $l('entry_default_view_account_newsletter'); ?></td>
                    <td>
                        <select name="default_view_account_newsletter">
                            <option value=""<?php if (empty($default_view_account_newsletter)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_newsletter==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>default_view_account_review_read_error
                -->
                <tr>
                    <td><?php echo $l('entry_default_view_account_order'); ?></td>
                    <td>
                        <select name="default_view_account_order">
                            <option value=""<?php if (empty($default_view_account_order)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_order==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_password'); ?></td>
                    <td>
                        <select name="default_view_account_password">
                            <option value=""<?php if (empty($default_view_account_password)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_password==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_payment'); ?></td>
                    <td>
                        <select name="default_view_account_payment">
                            <option value=""<?php if (empty($default_view_account_payment)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_payment==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_payment_receipt'); ?></td>
                    <td>
                        <select name="default_view_account_payment_receipt">
                            <option value=""<?php if (empty($default_view_account_payment_receipt)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_payment_receipt==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_order_payment'); ?></td>
                    <td>
                        <select name="default_view_account_order_payment">
                            <option value=""<?php if (empty($default_view_account_order_payment)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_order_payment==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_register'); ?></td>
                    <td>
                        <select name="default_view_account_register">
                            <option value=""<?php if (empty($default_view_account_register)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_register==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_review'); ?></td>
                    <td>
                        <select name="default_view_account_review">
                            <option value=""<?php if (empty($default_view_account_review)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_review==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_review_read'); ?></td>
                    <td>
                        <select name="default_view_account_review_read">
                            <option value=""<?php if (empty($default_view_account_review_read)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_review_read==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <!--
                <tr>
                    <td><?php echo $l('entry_default_view_account_review_read_error'); ?></td>
                    <td>
                        <select name="default_view_account_review_read_error">
                            <option value=""<?php if (empty($default_view_account_review_read_error)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_review_read_error==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                -->
                <tr>
                    <td><?php echo $l('entry_default_view_account_success'); ?></td>
                    <td>
                        <select name="default_view_account_success">
                            <option value=""<?php if (empty($default_view_account_success)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_success==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?php echo $l('entry_default_view_account_account'); ?></td>
                    <td>
                        <select name="default_view_account_account">
                            <option value=""<?php if (empty($default_view_account_account)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                            <?php foreach ($views as $key => $value) { ?>
                            <optgroup label="<?php echo $value['folder']; ?>">
                                <?php foreach ($value['files'] as $k => $v) { ?>
                                <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($default_view_account_account==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>
<?php echo $footer; ?>