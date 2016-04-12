<?php
$v_username     = Session::get('citizen_login_name');
$v_citizen_id   = Session::get('citizen_login_id');
$v_full_name    = (trim(Session::get('citizen_name')) != '') ? Session::get('citizen_name') : $v_username;
$v_email        = Session::get('citizen_email');
?>
<!--validate-->
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/additional-methods.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery_validate_vi.js" type="text/javascript"></script>
<style type="text/css">
    iframe{
        border: none;
        margin: 0px;
        padding: 0px;
        text-align: center;
        height: 370px;
        width: 480px;
    }
    .modal-content
    {
        width: 500px;
    }
</style>
      <div id="box-login" class="toggle" style="left: -200px;">
            <?php
                $v_username   = Session::get('citizen_login_name');
                $v_citizen_id = Session::get('citizen_login_id');
                $v_full_name  = (trim(Session::get('citizen_name')) != '') ? Session::get('citizen_name') : $v_username;
                $v_email      = Session::get('citizen_email');
            ?>
            
            <div id="div-toggle-login">
               <?php echo (trim($v_username) == '') ? __('login') : __('account') ?>
            </div>
            <div class="border-top"></div>
                <div class="box-login-content" >
                    <div class="title-login"><?php echo (trim($v_username) == '') ? __('login') : __('account') ?></div>
                   <?php if(trim($v_username) == ''):?>
                    <form class="form-group" action="" method="post" id="frmLogin">
                        <div class="row">
                            <label class="col-md-12  control-label"><?php echo __('account')?>:</label>
                            <div class="col-md-10 block">
                                <input type="text" class="form-control " name="txt_username" id="txt_username" placeholder="<?php echo __('username') ?>" required="true" title="(*)">
                            </div>
                            <div class="col-md-1 block">
                                <label for="txt_username" class="error" style="display: none;"></label>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-md-12    control-label" ><?php echo __('password')?>:</label>
                            <div class="col-md-10 block">
                                <input type="password" name="txt_password" id="txt_password" class="form-control " placeholder="<?php echo __('password') ?>" required="true" title="(*)">
                            </div>
                            <div class="col-md-1 block">
                                <label for="txt_password" class="error" style="display: none;"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 block">
                                <label class="save_pass col-md-5 block"><input type="checkbox" name="chk_save_password" id="chk_save_password" value="1">&nbsp;<?php echo __('remember me')?></label> 
                                <div class="col-md-5 block" style="text-align: right">
                                    <button type="submit" class="btn btn-login" ><?php echo __('login') ?></button>
                                </div>                
                                
                            </div>
                
                        </div>
                        <div class="row">
                            <a class="register" href="<?php echo SITE_ROOT ?>register"><?php echo __('regisger') ?></a>
                            &nbsp;&nbsp;-&nbsp;&nbsp;<a href="#" class="reset_password" data-toggle="modal" data-target="#dialog_reset_password"><?php echo __('Quên mật khẩu') ?></a>
                        </div>
                    </form>
                    <?php else: ;?>
                    
                     <div id="username">
                         <span style="font-weight: normal"><?php echo __('hello')?>!</span> <b><?php echo $v_full_name ?></b>
                    </div>
                    <div id="box-single-citizen" >
                    <div class="single">
                        <div class="email">
                            <?php echo $v_email; ?>
                        </div>
                        <hr style="margin: 5px;padding: 0"/>
                        <a href="<?php echo build_url_single_account_citizen(); ?>" class="account-detail"><?php echo __('account detail')?></a>
                        <a href="<?php echo build_url_single_account_citizen(0,true); ?>" class="account-detail"><?php echo __('history filing');?></a>
                    </div>
                    <div class="logout">
                        <button type="button"  class="btn btn-danger" onclick="btn_logout_onlcik();"><?php echo __('logout') ?></button>
                    </div>
                </div>
                    <?php endif; ?>
                </div>                
            <div class="border-button"></div>
            <div class="border-radius"></div>
            <script>
                $(document).ready(function()
                {
                    $('#box-login #div-toggle-login').click(function()
                    {
                        LoginLeft = $('#box-login').position().left || 0;
                        if(LoginLeft == -200)
                        {
                            $('#box-login').stop().animate({
                                                left: 0
                                              },300);
                        }
                        else
                        {
                            $('#box-login').stop().animate({
                                                left: -200
                                              },300);
                        }
                    });
                });
            </script>
        </div>

<div class="modal fade" id="dialog_reset_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="overflow: hidden">                
                <h2 class="modal-title" id="myModalLabel" style="float: left"><?php echo __('retrieve your password.') ?></h2>
                <form name="frmCloseModal" id="frmCloseModal" style="display: none">
                    <button name="close_window_modal" style="float: right" type="button" class="btn btn-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span><?php echo __('close');?></button>
                </form>
            </div>
            <iframe src="<?php echo $this->get_controller_url('frontend', 'frontend') . 'dsp_form_request_password' ?>" width="600px" height="400px"></iframe>
            
        </div>
    </div>
</div> 
<script type="text/javascript">
    $(document).ready(function($) {

         $('#frmLogin').validate({
             submitHandler: function(form) {
                 do_login();
                 return false;
             }
         });
    });
        function do_login()
        {
            var url = '<?php echo SITE_ROOT ?>do_login';
            $.ajax({
                url: url,
                type: 'POST',
                data: $('#frmLogin').serialize(),                
                success: function(data)
                {
                    $('#loading_login').html('');
                    if (data.toString() == '1')
                    {
                        location.reload();
                    }
                    else
                    {
                        alert(data.toString());
                        return false;
                    }
                }
            });
        }

        function btn_logout_onlcik()
        {
            var url = '<?php echo SITE_ROOT ?>do_logout';
            $.ajax({
                url: url,
                success: function()
                {
                    location.reload();
                }

            });
        }
</script>

