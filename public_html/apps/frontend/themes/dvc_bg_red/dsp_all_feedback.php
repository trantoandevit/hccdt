<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//du lieu header
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$v_website_id                       = $this->website_id; 

$VIEW_DATA['arr_css'] = array('single-page', 'synthesis', 'component', 'breadcrumb');
$VIEW_DATA['arr_script'] = array();


$message = get_post_var('message', '');
$v_name = get_post_var('txt_name', '');
$v_address = get_post_var('txt_address', '');
$v_email = get_post_var('txt_email', '');
$v_title = get_post_var('txt_title', '');
$v_content = get_post_var('txt_content', '');


$v_total_feedback = isset($arr_all_public_feedback[0]['TOTAL_RECORD']) ? $arr_all_public_feedback[0]['TOTAL_RECORD'] : '';
$v_limit_feeback = defined('_CONST_DEFAULT_ROWS_FEEBACK_PAGE') ? _CONST_DEFAULT_ROWS_FEEBACK_PAGE : 5;
$n = ceil($v_total_feedback /$v_limit_feeback );


$filter_page_no = get_request_var('page', 1);
$first_page = 1;
$filter_page_no == 1 ? $previous_page = 1 : $previous_page = $filter_page_no - 1;
$filter_page_no == $n ? $next_page = $n : $next_page = $filter_page_no + 1;
$last_page = $n;

?>
<?php $this->render('dsp_header', $VIEW_DATA, $this->theme_code); ?>
<div class="col-md-12 content" id="single-page">
    <div class="col-md-3  ">
        <?php
         $v_widget_path = __DIR__.DS.'dsp_widget.php';
            if(is_file($v_widget_path))
            {
                require $v_widget_path;
            }
        ?>
    </div>
    <!--End #widget-leftr-->
    <!--danh sach gop y-->
    <div class="col-md-9 ">
        <div class="col-md-12 block">
             <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                    <label  style="float: left;width: 50%"><?php echo __('list feedback') ?></label>
                    <div class="link-list-question">
                       <a href="<?php echo build_url_feedback($this->website_id); ?>">
                            <img src="<?php echo CONST_SITE_THEME_ROOT ?>images/icon-feedback.png" height="15x"  />
                                <?php echo __('send feedback')?>
                        </a>
                    </div>   
                </div>
            </div>
           
        <div class="col-md-12 block" id="main-content">
            <div class="div_feedback_list ">                
                <?php
                for ($i = 0; $i < count($arr_all_public_feedback); $i++):
                    $arr_feedback   = $arr_all_public_feedback[$i];
                    $v_pb_name      = $arr_feedback['C_NAME'];
                    $v_pb_date      = $arr_feedback['C_INIT_DATE'];
                    $v_pb_title     = $arr_feedback['C_TITLE'];
                    $v_pb_content   = $arr_feedback['C_CONTENT'];
                    $v_pb_reply     = $arr_feedback['C_REPLY'];
                    $v_website_name = $arr_feedback['C_WEBSITE_NAME'];
                    ?>
                    <div class="item">
                        <div class="feedback_info">
                             <i><?php echo $v_pb_title;?></i> 
                        </div>
                        <div class="feedback_title">
                            <b><?php echo __('full name')?></b>: &nbsp;   <i><?php echo $v_pb_name ?></i>
                            <br>
                            <b><?php echo __('date submitted');?></b>: &nbsp; <i><?php echo $v_pb_date; ?></i>
                        </div>
                        <div class="feedback_content">
                              <b><?php echo __('content');?></b>: &nbsp; <i><?php echo $v_pb_content; ?></i>
                                  
                        </div>
                        <div style="width: 100%;height: auto;">
                            <a style="float:right;color: #A70206;margin-right: 5px;" onclick="show_reply(<?php echo $i ?>)" href="javascript:void(0)"><?php echo __('view answer') ?></a>
                        </div>
                        <div class="feedback_reply" style="display:none" id="reply_<?php echo $i ?>">
                            <div class="respondents"><?php echo __('result'); ?></div>
                            <div class="content"><?php echo $v_pb_reply ?></div>
                        </div>
                    </div>
                <?php endfor; ?>
                <!--button filter page-->
                <div class="clear">&nbsp;</div>               
                <?php if ($n > 1): ?>
                    <div class="div-filter-page">
                        <?php if (get_request_var('page', 1) != $n): ?>
                            <span class="ButtonNext">
                                <a  href="<?php echo build_url_feedback($v_website_id, $next_page); ?>">
                                    <?php echo __('next page'); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                        &nbsp;&nbsp;&nbsp;

                        <?php if (get_request_var('page', 1) != 1): ?>
                            <span class="ButtonPre">
                                <a href="<?php echo build_url_feedback($v_website_id, $previous_page);?>">
                                    <?php echo __('back'); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <!--end filter page-->
            </div>
            <!--end danh sach gop y-->
        </div>
        <!--End #main-page-->
    </div>
</div>
<script>
    function show_reply(index)
    {
        id_reply = '#reply_'+index;
        $(id_reply).toggle();
    }
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
