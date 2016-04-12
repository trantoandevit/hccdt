<?php
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$arr_all_village  =  $arr_all_member['arr_all_village'];
$arr_all_district =  $arr_all_member['arr_all_district'];

$VIEW_DATA['arr_css']               = array('main','single-page','table');
$VIEW_DATA['arr_script']        = array();

$selected_member = get_request_var('member_id','');
$selected_spec   = get_request_var('spec_code','');

$this->render('dsp_header', $VIEW_DATA, $this->theme_code);
?>
<div class="col-md-12 content" style="margin-top: 10px">
    <div class="col-md-12">
        <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                    <label>
                         <a href="<?php echo build_url_send_internet_record()?>"><?php echo __('guidance for internet record')?></a>
                    </label>
                    <label class="active">
                         <?php echo __('record list')?>
                    </label>
                </div>
            </div>

    </div><!--end col 12-->
    <div class="col-md-12 ">
        <form class="form-horizontal" action="" method="get">
            <div class="form-group">
                <div class="col-md-6">
                    <label class="col-sm-4 control-label" style="white-space: nowrap;font-weight: bold"><?php echo __('receiving unit records')?>:</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="sel_member" onchange="filter_onchange()">
                            <option value="">--- Tất cả ---</option>
                            <?php foreach($arr_all_district as $arr_district):
                                    $v_name     = $arr_district['C_NAME'];
                                    $v_id       = $arr_district['PK_MEMBER'];
                                    $v_selected = ($v_id == $selected_member)?'selected':'';
                            ?>
                            <option <?php echo $v_selected?> value="<?php echo $v_id?>"><?php echo $v_name?></option>
                            <?php foreach($arr_all_village as $key => $arr_village):
                                    $v_village_name = $arr_village['C_NAME'];
                                    $v_village_id   = $arr_village['PK_MEMBER'];
                                    $v_parent_id    = $arr_village['FK_MEMBER'];
                                    if($v_parent_id != $v_id)
                                    {
                                        continue;
                                    }
                                    $v_selected = ($v_village_id == $v_member)?'selected':'';
                            ?>
                            <option <?php echo $v_selected?> value="<?php echo $v_village_id?>"><?php echo '---- '.$v_village_name?></option>
                            <?php 
                                unset($arr_all_village[$key]);
                                endforeach;
                            ?>
                            <?php endforeach;?>
                        </select >
                     </div>
                </div>
                <div class="col-md-6">
                    <label class="col-sm-4 control-label" style="white-space: nowrap;font-weight: bold">Lĩnh vực:</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="sel_spec" onchange="filter_onchange();">
                            <option value=''>-- <?php echo __('all field')?> --</option>
                            <?php echo $this->generate_select_option($arr_all_spec,$selected_spec)?>
                        </select >
                     </div>
                </div>
            </div>
        </form>
        <!--end form search-->
    </div><!--end col 12-->
    <div class="col-md-12 block">
        <div style="padding: 10px;" id='div-table-main'>
            <table class="table" >
                <tr class="th-border-bottom">
                    <th width="1%">#</th>
                    <th width="5%"><?php echo __('administrative procedures code')?></th>
                    <th width="*"><?php echo __('administrative procedures name')?></th>
                    <th width="15%"><?php echo __('public service level')?></th>
                    <th width="15%"><?php echo __('action')?></th>
                </tr>
                <?php 
                    $i=1;
                    $v_current_spec_code = '';
                ?>
                <?php foreach($arr_all_record_type as $arr_record_type):
                        $v_record_type_id   = $arr_record_type['PK_RECORD_TYPE'];
                        $v_record_type_code = $arr_record_type['C_CODE'];
                        $v_record_type_name = $arr_record_type['C_NAME'];
                        $v_spec_code        = $arr_record_type['C_SPEC_CODE'];
                        $v_spec_name        = $arr_record_type['C_SPEC_NAME'];
                        
                        $v_url = build_url_send_internet_record($selected_member,'',$v_record_type_id);
                        
                        if($v_current_spec_code != $v_spec_code)
                        {
                            echo "<tr class='success'><td colspan='5'><b>".__('field').":</b> $v_spec_name</td></tr>";
                            $v_current_spec_code = $v_spec_code;
                            $i = 1;
                        }
                ?>
                
                <tr>
                    <td class="center"><?php echo $i?></td>
                    <td class="center"><?php echo $v_record_type_code?></td>
                    <td >
                        <a href="<?php echo $v_url?>">
                        <?php echo $v_record_type_name?>
                        </a>
                    </td>
                    <td class="center">3</td>
                    <td class="center">
                        <div style="width: 50%;float: left">
                            <a href="<?php echo $v_url?>">
                            <span class="glyphicon glyphicon-edit"></span>
                                Đăng ký
                            </a>
                        </div>
                        <div style="width: 50%;float: left;border-left:solid 1px #CDCDCD;padding-left: 3px;">
                            <a target='_blank' class="under_line" href="<?php echo build_url_guidance(false, $v_record_type_id).'?&show=false'?>">
                            <span class="glyphicon glyphicon-eye-open"></span>
                            <?php echo __('administrative guide')?>
                        </a></div>
                    </td>
                </tr>
                <?php $i++;?>
                <?php endforeach;?>
            </table>
            <div>
                <strong>Chú ý:  </strong>Nhấn vào tên thủ tục, hoặc chuỗi chức năng đăng ký tại cột thao tác để đăng ký hồ sơ.
            </div>
        </div>
    </div>
    <script>
        
        function filter_onchange()
        {
            var sel_member = $('#sel_member').val();
            var sel_spec   = $('#sel_spec').val();
            var selector_table = '#div-table-main';
            var url = SITE_ROOT + 'dich_vu_cong/danh_sach/' + sel_member;
            
            if(sel_spec != '')
            {
                url = url + '-' + sel_spec   
            }
            $.ajax({
                type: 'post',
                url: url,
                  beforeSend: function() {
                     var img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $(selector_table).html(img);
                 },
                success: function(result)
                {
                    $(selector_table).html($(result).find(selector_table));
                }
            });
        }
    </script>
</div>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
