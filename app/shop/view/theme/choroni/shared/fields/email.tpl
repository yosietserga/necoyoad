<div class="email-entry form-entry">
    <label><?php echo $l('entry_email'); ?></label>
    <input data-label="<?php echo $l('entry_email'); ?>" type="email" name="email" id="email" title="Ingrese su email, &eacute;ste ser&aacute; verificado contra su servidor para validarlo" required="required" placeholder="Ingrese su email. E.j: miemail@xxx.com" value="<?php echo isset($email) && $email ? $email : ""; ?>" />
    <?php if (isset($error["email"]) && $error["email"]) { ?><span class="error" id="error_email"><?php echo $error["email"]; ?></span><?php } ?>
</div>