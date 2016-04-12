<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>
<?php
//header
//@session::init();
$this->template->title = __('advertising manager');
$this->template->display('dsp_header.php');
if(isset($arr_all_position[0]['PK_ADV_POSITION']))
{
    $v_position_id  = isset($_POST['hdn_position_id'])?$_POST['hdn_position_id']:$arr_all_position[0]['PK_ADV_POSITION'];
}
else
{
    $v_position_id = '';
}
//var_dump($v_position_id);
?>
<h2 class="module_title"><?php echo __('advertising manager');?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id',0);
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_advertising');
    echo $this->hidden('hdn_dsp_all_method','dsp_all_advertising');
    echo $this->hidden('hdn_update_method','update_advertising');
    echo $this->hidden('hdn_delete_method','delete_advertising');
    echo $this->hidden('hdn_position_id',$v_position_id);
    echo $this->hidden('hdn_item_id_swap',0);
    ?>

<div id="tabs_advertising">
    <ul>
        <?php foreach($arr_all_position as $row):?>
            <li>
                <a href="#position_detail_<?php echo $row['PK_ADV_POSITION'];?>" 
                   id="tab_<?php echo $row['PK_ADV_POSITION'];?>" 
                   value="<?php echo $row['PK_ADV_POSITION'];?>" 
                   data-name="<?php echo $row['C_NAME'];?>" 
                   onclick="single_position_onclick(this)"> 
                <?php echo $row['C_NAME'];?>
                </a>
            </li>
        <?php endforeach;?>
            <li><a href="#add_new_position" onclick="new_position_onclick()"><?php echo __('+')?></a></li>
    </ul>
    <div id="info_position">
        <div>
            <label><?php echo __('position name');?></label>
            <input type="textbox" value="" name="txt_position_name" id="txt_position_name">
            <input type="button" value="<?php echo __('update');?>" class="ButtonAccept" onclick="btn_update_position_onclick()">
        </div>
        <div>
            <input type="button" class="ButtonDelete" value="<?php echo __('delete position');?>" onclick="btn_delete_position_onclick()">
        </div>
        <label class="required" id="check_err_position"></label>
    </div>
    <?php foreach($arr_all_position as $row):
            $v_position = $row['PK_ADV_POSITION'];
    ?>
    <div id="position_detail_<?php echo $v_position;?>" style="min-height: 368px;">
    </div>
    <?php  endforeach;?>
    <div id="add_new_position" style="min-height: 368px;">
        <label><?php echo __('position name ');?></label>
        <input type="textbox" value="" name="txt_new_position_name" id="txt_new_position_name">
        <input type="button" value="<?php echo __('update');?>" class="ButtonAccept" onclick="btn_update_position_onclick()">
    </div>
</div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
           $('#tabs_advertising').tabs();
           <?php if($v_position_id !=''):?>
           var tab = "#position_detail_<?php echo $v_position_id;?>";
           $('#tabs_advertising').tabs('select',tab);
           
            $.ajax({
                type: 'post',
                url: '<?php echo $this->get_controller_url() . 'dsp_single_position/'.$v_position_id; ?>',
                beforeSend: function() {
                     img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $(tab).html(img);
                 },
                success: function(result)
                {
                    $(tab).html(result);
                }
            });
            
           var position_name =  $('#tab_<?php echo $v_position_id;?>').attr('data-name');
           $('#txt_position_name').attr('value',position_name);
           <?php else:?>
           $('#info_position').css({'display':'none'});
           <?php endif;?>
    });
    function single_position_onclick(position)
    {
       $('#check_err_position').html('');
       $('#txt_new_position_name').val('')
       $('#info_position').css({'display':'block'});
       
       var position_name = $(position).attr('data-name');
       var value         = $(position).attr('value'); 
       var tab           = "#position_detail_"+value;
       $('#txt_position_name').attr('value',position_name);
       $('#hdn_position_id').attr('value',value);
       
       $.ajax({
           type: 'post',
           url: '<?php echo $this->get_controller_url() . 'dsp_single_position/';?>'+value,
           beforeSend: function() {
                     img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $(tab).html(img);
                 },
           success: function(result)
           {
               $(tab).html(result);
           }
       });
    }
    function swap_order_advertising(id,id_swap)
    {
        $('#hdn_item_id').attr('value',id);
        $('#hdn_item_id_swap').attr('value',id_swap);
        var str = "<?php echo $this->get_controller_url()."swap_order"?>";
        $('#frmMain').attr('action',str);
        $('#frmMain').submit();
    }
    function new_position_onclick()
    {
        $('#info_position').css({'display':'none'});
    }
    function btn_update_position_onclick()
    {
        str="<?php echo $this->get_controller_url()."update_position";?>";
        $('#frmMain').attr('action',str);
        $('#frmMain').submit();
    }
    function btn_delete_position_onclick()
    {
        var tab     = "#position_detail_"+$('#hdn_position_id').val()+' input[name="chk"]';
        var count   = 0;
        $(tab).each(function(){
            count = count +1;
        });
        //alert(count);
        if(count < 1)
        {
            str="<?php echo $this->get_controller_url()."delete_position";?>";
            $('#frmMain').attr('action',str);
            $('#frmMain').submit();
        }
        else
        {
            $('#check_err_position').html('Vị trí này vẫn còn ảnh quảng cáo !!!');
        }
    }
</script>
<?php $this->template->display('dsp_footer.php');