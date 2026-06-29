<div>
    <h2>Contenidos</h2>
    
    <div class="clear"></div>
    <div style="float:right;">
        <a class="button" onclick="submitContent();"><?php echo $l('button_save'); ?></a>
    </div>
    <div class="clear"></div>
    
    <ul id="vtabs" class="vtabs">
        <li><a onclick="loadWrapper('Products');showTab(this)" id="pProducts" data-target="#tab_products"><?php echo $l('tab_products'); ?></a></li>
        <li><a onclick="loadWrapper('Categories');showTab(this)" id="pCategories" data-target="#tab_categories"><?php echo $l('tab_categories'); ?></a></li>
        <li><a onclick="loadWrapper('Manufacturers');showTab(this)" id="pManufacturers" data-target="#tab_manufacturers"><?php echo $l('tab_manufacturers'); ?></a></li>
        <li><a onclick="loadWrapper('Pages');showTab(this)" id="pPages" data-target="#tab_pages"><?php echo $l('tab_pages'); ?></a></li>
        <li><a onclick="loadWrapper('Posts');showTab(this)" id="pPosts" data-target="#tab_posts"><?php echo $l('tab_posts'); ?></a></li>
        <li><a onclick="loadWrapper('PostCategories');showTab(this)" id="pPostCategories" data-target="#tab_postcategories"><?php echo $l('tab_post_categories'); ?></a></li>
        <li><a onclick="loadWrapper('Banners');showTab(this)" id="pBanners" data-target="#tab_banners"><?php echo $l('tab_banners'); ?></a></li>
        <li><a onclick="loadWrapper('Menus');showTab(this)" id="pMenus" data-target="#tab_menus"><?php echo $l('tab_menus'); ?></a></li>
        <li><a onclick="loadWrapper('Downloads');showTab(this)" id="pDownloads" data-target="#tab_downloads"><?php echo $l('tab_downloads'); ?></a></li>
        <li><a onclick="loadWrapper('Coupons');showTab(this)" id="pCoupons" data-target="#tab_coupons"><?php echo $l('tab_coupons'); ?></a></li>
        <li><a onclick="loadWrapper('BankAccounts');showTab(this)" id="pBankAccounts" data-target="#tab_bank_accounts"><?php echo $l('tab_bank_accounts'); ?></a></li>
        <li><a onclick="loadWrapper('Customers');showTab(this)" id="pCustomers" data-target="#tab_customers"><?php echo $l('tab_customers'); ?></a></li>
    </ul>
    
    <div>
    
        <div id="tab_products" class="vtabs_page">
            <h2><?php echo $l('tab_products'); ?></h2>
            <div id="addsProductsWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_categories" class="vtabs_page">
            <h2><?php echo $l('tab_categories'); ?></h2>
            <div id="addsCategoriesWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_manufacturers" class="vtabs_page">
            <h2><?php echo $l('tab_manufacturers'); ?></h2>
            <div id="addsManufacturersWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_pages" class="vtabs_page">
            <h2><?php echo $l('tab_pages'); ?></h2>
            <div id="addsPagesWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_posts" class="vtabs_page">
            <h2><?php echo $l('tab_posts'); ?></h2>
            <div id="addsPostsWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_postcategories" class="vtabs_page">
            <h2><?php echo $l('tab_postcategories'); ?></h2>
            <div id="addsPostCategoriesWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_banners" class="vtabs_page">
            <h2><?php echo $l('tab_banners'); ?></h2>
            <div id="addsBannersWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_menus" class="vtabs_page">
            <h2><?php echo $l('tab_menus'); ?></h2>
            <div id="addsMenusWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_downloads" class="vtabs_page">
            <h2><?php echo $l('tab_downloads'); ?></h2>
            <div id="addsDownloadsWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_coupons" class="vtabs_page">
            <h2><?php echo $l('tab_coupons'); ?></h2>
            <div id="addsCouponsWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_bankaccounts" class="vtabs_page">
            <h2><?php echo $l('tab_bankaccounts'); ?></h2>
            <div id="addsBankAccountsWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
        <div id="tab_customers" class="vtabs_page">
            <h2><?php echo $l('tab_customers'); ?></h2>
            <div id="addsCustomersWrapper" class="contentPanelWrapper"></div>
            <div class="clear"></div><br />
        </div>
        
    </div>
</div>
<script>
function submitContent() {
    $.post('<?php echo $Url::createAdminUrl("store/store/savecontent"); ?>&store_id=<?php echo $_GET['store_id']; ?>',
    {
        'Products':$('#formProducts').serialize(),
        'Categories':$('#formCategories').serialize(),
        'Manufacturers':$('#formManufacturers').serialize(),
        'Pages':$('#formPages').serialize(),
        'Posts':$('#formPosts').serialize(),
        'PostCategories':$('#formPostCategories').serialize(),
        'Banners':$('#formBanners').serialize(),
        'Menus':$('#formMenus').serialize(),
        'Downloads':$('#formDownloads').serialize(),
        'Coupons':$('#formCoupons').serialize(),
        'BankAccounts':$('#formBankAccounts').serialize(),
        'Customers':$('#formCustomers').serialize()
    });
}
function showTab(a) {
    $('.vtabs_page').hide();
    $($(a).attr('data-target')).show();
}
function loadWrapper(o) {
    var wrapper = $('#adds'+ o +'Wrapper').html();
    if (wrapper.length == 0) {
        $('#adds'+ o +'Wrapper').html('<img src="<?php echo str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE); ?>loader.gif" alt="Cargando..." />');
        $.getJSON('index.php?r=store/store/'+ o.toLowerCase(),
        {
            'token':'<?php echo $_GET['token'] ?>',
            'store_id':'<?php echo $_GET['store_id'] ?>'
        },
        function(data) {
            
            if (data) {
                $('#adds'+ o +'Wrapper').html('<div class=\"row\"><label for=\"q\" style=\"float:left\">Filtrar listado:</label><input type=\"text\" value=\"\" name=\"q'+ o +'\" id=\"q'+ o +'\" placeholder=\"Filtrar\" /></div><div class=\"clear\"></div><br /><form id="form'+ o +'"><ul id=\"adds'+ o +'\"></ul></form>');
                $.each(data, function(i,item){
                    var html = '<li>';
                    if (typeof item.image != 'undefined') {
                        html += '<img src=\"' + item.image + '\" alt=\"' + item.name + '\" />';
                    }
                    html += '<b class=\"' + item.class + '\">' + item.name + '</b>';
                    html += '<input type=\"checkbox\" name=\"'+ o +'[]\" value=\"' + item.id + '\" style=\"display:none\" />';
                    html += '</li>';
                    $('#adds'+ o).html(html);
                });
            }
            
            $('#q'+ o).on('change',function(e){
                var that = this;
                var valor = $(that).val().toLowerCase();
                if (valor.length <= 0) {
                    $('#adds'+ o +' li').show();
                } else {
                    $('#adds'+ o +' li b').each(function(){
                        if ($(this).text().toLowerCase().indexOf( valor ) > 0) {
                            $(this).closest('li').show();
                        } else {
                            $(this).closest('li').hide();
                        }
                    });
                }
            });
            
            $('#adds'+ o +' li').on('click',function() {
                var b = $(this).find('b');
                if (b.hasClass('added')) {
                    b.removeClass('added').addClass('add');
                    $(this).find('input[type=checkbox]').removeAttr('checked');
                } else {
                    b.removeClass('add').addClass('added');
                    $(this).find('input[type=checkbox]').attr('checked','checked');
                }
            });
        });
    }
}
</script>