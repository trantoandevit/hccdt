<?php
//
//echo __FILE__;
//var_dump::display($arr_all_rating);
//echo __LINE__;
defined('DS') or die();
$v_file_default = CONST_SITE_THEME_ROOT . 'images/default-sticky.png';
?>
<!--start widget rating-->
<div class="widget rating <?php echo $widget_style;?>">
      <div class="widget-header">
       <h6><?php  echo $title; ?></h6>
    </div>    
    <ul>
        <?php
        $arr_all_rating = isset($arr_all_rating) ? $arr_all_rating :array();
        foreach ($arr_all_rating as $single_article):
        ?>
        <?php
         $v_article_id      = $single_article['PK_ARTICLE'];
         $v_article_slug    = $single_article['C_SLUG'];
         $v_title           = $single_article['C_TITLE'];
         $v_file_name       = $single_article['C_FILE_NAME'];
         
         $v_category_id     = $single_article['C_DEFAULT_CATEGORY'];
         $v_category_slug   = $single_article['C_CAT_SLUG'];
       
         
         $v_url_article     = build_url_article($v_category_slug, $v_article_slug, $this->website_id, $v_category_id, $v_article_id);
          if ($v_file_name != NULL OR $v_file_name != '')
            {
                $v_file_name = SITE_ROOT . 'upload/' . $v_file_name;
            }
            else
            {
                $v_file_name      = $v_file_default;
            }
        
        ?>
        <li>
            <div class="thumbnail">
                <a href="<?php echo $v_url_article;?>">
                    <img src="<?php echo $v_file_name;?>" width="100%" height="100%" />
                </a>
            </div>
            <div class="at-title">
                <a href="<?php echo $v_url_article;?>"><h2><?php echo $v_title;?></h2></a>
            </div>
        </li>
        <?php endforeach;?>
    </ul>
    
    
</div>
<!--end widget rating-->