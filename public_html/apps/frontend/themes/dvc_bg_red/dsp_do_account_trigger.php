<?php
$VIEW_DATA['title'] = $this->website_name;
$VIEW_DATA['v_banner'] = $v_banner;
$VIEW_DATA['arr_all_website'] = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('synthesis', 'single-page', 'component', 'breadcrumb');
$VIEW_DATA['arr_script'] = array();
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

$v_username       = isset($_GET['username']) ? $_GET['username'] : '';
$v_name           = get_request_var('txt_name', '');
$v_birthday       = get_request_var('txt_birthday', '');
$v_gender         = get_request_var('txt_gender', -1);
$v_tel            = get_request_var('txt_tel', '');
$v_address        = get_request_var('txt_address', '');
$v_tax_code       = get_request_var('txt_tax_code', '');
$v_company_perfix = get_request_var('txt_company_perfix', '');
$v_code           = get_request_var('txt_code', '');
?>
<!--validate-->
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/additional-methods.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery_validate_vi.js" type="text/javascript"></script>
<div class="clear" style="height: 10px"></div>
<div class="col-md-12 content" id="warpper-trigger" style="min-height: 400px;">
    <div class="col-md-12" style="padding-left: 10px;">
        <div class="div_title_bg-title-top"></div>
            <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                    <label><?php echo __('account') ?></label>
                    <label class="active"> 
                        <?php echo __('account trigger') ?>
                    </label>        
                </div>
                <div class="title-border-right"></div>
            </div>
        <div class="col-md-12 block" >
            <form class="form-horizontal" style="margin: 10px;" id="frmMain" method="POST">
                <?php echo $this->hidden('hdn_username', $v_username); ?>
                <?php echo $this->hidden('hdn_update_trigger_method', 'update_account_trigger'); ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 block">
                            <label class="col-md-3  block control-label">&nbsp;Cơ quan <span class="required">(*)</span>:</label>
                            <div class="col-md-6" >
                                <label class="col-md-3">
                                    <input required="true" onclick="rd_organ_onclick(this)" type="radio" name="rd_organ" id="rd_organ_single" value="0">
                                    &nbsp;Cá nhân
                                </label>
                                <label class="col-md-3">
                                    <input required="true" onclick="rd_organ_onclick(this)" type="radio" name="rd_organ" id="rd_organ" value="1">
                                    &nbsp;Tổ chức
                                </label>
                                <label for="rd_organ" class="error col-md-12 block" style="display: none;"></label>
                            </div>

                        </div>
                    </div>
                </div>
                <div id="content-form">

                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 block">
                            <label class="col-md-3 control-label">&nbsp;</label>
                            <div class="col-md-9 " >
                                <button type="submit" class="btn btn-warning"><?php echo __('update') ?></button>
                                <button type="button" class="btn btn-default" onclick="javascript:history.back()"><?php echo __('back') ?></button>
                                <div id="loading"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>            
        </div>
    </div>
    <div id="content-form-tmp" style="display: none">
        <div id="box-organ-single" >
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3 block control-label"><?php echo __('full name') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6" >
                            <input type="text" name="txt_name" id="txt_username" 
                                   value="<?php echo $v_name; ?>"
                                   class="form-control"   required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3  block control-label"><?php echo __('address') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6 " >
                            <input type="text" name="txt_address" id="txt_address" 
                                   value="<?php echo $v_address ?>"
                                   class="form-control"  required>
                        </div>
                    </div>
                </div>
            </div>
            <!--End #address-->

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3  block control-label"><?php echo __('birthday') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6 block">
                            <div class="col-md-5">
                                <input type="date" name="txt_birthday" id="txt_birthday" 
                                       value="<?php echo $v_birthday ?>"
                                       class="form-control" 
                                       data-rule="date"
                                       required>
                            </div>
                            <div class="col-md-7 block">
                                <label class="col-md-4  block control-label"><?php echo __('identity card') ?> <span class="required">(*)</span>:</label>
                                <div class="col-md-8">
                                    <input type="text" name="txt_identity_card" id="txt_identity_card" 
                                           class="form-control" 
                                           data-rule="identity card"
                                           required>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3  block control-label"><?php echo __('gender') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6 block">
                            <div class="col-md-5">
                                <select name="sel_gender" id="sel_gender" required="true"  class="form-control">
                                    <option value="">-- <?php echo __('gender') ?> --</option>
                                    <option value="0" <?php if ($v_gender == 0) echo 'selected' ?> > <?php echo __('male') ?> </option>
                                    <option value="1" <?php if ($v_gender == 1) echo 'selected' ?> > <?php echo __('female') ?> </option>
                                </select>
                            </div>
                            <!--End #gender-->
                            <div class="col-md-7 block">
                                <label class="col-md-4  block control-label"><?php echo __('tel') ?> <span class="required">(*)</span>:</label>
                                <div class="col-md-8">
                                    <input type="text" name="txt_tel" id="txt_tel"
                                           value="<?php echo $v_tel ?>"
                                           class="form-control" 
                                           data-rule="tel"
                                           required>
                                </div>
                            </div>
                            <!--End #tel-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3 control-label"><?php echo __('verification Code') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6" >
                            <input type="text" name="txt_code" id="txt_code" 
                                   value="<?php echo $v_code ?>"
                                   class="form-control" 
                                   required>
                        </div>
                    </div>
                </div>
            </div>
            <!--End #code-->
        </div>
        <!--End #box-organ-signle-->

        <div id="box-organ" >
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3 block control-label"><?php echo __('organization’s name ') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6" >
                            <input type="text" name="txt_name" id="txt_name" 
                                   value="<?php echo $v_name; ?>"
                                   class="form-control" 
                                   minlength="5" maxlength="30" required>
                        </div>
                    </div>
                </div>
            </div>
            <!--End #name-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3 block control-label"><?php echo __('organization’s name english') ?> :</label>
                        <div class="col-md-6" >
                            <input type="text" name="txt_name_en" id="txt_name_en" 
                                   value="<?php echo $v_name; ?>"
                                   class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <!--End #name en-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3  block control-label"><?php echo __('company perfix') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6 " >
                            <input type="text" name="txt_company_perfix" id="txt_company_perfix"
                                   value="<?php echo $v_company_perfix ?>"
                                   class="form-control" 
                                   required>
                        </div>
                    </div>
                </div>
            </div>
            <!--name prefix-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3  block control-label"><?php echo __('Sổ đăng ký kinh doanh') ?><span class="required">(*)</span>:</label>
                        <div class="col-md-6 " >
                            <input type="text" name="txt_business_registers" id="txt_business_registers"
                                   value=""
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
                        <div class="col-md-6 " >
                            <input type="date" name="txt_date" id="txt_date"
                                   value=""
                                   class="form-control"
                                   required>
                        </div>
                    </div>
                </div>
            </div>
            <!--end ngay cap-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3  block control-label"><?php echo __('Cơ quan cấp') ?><span class="required">(*)</span>:</label>
                        <div class="col-md-6 " >
                            <input type="text" name="txt_granting_agencies" id="txt_granting_agencies"
                                   value=""
                                   class="form-control"
                                   required>
                        </div>
                    </div>
                </div>
            </div>
            <!--end co quan cap-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3  block control-label"><?php echo __('tel') ?><span class="required">(*)</span>:</label>
                        <div class="col-md-6 " >
                            <input type="text" name="txt_tel" id="txt_tel" 
                                   value="<?php echo $v_tel ?>"
                                   class="form-control"  
                                   data-rule="tel"
                                   required>
                        </div>
                    </div>
                </div>
            </div>
            <!--End #tel-->

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3  block control-label"><?php echo __('tax code') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6 " >
                            <input type="text" name="txt_tax_code" id="txt_tax_code"
                                   value="<?php echo $v_tax_code ?>"
                                   class="form-control" 
                                   required>
                        </div>
                    </div>
                </div>
            </div>
            <!--End #tax_code-->

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3  block control-label"><?php echo __('address') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6 " >
                            <input type="text" name="txt_address" id="txt_address"
                                   value="<?php echo $v_address ?>"
                                   class="form-control" 
                                   required>
                        </div>
                    </div>
                </div>
            </div>
            <!--End #address -->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12 block">
                            <label class="col-md-6  block control-label"><?php echo __('Người đại diện') ?> <span class="required">(*)</span>:</label>
                            <div class="col-md-6 " >
                                <input type="text" name="txt_boss" id="txt_boss"
                                       value=""
                                       class="form-control" 
                                       required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12 block">
                            <label class="col-md-3  block control-label"><?php echo __('Chức vụ') ?> <span class="required">(*)</span>:</label>
                            <div class="col-md-6 " >
                                <input type="text" name="txt_position" id="txt_position"
                                       value=""
                                       class="form-control" 
                                       required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End nguoi dai dien and chuc vu -->

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 block">
                        <label class="col-md-3 control-label"><?php echo __('verification Code') ?> <span class="required">(*)</span>:</label>
                        <div class="col-md-6" >
                            <input type="text" name="txt_code" id="txt_code" 
                                   value="<?php echo $v_code ?>"
                                   class="form-control" 
                                   required>
                        </div>
                    </div>
                </div>
            </div>
            <!--End #code-->
        </div>
    </div>
    <!--End #left-sidebar-->

</div>

<script>
$(document).ready(function() {
    $('#frmMain').validate({
        submitHandler: function(form)
        {
            frmainMain_submit();
        }
    });
});


function frmainMain_submit()
{
    var url = '<?php echo SITE_ROOT; ?>' + $('#hdn_update_trigger_method').val();
    $.ajax({
        url: url,
        type: 'post',
        data: $('#frmMain').serialize(),
        beforeSend: function()
        {
            var img = '<center><img src="<?php echo SITE_ROOT; ?>public/images/loading.gif"/></center>';
            $('#loading').html(img);
        },
        success: function(data)
        {
            $('#loading').html('');
            if (parseInt(data) == 1)
            {
                $('#warpper-trigger').html('<h1 style="width:100%;text-align:center; color:Red;">Chào bạn ' + $('#txt_name').val() + ' đã cập nhật thông tin tài khoản thành công! <br/> Nhấn vào đây để quay lại <a style="font-size: 20px;" href=" <?php echo SITE_ROOT; ?>">trang chủ</a></h1>');
            }
            else
            {
                alert(data);
                return false;
            }
        }
    });
}

/**
 * Comment
 */
function rd_organ_onclick(selector)
{
    if ($(selector).attr('id') == 'rd_organ_single')
    {
        $('#content-form').html($('#content-form-tmp').find($('#box-organ-single')).clone(true));
    }
    else if ($(selector).attr('id') == 'rd_organ')
    {
        $('#content-form').html($('#content-form-tmp').find($('#box-organ')).clone(true));
    }
}
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
