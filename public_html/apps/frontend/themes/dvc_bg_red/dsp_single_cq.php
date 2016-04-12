<?php
$VIEW_DATA['title']                 = __('citizen question');
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$VIEW_DATA['arr_css']          = array('box-cat-feature','single-page','component');
$VIEW_DATA['arr_script']       = array();
$website_id                    = $this->website_id;

$v_website_id   = get_request_var('website_id',0);
$v_title        = $arr_single_cq['C_TITLE'];
$v_name         = $arr_single_cq['C_NAME'];
$v_content      = $arr_single_cq['C_CONTENT'];
$v_answer       = $arr_single_cq['C_ANSWER'];
$v_address      = $arr_single_cq['C_ADDRESS'];
$v_phone        = $arr_single_cq['C_PHONE'];
$v_email        = $arr_single_cq['C_EMAIL'];
$v_field_name   = $arr_single_cq['C_NAME_FIELD'];
$v_day_asked   = $arr_single_cq['C_DATE'];
$arr_cq_connection = isset($arr_cq_connection) ? $arr_cq_connection : array();
$v_field_rq_id      = get_request_var('field_id','') ;

?>
<?php $this->render('dsp_header', $VIEW_DATA, $this->theme_code);?>

<div class="col-md-12 single-answer" id="single-page">
    <div  class="col-md-3 ">
         <?php
               $v_widget_path = __DIR__.DS.'dsp_widget.php';
               if(is_file($v_widget_path))
               {
                   require $v_widget_path;
               }
           ?>
    </div>
    <!--End .block-->

    <div class="col-md-9 " id="main-page" style="padding-right: 20px;">
        <div class="col-md-12 block">
            <div class="div_title" >
                <div class="title-border-left"></div>
                <div class="title-content">
                    <label class="home" >
                        <a href="<?php echo SITE_ROOT; ?>">
                            <img src="<?php echo CONST_SITE_THEME_ROOT ?>images/home-page.png">
                        </a>
                    </label>   
                    <label >
                        <a href="<?php echo build_url_cq($website_id); ?>"><?php echo __('list question');?></a>
                    </label>
                    <label class="active"><?php echo __('question details'); ?></label>   
                </div>
            </div>
            <!--End title-->
            
                 <div class="col-md-12 block" id="main-content">
                     <div class="col-md-12 block row1">
                         <a href="<?php echo build_url_set_cq($this->website_id); ?>"><img src="<?php echo CONST_SITE_THEME_ROOT ?>images/jpeg.jpg" height="15px"  /><?php echo __('setting question') ?></a>
                         <a href="<?php echo build_url_cq($this->website_id);?>"><img src="<?php echo CONST_SITE_THEME_ROOT ?>images/list.png" height="15x"  /><?php echo __('list question') ?></a>
                     </div>
                     <hr />
                     <div class="col-md-12 block">
                         <div class="title">
                             <img src="<?php echo CONST_SITE_THEME_ROOT ?>images/icon_question.jpg" height="20px"  />
                             <b><?php echo $v_title?></b>
                             <ul>
                                 <li><label><?php echo __('asker'); ?>:&nbsp; <?php echo $v_name; ?></label></li>
                                 <li><label><?php echo __('day asked'); ?>:&nbsp; <?php echo $v_day_asked; ?></label></li>
                                 <li><label><?php echo __('field'); ?>:&nbsp; <?php echo $v_field_name; ?></label></li>
                             </ul>
                         </div>
                         <div class="clear" style="height: 5px"></div>
                         <div class="content-question">
                             <?php echo remove_html_tag(html_entity_decode($v_content));?>
                         </div>
                         <div class="box-answer">
                             <p><img src="<?php echo CONST_SITE_THEME_ROOT ?>images/A.png" height="15px"  />&nbsp;<b><?php echo __('answer');?></b></p>
                             <div class="content">
                                 <?php echo remove_html_tag(html_entity_decode($v_answer));?>
                             </div>
                         </div>
                             <!--End #Main-content-->
                            <div style="float:right">
                                <button type="button" class="btn btn-primary" onclick="history.go(-1)"><?php echo __('back');?></button>
                             </div>
                    <?php if(sizeof($arr_cq_connection) > 0): ?>
                              <div class="col-md-12 block connection">
                                <div class="title"><?php echo __('other questions'); ?></div>
                                <ul>
                                    <?php for($i =0;$i<count($arr_cq_connection);$i++):?>
                                         <?php 
                                            $v_question_id      = $arr_cq_connection[$i]['PK_CQ'];
                                            $v_question_name    = $arr_cq_connection[$i]['C_NAME'];
                                            $v_question_slug    = $arr_cq_connection[$i]['C_SLUG'];
                                            $v_website_id       = $this->website_id;
                                            $v_question_url         = build_url_cq_detail($v_website_id, $v_question_slug, $v_question_id);
                                            echo "<li><a href='$v_question_url'>$v_question_name</a></li>";
                                         ?>
                                    <?php endfor;?>
                                </ul>
                            </div>
                             
                    <?php endif;?>
                     
                 </div>
              
        </div>
    </div>
    <!--End #main-page-->
    </div>
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
