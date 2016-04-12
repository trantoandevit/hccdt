<?php
$VIEW_DATA['title']                 = __('submit questions');
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$v_website_id = get_request_var('website_id', 0);
$VIEW_DATA['arr_css'] = array('lookup', 'synthesis', 'single-page', 'component', 'main');
$VIEW_DATA['arr_script'] = array();
$v_field_rq_id = get_request_var('field_id', '');
?>
<?php $this->render('dsp_header', $VIEW_DATA, $this->theme_code); ?>
<!-- Upload -->
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/additional-methods.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery_validate_vi.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>

<div class="col-md-12 single-question" id="single-page">
    <div  class="col-md-3">
        <?php
        $v_widget_path = __DIR__ . DS . 'dsp_widget.php';
        if (is_file($v_widget_path)) {
            require $v_widget_path;
        }
        ?>
    </div>
    <!--End .block-->
    <div class="col-md-9" id="main-content">
        <div class="col-md-12 block" >
            <div id="box-question"  class="category_item" style="width: 100%;height: auto; float: left; margin-top: 0px;">
                <form id="frmMain" class="form-horizontal" name="frmMain" method="POST" action="<?php echo $this->get_controller_url() ?>insert_cq/<?php echo $v_website_id; ?>">

                    <div class="col-md-12 block info" >
                        <div class="col-md-12 block"  style="border: 1px solid #E5E4E4;border-radius: 4px;;border-bottom: none;background: #F5F5F5;height: 42px">
                            <div class="div_title_bg-title-top"></div>
                            <div class="div_title">
                                <div class="title-border-left"></div>
                                <div class="title-content">
                                    <label style="float: left;width: 50%"> 
                                        <?php echo __('Gửi yêu cầu tư vấn') ?>
                                    </label>
                                    <div class="link-list-question">
                                        <a href="<?php echo build_url_cq($this->website_id); ?>"><img src="<?php echo CONST_SITE_THEME_ROOT ?>images/list.png" height="15x"  /><?php echo __('list question') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="box-set-question" style="margin-top: -10px;">
                            <div class="clear" style="height: 10px"></div>
                            <div class="col-md-12 block">
                                <div class="col-md-3 block"> 
                                    <b style="font-size: 1.2em;"> <?php echo __('general info'); ?></b>
                                </div>
                                <div class="col-md-9 block"></div>
                            </div>
                            <div class="clear" style="height: 10px"></div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo __('Tên cá nhân/tổ chức'); ?>
                                    <label class="required">(*)</label>:
                                </label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="txt_name" id="txt_name" required />
                                </div>
                            </div>
                            <!--End .form-group-->

                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo __('address '); ?>
                                    <label class="required">(*)</label>:
                                </label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="txt_address" id="txt_address" required />
                                </div>
                            </div>
                            <!--End .form-group #address-->                       

                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo __('phone'); ?>
                                    <label class="required">(*)</label>:
                                </label>
                                <div class="col-md-6">
                                    <input type="text" minlength="6" maxlength="15" class="form-control" name="txt_phone" id="txt_phone" required phone="true" />
                                </div>
                            </div>
                            <!--End .form-group #phone-->

                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo __('email'); ?>
                                    <label class="required">(*)</label>:
                                </label>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" name="txt_email" id="txt_email" required="" aria-required="true" />
                                </div>
                            </div>
                            <!--End .form-group #phone-->

                            <div class="col-md-12 block">
                                <div class="category_name" >
                                    <b  style="font-size: 1.2em;"> <?php echo __('Nội dung yêu cầu tư vấn'); ?></b>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo __('field'); ?>
                                    :
                                </label>
                                <div class="col-md-6">
                                    <select class="form-control"  name="select_field" id="select_field">
                                        <?php foreach ($arr_all_field as $field): ?>
                                            <option value="<?php echo $field['PK_FIELD'] ?>"><?php echo $field['C_NAME'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <!--End .form-group #field-->
                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo __('title'); ?>
                                    <label class="required">(*)</label>:
                                </label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="txt_title" id="txt_title" required="" />
                                </div>
                            </div>
                            <!--End .form-group #title-->
                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo __('content'); ?>
                                    <label class="required">(*)</label>:
                                </label>
                                <div class="col-md-6">
                                    <textarea rows="10" class="form-control" name="txt_content" required></textarea>
                                </div>
                            </div>
                            <!--End .form-group #content-->

                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    <?php echo __('security code'); ?>
                                    <label class="required">(*)</label>:
                                </label>
                                <div class="col-md-6" id="div-box-capcha">
                                    <?php echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY) ?>
                                    <label class="error" ></label>
                                </div>
                            </div>
                            <!--End .form-group #sacurity-->

                            <div class="form-group">
                                <label class="col-md-3 control-label">
                                    &nbsp;
                                </label>
                                <div class="col-md-6" style="text-align: center">
                                    <input type="submit" class="btn btn-info" name="submit" onclick="//send_cq_onclick()" >
                                    <input type ="button" class="btn btn-primary" value="<?php echo __('back') ?>" onclick="history.go(-1)">    
                                </div>
                            </div>
                            <!--End .form-group #sacurity-->
                            <script> 
                                $(document).ready(function() {
                                    var url = '<?php echo $this->get_controller_url() ?>insert_cq/<?php echo $v_website_id; ?>';
                                    $('#frmMain').validate({
                                        submitHandler: function(form) {
                                            send_cq_onclick();
                                            return false;
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function send_cq_onclick()
    {         
        var url = '<?php echo $this->get_controller_url() ?>insert_cq/<?php echo $v_website_id; ?>';
        $.ajax({
            type: 'post',
            url: url,
            data: $('#frmMain').serialize(),
            success:function(returnVal){
                $('#recaptcha_reload').trigger("click");
                if(parseInt(returnVal) == 1)
                {
                    var  html = '<div style="padding-top: 10px;font-size: 1.4em;font-weight: bold;text-align: center;color: red;"><?php echo __('Gửi câu hỏi thành công. Chúng tôi sẽ trả lời bạn trong thời gian sớm nhất') ?> <br /> <button class="btn btn-primary" onclick=" window.history.back();">Quay lại</button></div>';
                    $('#box-question').html(html);
                    $('#box-question').css('min-height','300px');
                }
                else if(returnVal == 0)
                {
                    alert('<?php echo __('không thực hiện được yêu cầu!!') ?>');
                    $('#div-box-capcha').find('.error').hide();
                }
                else
                {
                    $('#div-box-capcha').find('.error').text(returnVal).show();
                }
                
            }
        });
    }
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
