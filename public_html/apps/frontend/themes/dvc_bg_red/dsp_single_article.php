<?php
$this->count_video = 0;
Session::init();


?>
<?php
//du lieu header
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['v_keywords']            = isset($arr_single_article['C_KEYWORDS']) ? $arr_single_article['C_KEYWORDS'] : '';
$VIEW_DATA['v_description']         = isset($arr_single_article['C_SUMMARY']) ? remove_html_tag($arr_single_article['C_SUMMARY']) : '';
//du lieu content
$v_article_sub_title                = isset($arr_single_article['C_SUB_TITLE']) ? $arr_single_article['C_SUB_TITLE'] : '';
$v_article_title                    = isset($arr_single_article['C_TITLE']) ? $arr_single_article['C_TITLE'] : '';
$v_begin_date                       = isset($arr_single_article['C_BEGIN_DATE']) ? $arr_single_article['C_BEGIN_DATE'] : '';

$v_article_sumary                   = isset($arr_single_article['C_SUMMARY']) ? $arr_single_article['C_SUMMARY'] : '';
$v_article_sumary = htmlspecialchars_decode($v_article_sumary);

$v_article_content                  = isset($arr_single_article['C_CONTENT']) ? $arr_single_article['C_CONTENT'] : '';
$v_article_content = htmlspecialchars_decode($v_article_content);

$v_xml_attach                       = isset($arr_single_article['C_XML_ATTACH']) ? $arr_single_article['C_XML_ATTACH'] : '';
$v_xml_other_news                   = isset($arr_single_article['C_XML_OTHER_NEWS']) ? $arr_single_article['C_XML_OTHER_NEWS'] : '';
$v_category_slug                    = isset($arr_single_article['C_SLUG_CAT']) ? $arr_single_article['C_SLUG_CAT'] : '';
$v_pen_name                         = isset($arr_single_article['C_PEN_NAME']) ? $arr_single_article['C_PEN_NAME'] : '';
$article_slug                       = isset($arr_single_article['C_SLUG_ARTICLE']) ? $arr_single_article['C_SLUG_ARTICLE'] : '';
$v_media_file_name                  = isset($arr_single_article['C_FILE_NAME']) ? $arr_single_article['C_FILE_NAME'] : '';
$v_article_tags                     = isset($arr_single_article['C_TAGS']) ? $arr_single_article['C_TAGS'] : '';
//vote
$rating_result                      = isset($arr_single_article['C_CACHED_RATING']) ? $arr_single_article['C_CACHED_RATING'] : 0;
$rating_count                       = isset($arr_single_article['C_CACHED_RATING_COUNT']) ? $arr_single_article['C_CACHED_RATING_COUNT'] : 0;

$website_id        = get_request_var('website_id', 0);
$category_id       = get_request_var('category_id', 0);
$article_id        = get_request_var('article_id', 0);

$pattern           = "/\[VIDEO\](.*)\[\/VIDEO\]/i";
$v_article_content = preg_replace_callback($pattern, 'replace_video', $v_article_content, -1, $count);
?>
<?php
$VIEW_DATA['arr_css']               = array('lookup','synthesis','single-article','single-page','component');
$VIEW_DATA['arr_script']        = array('gp-slide');
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);
?>
<div class="col-md-12 content" id="single-page">
       
    <div class="col-md-3 " id="left-sidebar">
        <?php
            $v_widget_path = __DIR__.DS.'dsp_widget.php';
            if(is_file($v_widget_path))
            {
                require $v_widget_path;
            }
        ?>
    </div>
    <!--End #left-sidebar-->
  
    <div id="main-content" class="col-md-9 ">
        <div class="col-md-12 box-content block">
            <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                    <label class="home">
                        <a href="<?php echo SITE_ROOT; ?>">
                            <img src="<?php echo CONST_SITE_THEME_ROOT ?>images/home-page.png">
                        </a>
                    </label>    
                    <label > 
                        <?php $n = count($arr_single_article['ROOT_CATEGORY']); ?>
                        <?php foreach ($arr_single_article['ROOT_CATEGORY'] as $cat_id => $cat_name): ?>
                            <a href="<?php echo build_url_category(auto_slug($cat_name), $this->website_id, $cat_id) ?>">
                                <?php echo $cat_name ?>
                            </a>
                        <?php endforeach; ?>
                    </label>
                    <label class="active"><?php echo get_leftmost_words($v_article_title, 10); ?></label>
                    <a class="rss" style="float: right; width: 8%; margin-top: 8px;text-align: right;" href="<?php echo build_url_rss($v_category_slug, $this->website_id, $category_id); ?>" target="_blank">
                        <img src="<?php echo CONST_SITE_THEME_ROOT . 'images/BUTTON_RSS.gif' ?>" >
                    </a>
                </div>
            </div>
      </div>
        
    <div class="div_article">            
            <div class="div_article_title">
                <?php echo $v_article_title; ?>
            </div
            
            <div class="div_article_begin_date_sub_title col-md-12">
               
                <div class="col-md-3 begin-date block">
                <span  style="float: left;">
                    <?php echo __('post date')?>: 
                    &nbsp;
                    <?php
                        $v_begin_date = date_create($v_begin_date);
                        echo date_format($v_begin_date, 'd/m/Y');
                    ?> 
                </span>
                </div>
                <div class="col-md-5 sub-title">
                   &nbsp;
                </div>
            <div class="col-md-4 addthis_button">
                       <!-- AddThis Button BEGIN -->
                    <div class="addthis_toolbox addthis_default_style ">
                           <!--<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>-->
                           <a class="addthis_button_tweet"></a>
                           <a class="addthis_counter addthis_pill_style"></a>
                           <!--vote-->
                           <?php
                           echo $this->hidden('hdn_rating', $rating_result);
                           echo $this->hidden('hdn_controller', $this->get_controller_url());
                           ?>
                           <ul class="rating_stars">
                               <li onmouseover="rating.mouseover(this)" onmouseout="rating.display_stars('.rating_stars', $('#hdn_rating').val())" onclick="rating.update(<?php echo $article_id ?>,1)" ></li>
                               <li onmouseover="rating.mouseover(this)" onmouseout="rating.display_stars('.rating_stars', $('#hdn_rating').val())" onclick="rating.update(<?php echo $article_id ?>,2)"></li>
                               <li onmouseover="rating.mouseover(this)" onmouseout="rating.display_stars('.rating_stars', $('#hdn_rating').val())" onclick="rating.update(<?php echo $article_id ?>,3)"></li>
                               <li onmouseover="rating.mouseover(this)" onmouseout="rating.display_stars('.rating_stars', $('#hdn_rating').val())" onclick="rating.update(<?php echo $article_id ?>,4)"></li>
                               <li onmouseover="rating.mouseover(this)" onmouseout="rating.display_stars('.rating_stars', $('#hdn_rating').val())" onclick="rating.update(<?php echo $article_id ?>,5)"></li>
                           </ul>
                           <!--<span>&nbsp;<label id="lbl_votecount"><?php echo $rating_count ?></label> <?php echo __('votes') ?></span>-->
                           <!--end vote-->
                           <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-5147ef1d123197b5"></script>
                       </div>
               <!-- AddThis Button END -->
            </div>
                <div class="col-md-12 block">
                    <?php if(trim($v_article_sub_title) != '') :?>
                        <div class="div_sub_title ">
                            <?php echo (trim($v_article_sub_title) != '') ? ' - ' . $v_article_sub_title : ''; ?>
                        </div>
                    <div class="clear" style="height: 10px"></div>
                    <?php endif;?>
                </div>     
            </div>
            <div class="clear"></div>
            <div class="div_article_summary col-md-12 block" align="justify">
                <?php echo $v_article_sumary; ?>
                
            </div>
            <div class="clear"></div>
            <div class="div_article_content">
                <?php echo $v_article_content; ?>
            </div>
            <?php if ($v_xml_attach != ''): ?>
                <div class="div_file_attach">
                    <?php
                    $dom    = simplexml_load_string($v_xml_attach);
                    $x_path = '//row';
                    $r      = $dom->xpath($x_path);
                    ?>
                    <div>
                        <img src="<?php echo CONST_SITE_THEME_ROOT; ?>images/attach.gif"/>
                        <label><?php echo __('File attach') ?></label>
                    </div>
                    <br>
                    <?php foreach ($r as $file_attach): ?>
                        <a href="<?php echo SITE_ROOT . "upload/" . strval($file_attach->attributes()->C_FILE_NAME); ?>"><?php echo strval($file_attach->attributes()->C_FILE_NAME); ?></a>
                        <br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="clear" style="height: 8px;"></div>
            <div class="div_pen_name" >
                <strong>
                    <font size="3"><?php echo $v_pen_name; ?></font>
                </strong>
            </div>
            <?php if ($v_article_tags != ''): ?>
                <div id="div_tags">
                    <h2 class="h2Acticle"></span></h2>
                    <?php
                    $arr_tags = explode(',', $v_article_tags);
                    ?>
                    <?php echo __('tag: '); ?>
                    <?php foreach ($arr_tags as $row_tags): ?>
                        <a href="<?php echo build_url_tags($website_id, $row_tags) ?>"><?php echo $row_tags; ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <a href="javascript:void(0)" class="print" onclick="print_onclick();" style="float: right;font-weight: bold;text-align: right;width: 100%">
                <img src="<?php echo CONST_SITE_THEME_ROOT . "images/Print.gif"; ?>">
                <?php echo __('print') ?>
            </a>
            <div class="clear"></div>
    <!--<div class="clear"></div>-->
    <?php if(sizeof($arr_other_news) >0):?>
    <div class="col-md-12 other-new">
        <div class="other-articles-ttile col-md-9 block"><?php echo __('other news');?></div>
        <div class="clear"></div>
        <div class="other-sticky">
            <ul >
                <?php foreach($arr_other_news as $arr_data):
                        $v_article_id   = $arr_data['PK_ARTICLE'];
                        $v_article_slug = $arr_data['C_SLUG'];
                        $v_begin_date   = $arr_data['C_BEGIN_DATE'];
                        $v_title        = $arr_data['C_TITLE'];
                ?>
                <li>
                    <a href="<?php echo build_url_article($v_category_slug, $v_article_slug, $website_id, $category_id, $v_article_id) ?>">
                        <?php echo $v_title?>
                    </a>
                    <span class="other-new-begin-date"> (<?php echo $v_begin_date;?>)</span>
                </li>
                <?php endforeach;?>
            </ul>
        </div>
    </div> 
    <?php endif;?>
    </div>
    
</div>
<!--End #Main-content-->
<script>
    function print_onclick()
    {
        str="<?php echo build_url_print($v_category_slug, $article_slug, $website_id, $category_id, $article_id) ?>";
        window.open(str,"",'scrollbars=1,width=700,height=600');
    }
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>