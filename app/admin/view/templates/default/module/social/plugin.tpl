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
    
    <div class="grid_12">
        <div class="box">
            <h1><?php echo $l('heading_title'); ?></h1>

            <div class="buttons">
                <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
                <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
                <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
            </div>

            <div class="clear"></div><br />

            <ul id="vtabs" class="vtabs">
                <li><a data-target="#tab_facebook" onclick="showTab(this)">Facebook</a></li>
                <li><a data-target="#tab_twitter" onclick="showTab(this)">Twitter</a></li>
                <li><a data-target="#tab_google" onclick="showTab(this)">Google</a></li>
                <li><a data-target="#tab_live" onclick="showTab(this)">Live/Hotmail</a></li>
                <li><a data-target="#tab_meli" onclick="showTab(this)">MercadoLibre</a></li>
                <li><a data-target="#tab_paypal" onclick="showTab(this)">PayPal</a></li>
            </ul>

            <div id="tabs">

            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <div id="tab_facebook" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>Facebook</h1></hgroup>
                            </div>
                                <small>Callback Url or Redirect Url: <?php echo HTTP_CATALOG; ?>api/facebook</small><br />
                                <small>Crear Facebook App Url: <a href="https://developers.facebook.com/apps">https://developers.facebook.com/apps</a></small>
                            <div class="clear"></div><br />

                            <div class="row">
                                <label>Facebook App ID</label>
                                <input name="social_facebook_app_id" value="<?php echo isset($social_facebook_app_id) ? $social_facebook_app_id : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>Facebook App Secret</label>
                                <input name="social_facebook_app_secret" value="<?php echo isset($social_facebook_app_secret) ? $social_facebook_app_secret : ''; ?>" style="width:40%" />
                            </div>

                        </div>
                    </div>
                </div>

                <div id="tab_twitter" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>Twitter</h1></hgroup>
                                <small>Callback Url or Redirect Url: <?php echo HTTP_CATALOG; ?>api/twitter</small><br />
                                <small>Crear Twitter App Url: <a href="https://dev.twitter.com/console">https://dev.twitter.com/console</a></small>
                            </div>
                            <div class="clear"></div><br />

                            <div class="row">
                                <label>Twitter Consumer Key</label>
                                <input name="social_twitter_consumer_key" value="<?php echo isset($social_twitter_consumer_key) ? $social_twitter_consumer_key : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>Twitter Consumer Secret</label>
                                <input name="social_twitter_consumer_secret" value="<?php echo isset($social_twitter_consumer_secret) ? $social_twitter_consumer_secret : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>Twitter Access Token</label>
                                <input name="social_twitter_oauth_token" value="<?php echo isset($social_twitter_oauth_token) ? $social_twitter_oauth_token : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>Twitter Access Token Secret</label>
                                <input name="social_twitter_oauth_token_secret" value="<?php echo isset($social_twitter_oauth_token_secret) ? $social_twitter_oauth_token_secret : ''; ?>" style="width:40%" />
                            </div>

                        </div>
                    </div>
                </div>

                <div id="tab_google" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>Google</h1></hgroup>
                                <small>Callback Url or Redirect Url: <?php echo HTTP_CATALOG; ?>api/google</small><br />
                                <small>Crear Google App Url: <a href="https://code.google.com/apis/console/">https://code.google.com/apis/console</a></small>
                            </div>
                            <div class="clear"></div><br />

                            <div class="row">
                                <label>Google Client ID</label>
                                <input name="social_google_client_id" value="<?php echo isset($social_google_client_id) ? $social_google_client_id : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>Google Client Secret</label>
                                <input name="social_google_client_secret" value="<?php echo isset($social_google_client_secret) ? $social_google_client_secret : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>Google API Key</label>
                                <input name="social_google_api_key" value="<?php echo isset($social_google_api_key) ? $social_google_api_key : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>Google Consumer Key</label>
                                <input name="social_google_consumer_key" value="<?php echo isset($social_google_consumer_key) ? $social_google_consumer_key : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>Google Consumer Secret</label>
                                <input name="social_google_consumer_secret" value="<?php echo isset($social_google_consumer_secret) ? $social_google_consumer_secret : ''; ?>" style="width:40%" />
                            </div>

                        </div>
                    </div>
                </div>

                <div id="tab_live" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>Live / Hotmail</h1></hgroup>
                            </div>
                                <small>Callback Url or Redirect Url: <?php echo HTTP_CATALOG; ?>api/live</small><br />
                                <small>Crear Live App Url: <a href="https://account.live.com/developers/applications/create">https://account.live.com/developers/applications/create</a></small>
                            <div class="clear"></div><br />

                            <div class="row">
                                <label>Live Client ID</label>
                                <input name="social_live_client_id" value="<?php echo isset($social_live_client_id) ? $social_live_client_id : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>Live Client Secret</label>
                                <input name="social_live_client_secret" value="<?php echo isset($social_live_client_secret) ? $social_live_client_secret : ''; ?>" style="width:40%" />
                            </div>

                        </div>
                    </div>
                </div>

                <div id="tab_meli" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>MercadoLibre</h1></hgroup>
                            </div>
                                <small>Callback Url or Redirect Url: <?php echo HTTP_CATALOG; ?>api/meli</small><br />
                                <small>Crear MercadoLibre App Url: <a href="https://applications.mercadolibre.com.ve/create">https://applications.mercadolibre.com.ve/create</a></small>
                            <div class="clear"></div><br />

                            <div class="row">
                                <label>MercadoLibre App ID</label>
                                <input name="social_meli_app_id" value="<?php echo isset($social_meli_app_id) ? $social_meli_app_id : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>MercadoLibre App Secret</label>
                                <input name="social_meli_app_secret" value="<?php echo isset($social_meli_app_secret) ? $social_meli_app_secret : ''; ?>" style="width:40%" />
                            </div>

                        </div>
                    </div>
                </div>

                <div id="tab_paypal" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>PayPal</h1></hgroup>
                            </div>
                                <small>Callback Url or Redirect Url: <?php echo HTTP_CATALOG; ?>api/paypal</small><br />
                                <small>Crear PayPal App Url: <a href="https://developer.paypal.com/webapps/developer/applications/myapps">https://developer.paypal.com/webapps/developer/applications/myapps</a></small>
                            <div class="clear"></div><br />

                            <div class="row">
                                <label>PayPal App ID</label>
                                <input name="social_paypal_app_id" value="<?php echo isset($social_paypal_app_id) ? $social_paypal_app_id : ''; ?>" style="width:40%" />
                            </div>

                            <div class="row">
                                <label>PayPal App Secret</label>
                                <input name="social_paypal_app_secret" value="<?php echo isset($social_paypal_app_secret) ? $social_paypal_app_secret : ''; ?>" style="width:40%" />
                            </div>

                        </div>
                    </div>
                </div>

            </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>