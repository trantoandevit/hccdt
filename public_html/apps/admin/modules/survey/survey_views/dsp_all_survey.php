<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
$this->template->title = 'Quản lý câu hỏi khảo sát chất lượng';
$this->template->display('dsp_header.php');
//don vi thanh vien
$arr_all_village  =  $arr_all_member['arr_all_village'];
$arr_all_district =  $arr_all_member['arr_all_district'];


$arr_all_survey     = isset($arr_all_survey) ? $arr_all_survey : array();

$v_filter           = isset($_REQUEST['txt_filter']) ? $_REQUEST['txt_filter'] : '';
$v_member_id        = isset($_REQUEST['sel_ft_member']) ? $_REQUEST['sel_ft_member'] : -1;
$v_begin_date       = isset($_REQUEST['txt_ft_begin_date']) ? $_REQUEST['txt_ft_begin_date'] : '';
$v_end_date         = isset($_REQUEST['txt_ft_end_date']) ? $_REQUEST['txt_ft_end_date'] : '';
$v_status           = isset($_REQUEST['sel_ft_status']) ? $_REQUEST['sel_ft_status'] : -1;

?>


<?php
/**
 * hien thi nut xoa va cap nhat
 */
function show_insert_delete_button()
{
    $html = '<div class="button-area">';
    $html .= '<input type="button" class="ButtonAdd" onClick="btn_addnew_onclick();" value="' . __('add new') . '"></input>';
    $html .= '<input type="button" class="ButtonDelete" onClick="btn_delete_onclick();" value="' . __('delete') . '"></input>';
    $html .= '</div>';

    echo $html;
}
?>

<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_item_id',0);
        echo $this->hidden('hdn_item_id_list','');
        
        echo $this->hidden('hdn_dsp_single_method','dsp_single_survey');
        echo $this->hidden('hdn_dsp_all_method','dsp_all_survey');
        echo $this->hidden('hdn_delete_method','dsp_delete_survey');
    ?>
    
    <!-- Toolbar -->
    <h2 class="module_title" style="width: auto">Danh sách các câu hỏi khảo sát</h2>
    <!--Start filter-->
    <div id="box-filter">
         <div class="Row">
            <div class="left-Col">
                <label>Tên câu hỏi</label>
            </div>
            <div class="right-Col">
                <input type="text" name="txt_filter" value="<?php echo $v_filter; ?>" size="80">
            </div>
        </div> 
        <!-- -->
        
        <div class="Row">
            <div class="left-Col">
               <?php echo __('Ngày bắt đầu từ ');?>
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_ft_begin_date" id="txt_ft_begin_date" 
                        value="<?php echo $v_begin_date;?>" 
                >
                &nbsp;<img src="<?php echo SITE_ROOT?>apps/admin/images/calendar.png" onclick="DoCal('txt_ft_begin_date')">

                &nbsp;<label>  <?php echo __('Đến ngày');?></label>
                <input type="textbox" name="txt_ft_end_date" id="txt_ft_end_date" 
                       value="<?php echo $v_end_date;?>" 
                    >
                 &nbsp;<img src="<?php echo SITE_ROOT?>apps/admin/images/calendar.png" onclick="DoCal('txt_ft_end_date')">
            </div>
        </div> <!--End date infor and dateend -->
        <div class="Row">
                <div class="left-Col">Đơn vị khảo sát</div>
                <div class="right-Col">
                        <select name="sel_ft_member" id="sel_ft_member">
                            <option value="-1">--Tất cả--</option>
                            <option value="0">Cổng thông tin</option>
                            <?php 
                                foreach($arr_all_district as $arr_district)
                                {
                                    $v_name = $arr_district['C_NAME'];
                                    $v_id   = $arr_district['PK_MEMBER'];
                                    $v_checked ='';
                                    $v_checked = ($v_id == $v_member_id)?' selected':'';
                                    echo "<option $v_checked value='$v_id'>$v_name</option>";
                                     foreach($arr_all_village as $key => $arr_village)
                                     {
                                        $v_village_name = $arr_village['C_NAME'];
                                        $v_village_id   = $arr_village['PK_MEMBER'];
                                        $v_parent_id    = $arr_village['FK_MEMBER'];
                                        if($v_parent_id != $v_id)
                                        {
                                            continue;
                                        }
                                        $v_checked = ($v_village_id == $v_member_id)?' selected':'';

                                        echo "<option $v_checked value='$v_village_id'> ----- $v_village_name</option>";
                                     }
                                }
                                
                                ?>
                        </select>
                        
                </div>
            </div>
        <!--End memeber-->
        <div class="Row">
                <div class="left-Col">Trạng thái</div>
                <div class="right-Col">
                        <select name="sel_ft_status" id="sel_ft_status">
                            <option value="-1"  >--Tất cả--</option>
                            <option value="0" <?php echo ($v_status == 0 ) ? ' selected' : '';?> >Không hoạt động</option>
                            <option value="1" <?php echo ($v_status == 1 ) ? ' selected' : '';?> >Hoạt động</option>
                        </select>
                        
                </div>
            </div>
        <!--End status-->
        
        <div class="Row">
            <div class="left-Col">&nbsp;</div>
             <div class="right-Col">
                    <input type="submit" class="ButtonSearch" value="Lọc" name="btn_filter">
             </div>
    </div>
    <!--End filter-->
    <?php show_insert_delete_button(); ?>
    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <colgroup>
            <col width="5%">
            <col width="30%">
            <col width="15%">
            <col width="15%">
            <col width="10%">
            <col width="10%">
            <col width="15%">
        </colgroup>
        <tr>
            <th>
                <input type="checkbox" name="chk_check_all" id="chk_all"/>
            </th>
            <th>Tên câu hỏi khảo sát</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th>Đơn vị khảo sát</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        <?php for($i = 0;$i < sizeof($arr_all_survey); $i ++):; ?>
         <?php
                $v_survey_id        = $arr_all_survey[$i]['PK_SURVEY'];
                $v_survey_name      = html_entity_decode($arr_all_survey[$i]['C_NAME']);
                $v_begin_date       = $arr_all_survey[$i]['C_BEGIN_DATE'];
                $v_begin_date       = jwDate::yyyymmdd_to_ddmmyyyy($v_begin_date);
                
                $v_end_date         = $arr_all_survey[$i]['C_END_DATE'];
                $v_end_date         = jwDate::yyyymmdd_to_ddmmyyyy($v_end_date);
                
                $v_status           = ($arr_all_survey[$i]['C_STATUS'] == 1) ? 'Hiển thị' : 'Không hoạt động';
                
                $v_member_id        = $arr_all_survey[$i]['C_MEMBER_NAME'];
            ?>
        
            <tr>
                 <td class="Center"  class="<?php echo ($i % 2) ? 'row1' : 'row0'; ?>" >
                        <input type="checkbox" name="chk" class="chk" id="item_<?php echo $i ?>"
                            value="<?php echo $v_survey_id; ?>"
                            />
                </td>
                <td><a data-id="<?php echo $v_survey_id; ?>" href="javascript:void()"   onclick="row_onclick('<?php echo $v_survey_id?>');" ><?php echo $v_survey_name; ?></a></td>
                <td><?php echo $v_begin_date; ?></a></td>
                <td><?php echo $v_end_date;?></td>
                <td><?php echo $v_member_id; ?></td>
                <td><?php echo $v_status;?></td>
                <td><a href="<?php echo $this->get_controller_url().'dsp_report_answer'.DS.$v_survey_id ;?>">Kết quả khảo sát</a></td>
            </tr>
       <?php endfor; ?>
    </table>
    <?php show_insert_delete_button(); ?>
</form>
<script>
    
    toggle_checkbox('#chk_all', '.chk');
    $(document).ready(function(){        
    });
    
</script>
<?php
$this->template->display('dsp_footer.php');

