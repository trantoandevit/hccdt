<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
$this->template->title = __('record type detail');
$this->template->display('dsp_header.php');

//don vi thanh vien
$arr_all_village  =  $arr_all_member['arr_all_village'];
$arr_all_district =  $arr_all_member['arr_all_district'];
        
?>
<script src="<?php echo SITE_ROOT; ?>public/tinymce/script/tiny_mce.js"></script>

<div class="row-fluid ">
    <?php
    $arr_single_record_type = $VIEW_DATA['arr_single_record_type'];
    if (isset($arr_single_record_type['PK_RECORD_TYPE']))
    {
        $v_record_type_id      = $arr_single_record_type['PK_RECORD_TYPE'];
        $v_xml_data            = $arr_single_record_type['C_XML_DATA'];
        $v_code                = $arr_single_record_type['C_CODE'];
        $v_name                = $arr_single_record_type['C_NAME'];
        $v_status              = $arr_single_record_type['C_STATUS'];
        $v_order               = $arr_single_record_type['C_ORDER'];
        $v_spec_code           = $arr_single_record_type['C_SPEC_CODE'];
        $v_scope               = $arr_single_record_type['C_SCOPE'];

        $dom_xml_data = simplexml_load_string($arr_single_record_type['C_XML_DATA']);

        $v_cach_thuc_thuc_hien = get_xml_value($dom_xml_data, "//item[@id='cach_thuc_thuc_hien']/value");
        $v_thoi_han_giai_quyet = get_xml_value($dom_xml_data, "//item[@id='thoi_han_giai_quyet']/value");
        $v_doi_tuong_thuc_hien = get_xml_value($dom_xml_data, "//item[@id='doi_tuong_thuc_hien']/value");
        $v_phi_le_phi          = get_xml_value($dom_xml_data, "//item[@id='le_phi']/value");
        $v_trinh_tu_thuc_hien  = get_xml_value($dom_xml_data, "//item[@id='txta_trinh_tu_thuc_hien']/value");
        $v_ho_so               = get_xml_value($dom_xml_data, "//item[@id='txta_ho_so']/value");
        $v_co_quan_thuc_hien   = get_xml_value($dom_xml_data, "//item[@id='txta_co_quan_thuc_hien']/value");
        $v_ket_qua             = get_xml_value($dom_xml_data, "//item[@id='txta_ket_qua']/value");
        $v_can_cu_phap_ly      = get_xml_value($dom_xml_data, "//item[@id='txta_can_cu_phap_ly']/value");
    }
    else
    {
        $v_record_type_id      = 0;
        $v_xml_data            = '';
        $v_code                = '';
        $v_name                = '';
        $v_status              = 1;
        $v_scope               = 3;
        $v_order               = $arr_single_record_type['C_ORDER'];
        $v_send_over_internet  = 0;
        $v_allow_verify_record = 0;

        $dom_xml_data = simplexml_load_string('<data/>');
        $v_cach_thuc_thuc_hien = '';
        $v_thoi_han_giai_quyet = '';
        $v_doi_tuong_thuc_hien = '';
        $v_phi_le_phi          = '';
        $v_trinh_tu_thuc_hien  = '';
        $v_ho_so               = '';
        $v_co_quan_thuc_hien   = '';
        $v_ket_qua             = '';
        $v_can_cu_phap_ly      = '';
    }
    $xpath = '//list_file//item';
    $arr_filetype_name = $dom_xml_data->xpath($xpath);
    $v_list_filettype_old_id = '';
    for($i =0;$i<count($arr_filetype_name);$i++)
    {
        $v_filetype_id   = (string)$arr_filetype_name[$i]->attributes()->id;
        $v_list_filettype_old_id .= $v_filetype_id.',';
    }
    ?>
    <form name="frmMain" method="post" id="frmMain" enctype="multipart/form-data" action=""class="form-horizontal">
        <?php
            echo $this->hidden('controller', $this->get_controller_url());

            echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record_type');
            echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record_type');
            echo $this->hidden('hdn_update_method', 'update_record_type');
            echo $this->hidden('hdn_delete_method', 'delete_record_type');
            
            echo $this->hidden('hdn_delete_file_list_id', '');
            
            echo $this->hidden('hdn_file_type_list_old_id', $v_list_filettype_old_id);
            
            echo $this->hidden('hdn_item_id', $v_record_type_id);
            echo $this->hidden('XmlData', $v_xml_data);
            echo $this->hidden('chk_internet', get_post_var('chk_internet',''));
            echo $this->hidden('sel_status', get_post_var('sel_status',''));

            $this->write_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
        ?>
        <h2 class="module_title"><?php echo __('update record type');?></h2>
        <div class="Row">
            <div class="Row">
                <div class="left-Col">
                    Mã loại hồ sơ <span class="required">(*)</span>
                </div>
                <div class="right-Col">
                    <input type="text" name="txt_code" value="<?php echo $v_code; ?>" id="txt_code"
                           class="inputbox" maxlength="50" size="10"
                           onKeyDown="handleEnter(this, event);" onkeyup="ConverUpperCase('txt_code', this.value)"
                           data-allownull="no" data-validate="text"
                           data-name="Mã loại hồ sơ"
                           data-xml="no" data-doc="no" autofocus="autofocus"
                           />
                </div>
            </div>

            <div class="Row">
                <div class="left-Col">Tên loại hồ sơ <span class="required">(*)</span></div>
                <div class="right-Col">
                    <textarea name="txt_name" id="txt_name"
                              class="inputbox" style="width:80%"
                              data-allownull="no" data-validate="text"
                              data-name="Tên loại hồ sơ"
                              data-xml="no" data-doc="no" rows="3"
                              ><?php echo $v_name; ?></textarea>   
                </div>
            </div>

            <div class="Row">
                <div class="left-Col">Thuộc Lĩnh vực <span class="required">(*)</span></div>
                <div class="right-Col">
                    <select name="sel_spec_code" id="sel_spec_code" class="ddl" data-allownull="no" data-validate="ddli" 
                            data-name="Lĩnh vực" data-xml="no" data-doc="no"
                            >
                        <option value="">--Chọn lĩnh vực--</option>
                        <?php echo $this->generate_select_option($arr_all_spec, $v_spec_code); ?>
                    </select>
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">Đơn vị tiếp nhận hồ sơ trực tuyến</div>
                <div class="right-Col">
                    <div class="div_scroll"style="width: 100%;">
                        <table class="box-memner">
                            <colgroup>
                                <col width="50%" />
                                <col width="50%" />
                            </colgroup>
                             <?php foreach($arr_all_district as $arr_district):
                                $v_name             = $arr_district['C_NAME'];
                                $v_id               = $arr_district['PK_MEMBER'];
                                $v_code_mapping     = $arr_district['C_CODE_MAPPING'];
                                $v_checked = in_array($v_id, $arr_record_type_member)?'checked':'';
                            ?>
                            <tr>
                                <td>
                                    <label class="full_content">
                                        <input onclick="toggle_member_onlick(this,'code_mapping_<?php echo $v_id; ?>')" type="checkbox" name="chk_member[]" value="<?php echo $v_id?>" <?php echo $v_checked?>>
                                        <?php echo $v_name;?>
                                    </label>
                                </td>
                                <td>
                                    <label class="full_content code_mapping_<?php echo $v_id; ?>" style="<?php echo ($v_checked != '') ? 'display: block' : 'display: none'?>">
                                        Mã thủ tục tương ứng: <input <?php echo ($v_checked == '') ? 'disabled' : ''?> type="textbox"  name="code_mapping[]" value="<?php echo $v_code_mapping?>" >
                                    </label>
                                </td>
                            </tr>
                            
                            
                            <?php foreach($arr_all_village as $key => $arr_village):
                                    $v_village_name = $arr_village['C_NAME'];
                                    $v_village_id   = $arr_village['PK_MEMBER'];
                                    $v_parent_id    = $arr_village['FK_MEMBER'];
                                    $v_code_mapping = $arr_village['C_CODE_MAPPING'];
                                    if($v_parent_id != $v_id)
                                    {
                                        continue;
                                    }
                                    $v_checked = in_array($v_village_id, $arr_record_type_member)?'checked':'';
                            ?>
                            <tr>
                                <td>
                                    <label class="full_content">
                                     <input onclick="toggle_member_onlick(this,'code_mapping_<?php echo $v_village_id; ?>')" type="checkbox" name="chk_member[]" value="<?php echo $v_village_id?>" <?php echo $v_checked?>>
                                        ----- <?php echo $v_village_name;?>
                                    </label>
                                </td>
                                <td>
                                    <label class="full_content code_mapping_<?php echo $v_village_id; ?>" style="<?php echo ($v_checked != '') ? 'display: block' : 'display: none'?>">
                                        Mã thủ tục tương ứng: <input <?php echo ($v_checked == '') ? 'disabled' : ''?> type="textbox" name="code_mapping[]" value="<?php echo $v_code_mapping?>" >
                                    </label>
                                </td>
                            </tr>
                            
                            <?php 
                                unset($arr_all_village[$key]);
                                endforeach;
                            ?>
                            <?php endforeach;?>
                            
                        </table>
                    </div>
                </div>
            </div>

            <div class="Row">
                <div class="left-Col"><?php echo __('scope'); ?></div>
                <div class="right-Col">
                    <label style="display: block">
                        <input type="radio" name="rd_scope" value="0" <?php if($v_scope == 0 && $v_scope != NULL) echo 'checked'?>/> <?php echo __('commune/ward')?>
                    </label>
                    <label style="display: block">
                        <input type="radio" name="rd_scope" value="1" <?php if($v_scope == 1) echo 'checked'?>/> <?php echo __('inter-communal->district')?>
                    </label>
                    <label style="display: block">
                        <input type="radio" name="rd_scope" value="2" <?php if($v_scope == 2) echo 'checked'?>/> <?php echo __('inter-district->communal')?>
                    </label>
                    <label style="display: block">
                        <input type="radio" name="rd_scope" value="3" <?php if($v_scope == 3) echo 'checked'?>/> <?php echo __('district')?>
                    </label>
                </div>
            </div>
            <div class="Row">
                <div class="left-Col"><?php echo __('order'); ?> <span class="required">(*)</span></div>
                <div class="right-Col">
                    <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                           class="input-small" maxlength="50" size="10"
                           onKeyDown="handleEnter(this, event);"
                           data-allownull="no" data-validate="number"
                           data-name="<?php echo __('order'); ?>"
                           data-xml="no" data-doc="no"
                           />
                </div>
            </div>

            <div class="Row">
                <div class="left-Col"><?php echo __('status'); ?></div>
                <div class="right-Col">
                    <label class="checkbox">
                        <input type="checkbox" name="chk_status" value="1" <?php echo ($v_status > 0) ? ' checked' : ''; ?> id="chk_status" />
                        <?php echo __('active status'); ?>
                    </label>
                    <br>
                    <label class="checkbox">
                        <input type="checkbox" name="chk_save_and_addnew" value="1" <?php echo ($v_record_type_id > 0) ? '' : ' checked'; ?> id="chk_save_and_addnew" />
                        <?php echo __('save and add new'); ?>
                    </label>
                </div>
            </div>

            <!-- XML data -->
            <?php
            $v_xml_file_name = 'xml_record_type_edit.xml';
            if ($this->load_xml($v_xml_file_name))
            {
                echo $this->render_form_display_single();
            }
            ?>
            <div class="button-area">
                <button type="button" name="update" class="ButtonAccept" onclick="btn_record_type_update();"><i class="icon-save"></i><?php echo __('update');?></button>
                <button type="button" name="cancel" class="ButtonBack" onclick="btn_back_onclick()"><i class="icon-reply"></i><?php echo __('go back'); ?></button>
            </div>
        </div><!--end row-->
        <h2 class="module_title"><?php echo __('information guides record type');?></h2>
        
        <div class="Row">
            <div class="Row">
                <div class="left-Col">Cách thức thực hiện:</div>
                <div class="right-Col">
                    <input type="text" name="cach_thuc_thuc_hien" id="cach_thuc_thuc_hien" value="<?php echo $v_cach_thuc_thuc_hien; ?>" class="span6" 
                           data-allownull="yes" data-validate="text"
                           data-name="Cách thức thực hiện"
                           data-xml="yes" data-doc="no"
                           />
                </div>
            </div>

            <div class="Row">
                <div class="left-Col">Thời hạn giải quyết:</div>
                <div class="right-Col">
                    <input type="text" name="thoi_han_giai_quyet" id="thoi_han_giai_quyet" size="60" value="<?php echo $v_thoi_han_giai_quyet; ?>" class="span6"
                           data-allownull="yes" data-validate="text"
                           data-name="Thời hạn giải quyết"
                           data-xml="yes" data-doc="no"
                           />
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">Đối tượng thực hiện:</div>
                <div class="right-Col">
                    <input type="text" name="doi_tuong_thuc_hien" id="doi_tuong_thuc_hien" size="60" value="<?php echo $v_doi_tuong_thuc_hien; ?>" class="span6"
                           data-allownull="yes" data-validate="text"
                           data-name="Đối tượng thực hiện"
                           data-xml="yes" data-doc="no"
                           />
                </div>
            </div>

            <div class="Row">
                <div class="left-Col">Phí, lệ phí</div>
                <div class="right-Col">
                    <textarea type="text" name="le_phi" id="le_phi"
                           data-allownull="yes" data-validate="text"
                           data-name="Phí, lệ phí"
                           data-xml="yes" data-doc="no"
                           ><?php echo $v_phi_le_phi; ?></textarea>
                </div>
            </div>

            <div class="Row">
                <div class="left-Col">Trình tự thực hiện:</div>
                <div class="right-Col">
                    <div id="container_trinh_tu_thuc_hien">
                        <textarea id="txta_trinh_tu_thuc_hien" name="txta_trinh_tu_thuc_hien"  
                                  data-allownull="yes" data-validate="text"
                                  data-name="Trình tự thực hiện"
                                  data-xml="yes" data-doc="no" ><?php echo $v_trinh_tu_thuc_hien;?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="Row">
                <div class="left-Col">Hồ sơ:</div>
                <div class="right-Col">
                    <div id="container_ho_so">
                        <textarea id="txta_ho_so" name="txta_ho_so"  
                                  data-allownull="yes" data-validate="text"
                                  data-name="Hồ sơ"
                                  data-xml="yes" data-doc="no"><?php echo $v_ho_so; ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="Row">
                <div class="left-Col">Cơ quan thực hiện:</div>
                <div class="right-Col">
                    <div id="container_co_quan_thuc_hien">
                        <textarea id="txta_co_quan_thuc_hien" name="txta_co_quan_thuc_hien" 
                                  data-allownull="yes" data-validate="text"
                                  data-name="Cơ quan thực hiện"
                                  data-xml="yes" data-doc="no"><?php echo $v_co_quan_thuc_hien; ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="Row">
                <div class="left-Col">Kết quả:</div>
                <div class="right-Col">
                    <div id="container_ket_qua">
                        <textarea id="txta_ket_qua" name="txta_ket_qua"   
                                  data-allownull="yes" data-validate="text"
                                  data-name="Kết quả"
                                  data-xml="yes" data-doc="no"><?php echo $v_ket_qua; ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="Row">
                <div class="left-Col">Căn cứ pháp lý:</div>
                <div class="right-Col">
                    <textarea id="txta_can_cu_phap_ly" name="txta_can_cu_phap_ly"  
                              data-allownull="yes" data-validate="text"
                              data-name="Căn cứ pháp lý"
                              data-xml="yes" data-doc="no"><?php echo $v_can_cu_phap_ly; ?></textarea>
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">File đính kèm:</div>
                <div class="right-Col">
                    <div class="control-group">
                        <div id="attach_file_list">
                            <div class="file span12">
                                <div class="span6">
                                    <?php
                                    $arr_accept = explode(',', _CONST_TYPE_FILE_ACCEPT);
                                    $class = '';
                                    foreach ($arr_accept as $ext) {
                                        $class .= " accept-$ext";
                                    }
                                    ?>
                                    <input type="file" 
                                           style="border: solid #D5D5D5; color: #000000"
                                           class="multi <?php echo $class; ?>"
                                           name="uploader[]" id="File1"/> 
                                    <font style="font-weight: normal;">Hệ thống chỉ chấp nhận đuôi file <?php echo _CONST_TYPE_FILE_ACCEPT?></font><br/>                               
                                </div>

                                <div class="span4 attachment-file-name">
                                    <?php if ($arr_all_template_file): ?>

                                        <?php foreach ($arr_all_template_file as $key => $val):; ?>
                                            <?php
                                            $v_file_name = $val['file_name'];
                                            $v_file_path = $val['path'];
                                            $v_file_type = $val['type'];
                                            $v_key = $key;
                                            $v_div_id_file = explode('_', $v_key, 2);
                                            ?>
                                            <div id="FILE_<?php echo $v_div_id_file[0]; ?>" >
                                                <div  class="attachment-thumbnail">
                                                    <img  src="<?php echo SITE_ROOT; ?>public/images/document.png" alt="Thumbnail">
                                                </div>
                                                <div class="attachment-action">
                                                    <input type="hidden" name="hdn_file_name[]" value="mau-tthc-tnmt.pdf" />
                                                    <div class="filename"><?php echo $v_file_name; ?></div>
                                                    <div class="edit-attachment-asset"><a href="javascript:void(0);" div_parent_id="<?php echo $v_div_id_file[0]; ?>" file-id="<?php echo $v_key; ?>"  onclick="onlick_delete_file(this)"><i class="icon-trash"></i> Xoá file</a></div>
                                                </div>  
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix" style="border-top: solid 1px #D5D5D5;width: 100%; height: 1px;margin: 5px 0"></div>
        <div class="Row">
             <div class="left-Col">Thêm file đính kèm:</div>
             <div class="right-Col">
                 <div id="add_filetype_upload">
                     <?php                        
                        for($o =0;$o<count($arr_filetype_name);$o ++)
                        {
                            $v_filetype_id   = (string)$arr_filetype_name[$o]->attributes()->id;
                            $v_filetype_name = (string)$arr_filetype_name[$o]->value;
                            $is_not_null     = isset($arr_filetype_name[$o]->attributes()->not_null) ? (string)$arr_filetype_name[$o]->attributes()->not_null : 0;
                            
                            $checked_not_file = '';
                            if($is_not_null == 1)
                            {
                                $checked_not_file = ' checked ';
                            }
                            echo ' <div class="file-input" data-id="'.trim($v_filetype_id).'">
                                    <input class="txt_filietype_name" type="text" value="'.$v_filetype_name.'" name="txt_file_type_old_'.trim($v_filetype_id).'" id="txt_file_type"  size="60"  />
                                    <label><input '. $checked_not_file .' class="txt_filietype_name" type="checkbox" name="chk_file_type_old_'.trim($v_filetype_id).'" /> Bắt buộc đính kèm</label>
                                    <button class="ButtonDelete" type="button" onclick="btn_delete_file_type_onclick(this)">'.__('delete').'</button>
                                    <br/>
                                </div>';
                        }
                     ?>
                     
                    
                 </div>
                 <div id="box-button-add" style="text-align: right">
                     <button type="button" class="ButtonAdd" onclick="btn_add_file_type_onclick();" > <?php echo __('add new')?></button>
                 </div>
             </div>
         </div>
            
            <td colspan="2">
                <div class="button-area">
                    <button type="button" name="update" class="ButtonAccept" onclick="btn_record_type_update();"><i class="icon-save"></i><?php echo __('update'); ?></button>
                    <button type="button" name="cancel" class="ButtonBack" onclick="btn_back_onclick()"><i class="icon-reply"></i><?php echo __('go back'); ?></button>
                </div>
        </div>
    </form>
    
    <div id="template-list-file" style="display: none">       
        <div class="file-input" data-id="0">
            <input class="txt_filietype_name" type="text" name="txt_file_type_new[]" id="txt_file_type"  size="60" required="" />
            <label><input class="txt_filietype_name" type="checkbox" name="chk_file_type_old[]" /> Bắt buộc đính kèm</label>
            <button class="ButtonDelete" type="button" onclick="btn_delete_file_type_onclick(this)"><?php echo __('delete')?></button>
            <br/>
        </div>
    </div>
    <!--End #template-list-file-->
</div><!-- ./row-fluid -->

<script>
    SITE_ROOT = "<?php echo SITE_ROOT ?>";
    tinyMCE_init(); 
    tinyMCE.execCommand('mceAddControl', false, 'txta_trinh_tu_thuc_hien');
    tinyMCE.execCommand('mceAddControl', false, 'txta_ho_so');
    tinyMCE.execCommand('mceAddControl', false, 'txta_co_quan_thuc_hien');
    tinyMCE.execCommand('mceAddControl', false, 'txta_ket_qua');
    tinyMCE.execCommand('mceAddControl', false, 'txta_can_cu_phap_ly');
    tinyMCE.execCommand('mceAddControl', false, 'le_phi');
    
    /**
     * hien thi textbox nhap mapping coe
     */
    function toggle_member_onlick(anchor,selector)
    {
       if($(anchor).is(':checked'))
       {
           $('.' + selector).show().find('input').removeAttr('disabled');
       }
       else
       {
           $('.' + selector).hide().find('input').attr('disabled','true');
       }
    }
    function btn_record_type_update()
    {
        $('#txta_trinh_tu_thuc_hien').html(tinyMCE.get('txta_trinh_tu_thuc_hien').getContent());
        $('#txta_ho_so').html(tinyMCE.get('txta_ho_so').getContent());
        $('#txta_co_quan_thuc_hien').html(tinyMCE.get('txta_co_quan_thuc_hien').getContent());
        $('#txta_ket_qua').html(tinyMCE.get('txta_ket_qua').getContent());
        $('#txta_can_cu_phap_ly').html(tinyMCE.get('txta_can_cu_phap_ly').getContent());
        $('#le_phi').html(tinyMCE.get('le_phi').getContent());
        var validate =1;
        $('#add_filetype_upload .txt_filietype_name').each(function(anchor){
            if($(this).val().trim() == '')
            {
                alert('Tên file đính kèm không được bỏ trống');
               validate =0;
            }
        });
        if(validate == 1)
        {
            btn_update_onclick();
        }
        
    }
    function onlick_delete_file(anchor) 
    {
        if(confirm('Bạn có chắc chắn xóa file này?'))
        {
            var file_id         =  $(anchor).attr('file-id');
            var div_parent_id   = $(anchor).attr('div_parent_id'); 
            var v_list_id = $('#hdn_delete_file_list_id').val() || '';
            if(v_list_id.trim().length > 0)
            {
                 v_list_id += '|' + file_id;
            }
            else
            {
                v_list_id = file_id;
            }
            $('#FILE_' + div_parent_id).remove();
            $('#hdn_delete_file_list_id').val(v_list_id);
        }
    }
    
    
    function btn_dsp_plaintext_form_struct_onclick()
    {
        var url = '<?php echo $this->get_controller_url(); ?>dsp_plaintext_form_struct/' + QS + 'sel_record_type=<?php echo $v_code; ?>';
        url += '&pop_win=1';

        showPopWin(url, 1000, 550);
    }
    
    
    function btn_add_file_type_onclick()
    {
        var html_add_filetype = $('.file-input','#template-list-file');
        $('#add_filetype_upload').append(html_add_filetype.clone(true));
    }
    
    function btn_delete_file_type_onclick(selector) 
    {
        var data_id = $(selector).parent('.file-input').attr('data-id') || 0;
        var  list_filetype_old_id = $('#hdn_file_type_list_old_id').val() || '';
        
        if(parseInt(data_id) > 0 && list_filetype_old_id.length >0)
        {
            $('#hdn_file_type_list_old_id').val(list_filetype_old_id.replace(data_id,''));
        }
        
        $(selector).parent('.file-input').remove();
    }
</script>
<?php $this->template->display('dsp_footer.php');