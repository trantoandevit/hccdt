<?php
defined('DS') or die();
?>
<div class='widget widget-subscribe <?php echo $widget_style ?>' data-code='subscribe'>
    <div class='widget-header'>
        <h6><?php echo __('subscribe') ?></h6>
    </div>
    <div class='widget-content'>
        <form method="post" action="">
            <p class="widget-content-title"><?php echo __('{subscribe intro}') ?></p>
            <?php echo __('please enter your email') ?>:<br/>
            <input type="text" name="txt_email"/>
            <a class="vote" href="javascriot:;">
                <span><?php echo __('submit') ?></span>
            </a>
        </form>
    </div>
</div>