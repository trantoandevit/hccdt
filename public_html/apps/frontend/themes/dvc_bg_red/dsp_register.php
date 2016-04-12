<?php
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css']               = array('synthesis','single-page','component','breadcrumb');
$VIEW_DATA['arr_script']            = array();
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);
$v_username  = get_request_var('txt_username','');
$v_email     = get_request_var('txt_email','');
$v_confirm_email = get_request_var('txt_confirm_email','');
?>
<!-- Upload -->
<script src="<?php echo SITE_ROOT?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>

<!--validate-->
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/additional-methods.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery_validate_vi.js" type="text/javascript"></script>
<style>
    #box-success-send-email
    {
        height: 400px;
        margin-top: 50px;
    }
    
</style>

<div class="col-md-12 content" style="margin-top: 10px;">
    <!--End #left-sidebar-->
    <div class="col-md-12" id="content" style="">
        <div class="div_title_bg-title-top" style="border: 1px solid #E5E4E4; border-radius: 4px 4px 0 0;"></div>
        <div class="div_title">
            <div class="title-border-left"></div>
            <div class="title-content">
                <label><?php echo __('register account')?></label>
            </div>
        </div>
        <div class="clear"></div>
        <div class="col-md-12 citizen-register" style="margin-top: -7px">
            <form class="form-horizontal" style="margin: 10px;" id="frmMain" method="POST">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 block">
                            <label class="col-md-3 block control-label"><?php echo __('username')?> <span class="required">(*)</span>:</label>
                            <div class="col-md-6" >
                                <input type="text" name="txt_username" id="txt_username" 
                                       value="<?php echo $v_username; ?>"
                                       class="form-control" 
                                       minlength="5" maxlength="30" required > 
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 block">
                            <label class="col-md-3  block control-label"><?php echo __('password')?> <span class="required">(*)</span>:</label>
                            <div class="col-md-6 " >
                                <input type="password" name="txt_password" id="txt_password" onchange="txt_password_onchange()"
                                       class="form-control" 
                                       minlength="6"  required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 block">
                            <label class="col-md-3  block control-label"><?php echo __('confirm password')?> <span class="required">(*)</span>:</label>
                            <div class="col-md-6 " >
                                <input type="password" name="txt_confirm_password" id="txt_confirm_password" 
                                       onblur="check_confirm_password_onblur()" 
                                       class="form-control" 
                                       minlength="6" required>
                            </div>
                            <div class="col-md-3">
                                <label name="confirm_pass_check" id="confirm_pass_check" class="required"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 block">
                            <label class="col-md-3  block control-label"><?php echo __('email')?> <span class="required">(*)</span>:</label>
                            <div class="col-md-6 " >
                                <input type="email" name="txt_email" id="txt_email" 
                                        value="<?php echo $v_email?>"
                                       class="form-control" 
                                       data-rule="email"
                                       required>
                            </div>                        
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 block">
                            <label class="col-md-3  block control-label"><?php echo __('confirm')?> email<span class="required">(*)</span>:</label>
                            <div class="col-md-6 " >
                                <input type="email" name="txt_confirm_email" id="txt_confirm_email" 
                                        value="<?php echo $v_confirm_email?>"
                                        onblur="check_confirm_email_onblur()"
                                       class="form-control" 
                                       data-rule="email"
                                       required>
                            </div>
                            <div class="col-md-3">
                                <label name="confirm_email_check" id="confirm_email_check" class="required"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 block">
                            <label for="recaptcha_response_field" class="col-md-3 control-label"><?php echo __('verification Code')?> <span class="required">(*)</span>:</label>
                            <div class="col-md-6" >
                                <?php echo recaptcha_get_html(_CONST_RECAPCHA_PUBLIC_KEY) ?>
                                <label id="error_capcha"  style="display: none;color: red; width:450px">Bạn chưa nhập mã xác nhận hoặc mã xác nhận chưa đúng!</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 block">
                            <label class="col-md-3 control-label">&nbsp;</label>
                            <div class="col-md-9 btn-send-register" >
                                <button type="submit" class="btn btn-warning"><?php echo __('update')?></button>
                                <button type="button" class="btn btn-default" onclick="javascript:history.back()"><?php echo __('back')?></button>
                                <div id="loading"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-md-12" id="box-success-send-email" style="display: none">
                <div class="clear" style="height: 10px"></div>
                <center>
                    <h3>XÁC NHẬN ĐỊA CHỈ EMAIL</h3>
                    <i style="margin: 5px auto;display: block;font-size: 1.3em">Để xác nhận tài khoản, vui lòng kiểm tra hộp email của bạn.</i>
                    <h4 id="email-trigger"></h4>
                    <div style="margin: 5px auto; color: #009600" id="message-send-code-trigger"></div>
                    <button onclick="send_code_trigger(this)" id="send-trigger" user-name="" type="button">Gửi lại link kích hoạt</button>
                    | <a id="back-home" href="<?php echo SITE_ROOT ?>">về trang chủ</a>
                    <br/>
                    <div id="loading-send-code"></div>
                    <br/>
                </center>
            </div>
        </div>
    </div>
    <div class="clear" style="height: 10px"></div>
</div>

<script>
     document.getElementById("frmMain").reset();
     
     
    $(document).ready(function() {
        
        $('#frmMain').validate({
            submitHandler: function(form) {
                                    do_register();
                                    return false;
                                 }
        });
    });
    
    function txt_password_onchange()
    {
        $('#txt_confirm_password').val('');
    }
    //Gui thong tin dang ky
    function do_register() 
    {
        var capcha = $('#recaptcha_response_field').val() || '';
        if(capcha.length == '')
        {
             $('#error_capcha').show();
             return false;
        }
        else
        {
            $('#error_capcha').hide();
        }
        var url = '<?php echo SITE_ROOT;?>' + 'do_register';
        
        $.ajax({
            type: "POST",
            url: url,
            data: $('#frmMain').serialize(),
            beforeSend: function() 
            {
                     var img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $('#loading').html(img);     
                     $('#frmMain .btn-send-register button').attr('disabled','disabled');
            },
            success: function(data){
                $('#frmMain .btn-send-register button').removeAttr('disabled');
                if(typeof(data) != 'undefined' && data.length >0)
                {
                    if(parseInt(data) != '1')
                     {
                         if(data == 'capcha_error')
                            {
                                $('#error_capcha').show();

                            }
                            else
                            {
                                alert(data);
                                $('#frmMain').show();
                            }
                            $('#recaptcha_reload').trigger("click");
                            $('#loading').html('');
                            return false;
                     }
                     else
                     {
                         $('#box-success-send-email').find('#email-trigger').text($('#txt_email').val());
                         $('#box-success-send-email').find('#send-trigger').attr('user-name',$('#frmMain #txt_username').val());
                         $('#box-success-send-email').show();
                         $('#frmMain').remove();
                     }
                     $('#loading').html('');
                }
            }
          });
    }
    function check_confirm_password_onblur()
    {
        if($('#frmMain #txt_password').val() == $('#frmMain #txt_confirm_password').val())
        {
            var html = '<img id="img_confirm_pass" src="<?php echo $this->image_directory.'AcceptButton.gif';?>"/>'
            $('#confirm_pass_check').html('');
            $('#img_confirm_pass').remove();
            $('#txt_confirm_password').parent().append(html);
        }
        else if($('#txt_confirm_password').val() != '')
        {
            $('#confirm_pass_check').html('<?php echo __("check confirm password");?>');
            $('#img_confirm_pass').remove();
        }
    }
    function check_confirm_email_onblur()
    {
        if($('#txt_email').val() == $('#txt_confirm_email').val() && $('#txt_email').val() != '')
        {
            var html = '<img id="img_confirm_email" src="<?php echo $this->image_directory.'AcceptButton.gif';?>"/>'
            $('#confirm_email_check').html('');
            $('#img_confirm_email').remove();
            $('#txt_confirm_email').parent().append(html);
        }
        else if($('#txt_confirm_email').val().trim!= '')
        {
            $('#confirm_email_check').html('<?php echo __("check confirm email");?>');
            $('#img_confirm_email').remove();
        }
    }
    
    /**
     * Gui yeu cau kich hoat qua mail
     */
    function send_code_trigger(selector) 
    {
        var user_name     =  $(selector).attr('user-name') || '';
        if(user_name.trim == '') return false;
        
        var url  = '<?php echo SITE_ROOT?>send_code_trigger';
        $.ajax({
            url:url,
            type: 'POST',     
            data:{username:user_name},
            beforeSend: function() 
            {
                     var img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $('#loading-send-code').html(img);    
                     $(selector).attr('disabled','disabled');
                     $(selector).addClass('active')
            },
            success:function(data)
            {
                $('#loading-send-code').html('');
                if(parseInt(data) == 1)
                 {
                     $('#message-send-code-trigger').html('Bạn đã gửi link kích hoạt thành công.');
                     $('#message-send-code-trigger').show();
                 }
                 else
                 {
                     $('#message-send-code-trigger').html('Email không tồn tại hoặc quá trình gửi đã xảy ra lỗi. Xin vui lòng thực hiện lại.');
                     $(selector).removeAttr('disabled');
                     $(selector).removeClass('active');
                 }
            }
        });
    }
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
