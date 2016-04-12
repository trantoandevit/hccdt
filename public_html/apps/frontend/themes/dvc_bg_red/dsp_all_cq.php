<?php
$VIEW_DATA['title']                 = __('question list');
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$v_website_id              = get_request_var('website_id',0);
$VIEW_DATA['arr_css']      = array('main','lookup','synthesis','single-article','single-page','component');
$VIEW_DATA['arr_script']   = array();

$v_status           = get_request_var('status',0);
$v_question_name    = get_request_var('txt_question_name','');
$v_field_rq_id      = get_request_var('field_id',0) ;
$v_listype_id      = get_request_var('sel_listype',0) ;
if($v_listype_id == 0)
{
    $v_listype_id = $v_field_rq_id    ;
}
$v_user_send_name   = get_request_var('txt_user_send_name','') ;

?>
<?php $this->render('dsp_header', $VIEW_DATA, $this->theme_code);?>
<div class="col-md-12 question-answer" id="single-page">
     <div class="col-md-3" id="left-sidebar">
        <?php
            $v_widget_path = __DIR__.DS.'dsp_widget.php';
            if(is_file($v_widget_path))
            {
                require $v_widget_path;
            }
        ?>
    </div>
    <!--End #left-sidebar-->

    <div class="col-md-9 " >
        <div class="col-md-12 block">
                 <div class="col-md-12 block" id="main-content">
                   <?php
                    $v_xml_hepl = SERVER_ROOT.'public'.DS.'xml'.DS.'xml_huong_dan_hoi_dap.xml';
                    @$dom = simplexml_load_file($v_xml_hepl);
                    if(is_file($v_xml_hepl) && $dom)
                    {
                       $xpath = '//content';
                       $v_content_help = $dom->xpath($xpath);
                       
                       echo '<fieldset id="question-help" class="my-fields col-md-12 block">
                                <legend>
                                    <img src="'. CONST_SITE_THEME_ROOT .'images/help-dnict.png " />
                                </legend>
                               '.(string)$v_content_help[0].'
                            </fieldset>';
                    }
                   ?>
                     <div class="clear"></div>
                     <!--End #question-help-->
                     <div class="col-md-12 block div-button">
                         <a class="btn btn-primary"  href="<?php echo build_url_set_cq($v_website_id);?>" class="ButtonCq">
                             <label class="glyphicon glyphicon-question-sign"></label>
                             <?php echo __('setting question') ?>
                         </a>
                     </div>
                     <div class="col-md-12 block" id="box-search">
                        <fieldset class="my-fields">
                            <legend><?php echo __('searching for questions');?></legend>
                            <form action="<?php echo build_url_cq($this->website_id);?>" method="get" name="frmMain" id="frmMain">
                                 <?php echo $this->hidden('status',$v_status); ?>
                                 <?php echo $this->hidden('field_id',$v_field_rq_id); ?>
                                <div class="Row col-md-12">
                                    <div class="left-Col col-md-2 block">
                                        <label> <?php echo __('title');?>:  </label>
                                    </div>
                                    <div class="right-Col col-md-4 block">
                                        <input type="text" name="txt_question_name" id="txt_question_name" value="<?php echo $v_question_name; ?>">
                                    </div>
                                </div>
                                <div class="clear" style="height: 10px;"></div>
                                <div class="Row col-md-12">
                                    <div class="left-Col col-md-2 block">
                                        <label> <?php echo __('Họ tên người gửi')?>:  </label>
                                    </div>
                                    <div class="right-Col col-md-4 block">
                                        <input type="text" name="txt_user_send_name" id="txt_question_name" value="<?php echo $v_user_send_name; ?>">
                                    </div>
                                </div>

                                <div class="clear" style="height: 10px;"></div>
                                <div class="Row col-md-12">
                                    <div class="left-Col col-md-2 block">
                                        &nbsp;
                                    </div>
                                    <div class="right-Col col-md-10 block">
                                        <div class="">
                                            <button class="btn btn-primary btn-sm" type="button" name="btn_submit" id="btn_submit_search" onclick="btn_search_onclick();" >
                                                <span class="glyphicon glyphicon-search "></span>
                                                    <?php echo __('search')?>
                                            </button>
                                            <button class="btn btn-primary btn-sm"  type="button" name="btn_submit" id="btn_submit_all" onclick="btn_search_all_onlick()">
                                                
                                                 <?php echo __('view all');?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                 
                             </form>
                         <!--End #frmMain-->
                        </fieldset>                         
                         
                     </div>
                     <!--End box-search-->
                     <div class="col-md-12 block" id="box-results">
                         <?php foreach($arr_all_cq as $cq_data):?>
                         <?php
                                $v_question_id          = $cq_data['PK_CQ'];
                                $v_field_id             = $cq_data['FK_FIELD'];
                                $v_question_name        = $cq_data['C_NAME'];
                                $v_question_address     = $cq_data['C_ADDRESS'];
                                $v_question_title       = $cq_data['C_TITLE'];
                                $v_question_content     = $cq_data['C_CONTENT'];
                                $v_question_answer      = $cq_data['C_ANSWER'];
                                $v_question_date        = $cq_data['C_DATE'];
                                $v_question_date        = jwDate::yyyymmdd_to_ddmmyyyy($v_question_date);
                                $v_question_slug        = $cq_data['C_SLUG'];
                                $v_field_name           = $cq_data['C_FIELD_NAME'];
                                $v_website_id           = $cq_data['FK_WEBSITE'];
                                $v_website_name         = $cq_data['C_WEBSITE_NAME'];
                                
                                $v_question_url         = build_url_cq_detail($v_website_id, $v_question_slug, $v_question_id);
                        ?>
                         <fieldset class="my-fields item">
                             <div class="item">
                                     <div class="question-title">
                                         <img src="<?php echo CONST_SITE_THEME_ROOT ?>images/icon_question.jpg" />
                                         <b><?php echo $v_question_title; ?></b>
                                     </div>
                                     <div class="name">
                                         <label><?php echo __('asker'); ?>:&nbsp; <?php echo $v_question_name; ?></label>
                                     </div>
                                     <div class="question-listype">
                                         <label><?php echo __('field'); ?>:&nbsp; <?php echo $v_field_name; ?></label>
                                     </div>
                                         
                                     <div class="organ">
                                         <label><?php echo __('agency answer')?>:&nbsp; <?php echo $v_website_name; ?></label>
                                     </div>
                                     <div class="question-content">
                                         <label>
                                             <?php echo remove_html_tag(html_entity_decode($v_question_content)); ?>
                                         </label>
                                     </div>
                                     <div class="answer">
                                         <img src="<?php echo CONST_SITE_THEME_ROOT ?>images/bg_entry_comment.jpg" />
                                         <a href="<?php echo $v_question_url; ?>"><?php echo __('view answer') ?></a>
                                     </div>
                                 </div>
                                 <!--End .item-->
                         </fieldset>                         
                        <?php endforeach; ?>
                                <!--button filter page-->
                                <div class="div_pagination" align="center">
                               <?php

                               $v_page              = get_request_var('page',1);
                               $website_id = $this->website_id;
                               $v_url      = build_url_cq($website_id);

                               $v_total_record = isset($arr_all_cq[0]['C_TOTAL']) ? $arr_all_cq[0]['C_TOTAL'] : 0;
                               $v_limit  = defined('_CONST_DEFAULT_ROWS_FIELD') ? _CONST_DEFAULT_ROWS_FIELD : 10;
                               $n              = ceil($v_total_record /$v_limit);
                               if (!$n)
                               {
                                   $n              = 1;
                               }
                               $filter_page_no = get_request_var('page', 1);
                               $first_page     = 1;
                               $previous_page  = $filter_page_no <= 1 ? 1 : $filter_page_no - 1;
                               $next_page      = $filter_page_no == $n ? $n : $filter_page_no + 1;
                               $last_page      = $n;
                               $i              = $filter_page_no <= 2 ? 1 : $filter_page_no - 1;
                               ?>
                               <?php if ($n > 1): ?>
                                   <ul class="pagination pagination-sm">
                                       <li>
                                           <a href="<?php echo $v_url . "&page=$first_page"; ?>" title="<?php echo __("first page") ?>"> |«</a>
                                       </li>
                                       <li>
                                           <a href="<?php echo $v_url . "&page=$previous_page"; ?>" title="<?php echo __("previous page") ?>">
                                               «
                                           </a>
                                       </li>
                                       <?php for ($i, $j = 1; $i <= $n && $j <= 5; $i++, $j++): ?>
                                           <li data-val="<?php echo $i; ?>">
                                               <a href="<?php echo $v_url . "&page=$i" ?>">
                                                   <strong><?php echo $i; ?></strong>
                                               </a>
                                           </li>
                                       <?php endfor; ?>
                                       <li>
                                           <a href="<?php echo $v_url . "&page=$next_page"; ?>" title="<?php echo __("next page") ?>">
                                               »
                                           </a>
                                       </li>
                                       <li>
                                           <a href="<?php echo $v_url . "&page=$last_page"; ?>" title="<?php echo __("last page") ?>"> »| </a>
                                       </li>
                                   </ul>
                               <?php endif; //n > 1  ?>
                           </div>
                     </div> 
                     <!--End #results-->
                     
                 </div>
                <!--End #Main-content-->
        </div>
    </div>
    <!--End #main-page-->
    </div>
<!--End #single-page-->
<script>
    function btn_search_onclick() 
    {
        f = document.forms.frmMain;
        $('#status').val(0);
        f.submit();
    }

    function btn_search_all_onlick() 
    {
        f = document.forms.frmMain;
        $('#status').val(1);
        f.submit();
    }
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
