<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

$this->template->title =__('member');
$this->template->display('dsp_header.php');

$arr_single_member = isset($arr_single_member) ? $arr_single_member : array();
$arr_single_member_level_1 = isset($arr_single_member_level_1) ? $arr_single_member_level_1 :array();

if(sizeof($arr_single_member) >0)
{
    $v_member_id            = $arr_single_member['PK_MEMBER'];
    $v_member_name          = $arr_single_member['C_NAME'];
    $v_member_code          = $arr_single_member['C_CODE'];
    $v_member_address       = $arr_single_member['C_ADDRESS'];
    $v_member_email         = $arr_single_member['C_EXCHANGE_EMAIL'];
    $rad_level              = $arr_single_member['C_SCOPE'];
    $v_status               = $arr_single_member['C_STATUS'];
    $v_member_parent_id     = $arr_single_member['FK_MEMBER'];
    $v_xml_data             = $arr_single_member['C_XML_DATA'];
    $v_short_code           = $arr_single_member['C_SHORT_CODE'];
    $v_village_id           = empty($arr_single_member['FK_VILLAGE_ID'])?0:$arr_single_member['FK_VILLAGE_ID'];;
	
	$txt_order				= isset($arr_single_member['C_ORDER'])  ? $arr_single_member['C_ORDER'] : 1;
}
else 
{
    $v_member_id            = 0;
    $v_member_name          = isset($_REQUEST['txt_member_name']) ? $_REQUEST['txt_member_name'] :  '';
    $v_member_code          = isset($_REQUEST['txt_member_code']) ? $_REQUEST['txt_member_code'] :  '';
    $v_member_address       = isset($_REQUEST['txt_member_address']) ? $_REQUEST['txt_member_address'] :  '';
    $v_member_email         = isset($_REQUEST['txt_member_email']) ? $_REQUEST['txt_member_email'] :  '';

    $rad_level              = isset($_REQUEST['rad_level']) ? $_REQUEST['rad_level'] :  0;
    $v_status               = isset($_REQUEST['chk_status']) ? $_REQUEST['chk_status'] :  '';
    $v_village_id           = 0;

    $v_member_parent_id     = isset($_REQUEST['sel_member_parent']) ? $_REQUEST['sel_member_parent'] :  0;
    $v_short_code           = isset($_REQUEST['txt_short_code']) ? $_REQUEST['txt_short_code'] :  0;
    $v_xml_data             = isset($_REQUEST['XmlData']) ? $_REQUEST['XmlData'] :  '<data></data>';
	$txt_order		        = isset($_REQUEST['txt_order']) ? (int)$_REQUEST['txt_order'] : 1;
}
?>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_item_id',$v_member_id);

        echo $this->hidden('hdn_dsp_single_method','dsp_single_member');
        echo $this->hidden('hdn_dsp_all_method','dsp_all_member');
        echo $this->hidden('hdn_update_method','update_member');
        echo $this->hidden('XmlData',$v_xml_data);
    ?>
    
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('member detail'); ?></h2>
    <!-- /Toolbar -->
        <div class="Row">
            <div class="left-Col">
               <?php echo __('name member');?>
                <label class="required">(*)</label>
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_member_name" id="txt_member_name" value="<?php echo $v_member_name;?>" 
                        data-allownull="no" data-validate="text" 
                        data-name="<?php echo __('name member')?>" 
                        data-xml="no" data-doc="no" 
                        autofocus="autofocus" 
                        size="60"/>
            </div>
        </div>
        
        <div class="Row">
            <div class="left-Col">
                <?php echo __('code member'); ?>
                <label class="required">(*)</label>
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_member_code" id="txt_member_code" value="<?php echo $v_member_code; ?>" 
                       data-allownull="no" data-validate="text" 
                       data-name="<?php echo __('code member') ?>" 
                       data-xml="no" data-doc="no" 
                       autofocus="autofocus" 
                       size="60"/>
            </div>
        </div>
        <!--short code-->
        <div class="Row" id="short_code">
            <div class="left-Col">
                <?php echo __('short code') .'(' .__('use for record no').')'; ?>
                <label class="required">(*)</label>
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_short_code" id="txt_short_code" value="<?php echo $v_short_code; ?>" 
                       data-allownull="no" data-validate="text" 
                       data-name="<?php echo __('short code') ?>" 
                       data-xml="no" data-doc="no" 
                       autofocus="autofocus" 
                       size="60"/>
            </div>
        </div>
       <!--End member code-->
        <div class="Row">
                <div class="left-Col">
                   <?php echo __('address');?>
                    <label class="required">(*)</label>
                </div>
                <div class="right-Col">
                    <input type="textbox" name="txt_member_address" id="txt_member_address" value="<?php echo $v_member_address;?>" 
                            data-allownull="no" data-validate="text" 
                            data-name="<?php echo __('address')?>" 
                            data-xml="no" data-doc="no" 
                            autofocus="autofocus" 
                            size="60"/>
                </div>
            </div>
       <!--End member address-->
        
        <div class="Row" id="exchange_email">
                <div class="left-Col">
                   <?php echo __('member email');?>
                    <label class="required">(*)</label>
                </div>
                <div class="right-Col">
                    <input type="textbox" name="txt_member_email" id="txt_member_email" value="<?php echo $v_member_email;?>" 
                            data-allownull="no" data-validate="email" 
                            data-name="<?php echo __('email')?>" 
                            data-xml="no" data-doc="no" 
                            autofocus="autofocus" 
                            size="60"/>
                </div>
            </div>
       <!--End member email-->
        <div class="Row">
            <div class="left-Col">
                <?php echo __('member level'); ?>
                <label class="required">(*)</label>
            </div>
            <div class="right-Col">
                <div style="float: left">
                    <label>
                        <input onclick="show_div_member_level(this);" <?php echo ($rad_level == 0)? 'checked': ''; ?> 
                               type="radio" value="0" data-type="" 
                               name="rad_level" id="rad_level_0" >
                                <?php echo __('member department')?>
                    </label>
                    <br />
                    <label>
                        <input onclick="show_div_member_level(this);" 
                               type="radio"  <?php echo ($rad_level == 1)? 'checked': ''; ?>  
                               value="1" data-type="" name="rad_level" 
                               id="rad_level_1" >
                                <?php echo __('member district')?>
                    </label>
                    <br />
                     <label>
                         <input onclick="show_div_member_level(this);" 
                                type="radio"  <?php echo ($rad_level == 2)? 'checked': ''; ?>  
                                value="2" data-type="" name="rad_level" id="rad_level_2" >
                                <?php echo __('member village')?>
                    </label>
                </div>
            </div>
        </div>
        <!--End member level-->
        
        <div id="box-member-level-2" class="Row" >
            <div class="left-Col">
                <?php echo __('of units member'); ?>
                <label class="required">(*)</label>
            </div>
            <div class="right-Col">
                <div style="float: left">
                    <select name="sel_member_parent" id="sel_member_parent" style="min-width: 330px" onchange="get_village(this)">
                        <option value="0" >----  Chọn đợn vị ----</option>
                        <?php 
                            for($i=0;$i<count($arr_single_member_level_1);$i ++)
                            {
                                $v_member_level_id   = $arr_single_member_level_1[$i]['PK_MEMBER'];
                                $v_parent_level_name = $arr_single_member_level_1[$i]['C_NAME'];
                                $v_selected         = ($v_member_level_id == $v_member_parent_id) ? " selected='selected' " : '';
                                
                                echo "<option $v_selected  value='$v_member_level_id'>$v_parent_level_name</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div id="service_info" >
            <?php
                $v_xml_file_name = 'xml_link.xml';
                if ($this->load_xml($v_xml_file_name))
                {
                    echo $this->render_form_display_single();
                }
            ?>
        </div>
        <!--End member level-->
        <div class="Row" id="row_village">
            <div class="left-Col">
                <?php echo __('is village')?>
                <label class="required">(*)</label>
            </div>
            <div class="right-Col" id="div_village" style="height: 200px;overflow: scroll;display: block;"> 
            </div>
        </div>
		<!--end is village-->
         <div class="Row">
            <div class="left-Col">
                Thứ tự hiển thị
            </div>
            <div class="right-Col">
                <div style="float: left">
                    
					<input name="txt_order" type="textbox" id="txt_order" value="<?php echo $txt_order; ?>" >
                </div>
            </div>
        </div>
        <!--end is village-->
         <div class="Row">
            <div class="left-Col">
                &nbsp;
            </div>
            <div class="right-Col">
                <div style="float: left">
                    <label><input name="chk_status" type="checkbox" <?php echo ($v_status == 1) ? 'checked="checked"' : ''; ?> >Hoạt động</label>
                </div>
            </div>
        </div>
        <!--End member status-->
        <label class="required" id="message_err"></label>
        <div class="button-area">
        <?php if(session::check_permission('SUA_MEMBER')):?>
            <input type="button" name="btn_update" id="btn_update" class="ButtonAccept" value="<?php echo __('update');?>" onclick="update_member();"/>
        <?php endif;?>
            <input type="button" name="btn_back" id="btn_cancel" class="ButtonBack" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>
        </div>
</form>

<script>
    var html_service_info;
    var html_short_code;
    var html_exchange_email;
    var html_village;
    $(document).ready(function() {
        //gan bien
        html_service_info   = $('#service_info').html();
        html_short_code     = $('#short_code').html();
        html_exchange_email = $('#exchange_email').html();
        html_village        = $('#row_village').html();
        //hien thi div
        
        show_div_member_level($('[name="rad_level"]:checked'));
        get_village($('#sel_member_parent'),<?php echo $v_village_id?>);
        
         //Fill data
        var formHelper = new DynamicFormHelper('','',document.frmMain);
        formHelper.BindXmlData();
    });
    
    function update_member()
    {
        var member_level =$('[name="rad_level"]:checked').val();
        if(parseInt(member_level) == 2)
        {
            var village = $('[name="rad_village"]:checked').val();
            if(typeof village == 'undefined')
            {
                $('#village_error').remove();
                $('#row_village').append('<span id="village_error" class="required">Bạn phải chọn đơn vị</span>');
                return false;
            }
        }
        btn_update_onclick();
    }
    /**
     * hien thi div thong tin khi la don vi cap xa
     */
    function show_div_member_level(anchor) 
    {
        var member_level_current = '<?php echo $v_member_level_id; ?>';
        var member_level = $(anchor).val();
        if(member_level == 2)//cap xa
        {
            //hien thi select distric
            $('#box-member-level-2').show();
            $('#row_village').html(html_village);
            
            //an thong tin service
            html_service_info = $('#service_info').html();
            $('#service_info').html('');
            
            html_short_code = $('#short_code').html();
            $('#short_code').html('');
            
            html_exchange_email = $('#exchange_email').html();
            $('#exchange_email').html('');
            
            
        }
        else//cap so-huyen
        {
            $('#box-member-level-2').hide();
            $('#sel_member_parent').val(0);
            
            $('#service_info').html(html_service_info);
            $('#short_code').html(html_short_code);
            $('#exchange_email').html(html_exchange_email);
            //xoa div village
            html_village = $('#row_village').html();
            $('#row_village').html('');
        }
    }
    function get_village(sel_parent,village_id)
    {
        var url = $('#controller').val() + 'arp_get_all_village';
        var district_id = $(sel_parent).val();
        var checked = '';
        if(typeof(village_id) == 'undefined')
        {
            village_id = 0;
        }
        
        if(parseInt(district_id) > 0)
        {
            $.ajax({
                url: url,
                type: 'POST',
                data:{district_id:district_id},
                beforeSend: function() {
                     img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $('#div_village').html(img);
                 },
                success:function(respond)
                {
                    var arr_village = JSON.parse(respond);
                    var html = '';
                    if(typeof arr_village.errors != 'undefined')
                    {
                        html = arr_village.errors;
                    }
                    else if(arr_village.length < 1)
                    {
                        html = 'Hiện tại chưa có xã trực thuộc';
                    }
                    else
                    {
                        for(var key in arr_village)
                        {
                            if(village_id == key)
                            {
                                checked = 'checked';
                            }
                            else
                            {
                                checked = '';
                            }
                            html += '<label><input type="radio" name="rad_village" value="'+key+'" '+checked+' onclick="auto_fill_data(this)"><span>'+arr_village[key]+'</span></label><br>';
                        }
                        $('#div_village').html(html);
                        //thay doi mau rad checked
                        change_rad_village_color($('[name="rad_village"]').filter('[value="'+village_id+'"]'));
                    }
                    $('#div_village').html(html);
                    //thay doi mau rad checked
                    change_rad_village_color($('[name="rad_village"]').filter('[value="'+village_id+'"]'));
                }
            }); 
        }
    }
    
    function auto_fill_data(rad_village)
    {
        var village_name = $(rad_village).parent().find('span').html();
        $('#txt_member_name').val(village_name);
        change_rad_village_color(rad_village)
    }
    function change_rad_village_color(rad_village)
    {
        //thay doi mau sac
        $('#div_village label').css('background','white');
        $(rad_village).parent().css('background','yellow');
    }
</script>
<?php
$this->template->display('dsp_footer.php');

