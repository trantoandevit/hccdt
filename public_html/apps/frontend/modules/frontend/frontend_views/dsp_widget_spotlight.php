<?php
defined('DS') or die('no direct access');
$v_pos_name = isset($arr_single_position['C_NAME']) ? $arr_single_position['C_NAME'] : __('spotlight');
?>
<?php if (!function_exists('render_widget_spotlight')): ?>
    <?php

    function render_widget_spotlight($arr_all_spotlight,$display_mode = '') 
    {
        ?>
        <?php $n = count($arr_all_spotlight) ?>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php
            $item       = $arr_all_spotlight[$i];
            $v_title    = $item[8];
            $v_slug     = $item[2];
            $v_file_name = $item[3];
            $v_cat_slug = $item[6];
            $v_article  = $item[1];
            $v_category = $item[0];
            $v_website  = $item[7];
            $v_url = build_url_article($v_cat_slug, $v_slug, $v_website, $v_category, $v_article);
             if ($v_file_name != '' && $v_file_name != NULL)  
            {
                $v_img = SITE_ROOT . 'upload/' . $item[3];
            } else {
                $v_img = CONST_SITE_THEME_ROOT . 'images/default-sticky.png';
            }
            ?>
             <?php if($display_mode == 'spotlight_list'): ?>
              <div class="Row">
                <div class="left-Col">
                   <?php   
                        echo ' <a href="'.$v_url.'" style="background:none;float:left;padding:0;">';
                        echo '<image src="'.$v_img.'" height="100%" width="100%" style="margin:5px;padding:0" />';
                        echo '</a>';
                    ?>
                </div>
                <div class="right-Col">
                    <a href="<?php echo $v_url ?>" <?php echo ($display_mode == 'spotlight_list') ? 'style="background:none;padding:0; display:block;margin:0 5px 0 52px;"': ''; ?> ><?php echo $v_title ?></a>
                </div>
             </div>
             <?php else:?>
                <li>
                   <a href="<?php echo $v_url ?>" <?php echo ($display_mode == 'spotlight_list') ? 'style="background:none;padding:0; display:block;margin:0 5px 0 52px;"': ''; ?> ><?php echo $v_title ?></a>
                </li>
            <?php endif;?>
        <?php endfor; ?>
        <?php
    }

//end function  
    ?>
<?php endif; ?>
<div class="widget widget-spotlight <?php echo $widget_style ?>" data-code="spotlight">
    <div class='widget-header'>
        <h6><?php echo $v_pos_name ?></h6>
    </div>
    <div class='widget-content'>
        <?php if ($display_mode == 'basic'): ?>
            <ul>
                <?php render_widget_spotlight($arr_all_spotlight) ?>
            </ul>
        <?php endif; //basic mode   ?>
        
        <?php if ($display_mode == 'advanced'): ?>
            <?php if (isset($arr_all_spotlight[0])): ?>
                <?php
                $item = array_shift($arr_all_spotlight);
                $v_title = $item[8];
                $v_slug = $item[2];
                $v_file_name = $item[3];
                $v_cat_slug = $item[6];
                $v_article = $item[1];
                $v_category = $item[0];
                $v_website = $item[7];
                $v_url = build_url_article($v_cat_slug, $v_slug, $v_website, $v_category, $v_article);
                if ($v_file_name != '' && $v_file_name != NULL)   
                {
                    $v_img = SITE_ROOT . 'upload/' . $item[3];
                } 
                else 
                {
                    $v_img = CONST_SITE_THEME_ROOT . 'images/default-sticky.png';
                }
                $v_summary = htmlspecialchars_decode($item[4]);
                $v_summary = remove_html_tag($v_summary);
                $v_summary = get_leftmost_words($v_summary, 60) . '...';
                $v_pen_name = $item[5];
                ?>
                <a href="<?php echo $v_url ?>">
                    <img style="width:100%" src="<?php echo $v_img ?>" alt="<?php echo $v_title ?>"/>
                    <p class="widget-content-title"><?php echo $v_title ?></p>
                </a>
                <p><?php echo $v_summary ?></p>
                <p class="widget-comment"><?php echo $v_pen_name ?></p>
    <?php endif; ?>
            <ul class="widget-ul-link">
    <?php render_widget_spotlight($arr_all_spotlight) ?>
            </ul>
    <?php endif; ?>
               
    <?php if ($display_mode == 'spotlight_list'): ?>
         <?php render_widget_spotlight($arr_all_spotlight,$display_mode) ?>
    <?php endif; //spotlight_list  ?>
                
    </div>
</div>