<?php
    defined('DS') or die();
    $session_key = 'WIDGET_MEDIA_ARTICLE_COUNT';
    $index       = (int) Session::get($session_key);
    Session::set($session_key, $index++);
?>
<?php 

$v_file_default = SITE_ROOT . 'public/images/default-sticky.png.png';
?>

<div class="widget video-clip">
    <div class="box-header">
    <span class="title">Video-clip</span>
    </div>
    <?php if(isset($arr_all_new_video_clip[0])):?>
    <?php 
        $first_video      = get_array_value($arr_all_new_video_clip, 0);
        $v_article_id     = get_array_value($first_video, 0);
        $v_art_slug       = get_array_value($first_video, 1);
        $v_art_title      = get_array_value($first_video, 2);
        $v_category_id    = get_array_value($first_video, 3);
        $v_category_slug  = get_array_value($first_video, 4);
        $v_file_name      = get_array_value($first_video, 5);
        $v_content        = get_array_value($first_video, 6);
        
        if(!file_exists(SERVER_ROOT.'upload/'.$v_file_name))
        {
            $v_file_name = $v_file_default;
        }
        else
        {
            $v_file_name = SITE_ROOT.'upload/'.$v_file_name;
        }
        
        preg_match("/\[VIDEO\](.*)\[\/VIDEO\]/i", $v_content, $matches, PREG_OFFSET_CAPTURE);
        $v_video_url = get_array_value(get_array_value($matches, 1), 0);    

    ?>
    <div class="video" >
        <center class="tieude"><h2><?php echo html_entity_decode($v_art_title); ?></h2></center>
        <center class="auto_video">
            <video  video-id-current="<?php echo $v_article_id ;?>"  width="100%" height="200px" poster="<?php echo $v_file_name;?>" controls="" name="video_play" id="video_play">
                <source src="<?php echo $v_video_url?>" type="video/mp4">
   
            </video>
        </center>
    </div>
    <?php endif;?>
    <div id="list-video">
        <ul>
        <?php for($i = 0; $i < count($arr_all_new_video_clip); $i++):?>
        <?php           
        $v_article_id      = $arr_all_new_video_clip[$i][0];
        $v_art_slug        = $arr_all_new_video_clip[$i][1];
        $v_art_title       = $arr_all_new_video_clip[$i][2];
        $v_category_id     = $arr_all_new_video_clip[$i][3];
        $v_category_slug   = $arr_all_new_video_clip[$i][4];
        $v_file_name       = $arr_all_new_video_clip[$i][5];
        $v_content         = $arr_all_new_video_clip[$i][6];
        if(!file_exists(SERVER_ROOT.'upload/'.$v_file_name))
        {
            $v_file_name = $v_file_default;
        }
        else
        {
            $v_file_name = SITE_ROOT.'upload/'.$v_file_name;
        }
        preg_match("/\[VIDEO\](.*)\[\/VIDEO\]/i", $v_content, $matches, PREG_OFFSET_CAPTURE);
        $v_video_url = get_array_value(get_array_value($matches, 1), 0); 
        
        ?>           
            <li video-id="<?php echo $v_article_id ;?>">
                <a  href="javascript:" onclick="show_video('<?php echo $v_video_url?>','<?php echo $v_file_name;?>','<?php echo $v_art_title; ?>','<?php echo $v_article_id; ?>');">
                <?php echo html_entity_decode($v_art_title); ?>
                </a>
            </li>
        <?php endfor; ?>  
        </ul> 
    </div>
</div>
<script>
    $('document').ready(function(){
            var video = document.getElementsByTagName('video')[0];
            $('#list-video li[video-id|="'+ video.getAttribute('video-id-current')+'"]').hide();
    });
    
    $('#list-video').slimscroll({
        height: '100px',
        color: '#006699',
        size: '10px',
        alwaysVisible: true
    });
    
    function show_video(video_url,img_url,title,article_id)
    {        
        $('.video h2').remove();
        $('.video .tieude').append('<h2>'+title+'</h2>');
        var video = document.getElementsByTagName('video')[0];
            video.setAttribute('video-id-current',article_id);
            $('#list-video li[video-id|="'+ video.getAttribute('video-id-current')+'"]').hide();
            $('#list-video li[video-id !="'+ video.getAttribute('video-id-current')+'"]').show();
            var sources       = video.getElementsByTagName('source');
            video.poster = img_url;
            sources[0].src    = video_url;            
            video.load();
    }
</script>
<!--end video_clip-->
