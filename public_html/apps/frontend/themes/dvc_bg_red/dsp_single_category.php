<?php
//du lieu header
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$v_website_id    = get_request_var('website_id', 0);
$v_category_id   = isset($arr_single_category['PK_CATEGORY']) ? $arr_single_category['PK_CATEGORY'] : '';
$v_category_slug = isset($arr_single_category['C_SLUG']) ? $arr_single_category['C_SLUG'] : '';
$v_category_name = isset($arr_single_category['C_NAME']) ? $arr_single_category['C_NAME'] : '';
$v_xml_article   = isset($arr_single_category['C_XML_ARTICLE']) ? $arr_single_category['C_XML_ARTICLE'] : '';

$v_count_article = isset($arr_single_category['TOTAL_RECORD']) ? $arr_single_category['TOTAL_RECORD'] : '';
$n               = ceil($v_count_article / get_system_config_value('archive_article_per_category'));
$filter_page_no  = get_request_var('page', 1);
$first_page      = 1;
$filter_page_no == 1 ? $previous_page   = 1 : $previous_page   = $filter_page_no - 1;
$filter_page_no == $n ? $next_page       = $n : $next_page       = $filter_page_no + 1;
$last_page       = $n;
$filter_page_no <= 2 ? $i_article       = 1 : $i_article       = $filter_page_no - 1;
$VIEW_DATA['arr_css']               = array('box-cat-feature','component','single-category','single-page','component','breadcrumb');
$VIEW_DATA['arr_script']            = array('');

?>
<?php $this->render('dsp_header', $VIEW_DATA, $this->theme_code); ?>
<div id="single-cat" class="col-md-12">
    <div class="col-md-3 block">
         <?php
            $v_widget_path = __DIR__.DS.'dsp_widget.php';
            if(is_file($v_widget_path))
            {
                require $v_widget_path;
            }
        ?>
    </div>
    <!--End #left-sidebar-->
    <div id="cat-content" class="col-md-9 block">
        <div class="col-md-12 ">
             <div class="div_title">
                 <div class="title-border-left"></div>
                 <div class="title-content">
                     <label class="home">
                        <a href="<?php echo SITE_ROOT; ?>">
                           <img src="<?php echo CONST_SITE_THEME_ROOT ?>images/home-page.png">
                        </a>
                    </label>    
                     <label class="active"> <?php echo  $v_category_name; ?> </label>
                     <a style="float: right; width: 10%; margin-top: 8px;text-align: right;" href="<?php echo build_url_rss($v_category_slug, $this->website_id, $v_category_id); ?>" target="_blank">
                        <img src="<?php echo CONST_SITE_THEME_ROOT . 'images/BUTTON_RSS.gif' ?>" >
                    </a>
                 </div>
            </div>            
            
      </div>
    <div class="clear"></div>
    <?php if ($v_xml_article != ''): ?>
    <div class="col-md-12">
        <div class="div-sticky-article ">
            <?php            
            $dom    = simplexml_load_string($v_xml_article);
            $x_path = "//row[position()=1]";
            $r      = $dom->xpath($x_path);

            $v_file_name    = isset($r[0]->attributes()->C_FILE_NAME) ? $r[0]->attributes()->C_FILE_NAME : '';
            $v_article_id   = $r[0]->attributes()->PK_ARTICLE;
            $v_article_slug = $r[0]->attributes()->C_SLUG;
            $v_begin_date   = $r[0]->attributes()->C_BEGIN_DATE;
            $v_title        = $r[0]->attributes()->C_TITLE;
            $v_summary      = remove_html_tag(htmlspecialchars_decode($r[0]->attributes()->C_SUMMARY));
            $ck_article     = $r[0]->attributes()->CK_NEW_ARTICLE;
            $new_article_mode = get_system_config_value(CFGKEY_NEW_ARTICLE_ICON);
            $img_new_url    = CONST_SITE_THEME_ROOT . 'images/new.png';   
            
            $v_url_image = CONST_SITE_THEME_ROOT . "images/default-sticky.png";
            $v_path_image =  SERVER_ROOT . "upload/" . $v_file_name;   
            if(is_file($v_path_image))
            {
                $v_url_image =  SITE_ROOT . "upload/" . $v_file_name;
            }
            ?>
            <div class="first-content">
                    <a href="<?php echo build_url_article($v_category_slug, $v_article_slug, $v_website_id, $v_category_id, $v_article_id) ?>">
                        <img src="<?php echo $v_url_image ?>" >
                    </a>
                <div class="box-content">
                     <div class="first-title">
                        <a href="<?php echo build_url_article($v_category_slug, $v_article_slug, $v_website_id, $v_category_id, $v_article_id) ?>" >
                            <?php echo $v_title; ?>
                            <label class="begin-date">
                                 (<?php
                                    $v_begin_date = date_create($v_begin_date);
                                    echo date_format($v_begin_date, 'd/m/Y');
                                ?>)
                            </label>
                           
                            <?php if ($ck_article <= 0 && $new_article_mode == 'true'): ?>
                                <!--<img src="<?php echo $img_new_url ?>" height="9"/>-->
                            <?php endif; ?>
                        </a>
                    </div>
                     <div class="first-summary">
                        <?php echo get_leftmost_words($v_summary,40);?> 
                    </div>
                </div>
            </div>
        </div>
      <div class="div-box-article">
        <?php
        $dom    = simplexml_load_string($v_xml_article);
        $x_path = "//row[position()>1]";
        $r      = $dom->xpath($x_path);
        
        $v_loop = (count($r) >= (_CONST_ARCHIVE_ARTICLE_PER_CATEGORY - 1))?(_CONST_ARCHIVE_ARTICLE_PER_CATEGORY - 1):count($r);
        ?>
        <?php
        for($i=0;$i<$v_loop;$i++):
            $row_article    = $r[$i];
            $v_file_name    = isset($row_article->attributes()->C_FILE_NAME) ? $row_article->attributes()->C_FILE_NAME : '';
            $v_article_id   = $row_article->attributes()->PK_ARTICLE;
            $v_article_slug = $row_article->attributes()->C_SLUG;
            $v_begin_date   = $row_article->attributes()->C_BEGIN_DATE;
            $v_title        = $row_article->attributes()->C_TITLE;
            $v_summary      = remove_html_tag(htmlspecialchars_decode($row_article->attributes()->C_SUMMARY));
            $ck_new_article = $row_article->attributes()->CK_NEW_ARTICLE;
            $new_article_mode = get_system_config_value(CFGKEY_NEW_ARTICLE_ICON);
            $img_new_url    = CONST_SITE_THEME_ROOT . 'images/new.png';
            
            $v_url_image = CONST_SITE_THEME_ROOT . "images/default-sticky.png";
            $v_path_image =  SERVER_ROOT . "upload/" . $v_file_name;   
            if(is_file($v_path_image))
            {
                $v_url_image =  SITE_ROOT . "upload/" . $v_file_name;
            }
            ?>
            <div class="div-item-article">
                <div class="item-content">
                        <a href="<?php echo build_url_article($v_category_slug, $v_article_slug, $v_website_id, $v_category_id, $v_article_id) ?>">
                            <img src="<?php echo $v_url_image ?>">
                        </a>
                    <div class="box-content">
                        <div class="item-title">
                                <a href="<?php echo build_url_article($v_category_slug, $v_article_slug, $v_website_id, $v_category_id, $v_article_id) ?>">
                                   <?php echo $v_title; ?>
                                     <label class="begin-date">
                                        (<?php
                                           $v_begin_date = date_create($v_begin_date);
                                           echo date_format($v_begin_date, 'd/m/Y');
                                       ?>)
                                   </label>
                                   <?php if ($ck_new_article <= 0 && $new_article_mode == 'true'): ?>
                                       <!--<img height="9" src="<?php echo $img_new_url ?>"/>-->
                                   <?php endif; ?>
                               </a>
                           </div>
                        <div class="item-summary">
                             <?php echo get_leftmost_words($v_summary, 40);?> 
                         </div>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    <!--other article-->
      </div>
        </div>
    <?php 
    $v_check = count($r) - $v_loop;
    
    if($v_check > 0):?>
    <div class="clear" style="height: 10px;width: 100%;">&nbsp;</div>
    <div class="other-new col-md-12" style="padding-left: 10px;">
        <div class="other-articles-ttile col-md-9 block"><?php echo __('other news');?></div>
        <div class="other-sticky">
            <ul >
                <?php 
                for($i;$i<count($r);$i++):
                    $row_article    = $r[$i];
                    $v_file_name    = isset($row_article->attributes()->C_FILE_NAME) ? $row_article->attributes()->C_FILE_NAME : '';
                    $v_article_id   = $row_article->attributes()->PK_ARTICLE;
                    $v_article_slug = $row_article->attributes()->C_SLUG;
                    $v_begin_date   = $row_article->attributes()->C_BEGIN_DATE;
                    $v_title        = $row_article->attributes()->C_TITLE;
                    $v_summary      = remove_html_tag(htmlspecialchars_decode($r[0]->attributes()->C_SUMMARY));
                    $ck_new_article = $row_article->attributes()->CK_NEW_ARTICLE;
                    $new_article_mode = get_system_config_value(CFGKEY_NEW_ARTICLE_ICON);
                    $img_new_url    = CONST_SITE_THEME_ROOT . 'images/new.png';
                ?>
                <li>
                    <a href="<?php echo build_url_article($v_category_slug, $v_article_slug, $v_website_id, $v_category_id, $v_article_id) ?>">
                        <?php echo $v_title;?> 
                        <font color="#565656"><i>(<?php echo $v_begin_date?>)</i></font>
                    </a>
                </li>
                <?php endfor;?>
            </ul>
        </div>
    </div>
    <?php endif;?>
    <!--button filter page-->
    <div class="clear">&nbsp;</div>
    <?php if ($n > 1): ?>
    <div class="div-filter-page">
        <?php if(get_request_var('page', 1) != $n):?>
        <span class="ButtonNext">
            <a href="<?php echo build_url_category($v_category_slug, $v_website_id, $v_category_id) . "/" . $next_page; ?>">
                <?php echo  __('next page')?>
            </a>
        </span>
        <?php endif;?>
        &nbsp;&nbsp;&nbsp;
        
        <?php if(get_request_var('page', 1) != 1):?>
        <span class="ButtonPre">
            <a href="<?php echo build_url_category($v_category_slug, $v_website_id, $v_category_id) . "/" . $previous_page; ?>">
               <?php echo __('back')?>
            </a>
        </span>
        <?php endif;?>
    </div>
    <?php endif;?>
    <?php endif;//neu $v_xml_article == '''?>
    <div class="clear" style="height: 10px">&nbsp;</div>
    </div>
    <!--End #cat-content-->
    
</div>
<script>
    $(document).ready(function(){
        $('.pagination li[data-val=<?php echo $filter_page_no; ?>]').attr('class','active');
        $('.pagination li[data-val=<?php echo $filter_page_no; ?>]').html($('.pagination li[data-val=<?php echo $filter_page_no; ?>] strong').html());
    });
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>