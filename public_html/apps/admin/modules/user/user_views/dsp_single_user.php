<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Cập nhật NSD';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------

$arr_single_user            = $VIEW_DATA['arr_single_user'];
$arr_parent_ou_path         = $VIEW_DATA['arr_parent_ou_path'];
$arr_all_group_by_user      = $VIEW_DATA['arr_all_group_by_user'];
$arr_all_job_title          = $VIEW_DATA['arr_all_job_title'];

$v_user_id            = isset($arr_single_user['PK_USER'])?$arr_single_user['PK_USER']:0;
$v_ou_id              = isset($arr_single_user['FK_OU'])?$arr_single_user['FK_OU']:0;
$v_name               = isset($arr_single_user['C_NAME'])?$arr_single_user['C_NAME']:'';
$v_login_name         = isset($arr_single_user['C_LOGIN_NAME'])?$arr_single_user['C_LOGIN_NAME']:'';
$v_order              = isset($arr_single_user['C_ORDER'])?$arr_single_user['C_ORDER']:1;
$v_status             = isset($arr_single_user['C_STATUS'])?$arr_single_user['C_STATUS']:0;
//$v_xml_data    = $arr_single_user['C_XML_DATA'];
$v_job_title          = isset($arr_single_user['C_JOB_TITLE'])?$arr_single_user['C_JOB_TITLE']:0;

$v_xml_work_history   = isset($arr_single_user['C_XML_WORK_HISTORY'])?$arr_single_user['C_XML_WORK_HISTORY']:'';
$v_xml_education      = isset($arr_single_user['C_XML_EDUCATION'])?$arr_single_user['C_XML_EDUCATION']:'';
$v_quit_job           = isset($arr_single_user['C_QUIT_JOB'])?$arr_single_user['C_QUIT_JOB']:0;
$v_alias              = isset($arr_single_user['C_ALIAS'])?$arr_single_user['C_ALIAS']:'';
$v_birth_day          = isset($arr_single_user['C_BIRTHDAY'])?$arr_single_user['C_BIRTHDAY']:'';
$v_gender             = isset($arr_single_user['C_GENDER'])?$arr_single_user['C_GENDER']:0;
$v_id_card            = isset($arr_single_user['C_ID_CARD'])?$arr_single_user['C_ID_CARD']:'';
$v_address            = isset($arr_single_user['C_ADDRESS'])?$arr_single_user['C_ADDRESS']:'';
$v_mobile             = isset($arr_single_user['C_MOBILE'])?$arr_single_user['C_MOBILE']:'';
$v_phone              = isset($arr_single_user['C_PHONE'])?$arr_single_user['C_PHONE']:'';
$v_fax                = isset($arr_single_user['C_FAX'])?$arr_single_user['C_FAX']:'';
$v_email              = isset($arr_single_user['C_EMAIL'])?$arr_single_user['C_EMAIL']:'';
$v_thumbnail_file     = isset($arr_single_user['C_PORTRAIT_FILE_NAME'])?$arr_single_user['C_PORTRAIT_FILE_NAME']:'';
    

$v_xml_data    = '';

?>
<form name="frmMain" method="post" id="frmMain" action=""><?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_user');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_user');
    echo $this->hidden('hdn_update_method', 'update_user');
    echo $this->hidden('hdn_delete_method', 'delete_user');

    echo $this->hidden('hdn_item_id', $v_user_id);
    echo $this->hidden('XmlData', $v_xml_data);

    echo $this->hidden('pop_win', $v_pop_win);

    echo $this->hidden('hdn_deleted_doc_file_id_list', '');

    echo $this->hidden('hdn_group_id_list', '');

    echo $this->hidden('hdn_grant_function', '');
    
    echo $this->hidden('hdn_grant_category','');
    
    echo $this->hidden('hdn_job_title','a');
    
    echo $this->hidden('hdn_website_id','');
    
    echo $this->hidden('hdn_grant_function_without_website','');
    
    echo $this->hidden('hdn_thumbnail',$v_thumbnail_file);
    ?>

    <div id="tabs_user">
        <ul>
            <li><a href="#user_info"><?php echo __('user personal info')?></a></li>
            <li><a href="#user_group"><?php echo __('in group')?></a></li>
            <li><a href="#user_permit"><?php echo __('grant permission to user')?></a></li>
        </ul>
        <!--thong tin nguoi dung------------------------------------------------------------------------------------------------------------------------->
        <div id="user_info">
            <!-- Cot tuong minh -->
            <table width="100%" class="main_table" cellpadding="0" cellspacing="0">
                <colgroup>
                    <col width="25%" />
                    <col width="75%" />
                </colgroup>
                <!--ten dang nhap-->
                <tr>
                    <td>
                        <?php echo __('login name')?><label class="required">(*)</label>
                    </td>
                    <td>
                        <?php if ($v_user_id < 1): ?>
                            <input type="text" name="txt_login_name" id="txt_login_name"value=""
                                class="inputbox" maxlength="50" style="width:50%"
                                onKeyDown="return handleEnter(this, event);"
                                data-allownull="no" data-validate="text"
                                data-name="<?php echo __('login name')?>"
                                data-xml="no" data-doc="no"
                                autofocus="autofocus"
                            />
                        <?php else: ?>
                            <strong><?php echo $v_login_name;?></strong>
                        <?php endif; ?>
                    </td>
                </tr><!--end ten dang nhap-->
                <?php  if ($v_user_id < 1): //neu la them moi?>
                    <!--password-->
                    <tr>
                        <td><?php echo __('password')?><label class="required">(*)</label></td>
                        <td>
                            <input type="password" name="txt_new_password" id="txt_new_password" value=""
                                class="inputbox" maxlength="50" style="width:50%"
                                onblur="check_password_onblur(this)" 
                                data-allownull="no" data-validate="text"
                                data-name="<?php echo __('password')?>"
                                data-xml="no" data-doc="no"
                            />
                            <label name="pass_check" id="pass_check" class="required"></label>
                        </td>
                    </tr>
                    <!--confirm password-->
                    <tr>
                        <td><?php echo __('confirm password')?> <label class="required">(*)</label></td>
                        <td>
                            <input type="password" name="txt_confirm_password" id="txt_confirm_new_password" value=""
                                class="inputbox" maxlength="50" style="width:50%"
                                onKeyDown="return handleEnter(this, event);" 
                                onblur="check_confirm_password_onblur()" 
                                data-allownull="no" data-validate="text"
                                data-name="<?php echo __('confirm password')?>"
                                data-xml="no" data-doc="no"
                            />
                            <label name="confirm_pass_check" id="confirm_pass_check" class="required"></label>
                        </td>
                    </tr>
                <?php else: //user da co san?>
                    <!--doi password-->
                    <tr>
                        <td><?php echo __('new password')?></td>
                        <td>
                            <input type="password" name="txt_new_password" id="txt_new_password" value=""
                                class="inputbox" maxlength="50" style="width:50%"
                                onKeyDown="return handleEnter(this, event);" 
                                onblur="check_password_onblur(this)" 
                                data-allownull="yes" data-validate="text"
                                data-name="<?php __('current password')?>"
                                data-xml="no" data-doc="no"
                            />
                            <label name="pass_check" id="pass_check" class="required"></label>
                        </td>
                    </tr>
                    <!--confirm password-->
                    <tr>
                        <td><?php echo __('confirm new password')?></td>
                        <td>
                            <input type="password" name="txt_confirm_new_password" id="txt_confirm_new_password" value=""
                                class="inputbox" style="width:50%"
                                onKeyDown="return handleEnter(this, event);" 
                                onblur="check_confirm_password_onblur()" 
                                data-allownull="yes" data-validate="text"
                                data-name="<?php __('new password')?>"
                                data-xml="no" data-doc="no"
                            />
                            <label name="confirm_pass_check" id="confirm_pass_check" class="required"></label>
                        </td>
                    </tr>
                <?php endif; ?>
                <!--ten nguoi su dung-->
                <tr>
                    <td><?php echo __('full name')?><label class="required">(*)</label></td>
                    <td>
                        <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_name;?>"
                            class="inputbox" maxlength="50" style="width:50%"
                            onKeyDown="return handleEnter(this, event);"
                            data-allownull="no" data-validate="text"
                            data-name="<?php echo __('user name')?>"
                            data-xml="no" data-doc="no"
                        />
                    </td>
                </tr><!--end ten nguoi su dung-->
                <!--bi danh-->
                <tr>
                    <td><?php echo __('alias')?></td>
                    <td>
                        <input type="text" name="txt_alias" id="txt_alias" value="<?php echo $v_alias?>"
                            class="inputbox" maxlength="50" style="width:30%"/>
                    </td>
                </tr>
                <!--ngay sinh-->
                <tr>
                    <td><?php echo __('birthday')?><label class="required">(*)</label></td>
                    <td>
                       <input type="text" name="txt_date_of_birth" id="txt_date_of_birth" value="<?php echo $v_birth_day?>" 
                         onClick="DoCal('txt_date_of_birth');" size="10" 
                         data-allownull="no" data-validate="date"
                          data-name="ngày sinh"
                          data-xml="no" data-doc="no"/> 
                        <img  height="16" width="16" src="<?php echo SITE_ROOT ?>public/images/calendar.png"
                                onClick="DoCal('txt_date_of_birth');"/>
                    </td>
                </tr>
                <!--gioi tinh-->
                <tr>
                    <td><?php echo __('gender')?></td>
                    <td>
                        <label>
                            <input type="radio" name="rad_sex" id="rad_sex" value="0" <?php echo ($v_gender == '0')?'checked':'';?>>
                            &nbsp; Nam
                        </label>

                        <label>
                            <input type="radio" name="rad_sex" id="rad_sex" value="1" <?php echo ($v_gender == '1')?'checked':'';?>>
                            &nbsp; Nữ
                        </label>
                    </td>
                </tr>
                <!--CMND-->
                <tr>
                    <td><?php echo __('id card')?><label class="required">(*)</label></td>
                    <td>
                        <input type="textbox" id="txt_identification_number" name="txt_identification_number" value="<?php echo $v_id_card?>" size="30"
                        data-allownull="no" data-validate="unsignNumber"
                          data-name="Số CMND"
                          data-xml="no" data-doc="no" />
                    </td>
                </tr>
                <!--address-->
                <tr>
                    <td><?php echo __('address')?> <label class="required">(*)</label></td>
                    <td>
                        <textarea style="width: 50%;" name="txt_address" id="txt_address" 
                          data-allownull="no" data-validate="text"
                          data-name="địa chỉ"
                          data-xml="no" data-doc="no"><?php echo $v_address?></textarea>
                    </td>
                </tr>
                <!--mobile-->
                <tr>
                    <td><?php echo __('mobile')?> <label class="required">(*)</label></td>
                    <td>
                        <input type="textbox" id="txt_mobile" name="txt_mobile" value="<?php echo $v_mobile?>" size="30" 
                       data-allownull="no" data-validate="unsignNumber"
                          data-name="sđt di động"
                          data-xml="no" data-doc="no"/>
                    </td>
                </tr>
                <!--phone-->
                <tr>
                    <td><?php echo __('phone')?></td>
                    <td>
                        <input  type="textbox" id="txt_phone" name="txt_phone" value="<?php echo $v_phone;?>" size="30" 
                                data-allownull="yes" data-validate="unsignNumber"
                          data-name="số điện thoại"
                          data-xml="no" data-doc="no"/>
                    </td>
                </tr>
                <!--fax-->
                <tr>
                    <td><?php echo __('fax')?></td>
                    <td>
                        <input type="textbox" id="txt_fax" name="txt_fax" value="<?php echo $v_fax;?>" size="30"
                                data-allownull="yes" data-validate="unsignNumber"
                          data-name=" mã fax"
                          data-xml="no" data-doc="no"/>
                    </td>
                </tr>
                <!--email-->
                <tr>
                    <td><?php echo __('email')?> <label class="required">(*)</label></td>
                    <td>
                        <input type="textbox" id="txt_email" name="txt_email" value="<?php echo $v_email;?>" size="30"
                       data-allownull="no" data-validate="email"
                          data-name="email"
                          data-xml="no" data-doc="no"/>
                    </td>
                </tr>
                <!--thuoc don vi-->
                <tr>
                    <td><?php echo __('in ou')?></td>
                    <td>
                        <?php 
                        $v_ou_patch='';
                        foreach ($arr_parent_ou_path as $id => $name)
                        {
                            $v_ou_patch .= '/'.$name; 
                        }
                        echo $this->hidden('hdn_parent_ou_id', $id);
                        ?>
                        <input type ="textbox" id="txt_ou_patch" name="txt_ou_patch" value="<?php echo $v_ou_patch;?>" 
                               style="width:50%"
                               DISABLED/>
                        <input type="button" class="ButtonAddOu" onclick="dsp_all_ou_to_add()">
                    </td>
                </tr><!--end thuoc don vi-->
                <!--chuc danh-->
                <tr>
                    <td>
                        <?php echo __('user job title')?>:
                    </td>
                    <td>
                        <select onchange="btn_job_title_onchange(this)">
                            <option value="-1">---Chọn chức danh---</option>
                            <?php echo $this->generate_select_option($arr_all_job_title,$v_job_title);?>
                        </select>
                    </td>
                </tr><!--end chuc danh-->
                <!--so thu tu-->
                <tr>
                    <td><?php echo __('order'); ?><label class="required">(*)</label></td>
                    <td>
                        <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                        class="inputbox" size="4" maxlength="3"
                        data-allownull="no" data-validate="unsignNumber"
                        data-name="<?php echo __('order'); ?>"
                        data-xml="no" data-doc="no"
                        />
                    </td>
                </tr><!--end so thu tu-->
                <!--trang thai-->
                <tr>
                    <td><?php echo __('status'); ?></td>
                    <td>
                         <input type="checkbox" name="chk_status" value="1"
                            <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                            id="chk_status"
                        /><label for="chk_status"><?php echo __('active status'); ?></label><br/>
                    </td>
                </tr>
                <!--dang lam viec-->
                <tr>
                    <td>&nbsp;</td>
                    <td>
                         <label>
                            <input type="checkbox" name="chk_quit_job" id="chk_quit_job" <?php echo ($v_quit_job == 1)?'checked':'';?>>
                            <?php echo __('quit job')?>
                        </label>
                    </td>
                </tr>
                <!--anh dai dien-->
                <tr>
                    <td><?php echo __('portrait'); ?></td>
                    <td>
                         <div class="ui-widget">
                            <div class="ui-widget-header ui-state-default ui-corner-top">
                                <h4>
                                    <a 
                                        href="javascript:;" style="float:right;text-decoration: underline;"
                                        onClick="delete_thumbnail_onclick();"
                                        >
                                            <?php echo __('delete') ?>
                                    </a>
                                    <font><?php echo __('thumbnail') ?></font>
                                </h4>

                            </div>
                            <div class="ui-widget-content Center" id="thumbnail_container" 
                                 style="padding-bottom:5px;" onClick="thumbnail_onclick();">
                                </br>
                                <?php if ($v_thumbnail_file): ?>
                                    <img 
                                        width="250" 
                                        src="<?php echo SITE_ROOT . 'upload/' . $v_thumbnail_file ?>"
                                        />
                                    <?php else: ?>
                                    <div style="width:250px;height: 150px;border:dashed #C0C0C0;margin: 0 auto;">
                                        <a href="javascript:;">
                                            <h4 class="Center">
                                                <?php echo __('choose image') ?>
                                            </h4>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div><!--widget thumbnail-->
                    </td>
                </tr>
            </table>
        </div><!--end thong tin nguoi dung-->
        
        
        <!--thuoc nhom------------------------------------------------------------------------------------------------------------------------->
        <div id="user_group">
            <div id="group_of_user" class="edit-box">
                <table width="100%" class="adminlist" cellspacing="0" border="1" id="tbl_user_in_group">
                    <colgroup>
                        <col width="5%" />
                        <col width="95%" />
                    </colgroup>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('group name');?>:</th>
                    </tr>
                    <?php foreach ($arr_all_group_by_user as $v_group_id => $v_group_name): ?>
                        <tr id="tr_<?php echo $v_group_id;?>">
                            <td class="center">
                                <input type="checkbox" name="chk_group" value="<?php echo $v_group_id;?>" id="chk_group_<?php echo $v_group_id;?>" />
                            </td>
                            <td>
                                <img src="<?php echo $this->template_directory . 'images/user-group16.png' ;?>" border="0" align="absmiddle" />
                                <label for="chk_group_<?php echo $v_group_id;?>"><?php echo $v_group_name;?></label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div id="group_of_users_action">
                <input type="button" name="btn_add_group" value="<?php echo __('join group');?>" class="ButtonAdd" onclick="dsp_all_group_to_add()"/><br/>
                <input type="button" name="btn_remove_group" value="<?php echo __('leave group');?>" class="ButtonDelete" onclick="remove_group_from_user()"/>
            </div>
        </div><!--end thuoc nhom-->
        
        <div class="clear">&nbsp;</div>
        <!--phan quyen cho nguoi dung------------------------------------------------------------------------------------------------------------------------->
        <div id="user_permit">
            <div id="permit_without_website">
                <label><?php echo __('Quyền quản trị')?></label>
                <?php
                    $this->load_xml('xml_permission_without_website.xml');
                    echo $this->render_form_display_single();
                ?>
            </div>
            <div>
            <label><?php echo __('select website')?></label>
            <select name="sel_application" onchange="get_website_permit(this.value)">
                <option value="">&nbsp;</option>
                <?php echo $this->generate_select_option($arr_all_website_option);?>
            </select>
            </div>
            <div id="website_permit"></div>
        </div><!--end phan quyen cho nguoi dung-->
        <br>
        <label class="required" id="btn_update_check_onclick"></label>
    </div>
    <!-- XML data -->
    <!-- Button -->
    <div class="button-area">
        <input type="button" name="btn_update_user" class="ButtonAccept" value="<?php echo __('update'); ?>" onclick="btn_update_user_onclick(); "/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="ButtonCancel" value="<?php echo __('cancel'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        //Fill data
        $('#txt_confirm_new_password').attr('DISABLED',1);
        
         v_url =  "<?php echo $this->get_controller_url(); ?>arp_user_permit_without_website/&user_id=" + $('#hdn_item_id').val();
                $.getJSON(v_url, function(current_permit) {
                    for (i=0; i<current_permit.length; i++)
                    {
                        q = '#' + current_permit[i];
                        $(q).attr('checked', true);
                    }
                });
                
        $("#tabs_user" ).tabs();
    });

    function dsp_all_group_to_add()
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_all_group_to_add/&pop_win=1';

        showPopWin(url, 450, 350, add_group);
    }
    function add_group(returnVal)
    {
        json_data = JSON.stringify(returnVal);

        for (i=0; i<returnVal.length; i++)
        {
            v_group_id = returnVal[i].group_id;
            v_group_name = returnVal[i].group_name;

            //Neu user chua co  thi them vao
            q = '#chk_group_' + v_group_id;
            if( $(q).length < 1 )
            {
                html = '<tr id="tr_' + v_group_id + '">';
                html += '<td class="center">';
                html +=     '<input type="checkbox" name="chk_group" value="' + v_group_id + '" id="chk_group_' + v_group_id + '" />';
                html += '</td>';
                html += '<td>';
                html += '<img src="<?php echo $this->template_directory;?>images/user-group16.png" border="0" align="absmiddle" />';
                html += '<label for="chk_group_' + v_group_id + '">' + v_group_name + '</label>';
                html += '</td></tr>';
                $('#tbl_user_in_group').append(html);
            }
        }
    }
    function dsp_all_ou_to_add()
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_all_ou_to_add/&pop_win=1';
        
        showPopWin(url, 450, 350, add_ou);
    }
    function add_ou(returnVal)
    {
        var ou_id=returnVal[0].ou_id;
        var ou_patch = returnVal[0].ou_patch;
       
        $('#txt_ou_patch').attr('value',ou_patch);
        $('#hdn_parent_ou_id').val(ou_id);
       
    }
    function remove_group_from_user()
    {
        var q = "input[name='chk_group']";
        $(q).each(function(index) {
            if ($(this).is(':checked'))
            {
                s = '#tr_' + $(this).val();
                $(s).remove();
            }
        });
    }

    function btn_update_user_onclick()
    {
        if($('#pass_check').html()!='' || $('#confirm_pass_check').html()!='')
        {
            $('#btn_update_check_onclick').html('');
            $('#btn_update_check_onclick').html('<?php echo __('check button update');?>');
            return false;
        }
        
        //Lay danh sach nhom
        var arr_group_id = new Array();
        var q = "input[name='chk_group']";
        $(q).each(function(index) {
            arr_group_id.push($(this).val());
        });

        document.frmMain.hdn_group_id_list.value = arr_group_id.join();
       
        //Lay danh sach ma function da danh dau
        var q = "#permit_without_website input[type='checkbox']";
        var arr_checked_function_without_web = new Array();
        $(q).each(function (index){
            if ($(this).is(':checked') && parseBoolean($(this).attr('data-xml')))
            {
                arr_checked_function_without_web.push($(this).attr('id'));
            }
        });

        var q = "#website_permit input[type='checkbox']";
        var arr_checked_function = new Array();
        var arr_checked_category = new Array();
        $(q).each(function(index) {
            if ($(this).is(':checked') && parseBoolean($(this).attr('data-xml')))
            {
                if(parseBoolean($(this).attr('data-category')))
                {
                    
                    arr_checked_category.push($(this).attr('data-id'));
                }
                else
                {
                    arr_checked_function.push($(this).attr('id'));
                }
            }
        });
 
        $('#hdn_grant_function_without_website').val(arr_checked_function_without_web.join());
        $('#hdn_grant_function').val(arr_checked_function.join());
        $('#hdn_grant_category').val(arr_checked_category.join());
        website_id = $('[name="sel_application"]').val();
        $('#hdn_website_id').val(website_id);
        btn_update_onclick();
    }


    function get_website_permit(website_id)
    {
        if (website_id > 0)
        {
            $.ajax({url:"<?php echo $this->get_controller_url();?>dsp_website_permit/"+website_id, success:function(result){
                    $("#website_permit").html(result);
                    //Danh dau cac quyen da duoc phan
                    
                    v_url =  "<?php echo $this->get_controller_url();?>arp_user_permit_on_website/&website_id=" + website_id + '&user_id=' + $('#hdn_item_id').val();
                    $.getJSON(v_url, function(current_permit) {
                        for (i=0; i<current_permit.length; i++)
                        {
                            q = '#' + current_permit[i];
                            $(q).attr('checked', true);
                        }
                    });
                   v_url = "<?php echo $this->get_controller_url();?>arp_user_permit_on_category/&user_id=" + $('#hdn_item_id').val();
                   $.getJSON(v_url, function(current_permit) {
                        for (i=0; i<current_permit.length; i++)
                        {
                            q = '#cat_' + current_permit[i];
                            $(q).attr('checked', true);
                        }
                    });
                }
            });
        }
        else
        {
        	$("#website_permit").html('');
        }
    }
    function btn_job_title_onchange(title)
    {
        $('#hdn_job_title').attr('value',$(title).val());
    }
    function check_password_onblur(pass)
    {
        var no = $(pass).val().length;
        var str = '&nbsp;<?php echo __("check password");?>';
        if(no <6 && no >0)
        {
            $('#pass_check').html('');
            $('#pass_check').html(str);
            $('#txt_confirm_new_password').attr("DISABLED",1);
        }
        else if(no==0)
        {
             $('#pass_check').html('');
             $('#txt_confirm_new_password').attr("DISABLED",1);
             $('#confirm_pass_check').html('');
        }
        else
        {
            $('#pass_check').html('');
            $('#txt_confirm_new_password').removeAttr('DISABLED');
        }
    }
    function check_confirm_password_onblur()
    {
        if($('#txt_new_password').val() == $('#txt_confirm_new_password').val())
        {
            var html = '<img id="img_confirm_pass" src="<?php echo $this->image_directory.'AcceptButton.gif';?>"/>'
            $('#confirm_pass_check').html('');
            $('#img_confirm_pass').remove();
            $('#txt_confirm_new_password').parent().append(html);
        }
        else if($('#txt_new_password').val()!= '')
        {
            $('#confirm_pass_check').html('<?php echo __("check confirm password");?>');
            $('#img_confirm_pass').remove();
        }
    }
    
    
    //xu ly anh dai dien
    function thumbnail_onclick()
    {
        var $url = "<?php echo $this->get_controller_url('advmedia', 'admin') . 'dsp_service/image'; ?>";
        showPopWin($url, 800, 600, function(json_obj){
            if(json_obj[0])
            {
                $file = json_obj[0]['path'];
                var $html = '</br><img width="250"';
                $html += 'onClick="thumbnail_onclick();"';
                $html += ' src="<?php echo SITE_ROOT . 'upload' . '/' ?>' + $file + '"/>';
                $('#thumbnail_container').html($html);
                $('#hdn_thumbnail').val($file);
            }
        });
    }
    
    function delete_thumbnail_onclick()
    {
        var $html = '</br>'
            + '<div style="width:250px;height: 150px;border:dashed #C0C0C0;margin: 0 auto;">'
            +'<a href="javascript:;">'
            + '<h4 class="center">'
            +    ' <?php echo __('choose image') ?>'
            +  '</h4>'
            + '</a>'
            +  '</div>';
        $('#thumbnail_container').html($html);
        $('#hdn_thumbnail').val('');
    }
    //end
    
//    tieu su
    $(document).ready(function (){
        <?php if($v_xml_education == ''):?>
            btn_add_html('education','1');
        <?php endif;?>
        <?php if($v_xml_work_history == ''):?>
            btn_add_html('work','1');
        <?php endif;?>
    });
    
    //them moi hoc van hoc cong viec
    function btn_add_html(type,not_decoration)
    {
        div_show = '';
        if(type == 'work')
        {
            id_no = parseInt($('#div_standard_work .ButtonDelete').length) + 1;
            
            current_html = build_html(type,id_no);
            div_show = '#div_work';
        }
        else if(type == 'education')
        {
            id_no = parseInt($('#div_standard_education .ButtonDelete').length) + 1;
            current_html = build_html(type,id_no);
            div_show = '#div_education';
        }
        
        if(typeof(not_decoration) == 'undefined')
        {
            current_html = '<div>' 
                        + 
                        '<center><div style="width: 50%;margin: 5px 0px 5px 0px;border-bottom: 1px #000000 solid">&nbsp;</div></center>' 
                        + 
                        current_html 
                        + 
                        '</div>';
        }
        else
        {
            current_html = '<div>' + current_html + '</div>';
        }
                
        $(div_show).append(current_html);
    }
    
    //xoa hoc van hoac cong viec
    function delete_onclick(object)
    {
        $(object).parent().parent().html('');
    }
    
    
    function build_html(type,id_no)
    {
        if(type == 'work')
        {
            html_work = '<div class="Row">\n\
                        <div class="left-Col">Công ty</div>\n\
                        <div class="right-Col">\n\
                            <input type="textbox" name="txt_work_name[]" id="txt_work_name_'+id_no+'" size="50"/>\n\
                        </div>\n\
                    </div>\n\
                    <div class="Row">\n\
                        <div class="left-Col">Địa chỉ</div>\n\
                        <div class="right-Col">\n\
                            <input type="textbox" name="txt_work_address[]" id="txt_work_address_'+id_no+'" size="70">\n\
                        </div>\n\
                    </div>\n\
                    <div class="Row">\n\
                        <div class="left-Col">Chức vụ</div>\n\
                        <div class="right-Col">\n\
                            <input type="textbox" name="txt_work_position[]" id="txt_work_position_'+id_no+'" size="30">\n\
                        </div>\n\
                    </div>\n\
                    <div class="Row">\n\
                        <div class="left-Col">Thời gian</div>\n\
                        <div class="right-Col">\n\
                            <input type="text" name="txt_work_start[]" id="txt_work_start_'+id_no+'" value="" onClick="DoCal(\'txt_work_start_'+id_no+'\');"/> \n\
                            &nbsp;&nbsp;&nbsp;&nbsp;\n\
                            <input type="text" name="txt_work_finish[]" id="txt_work_finish_'+id_no+'" value="" onClick="DoCal(\'txt_work_finish_'+id_no+'\');"/>\n\
                        </div>\n\
                    </div>\n\
                    <div class="button-area">\n\
                        <input type="button" class="ButtonDelete" value="Xóa" onclick="delete_onclick(this);"/>\n\
                    </div>';
            return html_work;
        }
        else if('education')
        {
            html_education = '<div class="Row">\n\
                            <div class="left-Col">Trường</div>\n\
                            <div class="right-Col">\n\
                                <input type="textbox" name="txt_education_school[]" id="txt_school_'+id_no+'" size="50"/>\n\
                            </div>\n\
                        </div>\n\
                        <div class="Row">\n\
                            <div class="left-Col">Địa chỉ</div>\n\
                            <div class="right-Col">\n\
                                <input type="textbox" name="txt_education_address[]" id="txt_education_address_'+id_no+'" size="70">\n\
                            </div>\n\
                        </div>\n\
                        <div class="Row">\n\
                            <div class="left-Col">Khóa</div>\n\
                            <div class="right-Col">\n\
                                <label>Từ năm: \n\
                                    &nbsp;&nbsp;\n\
                                    <input type="textbox" value="" name="txt_education_begin_year[]" id="txt_education_begin_year_'+id_no+'" size="5">\n\
                                    &nbsp;&nbsp; \n\
                                    Đến năm: &nbsp;&nbsp;\n\
                                    <input type="textbox" value="" name="txt_education_end_year[]" id="txt_education_end_year_'+id_no+'" size="5">\n\
                                </label>\n\
                            </div> \n\
                        </div>\n\
                        <div class="Row">\n\
                            <div class="left-Col">Trình độ</div>\n\
                            <div class="right-Col">\n\
                                <select name="sel_education_degree[]" id="sel_education_degree_'+id_no+'">\n\
                                    <option>THPT</option>\n\
                                    <option>Cao đẳng</option>\n\
                                    <option>Đại học</option>\n\
                                    <option>Thạc sỹ</option>\n\
                                    <option>Tiến sỹ</option>\n\
                                    <option>Giáo sư</option>\n\
                                </select>\n\
                            </div>\n\
                        </div>\n\
                        <div class="button-area">\n\
                            <input type="button" class="ButtonDelete" value="Xóa" onclick="delete_onclick(this);"/>\n\
                        </div>';
            return html_education;
        }
    }
//end tieu su
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');