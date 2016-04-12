<?php
$VIEW_DATA['title'] = $this->website_name;
$VIEW_DATA['v_banner'] = $v_banner;
$VIEW_DATA['arr_all_website'] = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('synthesis', 'single-page', 'component', 'breadcrumb');
$VIEW_DATA['arr_script'] = array();

$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

$v_citizen_id           = isset($arr_single_citizen_account['PK_CITIZEN']) ? $arr_single_citizen_account['PK_CITIZEN'] : 0;
$v_username             = isset($arr_single_citizen_account['C_USERNAME']) ? $arr_single_citizen_account['C_USERNAME'] : '';
$v_email                = isset($arr_single_citizen_account['C_EMAIL']) ? $arr_single_citizen_account['C_EMAIL'] : '';
$v_xml_data             = isset($arr_single_citizen_account['C_XML_DATA']) ? $arr_single_citizen_account['C_XML_DATA'] : '<root></root>';
$v_organ                = isset($arr_single_citizen_account['C_ORGAN']) ? $arr_single_citizen_account['C_ORGAN'] : '';
$v_status               = isset($arr_single_citizen_account['C_STATUS']) ? $arr_single_citizen_account['C_STATUS'] : NULL;
$v_email_new_confirm    = isset($arr_single_citizen_account['C_EMAIL_CONFIRM']) ? $arr_single_citizen_account['C_EMAIL_CONFIRM'] : NULL;

@$dom = simplexml_load_string($v_xml_data, 'SimpleXMLElement', LIBXML_NOCDATA);
?>

<!--validate-->
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/additional-methods.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery_validate_vi.js" type="text/javascript"></script>


<div class="col-md-12"> 
    <div class="col-md-12 content">
    <div class="clear" style="height: 10px"></div>
    <div class="col-md-12 block register" id="single-page">
        <div class="div_title_bg-title-top"></div>
        <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                    <label > 
                        <?php echo __('account detail') ?>
                    </label>
                </div>
        </div>
        
        <?php if ($v_status == 1): ?>
            <div class="col-md-12">
                <?php if (trim($v_email_new_confirm) != ''): ?>
                    <div class="col-md-12" id="box-success-send-email">
                        <center>
                            <h3>XÁC NHẬN ĐỊA CHỈ EMAIL</h3>
                            <i style="margin: 5px auto;display: block;font-size: 1.3em">Email của bạn đã được yêu cầu thay đổi<br/>
                                Vui lòng vào hòm thư mới để xác nhận thay đổi
                            </i>
                            <b><?php echo $v_email_new_confirm ?></b> 
                            <h4 id="email-trigger"></h4>
                            <div style="margin: 5px auto; color: #009600" id="message-send-code-trigger"></div>
                            <button onclick="send_email_activation_code(this)" id="send-trigger" user-name="<?php echo $v_username; ?>" type="button">Gửi lại link kích hoạt</button>
                            | <a id="back-home" onclick="fc_destroy_change_email(this)"  user-name="<?php echo $v_username; ?>" href="javascript:void(0)">Hủy thay đổi email</a>
                            <br/>
                            <div id="loading-send-code"></div>
                            <br/>
                        </center>
                    </div>
                <?php endif; ?>
                <form class="form-horizontal" action="<?php echo SITE_ROOT ?>do_upate_citizen_account" style="margin: 10px;" id="frmMain" method="POST">
                    <?php
                    echo $this->hidden('hdn_citizen_id', $v_citizen_id);
                    ?>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 block">
                                <label class="col-md-3 block control-label"><?php echo __('username') ?>:</label>
                                <div class="col-md-3" >
                                    <input disabled="true" type="text" class="form-control" value="<?php echo $v_username; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End username-->

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 block">
                                <label class="col-md-3  block control-label"><?php echo __('new password') ?>:</label>
                                <div class="col-md-3 " >
                                    <input type="password" name="txt_new_password" id="txt_new_password" 
                                           class="form-control" 
                                           minlength="6">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End new password-->

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 block">
                                <label class="col-md-3  block control-label"><?php echo __('confirm new password') ?>:</label>
                                <div class="col-md-3 " >
                                    <input type="password" name="txt_confirm_new_password" id="txt_confirm_new_password" 
                                           onblur="check_confirm_password_onblur()" 
                                           class="form-control" 
                                           minlength="6" >
                                </div>
                                <div class="col-md-3" id="pass-error-confirm">
                                    <label name="txt_confirm_new_password" id="confirm_pass_check" class="required"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End comfirm new password-->

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 block">
                                <label class="col-md-3  block control-label"><?php echo __('current password') ?> <span class="required">(*)</span>:</label>
                                <div class="col-md-3 " >
                                    <input type="password" name="txt_current_password" id="txt_current_password" 
                                           onblur="check_confirm_password_onblur()" 
                                           class="form-control" 
                                           required="true">
                                </div>
                                <div class="col-md-3">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End current password-->
                    <?php if ($v_organ == 0): // Ca nhan ?>
                        <?php
                        $v_gender = $v_identity_card = $v_birth_day = $v_address = $v_tel = $v_name = null;
                        if ($dom)
                        {
                            $obj_value = $dom->xpath('//item');

                            $v_tel              = isset($obj_value[0]->tel) ? (string) $obj_value[0]->tel : '';
                            $v_name             = isset($obj_value[0]->name) ? (string) $obj_value[0]->name : '';
                            $v_address          = isset($obj_value[0]->address) ? (string) $obj_value[0]->address : '';
                            $v_birth_day        = isset($obj_value[0]->birthday) ? (string) $obj_value[0]->birthday : '';
                            $v_birth_day        = jwDate::ddmmyyyy_to_yyyymmdd($v_birth_day);
                            $v_identity_card    = isset($obj_value[0]->identity_card) ? (string) $obj_value[0]->identity_card : '';
                            $v_gender           = isset($obj_value[0]->gender) ? (string) $obj_value[0]->gender : '';
                            '';
                        }
                        ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 block">
                                    <label class="col-md-3  block control-label"><?php echo __('full name') ?> <span class="required">(*)</span>:</label>
                                    <div class="col-md-7 " >
                                        <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_name; ?>"
                                               onblur="check_confirm_password_onblur()" 
                                               class="form-control" 
                                               required="true">
                                    </div>
                                    <div class="col-md-3"></div>
                                </div>
                            </div>
                        </div>
                        <!--End full name-->

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 block">
                                    <label class="col-md-3  block control-label"><?php echo __('birthday') ?> <span class="required">(*)</span>:</label>
                                    <div class="col-md-3 " >
                                        <input type="text" name="txt_birth_day" id="txt_birth_day" 
                                               value="<?php echo $v_birth_day ?>"
                                               class="form-control" 
                                               required>
                                    </div>
                                    <div class="col-md-4 " >
                                        <label class="control-label">
                                            <i>Định dạng(dd-mm-yyyy)</i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--End birthday-->

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" >
                                    <div class="col-md-12 block">
                                        <label class="col-md-6  block control-label"><?php echo __('gender') ?> <span class="required">(*)</span>:</label>
                                        <div class="col-md-6" >
                                            <select  style="width: 50%" name="sel_gender" style="width: 30%" id="sel_gender" required="true" class="form-control">
                                                <option value="0" <?php echo ($v_gender == '0') ? 'selected' : ''; ?> > Nam</option>
                                                <option value="1" <?php echo ($v_gender == '1') ? 'selected' : ''; ?>> Nữ</option>
                                            </select>
                                        </div>                        
                                    </div>
                                </div>
                                <!--End gender-->
                                <div class="col-md-6" >
                                    <div class="col-md-12 block">
                                        <label class="col-md-2  block control-label"><?php echo __('identity card') ?> <span class="required">(*)</span>:</label>
                                        <div class="col-md-6 " >
                                            <input type="text" name="txt_identity_card" id="txt_identity_card" 
                                                   value="<?php echo $v_identity_card ?>"
                                                   class="form-control" 
                                                   required>
                                        </div>                        
                                    </div>
                                </div>
                                <!--End identity card-->
                            </div>
                        </div>
                        <!--End gender and identity card-->
                    <?php else: // To chuc  ?>
                        <?php
                        $v_gender = $v_identity_card = $v_birth_day = $v_address = $v_tel = $v_name = null;
                        $v_tel    = $v_name = $v_address = $v_company_perfix = $v_tax_code = $v_boss = $v_boss_position = NULL;

                        if ($dom)
                        {
                            $obj_value              = $dom->xpath('//item');
                            $v_tel                  = isset($obj_value[0]->tel) ? (string) $obj_value[0]->tel : '';
                            $v_name                 = isset($obj_value[0]->name) ? (string) $obj_value[0]->name : '';
                            $v_address              = isset($obj_value[0]->address) ? (string) $obj_value[0]->address : '';
                            $v_tax_code             = isset($obj_value[0]->tax_code) ? (string) $obj_value[0]->tax_code : '';
                            $v_company_perfix       = isset($obj_value[0]->company_prefix) ? (string) $obj_value[0]->company_prefix : '';
                            $v_company_name_en      = isset($obj_value[0]->name_en) ? (string) $obj_value[0]->name_en : '';
                            $v_business_registers   = isset($obj_value[0]->business_registers) ? (string) $obj_value[0]->business_registers : '';
                            $v_business_date        = isset($obj_value[0]->business_date) ? (string) $obj_value[0]->business_date : '';
                            $v_business_date        = jwDate::ddmmyyyy_to_yyyymmdd($v_business_date);
                            $v_granting_agencies    = isset($obj_value[0]->granting_agencies) ? (string) $obj_value[0]->granting_agencies : '';
                            $v_boss                 = isset($obj_value[0]->boss) ? (string) $obj_value[0]->boss : '';
                            $v_boss_position        = isset($obj_value[0]->boss_position) ? (string) $obj_value[0]->boss_position : '';
                        }
                        ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 block">
                                    <label class="col-md-3  block control-label"><?php echo __('Tên công ty') ?> <span class="required">(*)</span>:</label>
                                    <div class="col-md-7 " >
                                        <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_name; ?>"
                                               onblur="check_confirm_password_onblur()" 
                                               class="form-control" 
                                               required="true">
                                    </div>
                                    <div class="col-md-3"></div>
                                </div>
                            </div>
                        </div>
                        <!--End company name-->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 block">
                                    <label class="col-md-3  block control-label"><?php echo __('Tên tiếng anh') ?>:</label>
                                    <div class="col-md-7 " >
                                        <input type="text" name="txt_name_en" id="txt_name_en" value="<?php echo $v_company_name_en; ?>"
                                               onblur="check_confirm_password_onblur()" 
                                               class="form-control">
                                    </div>
                                    <div class="col-md-3"></div>
                                </div>
                            </div>
                        </div>
                        <!--End company name en-->

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 block">
                                    <label class="col-md-3  block control-label"><?php echo __('Sổ đăng ký kinh doanh') ?><span class="required">(*)</span>:</label>
                                    <div class="col-md-7 " >
                                        <input type="text" name="txt_business_registers" id="txt_business_registers"
                                               value="<?php echo $v_business_registers ?>"
                                               class="form-control"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end so dang ky-->

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 block">
                                    <label class="col-md-3  block control-label"><?php echo __('Ngày cấp') ?><span class="required">(*)</span>:</label>
                                    <div class="col-md-3 " >
                                        <input type="text" name="txt_date" id="txt_date" 
                                               value="<?php echo $v_business_date ?>"
                                               date-format="dd/mm/yyyy"
                                               class="form-control"
                                               required>
                                    </div>
                                    <div class="col-md-4 " >
                                        <label class="control-label">
                                            <i>Định dạng(dd-mm-yyyy)</i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end ngay cap-->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 block">
                                    <label class="col-md-3  block control-label"><?php echo __('Cơ quan cấp') ?><span class="required">(*)</span>:</label>
                                    <div class="col-md-7 " >
                                        <input type="text" name="txt_granting_agencies" id="txt_granting_agencies"
                                               value="<?php echo $v_granting_agencies ?>"
                                               class="form-control"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end co quan cap-->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" >
                                    <div class="col-md-12 block">
                                        <label class="col-md-6  block control-label"><?php echo __('Tên viết tắt') ?> <span class="required">(*)</span>:</label>
                                        <div class="col-md-6" >
                                            <input type="text" name="txt_company_perfix" id="txt_company_perfix" 
                                                   value="<?php echo $v_company_perfix ?>"
                                                   class="form-control" 
                                                   required>
                                        </div>                        
                                    </div>
                                </div>
                                <!--End gender-->
                                <div class="col-md-6" >
                                    <div class="col-md-12 block">
                                        <label class="col-md-2  block control-label"><?php echo __('tax code') ?> <span class="required">(*)</span>:</label>
                                        <div class="col-md-6 " >
                                            <input type="text" name="txt_tax_code" id="txt_tax_code" 
                                                   value="<?php echo $v_tax_code ?>"
                                                   class="form-control" 
                                                   required>
                                        </div>                        
                                    </div>
                                </div>
                                <!--End tax code-->
                            </div>
                        </div>
                        <!--End company perfix and tax code-->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-12 block">
                                        <label class="col-md-6  block control-label"><?php echo __('Người đại diện') ?> <span class="required">(*)</span>:</label>
                                        <div class="col-md-6 " >
                                            <input type="text" name="txt_boss" id="txt_boss"
                                                   value="<?php echo $v_boss ?>"
                                                   class="form-control" 
                                                   required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-12 block">
                                        <label class="col-md-2  block control-label"><?php echo __('Chức vụ') ?> <span class="required">(*)</span>:</label>
                                        <div class="col-md-6 " >
                                            <input type="text" name="txt_position" id="txt_position"
                                                   value="<?php echo $v_boss_position ?>"
                                                   class="form-control" 
                                                   required >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--End nguoi dai dien and chuc vu -->

                    <?php endif; ?>
                    <div class="form-group">
                        <div class="row" >
                            <div class="col-md-6" >
                                <div class="col-md-12 block">
                                    <label class="col-md-6  block control-label"><?php echo __('email') ?> <span class="required">(*)</span>:</label>
                                    <div class="col-md-6 " >
                                        <input type="email" name="txt_email" id="txt_email" 
                                               value="<?php echo $v_email ?>"
                                               class="form-control" 
                                               data-rule="email"
                                               required>
                                    </div>                        
                                </div>
                            </div>
                            <!--End email-->
                            <div class="col-md-6" >
                                <div class="col-md-12 block">
                                    <label class="col-md-2  block control-label"><?php echo __('tel') ?> <span class="required">(*)</span>:</label>
                                    <div class="col-md-6 " >
                                        <input type="text" name="txt_tel" id="txt_tel" 
                                               value="<?php echo $v_tel; ?>"
                                               class="form-control" 
                                               data-rule="tel"
                                               required>
                                    </div>                        
                                </div>
                            </div>
                            <!--End tel-->
                        </div>
                    </div>
                    <!--End email and tel-->

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 block">
                                <label class="col-md-3  block control-label"><?php echo __('address') ?> <span class="required">(*)</span>:</label>
                                <div class="col-md-7 " >
                                    <input type="text" name="txt_address" id="txt_address" 
                                           value="<?php echo $v_address ?>"
                                           class="form-control" 
                                           required>
                                </div>                        
                            </div>
                        </div>
                    </div>
                    <!--End address-->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 block">
                                <label class="col-md-3 control-label">&nbsp;</label>
                                <div class="col-md-9 " >
                                    <button type="submit" class="btn btn-success"><?php echo __('update') ?></button>
                                    <button type="button" class="btn btn-default" onclick="javascript:history.back()"><?php echo __('back') ?></button>
                                    <div id="loading"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>            

            </div>
            <?php
        elseif ($v_status == -1):
            ?>
            <div class="col-md-12" id="box-success-send-email">
                <center>
                    <h1>Tài khoản của bạn chưa được kích hoạt</h1>
                    <h2>XÁC NHẬN ĐỊA CHỈ EMAIL</h2>
                    <i style="margin: 5px auto;display: block;font-size: 1.3em">Để xác nhận tài khoản, vui lòng kiểm tra hộp email của bạn.</i>
                    <h4 id="email-trigger"></h4>
                    <div style="margin: 5px auto; color: #009600" id="message-send-code-trigger"></div>
                    <button onclick="send_code_trigger(this)" id="send-trigger" user-name="<?php echo $v_username; ?>" type="button">Gửi lại link kích hoạt</button>
                    | <a id="back-home" href="<?php echo SITE_ROOT ?>">về trang chủ</a>
                    <br/>
                    <div id="loading-send-code"></div>
                    <br/>
                </center>
            </div>
        <?php elseif ($v_status == 0): ?>
            <h2 style="color: red">Tài khoản của bạn đã bị khóa <a href="<?php echo SITE_ROOT ?>">Quay lại trang chủ</a></h2>
        <?php endif; ?>

    </div>
    <div class="col-md-3 block" id="left-sidebar">
        <?php
        $v_widget_path = __DIR__ . DS . 'dsp_widget.php';
        if (is_file($v_widget_path))
        {
            require $v_widget_path;
        }
        ?>
    </div>
    <!--End #left-sidebar-->
</div>
</div>
<div class="clear" style="height: 10px"></div>
<script>
    $(document).ready(function(){
        $('#txt_birth_day,#txt_date').datepicker({
            format: 'dd-mm-yyyy'
        });
    });
function check_confirm_password_onblur()
{
    if ($('#txt_new_password').val() != '' || $('#txt_confirm_new_password').val() != '')
    {
        $('#txt_new_password').attr('required', 'true');
        $('#txt_confirm_new_password').attr('required', 'true');
        if ($('#txt_new_password').val() == $('#txt_confirm_new_password').val())
        {
            var html = '<img id="img_confirm_pass" src="<?php echo $this->image_directory . 'AcceptButton.gif'; ?>"/>'
            $('#confirm_pass_check').html('');
            $('#img_confirm_pass').remove();
            $('#txt_confirm_new_password').parents('.form-group').find('#pass-error-confirm').append(html);
        }
        else if ($('#txt_new_password').val() != '')
        {
            $('#confirm_pass_check').html('<?php echo __("check confirm password"); ?>');
            $('#img_confirm_pass').remove();
        }
    }
    else
    {
        $('#txt_new_password').removeAttr('required');
        $('#txt_confirm_new_password').removeAttr('required');
    }

}

/**
 * Gui yeu cau kich hoat qua mail
 */
function send_code_trigger(selector)
{
    var user_name = $(selector).attr('user-name') || '';
    if (user_name.trim == '')
        return false;

    var url = '<?php echo SITE_ROOT ?>send_code_trigger';
    $.ajax({
        url: url,
        type: 'POST',
        beforeSend: function()
        {
            var img = '<center><img src="<?php echo SITE_ROOT; ?>public/images/loading.gif"/></center>';
            $('#loading-send-code').html(img);
            $(selector).attr('disabled', 'disabled');
            $(selector).addClass('active')
        },
        error: function()
        {
            alert('Đã xảy ra lỗi không thể gửi yêu cầu. Vui lòng thử lại vào lúc khác');
            $('#loading-send-code').html('');
            $(selector).removeAttr('disabled');
            $(selector).removeClass('active');
        },
        data: {username: user_name},
        success: function(data)
        {
            $('#loading-send-code').html('');
            if (parseInt(data) == 1)
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

/**
 * Gui yeu cau kich hoat qua mail
 */
function send_email_activation_code(selector)
{
    var user_name = $(selector).attr('user-name') || '';
    if (user_name.trim == '')
        return false;

    var url = '<?php echo build_url_trigger_change_email('', '', true, 0); ?>';
    $.ajax({
        url: url,
        type: 'POST',
        beforeSend: function()
        {
            var img = '<center><img src="<?php echo SITE_ROOT; ?>public/images/loading.gif"/></center>';
            $('#loading-send-code').html(img);
            $(selector).attr('disabled', 'disabled');
            $(selector).addClass('active')
        },
        error: function()
        {
            alert('Đã xảy ra lỗi không thể gửi yêu cầu. Vui lòng thử lại vào lúc khác');
            $('#loading-send-code').html('');
            $(selector).removeAttr('disabled');
            $(selector).removeClass('active');
        },
        data: {username: user_name},
        success: function(data)
        {
            $('#loading-send-code').html('');
            if (parseInt(data) == 1)
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
// Huy thay doi email
function fc_destroy_change_email(selector)
{
    var user_name = $(selector).attr('user-name') || '';
    if (user_name.trim == '')
        return false;

    var url = '<?php echo build_url_trigger_change_email($v_username, $v_citizen_id, FALSE, FALSE); ?>';
    $.ajax({
        url: url,
        type: 'POST',
        beforeSend: function()
        {
            var img = '<center><img src="<?php echo SITE_ROOT; ?>public/images/loading.gif"/></center>';
            $('#loading-send-code').html(img);
            $(selector).attr('disabled', 'disabled');
            $(selector).addClass('active')
        },
        error: function()
        {
            alert('Đã xảy ra lỗi không thể gửi yêu cầu. Vui lòng thử lại vào lúc khác');
            $('#loading-send-code').html('');
            $(selector).removeAttr('disabled');
            $(selector).removeClass('active');
        },
        data: {username: user_name},
        success: function(data)
        {
            $('#loading-send-code').html('');
            if (parseInt(data) == 1)
            {
                location.reload();
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
