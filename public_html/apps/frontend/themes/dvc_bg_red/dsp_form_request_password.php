<?php $this->render('dsp_header_pop_win', array(), $this->theme_code); ?>
<!--validate-->
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/additional-methods.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery_validate_vi.js" type="text/javascript"></script>
<div class="modal-body" style="width: 400px;height: 300px">
    <form class="form-horizontal" method="post" action="<?php echo($this->get_controller_url('frontend', 'frontend') . 'do_reset_password') ?>" id="frm_send_request" name="frm_send_request">
        <div class="form-group">
            <label for="username" class="col-md-2 col-xs-4 control-label block"><b><?php echo __('user name') ?></b><span class="required">(*)</span></label>
            <div class="col-md-10 col-xs-8 block">
                <input type="text"  class="form-control" id="txt_reset_username" name="txt_reset_username" placeholder="<?php echo __('user name') ?>"/>
                <label for="txt_reset_username" class="error"></label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 col-xs-4 control-label block" for="email"><b><?php echo __('email') ?></b><span class="required">(*)</span></label>
            <div class="col-md-10 col-xs-8 block" >
                <input type="email" class="form-control" id="txt_reset_email" name="txt_reset_email" placeholder="<?php echo __('email') ?>"/>
                <label for="txt_reset_email" class="error"></label>
            </div>
        </div>
        <div class="form-group">
            <label for="email" class="col-md-2 col-xs-4 control-label block"><b><?php echo __('code') ?></b><span class="required">(*)</span></label>
            <div class="col-md-10 col-xs-8 block" >
                <?php echo recaptcha_get_html(_CONST_RECAPCHA_PUBLIC_KEY) ?>
                <?php if (isset($response) && isset($response['message']['recapcha']) && $response['message']['recapcha'] != ''): ?>
                    <label class="error"><?php echo $response['message']['recapcha'] ?></label>
                <?php endif; ?>
                <label for="recaptcha_response_field" class="error" style="display: none"></label>
            </div>
        </div>
    </form>
</div>
<div style="width: 100%;text-align: center;margin-top: 15px;">
    <button type="submit" class="btn btn-primary" id="btn_send_request" name="btn_send_request"><?php echo __('send request') ?></button>
    <button id="close-modal" type="button" class="btn btn-danger" onclick="parent.window.frmCloseModal.close_window_modal.click();">
        <span aria-hidden="true">&times;</span><?php echo __('close');?>
    </button>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#frm_send_request').validate({
            rules: {
                txt_reset_username: {
                    required: true,
                    remote: {
                        url: "<?php echo($this->get_controller_url('frontend', 'frontend') . 'check_username_exist') ?>",
                        type: "POST",
                        data: {
                            username_reset: function() {
                                return $("#txt_reset_username").val();
                            }
                        }
                    }
                },
                txt_reset_email: {
                    required: true,
                    email: true
                },
                recaptcha_response_field: {
                    required: true
                }
            },
            messages: {
                txt_reset_username: {
                    required: "<?php echo __('please specify your name.') ?>",
                    remote: "<?php echo __('username not existing.') ?>"
                },
                txt_reset_email: {
                    required: "<?php echo __('please specify email.') ?>",
                    email: "<?php echo __('our email address must be in the format of name@domain.com') ?>"
                },
                recaptcha_response_field: {
                    required: "<?php echo __('please specify code.') ?>"
                }
            }
        });

        $("#btn_send_request").click(function() {
            var valid_form = $("#frm_send_request").valid();
            if (valid_form) {
                $('#frm_send_request').submit();
                $('#btn_send_request,#close-modal').attr('disabled', 'disabled');
            }
        });
    });
</script>
<?php
$this->render('dsp_footer_pop_win', array(), $this->theme_code);