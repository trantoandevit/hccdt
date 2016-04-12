<?php
    $v_cat_slug    = isset($arr_category['C_SLUG'])?$arr_category['C_SLUG']:'';
    $v_category_id = isset($arr_category['PK_CATEGORY'])?$arr_category['PK_CATEGORY']:'';
    $v_website_id  = $this->website_id;
    
    //echo 'File: ' . __FILE__ . '<br>Line: ' . __LINE__;var_dump::display($arr_all_article); 
?>
<table class="video_article_table" cellspacing="0" cellpadding="0">
    <tbody><tr><td colspan="6" class="bg_video_bottom"></td></tr>
        <tr>
            <td class="label_video"></td>
            <td colspan="5">
                <div class="jcarousel-skin-tango" >
                    <ul id="mycarousel">
                        <?php foreach($arr_all_article as $item):
                            $v_begin_date           = $item['C_BEGIN_DATE'];
                            $v_begin_date_YYYYMMDD  = $item['C_BEGIN_DATE_YYYYMMDD'];
                            $v_article_id           = $item['PK_ARTICLE'];
                            $v_title                = $item['C_TITLE'];
                            $v_summary              = $item['C_SUMMARY'];
                            $v_file_name            = $item['C_FILE_NAME'];
                            $v_slug_art             = $item['C_SLUG'];
                            $v_redirect_url			= $item['C_REDIRECT_URL'];
		                       
                            if ($v_redirect_url == NULL OR $v_redirect_url == '')
                            {
                            	$v_article_url = build_url_article($v_cat_slug, $v_slug_art, $v_website_id, $v_category_id, $v_article_id);
                            }
                            else
                            {
                            	$v_article_url = "javascript:VPPlay('" . $v_redirect_url . "','');";
                            }
                            
		                    ?>
		                    <li style="padding: 5px 10px 5px 10px">
		                        <div class="table_td_article_content">
		                            <div class="article_image" title="cssheader=[boxover-cssheader] cssbody=[boxover-cssbody] header=[<?php echo $v_title?>] body=[<?php echo remove_html_tag($v_summary)?>]" >
		                                <?php if ($v_file_name != ''): ?>
		                                       <a href="<?php echo $v_article_url; ?>" title="<?php echo $v_title;?>">
		                                           <img src="<?php echo SITE_ROOT . "upload/" . $v_file_name ?>" class="hot_article_image" style="width: 115px;height: 70px;">
		                                       </a>
		                                   <?php else: ?>
		                                       <a href="<?php echo $v_article_url; ?>" title="<?php echo $v_title;?>">
		                                           <img src="<?php echo CONST_SITE_THEME_ROOT . "images/default-sticky.png"; ?>" class="hot_article_image" style="width: 115px;height: 70px;">
		                                       </a>
		                                   <?php endif; ?>
		                            </div>
		                            <div class="article_title1">
		                                <a href="<?php echo $v_article_url; ?>" title="<?php echo $v_title;?>"><?php echo $v_title;?></a>
		                            </div>
		                        </div>
		                    </li>   
                        <?php endforeach;?>
                    </ul>
                </div>
            </td>
        </tr>
        <tr><td colspan="6" class="bg_video_bottom"></td></tr>
    </tbody>
</table>
        <script type="text/javascript">jQuery(document).ready(function() {jQuery('#mycarousel').jcarousel({wrap: 'circular'});});</script>