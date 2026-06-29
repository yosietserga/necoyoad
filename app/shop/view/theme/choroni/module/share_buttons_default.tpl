<div class="share_buttons">
    <ul class="rrssb-buttons clearfix">
        <li class="rrssb-facebook">
            <a onclick="popupWindow('https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>', 'Facebook', 600 , 480); return false;" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>">
                <span class="rrssb-text">facebook</span>
            </a>
        </li>
        <li class="rrssb-twitter">
            <a href="https://twitter.com/home?status=<?php echo urlencode($url); ?>" class="popup">
                <span class="rrssb-text">twitter</span>
            </a>
        </li>
        <li class="rrssb-googleplus">
            <a href="https://plus.google.com/share?url=<?php echo urlencode($url); ?>" class="popup">
                <span class="rrssb-text">google+</span>
            </a>
        </li>
    </ul>
</div>