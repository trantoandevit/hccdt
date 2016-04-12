<?php $this->render('dsp_header_pop_win', array(), $this->theme_code); ?>
<style type="text/css">
    .view{padding: 20px;}
    .vew p b{color: #ff3399}
</style>
<div class='view lead'>
    <p>Chúng tôi đã gửi mail về hòm thư: <b><mark><?php echo $VIEW_DATA['email'] ?></mark></b> của bạn.</p>
    <p>Bạn vào hòm thư kiểm tra (Nếu không có trong hộp thư đến bạn vui lòng kiểm tra hộp thư spam) và làm theo hướng dẫn.</p>
    <p>Cám ơn bạn đã sử dụng dịch vụ của chúng tôi.</p>
</div>
<button style="float: right;margin-right: 10px;" type="button" class="btn btn-danger" onclick="parent.window.frmCloseModal.close_window_modal.click();">
        <span aria-hidden="true">&times;</span><?php echo __('close');?>
</button>
