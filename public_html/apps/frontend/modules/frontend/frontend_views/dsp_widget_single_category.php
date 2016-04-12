<?php
    defined('DS') or die('no direct access');
    $arr_single_category = isset($arr_single_category) ? $arr_single_category :array();
    
    $v_category_name = isset($arr_single_category[0]['C_CATEGORY_NAME']) ? $arr_single_category[0]['C_CATEGORY_NAME'] : '';
    $v_category_id   = isset($arr_single_category[0]['PK_CATEGORY']) ? $arr_single_category[0]['PK_CATEGORY'] : '';
    $category_slug   = isset($arr_single_category[0]['C_SLUG_CAT']) ? $arr_single_category[0]['C_SLUG_CAT'] : '';
    $v_limit         = isset($arr_single_category[0]['C_LIMIT']) ? $arr_single_category[0]['C_LIMIT']  : '10';
?>
<div class="single_category">
    <div class="widget_title">
        <img src="<?php echo CONST_SITE_THEME_ROOT . 'images/icon_title.gif'?>" />
        <label><?php echo $v_category_name?></label>
    </div>
    <div class="div_content">
        <?php 
        $i = 1;
        foreach($arr_single_category as $arr_article):
                $v_title      = $arr_article['C_TITLE'];
                $v_date       = $arr_article['C_BEGIN_DATE_DDMMYY'];  
                $article_slug = $arr_article['C_SLUG']; 
                $article_id   = $arr_article['PK_ARTICLE']; 
                $website_id   = $this->website_id;
                
                $v_url   = build_url_article($category_slug, $article_slug, $website_id, $v_category_id, $article_id)
        ?>
        <div class="item <?php echo ($i == count($arr_single_category))?'last':'';?>">
            <a href="<?php echo $v_url;?>">
                <?php echo $v_title?>
                <label class="datetime">(<?php echo $v_date?>)</label>
            </a>
        </div>
        <?php 
        $i++;
        endforeach;
        ?>
    </div>
    <?php if((count($arr_single_category) >= $v_limit)) : ?>
    <div class="div_footer">
        <a href="<?php echo build_url_category($category_slug, $website_id, $v_category_id)?>">
            <?php echo __('view all')?> >>
        </a>
    </div>
    <?php endif;?>
</div>


