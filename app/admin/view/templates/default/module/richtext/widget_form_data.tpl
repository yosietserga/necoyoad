<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label for="widget_richtext<?php echo $name; ?>_content_type"><?php echo $l('Content Type'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][content_type]" id="widget_richtext<?php echo $name; ?>_content_type" showquick="off" onchange="if (this.value==='') { $('#widget_richtext_<?php echo $name; ?>_post_id').hide(); $('#widget_richtext_<?php echo $name; ?>_html_content').hide(); }if (this.value==='post_id') { $('#widget_richtext_<?php echo $name; ?>_post_id').show(); $('#widget_richtext_<?php echo $name; ?>_html_content').hide(); } if (this.value==='html_content') { $('#widget_richtext_<?php echo $name; ?>_post_id').hide(); $('#widget_richtext_<?php echo $name; ?>_html_content').show(); }">
        <option value=""><?php echo $l('Select One'); ?></option>
        <option value="html_content"<?php if (isset($settings['content_type']) && 'html_content'===$settings['content_type']) { ?> selected="selected"<?php } ?>><?php echo $l('HTML Content'); ?></option>
        <option value="post_id"<?php if (isset($settings['content_type']) && 'post_id'===$settings['content_type']) { ?> selected="selected"<?php } ?>><?php echo $l('A Page'); ?></option>
    </select>
</div>

<div class="row" id="widget_richtext_<?php echo $name; ?>_post_id"<?php if (isset($settings['content_type']) && 'post_id'!==$settings['content_type']) echo ' style="display:none;"'; ?>>
    <label><?php echo $l('Select A Page'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][post_id]" id="widget_richtext<?php echo $name; ?>" showquick="off">
        <option value=""><?php echo $l('text_select'); ?></option>
        <?php foreach ($pages as $result) { ?>
        <option value="<?php echo $result['post_id']; ?>"<?php if (isset($settings['post_id']) && $result['post_id']==$settings['post_id']) { ?> selected="selected"<?php } ?>><?php echo $result['title']; ?></option>
        <?php } ?>
    </select>
</div>

<div class="row" id="widget_richtext_<?php echo $name; ?>_html_content"<?php if (isset($settings['content_type']) && 'html_content'!==$settings['content_type']) echo ' style="display:none;"'; ?>>
    <label><?php echo $l('Entry HTML Content'); ?></label>

    <div class="clear"></div>

    <div id="languages_<?php echo $name; ?>" class="htabs2">
        <?php foreach ($languages as $language) { ?>
        <?php $i = $language["language_id"] .'_'. $name; ?>
        <a tab="#language_<?php echo $i; ?>" class="htab2" onclick="loadCKEditor( 'description<?php echo $i; ?>' )">
            <img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
            <?php echo $language['name']; ?>
        </a>
        <?php } ?>
    </div>

    <div class="clear"></div>

    <?php foreach ($languages as $language) {
        $i = $language["language_id"] .'_'. $name; ?>
    <div id="language_<?php echo $i; ?>" style="width:90%;">
        <textarea name="Widgets[<?php echo $name; ?>][settings][descriptions][<?php echo $language['language_id']; ?>][description]" id="description<?php echo $i; ?>"><?php echo $settings['descriptions'][$language['language_id']]['description']??""; ?></textarea>
    </div>
    <?php } ?>
    <script type="text/javascript">
        if (typeof loadCKEditor != 'function') {
            function loadCKEditor( textarea ) {
                let editor;

                let options = {
                    filebrowserBrowseUrl:      '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
                    filebrowserImageBrowseUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
                    filebrowserFlashBrowseUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
                    filebrowserUploadUrl:      '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
                    filebrowserImageUploadUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
                    filebrowserFlashUploadUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
                    height:200
                };

                if ($('#'+ textarea).length > 0) {
                    if (!CKEDITOR.instances[ textarea ]) {
                        editor = CKEDITOR.replace( textarea, options );

                        CKEDITOR.instances[ textarea ].setData( $( '#'+ textarea ).val() );

                        CKEDITOR.instances[ textarea ].on('change', function() { 
                            $( '#'+ textarea ).val( CKEDITOR.instances[textarea].getData() );
                            $( '#'+ textarea ).trigger( 'change' );
                            if (typeof saveWidget == 'function') saveWidget('<?php echo $name; ?>', '<?php echo $module; ?>');
                        });

                    }
                } else if (parent.$('#'+ textarea).length > 0) {
                    if (!parent.CKEDITOR.instances[ textarea ]) {
                        editor = parent.CKEDITOR.replace( textarea, options );

                        parent.CKEDITOR.instances[ textarea ].setData( $( '#'+ textarea ).val() );

                        parent.CKEDITOR.instances[ textarea ].on('change', function() { 
                            $( '#'+ textarea ).val( parent.CKEDITOR.instances[textarea].getData() );
                            $( '#'+ textarea ).trigger( 'change' );
                            if (typeof parent.saveWidget == 'function') parent.saveWidget('<?php echo $name; ?>', '<?php echo $module; ?>');
                        });

                    }
                }

                if (editor) {
                    editor.config.allowedContent = true;

                    <?php
                    $cssrules = 
                        "assets/theme/". 
                        ( $this->config->get('config_template') ? $this->config->get('config_template') : 'choroni' ) 
                        ."/css/theme.css";

                    if (file_exists(DIR_ROOT . $cssrules)) {
                        echo "editor.config.contentsCss = '". HTTP_CATALOG . $cssrules ."';";
                    }
                    ?>
                }
            }
        }

        if (typeof initEditor != 'function') {
            function initEditor() {
                if ($('#languages_<?php echo $name; ?> .htab2').length > 0) {
                    $('#languages_<?php echo $name; ?> .htab2').on('click', function () {

                        $(this).closest('.htabs2').find('.htab2').each(function () {
                            $($(this).attr('tab')).hide();
                            $(this).removeClass('selected');
                        });

                        $(this).addClass('selected');

                        $($(this).attr('tab')).show(function() {                        
                            Object.keys(CKEDITOR.instances).map(k => { 
                                let editor = CKEDITOR.instances[k]; 
                                if (editor) editor.destroy( true ); 
                            });
                        });
                    });
                } else if (parent.$('#languages_<?php echo $name; ?> .htab2').length > 0) {

                    parent.$('#languages_<?php echo $name; ?> .htab2').on('click', function () {

                        $(this).closest('.htabs2').find('.htab2').each(function () {
                            parent.$( $(this).attr('tab') ).hide();
                            $(this).removeClass('selected');
                        });

                        $(this).addClass('selected');

                        parent.$( $(this).attr('tab') ).show(function() {                        
                            Object.keys(parent.CKEDITOR.instances).map(k => { 
                                let editor = parent.CKEDITOR.instances[k]; 
                                if (editor) editor.destroy( true ); 
                            });
                        });
                    });
                }
            }
        }

    	$(function() {
		    initEditor();
    	});
    </script>

</div>