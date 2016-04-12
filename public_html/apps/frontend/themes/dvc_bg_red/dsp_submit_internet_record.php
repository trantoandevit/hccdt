<?php
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css']               = array();
$VIEW_DATA['arr_script']        = array();

$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

$arr_all_village  =  $arr_all_member['arr_all_village'];
$arr_all_district =  $arr_all_member['arr_all_district'];

$v_selected_member = get_request_var('member_id','');

$v_spec_name        = isset($arr_record_type['C_SPEC_NAME'])?$arr_record_type['C_SPEC_NAME']:'';
$v_record_type_id   = isset($arr_record_type['PK_RECORD_TYPE'])?$arr_record_type['PK_RECORD_TYPE']:'';
$v_record_type_code = isset($arr_record_type['C_CODE'])?$arr_record_type['C_CODE']:'';
$v_record_type_name = isset($arr_record_type['C_NAME'])?$arr_record_type['C_NAME']:'';
$v_xml_data         = isset($arr_record_type['C_XML_DATA']) ? $arr_record_type['C_XML_DATA'] : '<data/>';

$v_member_name      = isset($response['field']['txt_name'])?$response['field']['txt_name']:'';
$v_phone            = isset($response['field']['txt_phone'])?$response['field']['txt_phone']:'';
$v_email            = isset($response['field']['txt_email'])?$response['field']['txt_email']:'';
$v_note             = isset($response['field']['txt_note'])?$response['field']['txt_note']:'';
$v_address          = isset($response['field']['txt_address'])?$response['field']['txt_address']:'';
$v_selected_member  = isset($response['field']['hdn_member_id'])?$response['field']['hdn_member_id']:$v_selected_member;

$arr_single_citizen = isset($arr_single_citizen) ? $arr_single_citizen : array();

if(sizeof($arr_single_citizen) > 0)
{
    $v_email             = $arr_single_citizen['C_EMAIL'];
    $v_account_xml       = $arr_single_citizen['C_XML_DATA'];
    $v_organ             = $arr_single_citizen['C_ORGAN'];
    @$dom = simplexml_load_string($v_account_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    if(@$dom)
    {
        if($v_organ == 0)
        {
            $obj_value      = $dom->xpath('//item');
            $v_phone        = isset($obj_value[0]->tel) ? (string) $obj_value[0]->tel : '';
            $v_member_name  = isset($obj_value[0]->name) ? (string) $obj_value[0]->name : '';
            $v_address      = isset($obj_value[0]->address) ? (string) $obj_value[0]->address : '';

        }
        else
        {
            $v_phone        = isset($obj_value[0]->tel) ? (string) $obj_value[0]->tel : '';
            $v_member_name  = isset($obj_value[0]->name) ? (string) $obj_value[0]->name : '';
            $v_address      = isset($obj_value[0]->address) ? (string) $obj_value[0]->address : '';

        }
    }
}

if(!isset($response))
{
    $response['success'] = false;
}
?>
<!-- Upload -->
<script src="<?php echo SITE_ROOT?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>

<!--validate-->
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/additional-methods.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/validate/jquery_validate_vi.js" type="text/javascript"></script>
<div class="col-md-12">
<?php if($response['success'] == true):?>
    <div class="col-md-12 block" style="
                                text-align: center;
                                font-size: 16px;
                                margin-top: 50px;
                                min-height: 500px;
                                padding: 30px;">
        <p style="font-size: 20px;margin-top: 50px;">
            Hồ sơ "<?php echo $response['record_type_name'];?>" của bạn đang được kiểm tra và sẽ chuyển đến cơ quan chuyên môn.
            <br/>
            Thông tin về tình trạng xử lý hồ sơ được gửi về hòm thư: <?php echo $response['txt_email'];?>.
            <br/>
            Xin cảm ơn, đã sử dụng dịch vụ.<br/>
            <a style="  border: solid 1px #802902;
                        background-color: rgb(229, 90, 23);
                        color: white;"
                        class="btn go-back" href="<?php echo SITE_ROOT ?>"><?php echo __('home page') ?></a>
        </p>
    
 </div>
    <?php else:?>

    <div class="col-md-12 content">
        <div class="div-synthesis">
            <div class="div_title_bg-title-top"></div>
            <div class="div_title">
                 <div class="title-border-left"></div>
                    <div class="title-content">
                        <label>
                            <?php echo __('submit internet record')?>  
                        </label>
                    </div>
            </div>
        </div>
        <div class="submit-record">
        <form class="form-horizontal " style="margin: 10px;" id="frmMain" enctype="multipart/form-data" method="POST" >
            <?php
                echo $this->hidden('hdn_member_id',$v_selected_member);
                echo $this->hidden('hdn_record_type_id',$v_record_type_id);
                echo $this->hidden('hdn_record_type_code',$v_record_type_code);
                echo $this->hidden('hdn_url_submit_record',build_url_submit_internet_record());
                echo $this->hidden('hdn_tmp_record_no',  strtoupper(Suid::encode(Date('ymdHis'))));
            ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label class="col-md-3 control-label"><?php echo __('administrative procedures name')?> <span class="required">(*)</span></label>
                        <div class="col-md-9" >
                            <select class="form-control" id="sel_record_type" required onchange="record_type_onchange(this);">
                                <option value=""></option>
                                <?php foreach($arr_all_record_type as $arr_record_type):
                                        $record_type_code = $arr_record_type['C_CODE'];
                                        $record_type_id   = $arr_record_type['PK_RECORD_TYPE'];
                                        $record_type_name = $arr_record_type['C_NAME'];
                                        $spec_name        = $arr_record_type['C_SPEC_NAME'];
                                        $list_member      = $arr_record_type['C_LIST_MEMBER'];
                                        $v_selected = ($v_record_type_code == $record_type_code)?'selected':'';
                                ?>
                                <option  data-list_member="<?php echo $list_member?>" 
                                         data-record_type_code="<?php echo $record_type_code?>" 
                                         data-spec_name="<?php echo $spec_name?>" <?php echo $v_selected;?> 
                                         value="<?php echo $record_type_id;?>">
                                             <?php echo $record_type_code . ' - ' .$record_type_name;?>
                                </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="col-md-3 control-label"><?php echo __('field')?></label>
                        <div class="col-md-9 " >
                            <p class="form-control-static" id="spec_name"><?php echo $v_spec_name?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label class="col-md-3 control-label"><?php echo __('receiving unit records')?> <span class="required">(*)</span></label>
                        <div class="col-md-9 " >
                            <select class="form-control" name="sel_member" id="sel_member" onchange="sel_member_onchange(this);" required>
                                <option data-short_code='' value=''></option>
                                <?php foreach($arr_all_district as $arr_district):
                                        $v_name       = $arr_district['C_NAME'];
                                        $v_id         = $arr_district['PK_MEMBER'];
                                        $v_short_code = $arr_district['C_SHORT_CODE'];
                                        $v_selected   = ($v_id == $v_selected_member)?'selected':'';
                                ?>
                                <option data-short_code="<?php echo $v_short_code?>" <?php echo $v_selected?> value="<?php echo $v_id?>"><?php echo $v_name?></option>
                                <?php foreach($arr_all_village as $key => $arr_village):
                                        $v_village_name = $arr_village['C_NAME'];
                                        $v_village_id   = $arr_village['PK_MEMBER'];
                                        $v_parent_id    = $arr_village['FK_MEMBER'];
                                        $v_short_code   = $arr_village['C_SHORT_CODE'];
                                        if($v_parent_id != $v_id)
                                        {
                                            continue;
                                        }
                                        $v_selected = ($v_village_id == $v_selected_member)?'selected':'';
                                ?>
                                <option data-short_code="<?php echo $v_short_code?>" <?php echo $v_selected?> value="<?php echo $v_village_id?>"><?php echo '---- '.$v_village_name?></option>
                                <?php 
                                    unset($arr_all_village[$key]);
                                    endforeach;
                                ?>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" style="display: none">
                        <label class="col-md-3 control-label"><?php echo __('record no')?></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="txt_record_no" 
                                   name="txt_record_no"
                                   value="" 
                                   required readonly />
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="txt_name" class="col-md-3 control-label"><?php echo __('full name')?><span class="required">(*)</span></label>
                        <div class="col-md-9 " >
                            <input type="text" class="form-control" id="txt_name"  
                                   value="<?php echo $v_member_name?>"
                                   name="txt_name" placeholder="<?php echo __('full name')?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">&nbsp;</div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="txt_email" class="col-md-3 control-label">Email <span class="required">(*)</span></label>
                        <div class="col-md-9">
                            <input type="email" class="form-control" id="txt_email"  
                                 value="<?php echo $v_email?>"
                                 name="txt_email" placeholder="Email" required data-rule="email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="txt_phone"   class="col-md-3 control-label"><?php echo __('phone')?> <span class="required">(*)</span></label>
                        <div class="col-md-9 " >
                            <input type="text" class="form-control" id="txt_phone" minlength="6" maxlength="15"  
                                   value="<?php echo $v_phone?>"
                                   name="txt_phone" placeholder="<?php echo __('phone')?>" required phone="true"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="txt_address" class="col-md-3 control-label"><?php echo __('address')?> <span class="required">(*)</span></label>
                        <div class="col-md-9">
                            <textarea class="form-control"   rows="5" id="txt_address" name="txt_address" required><?php echo $v_address?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="col-md-3 block control-label"><?php echo __('note')?></label>
                        <div class="col-md-9 " >
                            <textarea class="form-control" rows="5" id="txt_note" name="txt_note"><?php echo $v_note?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6" id="div_upload_file">
                        <label class="col-md-3 control-label"><?php echo __('attach file')?> <span class="required">(*)</span></label>
                        <div class="col-md-9 " >
                            <?php
                                $arr_accept = explode(',', _CONST_TYPE_FILE_ACCEPT);
                                $class = '';
                                foreach ($arr_accept as $ext) {
                                    $class .= " accept-$ext";
                                }
                            ?>
                            <?php
                                @$dom = simplexml_load_string($v_xml_data);
                                if(@$dom):
                                    $v_xpath = '//list_file//item';
                                    $arr_filetype = $dom->xpath($v_xpath);
                                    for($i =0; $i<count($arr_filetype);$i++):
                                        $v_filetype_id   = (string)$arr_filetype[$i]->attributes()->id;
                                        $v_filetype_name = (string)$arr_filetype[$i]->value;
                                        ?>
                                        <strong><?php echo $v_filetype_name?></strong>
                                        <input type="file" 
                                                    style="border: solid #D5D5D5; color: #000000"
                                                    class="multi <?php echo $class?> max-1" 
                                                    name="uploader[]" required/> 
                                        <p class="help-block">Hệ thống chỉ chấp nhận đuôi file <?php echo _CONST_TYPE_FILE_ACCEPT?></p>
                                        <?php
                                        if(isset($response) && isset($response['message']['uploader_'.trim($v_filetype_id)]) && $response['message']['uploader_'.trim($v_filetype_id)] != '')
                                        {
                                            echo '<label for="uploader" class="error">'.$response['message']['uploader_'.trim($v_filetype_id)].'</label>';
                                        }
                                    endfor;
                                endif;
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="recaptcha_response_field" class="col-md-3 block control-label"><?php echo __('verification Code')?> <span class="required">(*)</span></label>
                        <div class="col-md-9 block" >
                               <?php echo recaptcha_get_html(_CONST_RECAPCHA_PUBLIC_KEY) ?>
                                <?php if(isset($response) && isset($response['message']['recapcha']) && $response['message']['recapcha'] != ''):?>
                                <label class="error"><?php echo $response['message']['recapcha']?></label>
                                <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
            <div style="text-align: center" >
                <button type="submit" class="btn btn-warning"><?php echo __('send')?></button>
                <button type="button" class="btn btn-default" onclick="javascript:history.back()"><?php echo __('back')?></button>
            </div>
        </form>
            </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#frmMain').validate();
        sel_member_onchange($('#sel_member'));
    });
    function sel_member_onchange(sel_member)
    {
        $('#hdn_member_id').val($(sel_member).val());
        change_record_no();
    }
    //thay doi ma thu tuc
    function change_record_no()
    {
           var record_no = $('#hdn_record_type_code').val() 
                        + '-' + $('#sel_member').find('option:selected').attr('data-short_code') 
                        + '-' + $('#hdn_tmp_record_no').val();
            $('#txt_record_no').val(record_no); 
    }
    
    function record_type_onchange(sel_record_type)
    {
        //thay doi gia chi hdn
        $('#hdn_record_type_id').val($(sel_record_type).val());
        $('#hdn_record_type_code').val($(sel_record_type).find(':selected').attr('data-record_type_code'));
        //thay ten linh vuc
        $('#spec_name').html($(sel_record_type).find('option:selected').attr('data-spec_name'));
        
        //hien thi don vi tiep nhan loai thu tuc
        var list_member = $(sel_record_type).find('option:selected').attr('data-list_member');
        $('#sel_member option').show();
        $('#sel_member').val('');
        $('#sel_member option').each(function(){
            if(list_member.indexOf($(this).val()) == -1)
            {
                $(this).hide();
            }
        });
        //thay doi record no
        change_record_no();
        //lay du lieu list media cua thuc tuc moi
        var url_submit_record = $('#hdn_url_submit_record').val();
        $.ajax({
            type: "GET",
            url: url_submit_record,
            data: {record_type:$(sel_record_type).val()},
            beforeSend: function() {
                     var img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $('#div_upload_file').html(img);
                 },
            success: function(res){
                var html = $(res).find('#div_upload_file').html();
                $('#div_upload_file').html(html);
            }
          });
    }
    $(document).ready(function(){
        //hien thi don vi tiep nhan loai thu tuc
        var list_member = $('#sel_record_type').find('option:selected').attr('data-list_member') || '';
        
        $('#sel_member option').show();
        $('#sel_member').val('');
        $('#sel_member option').each(function(){
            if(list_member.indexOf($(this).val()) == -1)
            {
                $(this).hide();
            }
            else if($(this).val() == $('#hdn_member_id').val())
            {
                $(this).attr('selected','1');
            }
        });
    });
</script>
<?php endif;//end check success?>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
