<?php
$VIEW_DATA['title'] = $this->website_name;
$VIEW_DATA['v_banner'] = $v_banner;
$VIEW_DATA['arr_all_website'] = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('synthesis', 'component');
$VIEW_DATA['arr_script'] = array();
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

$v_code       =  get_post_var('txt_code','');
$arr_single_citizen = isset($arr_single_citizen) ? $arr_single_citizen:array();
$v_citizen_id = isset($arr_single_citizen['id']) ? $arr_single_citizen['id'] :0 ;
$username     = isset($arr_single_citizen['username']) ? $arr_single_citizen['username'] :'' ;
$error   = isset($error) ? $error : '';
$success = isset($success) ? $success :'';
?>

<!--validate-->
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/additional-methods.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery_validate_vi.js" type="text/javascript"></script>

<div class="col-md-12 content">
    <div class="col-md-12" id="content">
        <div class="clear" style="height: 10px"></div>
          <div class="div_title_bg-title-top"></div>
        <div class="div_title">
              <div class="title-border-left"></div>
              <div class="title-content">
                  <label > 
                      <?php echo __('Xác nhận thay đổi email') ?>
                  </label>
              </div>
        </div>  
        <div class="col-md-12">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="row" style="text-align: center">
                        <?php
                            if(trim($error) != '')
                            {
                              echo '<h4 style="color:red">'.$error .'</h4>';   
                            }
                            else
                            {
                                if($success == 'TRUE')
                                {
                                    echo  '<h4 style="color:#008000;height:200px">Bạn đã xác nhận đổi email thành công! Nhấn vào <a href="'.SITE_ROOT.'">link liên kết sau đây để quay về trang chủ</a></h4>';
                                }
                                else
                                {
                                    echo $success;
                                }
                                    
                            }
                        ?>
                    </div>
                <br/>
                <?php if(sizeof($arr_single_citizen) >0 && $success != 'TRUE'): ?>
                <form action="" name="frmMain" id="frmMain" method="POST">
                    <?php echo $this->hidden('hdn_citizen_id',$v_citizen_id);?>
                    <div class="form-group">
                        <div class="row">
                            <label class="control-label col-md-3"><?php echo __('Tên tài khoản') ?>:</label>
                            <div class="col-md-6">
                                <b>  <?php echo isset($username) ? $username : ''?></b>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label class="control-label col-md-3"><?php echo __('verification Code') ?><span class="required">(*)</span>:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" required="" name="txt_code" id="txt_code" value="<?php echo $v_code;?>"> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label class="control-label col-md-3"></label>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary" ><?php echo __('update')?></button> &nbsp;
                                <a href="<?php echo SITE_ROOT?>"><?php echo __('Quay về trang chủ')?></a>
                            </div>
                        </div>
                    </div>
            </form>
                <?php endif; ?>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#frmMain').validate()
    });
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
