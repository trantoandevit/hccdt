<?php
defined('DS') or die('no direct access');
$this->template->title = $arr_single_staff['C_NAME'];
$this->template->left_menu = FALSE;
$this->template->controller_url = $controller_url = $this->get_controller_url(). 'touch_screen';
$this->template->display('dsp_header_touchscreen.php');

$v_staff_code = $arr_single_staff['C_CODE'];
$v_dir_img_staff_logo = CONST_DIRECT_VOTE_IMAGES . $v_staff_code . '.jpg';
//Anh logo mac dinh
$v_url_img_staff_logo = SITE_ROOT.'public/images/logo_default.jpg';
if (file_exists($v_dir_img_staff_logo)) 
{
    $v_url_img_staff_logo = CONST_URL_VOTE_IMAGES . $v_staff_code . '.jpg';
}

$v_staff_name = $arr_single_staff['C_NAME'];
?>
<?php 
    $today = date('d-m-Y');
    echo hidden('hdn_user_id', $staff_id);
    echo hidden('hdn_today', $today);
?> 
<div class="row" style="padding: 10px 0px;">
    <div class="col-sm-12 staff_name">
        <center>
            <?php echo $v_staff_name?>
        </center>
    </div>
    <div class="col-sm-12">
        <div class="col-sm-2">
            <center>
                <img src="<?php echo $v_url_img_staff_logo; ?>" width="100%" heght="100%"> 
            </center>
        </div>
        <div class="col-sm-10">
            <div id="list">
                <ul>
                    <?php
                        if(is_array($criterail)):
                            foreach($criterail as $row):
                    ?>
                        <li id="vote-<?php echo $row['PK_LIST']; ?>" class="update_vote" data-id="<?php echo $row['PK_LIST']; ?>" onclick="update_vote_onclick(this)">
                            <img src="<?php echo FULL_SITE_ROOT; ?>public/images/<?php echo $row['IMAGE_LINK']; ?>"  />
                            <a href="javascript:void(0)"><?php echo $row['C_NAME']; ?>
                                <span >
                                    <?php echo __('total vote')?>
                                    <br/><strong><?php echo isset($row['C_VOTE']) ? (((int)$row['C_VOTE'] >0)? $row['C_VOTE']: 0) :0; ?></strong>
                                </span>
                            </a>
                        </li>
                    <?php
                            endforeach;
                        endif;
                    ?>
                </ul>
            </div>
            <div id="div_load_img"></div>
        </div>
    </div>
</div>
<script>
    function update_vote_onclick(li)
    {
        var today              = $('#hdn_today').val();
        var user_id            = $('#hdn_user_id').val();
        var fk_criterial       = $(li).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '<?php echo $this->get_controller_url() . 'do_update_vote';?>',
            data: {user_id:user_id,today:today,fk_criterial:fk_criterial},
            beforeSend: function() {
                $('#btn_evaluation').attr("disabled", "disabled");
                var img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                $('#div_load_img').html(img);
            },
            success: function(data) 
            {
                var criteria;
                var vote;
                if(data != '0')
                {
                    var obj_result = JSON.parse(data);
                    for(var key in obj_result)
                    {
                        criteria = obj_result[key].PK_LIST;
                        vote     = obj_result[key].C_VOTE;
                        $('#list ul li').filter('[data-id="'+criteria+'"]').find('strong').html(vote);
                    }
                }
                $('#div_load_img').html('');
            }
        });
    }
</script>
<?php $this->template->display('dsp_footer_touchscreen.php'); ?>