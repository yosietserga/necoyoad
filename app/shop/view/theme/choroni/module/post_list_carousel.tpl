<ul>
        <?php foreach($posts as $post) { ?>
        <li class="catalog-item">
            <!-- post-picture -->
            <figure class="picture">
                <a href="<?php echo $Url::createUrl('content/post', array('post_id'=>$post['post_id'])); ?>" class="thumb" title="<?php echo $post['name']; ?>">
                    <img src="<?php echo $post['thumb']; ?>" alt="<?php echo $post['name']; ?>"/>
                </a>
            </figure>
            <!--/post-picture-->
            <!-- post-info -->
            <div class="info nt-hoverdir">
                <?php if ($post['rating']) { ?>
                <div class="rating">
                    <img src="<?php echo HTTP_IMAGE; ?>stars_<?php echo $post['rating'] . '.png'; ?>" alt="<?php echo $post['stars']; ?>" />
                </div>
                <?php }else { ?>
                <div class="rating" style="min-height: 1.063em; width: 100%;"></div>
                <?php }?>

                <a href="<?php echo $Url::createUrl('content/post',array('post_id'=>$post['post_id'])); ?>" title="<?php echo $post['name']; ?>" class="name">
                    <?php echo $post['name']; ?>
                </a>
                <!--
                    <div class="post-date" title="<?php echo $l('text_created'); ?>">
                        <span><?php echo $l('text_created'); ?></span>
                        <small><?php echo $post['date_added']; ?></small>
                    </div>
                -->
                <p class="overview"><?php echo substr($post['overview'],0,100)."... "; ?>&nbsp;<a href="<?php echo $Url::createUrl('content/post',array('post_id'=>$post['post_id'])); ?>" title="<?php echo $post['name']; ?>">Más detalles</a></p>

                <div class="description description"><?php echo $post['description']; ?></div>

                <div class="group group--btn" role="group">
                    <div class="btn btn-detail" data-action="see-post">
                        <a title="<?php echo $button_see_post; ?>" href="<?php echo $Url::createUrl('content/post',array('post_id'=>$post['post_id'])); ?>"><?php echo $l('Read More'); ?></a>
                    </div>
                </div>

                <ul class="glossary-item-footer">
                    <li class="post-visits"><?php echo (int)$post['visits']; ?> <?php echo $l('Visits'); ?></li>
                    <li class="post-follow"><?php echo (int)$post['followers']; ?> <?php echo $l('Followers'); ?></li>
                    <li class="post-likes"><?php echo (int)$post['likes']; ?> <?php echo $l('Likes'); ?></li>
                    <li class="post-dislikes"><?php echo (int)$post['dislikes']; ?> <?php echo $l('Dislikes'); ?></li>
                </ul>
            </div>
            <!-- /post-info -->
        </li>
        <?php } ?>
    </ul>