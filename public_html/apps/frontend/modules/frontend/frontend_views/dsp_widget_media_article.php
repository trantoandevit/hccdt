<?php
defined('DS') or die();
$session_key = 'WIDGET_MEDIA_ARTICLE_COUNT';
$index       = (int) Session::get($session_key);
Session::set($session_key, $index++);
?>
<div class="widget_media_article" id="widget_media_article_<?php echo $index ?>">
    <ul>
        <li>
            <a href="#widget_media_article_tab_video_<?php echo $index ?>">
                <span>
                    <?php echo __('television') ?>
                </span>
            </a>
        </li>
        <li>
            <a href="#widget_media_article_tab_gallery_<?php echo $index ?>">
                <span>
                    <?php echo __('photo gallery') ?>
                </span>
            </a>
        </li>
    </ul>
    <div class="widget_media_article_tab_video" id="widget_media_article_tab_video_<?php echo $index ?>">
        <?php
        $first_video = get_array_value($arr_all_new_video, 0);
        $v_title     = get_array_value($first_video, 2);
        $v_slug      = get_array_value($first_video, 1);
        $v_id        = get_array_value($first_video, 0);
        $v_cat_id    = get_array_value($first_video, 3);
        $v_cat_slug  = get_array_value($first_video, 4);
        $v_content   = get_array_value($first_video, 6);
        $v_img       = get_array_value($first_video, 5);

        if (file_exists(SERVER_ROOT . 'upload' . DS . $v_img))
        {
            $v_img = SITE_ROOT . 'upload/' . $v_img;
        }
        else
        {
            $v_img = SITE_ROOT . 'public/images/langson.png';
        }

        preg_match("/\[VIDEO\](.*)\[\/VIDEO\]/i", $v_content, $matches, PREG_OFFSET_CAPTURE);
        $v_video_url = get_array_value(get_array_value($matches, 1), 0);

        $v_url = build_url_article($v_cat_slug, $v_slug, $this->website_id, $v_cat_id, $v_id);
        ?>
        <a href="<?php echo $v_url ?>" style="display:block;overflow: hidden;"><div class="title"><?php echo $v_title ?></div></a>
        <div class="jw_container">
            <embed 
                id="hotplayer" 
                src="<?php echo SITE_ROOT ?>public/jwplayer/player.swf" 
                width="100%" height="100%" type="application/x-shockwave-flash" 
                data="<?php echo SITE_ROOT ?>public/jwplayer/player.swf" 
                allowscriptaccess="always" allowfullscreen="true" wmode="transparent" 
                flashvars="height=100&amp;width=100&amp;plugins=ova&amp;file=<?php echo$v_video_url ?>&amp;image=<?php echo $v_img ?>&amp;provider=video&amp;controlbar=bottom&amp;volume=100&amp;stretching=exactfit"
                />
        </div>
        <ul>
            <?php $n     = count($arr_all_new_video) ?>
            <?php for ($i = 1; $i < $n; $i++): ?>
                <?php
                $item       = $arr_all_new_video[$i];
                $v_title    = get_array_value($item, 2);
                $v_slug     = get_array_value($item, 1);
                $v_id       = get_array_value($item, 0);
                $v_cat_id   = get_array_value($item, 3);
                $v_cat_slug = get_array_value($item, 4);
                $v_url      = build_url_article($v_cat_slug, $v_slug, $this->website_id, $v_cat_id, $v_id);
                ?>
                <li><a href="<?php echo $v_url ?>"><?php echo $v_title ?></a></li>
            <?php endfor; ?>
        </ul>
    </div>
    <div class="widget_media_article_tab_gallery" id="widget_media_article_tab_gallery_<?php echo $index ?>">
        <?php $n          = count($arr_all_pg) ?>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php
            $item        = $arr_all_pg[$i];
            $v_id        = get_array_value($item, 0);
            $v_title     = get_array_value($item, 1);
            $v_slug      = get_array_value($item, 2);
            $v_summary   = strip_tags(get_leftmost_words(get_array_value($item, 4), 25), '<a>');
            $v_thumbnail = get_array_value($item, 3);
            $v_thumbnail = file_exists(SERVER_ROOT . 'upload' . DS . $v_thumbnail) ?
                    SITE_ROOT . 'upload/' . $v_thumbnail :
                    SITE_ROOT . 'public/images/langson.png';

            $v_url = build_url_photo_gallery($this->website_id, $v_slug, $v_id);
            ?>
            <div class="">
                <a href="<?php echo $v_url ?>"><img align="" src="<?php echo $v_thumbnail ?>" class="thumbnail" alt="<?php echo $v_slug ?>"/></a>
                <div class="">
                    <a href="<?php echo $v_url ?>">
                        <?php echo $v_title ?>
                    </a><br>
                    <?php echo $v_summary ?>
                </div>
            </div> 
        <?php endfor; ?>
    </div>
</div>
<script>
    $(document).ready(function(){
        if(! $.fn.widget_tabs){
            console.log('Cần lập trình hàm $.fn.widget_tabs trong theme');
        }
        $('.widget_media_article').widget_tabs('media_article');
    });
</script>