<?php
    defined('DS') or die('no direct access');
    $v_event_name = isset($arr_event_title['C_NAME']) ? $arr_event_title['C_NAME'] : __('event');
    @$dom = simplexml_load_string($arr_all_event['C_XML_ARTICLE']);
    $arr_event = array();
    if($dom)
    {
         $arr_event = $dom->row;
    }
?>

<?php if (!function_exists('render_widget_event')): ?>
    <?php

    function render_widget_event($arr_events, $start, $display_mode, $website_id) 
    {
        ?>
        <?php $n = count($arr_events) ?>
        <?php for ($i = $start; $i < $n; $i++): ?>
            <?php
            $item       = $arr_events[$i]->attributes();
            $v_title    = $item->C_TITLE;
            $v_slug     = $item->C_SLUG;
            $v_cat_slug = $item->C_SLUG_CAT;
            $v_article  = $item->PK_ARTICLE;
            $v_category = $item->FK_CATEGORY;
            $v_website  = $website_id;
            $v_image_link = $item->C_FILE_NAME;
            $v_url = build_url_article($v_cat_slug, $v_slug, $v_website, $v_category, $v_article);
             if (file_exists(SERVER_ROOT . 'upload/' . $v_image_link) && $v_image_link) 
            {
                $v_img = SITE_ROOT . 'upload/' . $v_image_link;
            } else {
                $v_img = CONST_SITE_THEME_ROOT . 'images/default-sticky.png';
            }
            ?>
            <?php if($display_mode == 'basic' || $display_mode == 'advanced'): ?>
            <li>
                <a href="<?php echo $v_url ?>" title="<?php echo $v_title ?>"><?php echo $v_title ?></a>
            </li>
            <?php endif; ?>
            <?php if($display_mode == 'event_list'): ?>
             <div class="Row">
                <div class="left-Col">
                    <a href="<?php echo $v_url ?>" title="<?php echo $v_title ?>">
                        <img src="<?php echo $v_img; ?>" alt="<?php echo $v_title ?>" />
                    </a>
                </div>
                <div class="right-Col">
                    <a href="<?php echo $v_url ?>" title="<?php echo $v_title ?>"><?php echo $v_title ?></a>
                </div>
             </div>
            <?php endif; ?>
        <?php endfor; ?>
        <?php
    }
    //end function
    ?>  
<?php endif; ?>

<div class="widget widget-event <?php echo $widget_style ?>" data-code="event">
    <div class='widget-header'>
        <h6><?php echo $v_event_name ?></h6>
    </div>
    <div class='widget-content'>
        <?php if ($display_mode == 'basic'): ?>
            <!-- basic mode -->
            <ul class="basic_event">
                <?php render_widget_event($arr_event, 0, 'basic', $this->website_id) ?>
            </ul>
            <!-- end basic mode -->
        <?php endif; //basic mode   ?>
        <?php if ($display_mode == 'advanced'): ?>
        <!-- advanced mode -->
        <div class="basic_event">
            <?php if (isset($arr_event[0])): ?>
                <?php
                $item = $arr_event[0]->attributes();
                $v_title    = $item->C_TITLE;
                $v_slug     = $item->C_SLUG;
                $v_cat_slug = $item->C_SLUG_CAT;
                $v_article  = $item->PK_ARTICLE;
                $v_category = $item->FK_CATEGORY;
                $v_website  = $this->website_id;
                $v_summary  = $item->C_SUMMARY;
                $v_date     = date('d/m/Y H:i', strtotime($item->C_BEGIN_DATE_SQL));
                $v_url = build_url_article($v_cat_slug, $v_slug, $v_website, $v_category, $v_article);
                $v_summary = remove_html_tag($v_summary);
                $v_summary = get_leftmost_words($v_summary, 100) . '...';
                ?>
                <a class="title" href="<?php echo $v_url ?>"><?php echo $v_title ?></a>
                <span class="create-date"><?php echo $v_date; ?></span>
                <p class="sunmary" ><?php echo remove_html_tag(html_entity_decode($v_summary)); ?></p>
                <a class="next" href="<?php echo $v_url; ?>" title="<?php echo $v_title; ?>"><?php echo __('detail')?>...</a>
    <?php endif; ?>
            <ul class="widget-ul-link">
    <?php render_widget_event($arr_event, 1, 'advanced', $this->website_id) ?>
            </ul>
        </div>
            <!-- end advanced mode -->
    <?php endif; ?>
                
    <?php if ($display_mode == 'event_list'): ?>
    <!-- event_list mode -->   
     <?php render_widget_event($arr_event, 0, 'event_list', $this->website_id) ?>
    <!-- end event_list mode -->
<?php endif; //event_list  ?>
                
    </div>
    </div>
<!-- end widget event -->