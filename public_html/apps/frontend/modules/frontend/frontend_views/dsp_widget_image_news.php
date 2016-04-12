<?php
    defined('DS') or die();
//    $session_key = 'WIDGET_MEDIA_ARTICLE_COUNT';
//    $index       = (int) Session::get($session_key);
//    Session::set($session_key, $index++);

//echo __FILE__;
//var_dump::display($arr_all_image_news);
//echo __LINE__;
$v_file_default = CONST_SITE_THEME_ROOT . 'images/default-sticky.png';

$v_article_id     =  '';
$v_title          =  '';
$v_sub_titel      =  '';
$v_summary        =  '';
$v_content        =  '';
$v_ar_slug        =  '';
$v_file_name      =  '';
$v_cat_slug       =  '';
$v_category_id    =  '';
?>

<div class="widget image-news <?php echo $widget_style;?>">
    <div class="widget-header">
       <h6><?php  echo $title; ?></h6>
    </div>    
    <?php
           for($i=0 ;$i<count($arr_all_image_news); $i++):
            $v_article_id     = intval($arr_all_image_news[$i]['PK_ARTICLE']) ;
            $v_title          = $arr_all_image_news[$i]['C_TITLE'] ;
            $v_sub_titel      = $arr_all_image_news[$i]['C_SUB_TITLE'] ;
            $v_summary        = $arr_all_image_news[$i]['C_SUMMARY'];
            $v_summary        = isset($v_summary) ? get_leftmost_words($v_summary,40) : '';
            $v_content        = $arr_all_image_news[$i]['C_CONTENT'] ;
            $v_ar_slug        = $arr_all_image_news[$i]['C_SLUG'] ;
            $v_file_name      = $arr_all_image_news[$i]['C_FILE_NAME'] ;
            $v_cat_slug       = $arr_all_image_news[$i]['C_SLUG_CATEGORY'] ;
            $v_category_id    = $arr_all_image_news[$i]['PK_CATEGORY'];
            $v_url       = build_url_article($v_cat_slug, $v_ar_slug, $this->website_id, $v_category_id, $v_article_id);

            if ($v_file_name != NULL && $v_file_name != '')
            {
                $v_file_name = SITE_ROOT . 'upload/' . $v_file_name;
            }
            else
            {
                $v_file_name      = $v_file_default;
            }
            if($i == 0)
            {
        ?>
            <div class="widget-image-news-content">
                <div class="img">  
                    <a href="<?php echo $v_file_name; ?>"  title="<?php echo $v_title; ?>" >
                    <image src="<?php echo $v_file_name; ?>" width="100%" height="100%" />   
                    </a>                
                </div>
                <span class="logo-image"></span>
                <h2 class="title">
                    <a href="<?php echo $v_url; ?>"> <?php echo $v_title; ?></a>
                </h2>
            </div>
         <?php 
          }
          else 
          {
        ?>
    <div id="list image-news">
        <ul>
            <li>
                <a href="<?php echo $v_url; ?>"> <?php echo $v_title; ?></a>
            </li>
        </ul>          
    </div>   
        <?php
          }
         endfor;
    ?>
    <a  class="connection" href="<?php echo build_url_all_img_news($this->website_id)?>">Các tin ảnh khác</a>  
</div>
<!--end video_clip-->
