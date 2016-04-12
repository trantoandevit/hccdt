<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//du lieu header
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('single-page', 'synthesis', 'component', 'breadcrumb');
$VIEW_DATA['arr_script'] = array();


$message = get_post_var('message', '');
$v_name = get_post_var('txt_name', '');
$v_address = get_post_var('txt_address', '');
$v_email = get_post_var('txt_email', '');
$v_title = get_post_var('txt_title', '');
$v_content = get_post_var('txt_content', '');


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
    <div class="col-md-9" >
        <div class="col-md-12 block" style="border: 1px solid #E5E4E4;border-radius: 4px;border-bottom: none;padding-top: 4px;background: #F5F5F5;height: 42px;border-right: none">
            <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                    <label  style="float: left;width: 50%"><?php echo __('feedback') ?></label>
                    <div class="link-list-question">
                        <a href="<?php echo build_url_feedback($this->website_id,1)?>">
                        <img src="<?php echo CONST_SITE_THEME_ROOT ?>images/list.png" height="15x"  />
                        <?php echo __('list feedback')?>
                    </a>
                    </div>   
                </div>
            </div>
        </div>
        
        <div class="col-md-12 block" id="feedback" style="margin-top: -1px;">
            <div class="clear" style="height: 10px"></div>
            <!--end danh sach gop y-->
            <form name="frmMain" id="frmMain" action="" method="POST" enctype="multipart/form-data" >
                <?php
                session::init();
                echo $this->hidden('controller', $this->get_controller_url());
                echo $this->hidden('hdn_update_method', 'insert_feedback');
                echo $this->hidden('hdn_item_id', 0);
                echo $this->hidden('hdn_item_id_list', '');
                echo $this->hidden('XmlData', '');
                ?>
                <!--header-->
                <div class="feebback-header">
                    <span >
                        <img src="<?php echo CONST_SITE_THEME_ROOT . 'images/contac.jpeg' ?>" style="ImgContactIcon">
                        &nbsp;&nbsp;<?php echo __('general info') ?>
                    </span>
                </div>
                <!--message-->
                <label style="margin: 8px 0px 10px 0px"class="required"><?php echo $message; ?></label>
                <!--ho va ten-->

                <div class="form-group col-md-12 block">
                    <label class="col-md-2 control-label">
                        <?php echo __('full name'); ?><label class="required">(*)</label>
                    </label>
                    <div class="col-md-6">
                        <input  class="form-control" type="textbox" name="txt_name" id="txt_name" value="<?php echo $v_name ?>" size="80"
                                data-allownull="no" data-validate="text"
                                data-name="<?php echo __('full name'); ?>">
                    </div>
                </div>
                <div class="clear" style="height: 10px;"></div>
                <!--dia chi-->

                <div class="form-group col-md-12 block">
                    <label class="col-md-2 control-label">
                        <?php echo __('address') ?> 
                    </label>
                    <div class="col-md-6">
                        <input class="form-control"  type="textbox" name="txt_address" id="txt_address" value="<?php echo $v_address ?>" size="80">
                    </div>
                </div>
                <div class="clear" style="height:10px;"></div>
                <!--emai-->

                <div class="form-group col-md-12 block">
                    <label class="col-md-2 control-label">
                        <?php echo __('email') ?> <label class="required">(*)</label>
                    </label>
                    <div class="col-md-6">
                        <input  class="form-control" type="textbox" name="txt_email" id="txt_email" value="<?php echo $v_email ?>" size="80"
                                data-allownull="no" data-validate="email"
                                data-name="<?php echo __('email'); ?>">
                    </div>
                </div>          
                <div class="clear" style="height:10px;"></div>
                <!--tieu de-->
                <div class="form-group col-md-12 block">
                    <label class="col-md-2 control-label">
                        <?php echo __('title'); ?><label class="required">(*)</label>
                    </label>
                    <div class="col-md-6">
                        <input  class="form-control" type="textbox" name="txt_title" id="txt_title" value="<?php echo $v_title ?>" size="80"
                                data-allownull="no" data-validate="text"
                                data-name="<?php echo __('title') ?>">
                    </div>
                </div>          
                <div class="clear" style="height:10px;"></div>
                <!--content-->
                <div class="form-group col-md-12 block">
                    <label class="col-md-2 control-label">
                        <?php echo __('content') ?>
                    </label>
                    <div class="col-md-6">
                        <textarea class="form-control" name="txt_content" id="txt_content" style="min-height: 134px;" ><?php echo $v_content; ?></textarea>
                    </div>
                </div>       
                <div class="clear" style="height:10px;"></div>
                <!--file dinh kem-->
                <div class="form-group col-md-12 block">
                    <label class="col-md-2 control-label">
                        <?php echo __('attachments'); ?>
                    </label>
                    <div class="col-md-6">
                        <input  type="file" name="file_upload" id="file_upload">
                    </div>
                </div>    
                <div class="clear" style="height:10px;"></div>
                <!--ma xac nhan-->
                <div class="form-group col-md-12 block">
                    <label class="col-md-2 control-label">
                        <?php echo __('verification Code') ?> <label class="required">(*)</label>
                    </label>
                    <div class="col-md-6">
                        <input type="textbox" name="txt_captcha_code" id="txt_captcha_code" size="10" class="txt-style"
                               data-allownull="no" data-validate="text"
                               data-name="<?php echo __('verification Code') ?>"/>
                        &nbsp;
                        <img id="siimage" width="120px" height="40px" 
                             src="<?php echo CONST_SITE_THEME_ROOT . 'captcha/securimage_show.php' ?>" 
                             alt="CAPTCHA Image"/>
                        <a tabindex="-1" style="border-style: none;" href="#" title="Refresh Image" 
                           onclick="document.getElementById('siimage').src = '<?php echo CONST_SITE_THEME_ROOT ?>captcha/securimage_show.php?sid=' + Math.random(); this.blur(); return false">
                            &nbsp;
                            <img width="22px" height="22px" 
                                 src="<?php echo CONST_SITE_THEME_ROOT ?>captcha/images/refresh.png" alt="Reload Image"
                                 onclick="this.blur()" align="bottom" border="0">
                        </a>
                    </div>
                </div>  
                <div class="clear" style="height:10px;"></div>
                <!--button-->
                <div class="form-group col-md-12 block">
                    <label class="col-md-2 control-label">
                        &nbsp;
                    </label>
                    <div class="col-md-6">
                        <input type="button" class="btn btn-info" onclick="btn_update_onclick();" value="Gửi">
                    </div>
                </div> 
                <div class="clear" style="height:10px;"></div>
                <div class="clear" style="height:10px;"></div>
            </form>
        </div>
        <!--End #main-page-->
    </div>
    <div class="clear" style="height: 10px;"></div>
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
