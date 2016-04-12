<?php
defined('DS') or die('no direct access');
?>
<label>
    <?php echo __('video limit') ?></br>
    <input type="text" name="txt_video_limit" style="width: 100%;" value="<?php echo $video_limit; ?>"/>
</label>
<label>
    <?php echo __('gallery limit') ?></br>
    <input type="text" name="gallery_limit" style="width: 100%;" value="<?php echo $gallery_limit; ?>"/>
</label>