<?php
//du lieu header
$VIEW_DATA['title'] = $this->website_name;
$VIEW_DATA['v_banner'] = $v_banner;
$VIEW_DATA['arr_all_website'] = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('box-cat-feature', 'component', 'single-category', 'single-page', 'component', 'breadcrumb');
$VIEW_DATA['arr_script'] = array('');
?>
<?php $this->render('dsp_header', $VIEW_DATA, $this->theme_code); ?>
<div class="col-md-12">
    <div class="col-md-12 content">
        <div class="col-md-12 block" id="content_new_pass">
            <div class="div-synthesis">
                <div class="div_title_bg-title-top"></div>
                <div class="div_title">
                    <div class="title-border-left"></div>
                    <div class="title-content">
                        <label> 
                            <?php echo __('Reset Password') ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 block" id="content">
                <form class="form-horizontal" role="form" method="POST" id="frm_reset_password" action="<?php echo $this->get_controller_url('frontend', 'frontend') . 'do_change_password' ?>">
                    <input type="hidden" name="email" value="<?php echo $VIEW_DATA['email'] ?>"/>
                    <div class="form-group">
                        <label for="new-password" class="col-sm-3 control-label" style="white-space: nowrap"><b><?php echo __('new password') ?></b><span class="required">(*)</span></label>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" 
                                   id="new_pass" name="new_pass" 
                                    minlength="6" 
                                   placeholder="<?php echo __('new password') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="re-password" class="col-sm-3 control-label" style="white-space: nowrap"><b><?php echo __('confirm password') ?></b><span class="required">(*)</span> </label>
                        <div class="col-sm-4">
                            <input type="password" 
                                     minlength="6" 
                                     class="form-control" id="re_pass" 
                                     name="re_pass" placeholder="<?php echo __('confirm password') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-12">
                            <button type="submit" class="btn btn-primary"><?php echo __('reset password') ?></button>
                        </div>
                    </div>
                </form>
            </div> <!--end div-synthesis-->
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#frm_reset_password').validate({
            rules: {
                new_pass: {
                    required: true,
                    min: 6
                },
                re_pass: {
                    required: true,
                    equalTo: "#new_pass",
                    min: 6
                }
            },
            messages: {
                new_pass: {
                    required: "<?php echo __('new-password not empty.') ?>",
                    min: "<?php echo __('please enter a value greater than or equal to 6.') ?>"
                },
                re_pass: {
                    required: "<?php echo __('pre-password not empty.') ?>",
                    equalTo: "<?php echo __('new-password and Pre-Password same value again.') ?>",
                    min: "<?php echo __('please enter a value greater than or equal to 6.') ?>"
                }
            }
        });
    });
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>