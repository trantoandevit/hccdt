<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');} ?>
<?php
//display header
$this->template->title = __('poll detail');
$this->template->display('dsp_header.php');

$v_poll_id         = isset($arr_single_poll['PK_POLL'])?$arr_single_poll['PK_POLL']:'';  
$v_poll_name       = isset($arr_single_poll['C_NAME'])?$arr_single_poll['C_NAME']:'';
$v_poll_slug       = isset($arr_single_poll['C_SLUG'])?$arr_single_poll['C_SLUG']:'';
$v_poll_status     = isset($arr_single_poll['C_STATUS'])?$arr_single_poll['C_STATUS']:'';

$v_begin_date_time  = isset($arr_single_poll['C_BEGIN_DATE'])?$arr_single_poll['C_BEGIN_DATE']:'';
$arr_begin_date_time= explode(' ', $v_begin_date_time);
$v_begin_date       = isset($arr_begin_date_time[0])?$arr_begin_date_time[0]:'';
$v_begin_time       = isset($arr_begin_date_time[1])?$arr_begin_date_time[1]:date("H:i:s");

$v_begin_end_time   = isset($arr_single_poll['C_END_DATE'])?$arr_single_poll['C_END_DATE']:'';
$arr_end_date_time  = explode(' ', $v_begin_end_time);
$v_end_date         = isset($arr_end_date_time[0])?$arr_end_date_time[0]:'';
$v_end_time         = isset($arr_end_date_time[1])?$arr_end_date_time[1]:date("H:i:s");

?>

<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id',$v_poll_id);
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_poll');
    echo $this->hidden('hdn_dsp_all_method','dsp_all_poll');
    echo $this->hidden('hdn_update_method','update_poll');
    echo $this->hidden('hdn_delete_method','delete_poll');
    echo $this->hidden('XmlData','');
    
    echo $this->hidden('hdn_item_id_list_old','');
    echo $this->hidden('hdn_item_id_list_new','');
    echo $this->hidden('hdn_id_delete_answer_list','');
    //Luu dieu kien loc
    ?>
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('poll detail'); ?></h2>
    <!-- /Toolbar -->
    <div>
        <div class="Row">
            <div class="left-Col">
               <?php echo __('question');?>
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_poll_name" id="txt_poll_name" value="<?php echo $v_poll_name;?>" 
                        data-allownull="no" data-validate="text" 
                        data-name="<?php echo __('question')?>" 
                        data-xml="no" data-doc="no" 
                        autofocus="autofocus" 
                        size="70">
            </div>
        </div>
         <div class="Row">
            <div class="left-Col">
               <?php echo __('answer');?>
            </div>
            <div class="right-Col">
                <div name="div_poll_answer" id="div_poll_answer">
                    <?php 
                    foreach ($arr_all_answer as $row_answer):
                        $v_answer_id   = $row_answer['PK_POLL_DETAIL'];
                        $v_poll_answer = $row_answer['C_ANSWER'];
                        $v_vote        = $row_answer['C_VOTE'];
                    ?>
                    <div class="Row" id="row_<?php echo $v_answer_id;?>">
                        <input type="checkbox" name="chk_old" id="chk_old" value="<?php echo $v_answer_id;?>" data-poll_answer="<?php echo $v_poll_answer;?>"/>
                        <input type="textbox" name="txt_poll_answer[]" id="txt_poll_answer" value ="<?php echo $v_poll_answer;?>" size="50"/>
                        <?php echo $v_vote;?>
                    </div>
                    <?php endforeach;?>
                </div>
                <div class="button-area">
                    <input type="button" name="btn_add_article" id="btn_add_article" class="ButtonAdd" onclick="btn_add_answer_onclick();" value="<?php echo __('add answer'); ?>" />
                    <input type="button" name="btn_delete_article" id="btn_delete_article" class="ButtonDelete" onclick="btn_delete_answer_onclick();" value="<?php echo __('delete'); ?>" />
                </div>
            </div> 
        </div>
        <div class="Row">
            <div class="left-Col">
               <?php echo __('begin date');?>
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_begin_date" value="<?php echo $v_begin_date;?>" id="txt_begin_date" 
                       data-allownull="no" data-validate="date" 
                       data-name="<?php echo __('begin date')?>" 
                       data-xml="no" data-doc="no" 
                       autofocus="autofocus" 
                />
                &nbsp;
                <img src="<?php echo $this->image_directory."calendar.png";?>" onclick="DoCal('txt_begin_date')">
                &nbsp; : &nbsp;
                <input type="textbox" name="txt_begin_time" value="<?php echo $v_begin_time;?>"/>
            </div>
        </div>
        
        <div class="Row">
            <div class="left-Col">
               <?php echo __('end date');?>
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_end_date" value="<?php echo $v_end_date;?>" id="txt_end_date"
                       data-allownull="no" data-validate="date" 
                       data-name="<?php echo __('end date')?>" 
                       data-xml="no" data-doc="no" 
                       autofocus="autofocus" 
                />
                &nbsp;
                <img src="<?php echo $this->image_directory."calendar.png";?>" onclick="DoCal('txt_end_date')">
                &nbsp; : &nbsp;
                <input type="textbox" name="txt_end_time" value="<?php echo $v_end_time;?>"/>
                <br />
                <label style="color: red;display: none"  id="error-date"><?php echo __('end date not smaller start date');?></label>
            </div>
            
            
        </div>
        
        <div class="Row">
            <div class="left-Col">
                <?php echo __('status'); ?>
            </div>
            <div class="right-Col">
                <select name="poll_status" id="poll_status">
                    <option value="0"><?php echo __('display none')?></option>
                    <option value="1" <?php echo ($v_poll_status==1)?'selected':'';?>><?php echo __('display')?></option>
                </select>
            </div>
        </div>
    </div>
        <br>
        <label class="required" id="message_err"></label>
        <br>
        <div class="button-area">
            <input type="button" name="btn_update" id="btn_update" class="ButtonAccept" value="<?php echo __('update');?>" onclick="btn_accept_onclick();"/>
            <input type="button" name="btn_back" id="btn_cancel" class="ButtonBack" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>
        </div>
</form>
<script>
var arr_answer_delete = new Array();
function btn_add_answer_onclick()
{
   var html='';
   html+='<div class="Row" id="row_new">';
   html+='<input type="checkbox" name="chk_new" id="chk_new"/>';
   html+='<input type="textbox" name="txt_poll_answer_new" id="txt_poll_answer" value ="" size="50"/>0</div>';
   $('#div_poll_answer').append(html);
}
function btn_delete_answer_onclick()
{
   
    $('#div_poll_answer input[type="checkbox"]').each(function(index){
      if($(this).is(':checked'))
      {
          if($(this).attr('id')=='chk_old')
          {
              arr_answer_delete.push($(this).val());
          }
          $(this).parent().remove();
      }
    });
    $('#hdn_id_delete_answer_list').val(arr_answer_delete.join());
}
function btn_accept_onclick()
{
    var arr_answer_old = new Array();
    var arr_answer_new = new Array();
    
    var begin_date      = $('#txt_begin_date').val();
    var end_date        = $('#txt_end_date').val();
//        var current_date    = getdate();
    if(paresDate_getTime(begin_date) > paresDate_getTime(end_date))
    {
        $('#error-date').show();
        return;
    }
    else
    {
        $('#error-date').hide();
    }
     $('#div_poll_answer input[name="chk_old"]').each(function(index){
         v_answer_id   = $(this).val();
         arr_answer_old.push(v_answer_id);
     });
     $('#hdn_item_id_list_old').val(arr_answer_old.join());
     
    $('#div_poll_answer input[name="txt_poll_answer_new"]').each(function(index){
        v_answer_id   = $(this).val();
        arr_answer_new.push(v_answer_id);
    });
    $('#hdn_item_id_list_new').val(arr_answer_new.join());
    btn_update_onclick();
}

    function paresDate_getTime(str) 
    {
        date = str.split('-');
        return new Date(date[2],date[1],date[0]).getTime();
    }
</script>
<?php
$this->template->display('dsp_footer.php');
?>