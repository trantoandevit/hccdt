<?php
$v_username         = isset($username)? $username :'';
$v_email            = isset($email) ? $email : '';
$v_code             = isset($code) ? $code :'';
$v_create_date      = isset($create_date) ? $create_date :'';

$_trigger_type      = isset($type)? $type : '';
$v_citizen_id      = isset($citizen_id)? $citizen_id : 0;

?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Kịch hoạt tài khoản - <?php echo _CONST_UNIT_NAME; ?></title>
<div id="hx-box-trigger-account" style="width: 100%;overflow: hidden;margin: 5px;">
    <div class="hx-header" style="height: 50px;background:#003399 ">
        <h2 style="line-height: 50px;color: white;text-align: center"><?php echo _CONST_UNIT_NAME; ?></h2>
    </div>
  
    <div class="hx-content">
        <?php if($_trigger_type === 'trigger_reset_password'):?>
        <div class="hx-title-content" style="margin-left: 10px;">
            <br/>
            Xin chào: <b><?php echo $v_username;?></b><br/><br/>
            Cám ơn bạn đã sử dụng chức năng thay đổi mật khẩu tại <b><?php echo _CONST_UNIT_NAME; ?></b>
        </div>
        <div class="hx-content">
            Để xác nhận thay đổi Email bạn vui lòng nhấn vào 
            <a href="<?php echo build_url_change_password($v_email, $v_code);?>"/>link xác nhận</a> để xác nhận thay đổi mật khẩu.
            <br/>
            Cám ơn bạn đã sử dụng dịch vụ của chúng tôi!
        </div>
    <?php elseif($_trigger_type == 'trigger_email'):  ?>
        <div class="hx-title-content" style="margin-left: 10px;">
            <br/>
            <br/>
            Thông tin của bạn tại <b><?php echo _CONST_UNIT_NAME; ?></b> (website : <a href="<?php echo FULL_SITE_ROOT?>"> <?php echo FULL_SITE_ROOT?> </a>)
            <br/>
            Cám ơn bạn đã sử dụng chức năng thay đổi Email tại <b><?php echo _CONST_UNIT_NAME; ?></b> vào lúc <?php echo $v_create_date?>

        </div>
        <hr/>
        <div class="hx-content">
            <br/>
            Xin chào: <b><?php echo $v_username;?></b><br/> 
            Để xác nhận thay đổi Email bạn vui lòng nhấn vào <a href="<?php echo build_url_trigger_change_email($v_username,$v_citizen_id,false,true); ?>">link xác nhận</a> để xác nhận thay đổi email
            Mã kích hoạt tài khoản của bạn là: <span style="color: red"><?php echo $v_code; ?></span>
            <br/>
            Nếu không phải bạn yêu cầu thay đổi Email hoặc bạn không muốn thay đổi Email. Bạn vui lòng nhấn vào <a href="<?php echo build_url_trigger_change_email($v_username,$v_citizen_id,FALSE,false); ?>?&send_to_email=1" >link hủy thay đổi email</a> để hủy yêu cầu thay đổi email.
            <br/>
            Nếu bạn vẫn gặp vấn đề khi đăng ký xin vui lòng liên hệ với một thành viên của nhân viên hỗ trợ của chúng tôi tại <a href="mailto:<?php echo get_system_config_value(CFGKEY_MAIL_SERVER); ?>" ><?php echo get_system_config_value(CFGKEY_MAIL_SERVER); ?></a>
        </div>
    <?php else:?>
        <div class="hx-title-content" style="margin-left: 10px;">
            <br/>
            Xin chào: <b><?php echo $v_username;?></b><br/>
            <br/>
            Thông tin của bạn tại <b><?php echo _CONST_UNIT_NAME; ?></b> (website : <a href="<?php echo FULL_SITE_ROOT?>"><?php echo FULL_SITE_ROOT?></a>)
            <br/>
            Cám ơn bạn đã đăng ký để trở thành thành viên của chúng tôi tại <b><?php echo _CONST_UNIT_NAME; ?></b> vào lúc <?php echo $v_create_date?>

        </div>
        <hr/>
        <div class="hx-content">
            <br/>
            Xin chào: <b><?php echo $v_username;?></b><br/> 
            Trước khi chúng ta hoàn tất kích hoạt tài khoản của bạn. Bạn cần thực hiện một bước cuối cùng này cần phải thực hiện để hoàn tất việc đăng ký.
            <br/>
            Xin lưu ý - Bạn phải hoàn tất bước cuối cùng này để trở thành thành viên chính thức. Và tài khoản của bạn sẽ được cập nhật.
            <br/>
            <br/>
            Để hoàn tất việc đăng ký của bạn, nhấn vào link liên kết <a href="<?php echo account_trigger($v_username);?>">link kích hoạt</a>
            <br/>
            <br/>
            Tên tài khoản của bạn: <b><?php echo $v_username;?></b>
            <br/>
            Mã kích hoạt tài khoản của bạn là: <span style="color: red"><?php echo $v_code; ?></span>
            <br/>
            <br/>
        </div> 
    <?php   endif;?>
    </div>
    
    <hr/>
    <div class="hx-footer" style="text-align: center;overflow: hidden;margin-top: 20px;">
     <?php require_once __DIR__.'/dsp_content_footer.php';?>
    </div>
</div>

