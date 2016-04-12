<?php
defined('DS') or die('no direct access');
$v_index = Session::get('COUNT_WIDGET_MOST_VISITED');
Session::set('COUNT_WIDGET_MOST_VISITED', $v_index + 1);
?>
<style>

</style>
<div class="widget widget-most-visited" data-code="widget-most-visited">
     <div class='widget-header'>
       <h6><?php echo __('most visited') ?></h6>
    </div>
    
  <div class='widget-content'>
      <?php echo "<div class='widget-page'>" ?>
            <?php $n       = count($arr_all_article); ?>
            <?php for ($i = 0; $i < $n; $i++): ?>
                <?php
                $item            = $arr_all_article[$i];
                $v_article_id    = $item['PK_ARTICLE'];
                $v_category_id   = $item['PK_CATEGORY'];
                $v_article_slug  = $item['C_SLUG'];
                $v_category_slug = $item['C_CAT_SLUG'];
                $v_title         = remove_html_tag($item['C_TITLE']);
                
                $v_url           = build_url_article($v_category_slug, $v_article_slug, $this->website_id, $v_category_id, $v_article_id);
                
                if ($item['C_FILE_NAME'] != NULL && $item['C_FILE_NAME'] != '')
                {
                    $v_img = SITE_ROOT . 'upload/' . $item['C_FILE_NAME'];
                }
                else
                {
                    $v_img = CONST_SITE_THEME_ROOT . 'images/default-sticky.png';
                }
                ?>
                <div class=" Row">
                    <div class="left-Col">
                            <a href="<?php echo $v_url ?>">
                                <img src="<?php echo $v_img ?>" alt="<?php echo $v_title ?>"/>
                            </a>
                    </div>
                    <div class="right-Col">
                        <a href="<?php echo $v_url ?>"><?php echo $v_title ?></a>
                    </div>
                </div>
            <?php endfor; ?>
    <?php echo '</div>' ?>
    </div>
</div>