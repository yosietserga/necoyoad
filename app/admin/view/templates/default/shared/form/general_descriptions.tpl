<?php if (isset($languages) && is_array($languages) && !empty($languages)) { ?>
<div id="languages" class="htabs2">
    <?php foreach ($languages as $language) { ?>
    <a tab="#language<?php echo $language['language_id']; ?>" class="htab2"><img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
    <?php } ?>
    <?php foreach ($languages as $language) { ?>
    <div id="language<?php echo $language['language_id']; ?>">
        
        <?php if (isset($this->form_vars['descriptions']['fields']['title'])) { ?>
        <div class="row">
            <label><?php echo $l('entry_title'); ?></label>
            <input class="page" id="description_<?php echo $language['language_id']; ?>_title" name="descriptions[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($descriptions[$language['language_id']]) ? $descriptions[$language['language_id']]['title'] : ''; ?>" required="true" style="width:40%" />
        </div>
        <div class="clear"></div>
        <input type="hidden" id="description_<?php echo $language['language_id']; ?>_keyword" name="descriptions[<?php echo $language['language_id']; ?>][keyword]" value="<?php echo $descriptions[$language[ 'language_id']][ 'keyword'] ?? ''; ?>" />
        <?php } //end if ?>

        <?php if (isset($this->form_vars['descriptions']['fields']['meta_description'])) { ?>
        <div class="row">
            <label><?php echo $l('entry_meta_description'); ?></label>
            <textarea title="<?php echo $l('help_meta_description'); ?>" name="descriptions[<?php echo $language['language_id']; ?>][meta_description]" cols="40" rows="5" style="width:40%"><?php echo isset($descriptions[$language[ 'language_id']]) ? $descriptions[$language[ 'language_id']][ 'meta_description'] : ''; ?></textarea>
        </div>
        <div class="clear"></div>
        <?php } //end if ?>

        <?php if (isset($this->form_vars['descriptions']['fields']['description'])) { ?>
        <div class="row">
            <label><?php echo $l('entry_description'); ?></label>
            <div class="clear"></div>
            <textarea title="<?php echo $l('help_description'); ?>" name="descriptions[<?php echo $language['language_id']; ?>][description]" id="description<?php echo $language['language_id']; ?>"><?php echo isset($descriptions[$language[ 'language_id']]) ? $descriptions[$language[ 'language_id']][ 'description'] : ''; ?></textarea>
        </div>
        <div class="clear"></div>
        <?php } //end if ?>
    </div>
    <?php } //end foreach ?>
</div>
<?php } //end if ?>
