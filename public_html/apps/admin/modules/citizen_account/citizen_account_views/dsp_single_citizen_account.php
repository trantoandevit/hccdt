<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>

<?php
$this->template->title = __('manager citizen account');
$this->template->display('dsp_header.php');

$v_account_id = isset($arr_single_account['PK_CITIZEN']) ? $arr_single_account['PK_CITIZEN'] : '';
$v_account_username = isset($arr_single_account['C_USERNAME']) ? $arr_single_account['C_USERNAME'] : '';
$v_account_email = isset($arr_single_account['C_EMAIL']) ? $arr_single_account['C_EMAIL'] : '';
$v_account_organ = isset($arr_single_account['C_ORGAN']) ? $arr_single_account['C_ORGAN'] : '';
$v_account_status = isset($arr_single_account['C_STATUS']) ? $arr_single_account['C_STATUS'] : '';
$v_account_xml = isset($arr_single_account['C_XML_DATA']) ? $arr_single_account['C_XML_DATA'] : '<root></root>';
?>
<h2 class="module_title"><?php echo __('single citizen'); ?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', $v_account_id);

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_account');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_account');
    echo $this->hidden('hdn_update_method', 'update_account');
    echo $this->hidden('hdn_status', $v_account_status);
    echo $this->hidden('XmlData', '');
    ?>

    <div id="box-single-custommer" style="padding-left: 10px">
        <div class="Row">
            <div class="left-Col2">
                <div class="left-Col"><label><?php echo __('username'); ?>:</label></div>
                <div class="right-Col">
                    <input type="text" disabled="true"  style="width: 90%" name="txt_username" id="txt_username" value="<?php echo $v_account_username ?>" />
                </div>
            </div>

            <div class="left-Col2">
                <div class="left-Col"><label><?php echo __('email'); ?>:</label></div>
                <div class="right-Col">
                    <input type="text" disabled="true"  style="width: 80%" name="txt_email" id="txt_email" value="<?php echo $v_account_email ?>" />
                </div>
            </div>
        </div>
        <?php
        if ($v_account_organ == 0): //hien thi thong tin cua ca nhan
            $dom = simplexml_load_string($v_account_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $obj_value = $dom->xpath('//item');

            $v_tel = isset($obj_value[0]->tel) ? (string) $obj_value[0]->tel : '';
            $v_name = isset($obj_value[0]->name) ? (string) $obj_value[0]->name : '';
            $v_address = isset($obj_value[0]->address) ? (string) $obj_value[0]->address : '';
            $v_birth_day = isset($obj_value[0]->birthday) ? (string) $obj_value[0]->birthday : '';
            $v_identity_card = isset($obj_value[0]->identity_card) ? (string) $obj_value[0]->identity_card : '';
            $v_gender = isset($obj_value[0]->gender) ? (string) $obj_value[0]->gender : '';
            ?>
            <!--thong tin ca nhan-->
            <div class="custommer" id="box-personal">
                <div class="Row"><!--ho va ten cong dan-->
                    <div class="left-Col" style="width: 10%">
                        <label><?php echo __('full name'); ?>:</label>
                    </div>
                    <div class="right-Col">
                        <input type="text" disabled="true"  style="width: 90%" name="txt_name" id="txt_name" value="<?php echo $v_name ?>"/>
                    </div>
                </div>

                <div class="Row"><!--gioi tinh va so dien thoai-->
                    <div class="left-Col2">
                        <div class="left-Col"><label><?php echo __('gender'); ?>:</label></div>
                        <div class="right-Col">
                            <select disabled="true"  name="sel_gender" style="width: 30%" id="sel_gender">
                                <option value="0" <?php echo ($v_gender == '0') ? 'selected' : ''; ?> > Nam</option>
                                <option value="1" <?php echo ($v_gender == '1') ? 'selected' : ''; ?>> Nữ</option>
                            </select>
                        </div>
                    </div>

                    <div class="left-Col2">
                        <div class="left-Col"><label><?php echo __('tel'); ?>:</label></div>
                        <div class="right-Col">
                            <input type="text" disabled="true"  style="width: 60%" name="txt_tel" id="txt_tel" value="<?php echo $v_tel ?>" />
                        </div>
                    </div>
                </div>

                <div class="Row"><!--dia chi cong dan-->
                    <div class="left-Col" style="width: 10%"><label><?php echo __('address'); ?>:</label></div>
                    <div class="right-Col">
                        <input type="text" disabled="true"  style="width: 90%"  name="txt_address" id="txt_address" value="<?php echo $v_address ?>"/>
                    </div>
                </div>    
                <div class="Row">
                    <div class="left-Col2">
                        <div class="left-Col"><label><?php echo __('brithday'); ?>:</label></div>
                        <div class="right-Col">
                            <input type="text" value="<?php echo $v_birth_day ?>" disabled="true"> 
                        </div>
                    </div>
                </div>
                <!--ngay sinh-->    
            </div><!--end thong tin ca nhan-->

        <?php endif; ?>
        <?php
        if ($v_account_organ == 1): //hien thi thong tin cua to chuc
            $dom = simplexml_load_string($v_account_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $obj_value = $dom->xpath('//item');

            $v_tel = isset($obj_value[0]->tel) ? (string) $obj_value[0]->tel : '';
            $v_name = isset($obj_value[0]->name) ? (string) $obj_value[0]->name : '';
            $v_address = isset($obj_value[0]->address) ? (string) $obj_value[0]->address : '';
            $v_birth_day = isset($obj_value[0]->birthday) ? (string) $obj_value[0]->birthday : '';
            if (trim($v_birth_day) != '')
            {
                $v_birth_day = jwDate::yyyymmdd_to_ddmmyyyy($v_birth_day);
            }
            $v_tax_code = isset($obj_value[0]->tax_code) ? (string) $obj_value[0]->tax_code : '';
            $v_company_perfix = isset($obj_value[0]->company_prefix) ? (string) $obj_value[0]->company_prefix : '';
            $v_company_name_en = isset($obj_value[0]->name_en) ? (string) $obj_value[0]->name_en : '';
            $v_business_registers = isset($obj_value[0]->business_registers) ? (string) $obj_value[0]->business_registers : '';
            $v_business_date = isset($obj_value[0]->business_date) ? (string) $obj_value[0]->business_date : '';
            $v_granting_agencies = isset($obj_value[0]->granting_agencies) ? (string) $obj_value[0]->granting_agencies : '';
            $v_boss = isset($obj_value[0]->boss) ? (string) $obj_value[0]->boss : '';
            $v_boss_position = isset($obj_value[0]->boss_position) ? (string) $obj_value[0]->boss_position : '';
            ?>
            <!--thong tin to chuc-->
            <div class="custommer" id="box-organize">
                <!--End username and email-->
                <div class="Row">
                    <div class="left-Col" style="width: 10%"><label><?php echo __('organize name'); ?>:</label></div>
                    <div class="right-Col">
                        <input type="text" disabled="true"  style="width: 100%"  name="txt_organize" id="txt_organize" value="<?php echo $v_name ?>" />
                    </div>
                </div>
                <!--End full name-->
                <div class="Row">
                    <div class="left-Col" style="width: 10%"><label><?php echo __('organize name english'); ?>:</label></div>
                    <div class="right-Col">
                        <input type="text" disabled="true"  style="width: 100%"  name="txt_organize_en" id="txt_organize_en" value="<?php echo $v_company_name_en ?>" />
                    </div>
                </div>
                <!--End name english-->
                <div class="Row">
                    <div class="left-Col" style="width: 10%"><label><?php echo __('company perfix'); ?>:</label></div>
                    <div class="right-Col">
                        <input type="text" disabled="true"  style="width: 100%" name="txt_company_perfix" id="txt_company_perfix" value="<?php echo $v_company_perfix ?>" />
                    </div>
                </div>
                <!--End name prefix-->
                <div class="Row">
                    <div class="right-Col2">
                        <div class="left-Col"><label><?php echo __('business registers'); ?>:</label></div>
                        <div class="right-Col">
                            <input type="text" disabled="true"  style="width: 80%" name="txt_business_registers" id="txt_business_registers" value="<?php echo $v_business_registers ?>" />
                        </div>
                    </div>
                    <!--End so dang ky kinh doanh-->
                    <div class="right-Col2">
                        <div class="left-Col"><label><?php echo __('date use'); ?>:</label></div>
                        <div class="right-Col">
                            <input type="text" disabled="true"  style="width: 80%" name="txt_date" id="txt_date" value="<?php echo $v_business_date ?>" />
                        </div>
                    </div>
                    <!--End cap ngay-->
                    <div class="right-Col2">
                        <div class="left-Col"><label><?php echo __('granting agencies'); ?>:</label></div>
                        <div class="right-Col">
                            <input type="text" disabled="true"  style="width: 80%" name="txt_granting_agencies" id="txt_granting_agencies" value="<?php echo $v_granting_agencies ?>" />
                        </div>
                    </div>
                    <!--End co quan cap-->
                </div>
                <div class="Row">
                    <div class="right-Col2">
                        <div class="left-Col"><label><?php echo __('tel'); ?>:</label></div>
                        <div class="right-Col">
                            <input type="text" disabled="true"  style="width: 80%" name="txt_tel" id="txt_tel" value="<?php echo $v_tel ?>" />
                        </div>
                    </div>
                    <!--End Tel-->
                </div>
                <div class="Row">
                    <div class="left-Col" style="width: 10%"><label><?php echo __('address'); ?>:</label></div>
                    <div class="right-Col">
                        <input type="text" disabled="true"  style="width: 100%"  name="txt_address" id="txt_address" value="<?php echo $v_address ?>" />
                    </div>
                </div>    
                <!--End address-->

                <div class="Row">
                    <div class="left-Col2">
                        <div class="left-Col"><label><?php echo __('tax-code'); ?>:</label></div>
                        <div class="right-Col">
                            <input type="text" disabled="true"  style="width: 80%" name="txt_tax_code" id="txt_tax_code" value="<?php echo $v_tax_code ?>" />
                        </div>
                    </div>
                     <!--End tex-code-->
                </div>
                <div class="Row">
                    <div class="left-Col2">
                        <div class="left-Col"><label><?php echo __('boss'); ?>:</label></div>
                        <div class="right-Col">
                            <input type="text" disabled="true"  style="width: 80%" name="txt_boss" id="txt_boss" value="<?php echo $v_boss ?>" />
                        </div>
                    </div>
                    <!--end nguoi dai dien-->
                    <div class="left-Col2">
                        <div class="left-Col"><label><?php echo __('position'); ?>:</label></div>
                        <div class="right-Col">
                            <input type="text" disabled="true"  style="width: 80%" name="txt_position" id="txt_position" value="<?php echo $v_boss_position ?>" />
                        </div>
                    </div>
                    <!--end chuc vu-->
                </div>
            </div><!--end thong tin to chuc-->
        <?php endif; ?>

        <div class="Row">
            <div class="left-Col2">
                <div class="left-Col"><label><?php echo __('status'); ?>:</label></div>
                <div class="right-Col">
                    <select style="width: 60%" name="sel_status" id="sel_status" onchange="sel_status_onchange(this)">
                        
                        <?php
                            if($v_account_status == -1)
                            {
                                echo '<option value="-1" disable="true" > Chưa kích hoạt</option>';
                            }
                            else
                            {
                        ?>
                            <option value="0" <?php echo ($v_account_status == '0') ? 'selected' : ''; ?> > Khóa tài khoản</option>
                            <option value="1" <?php echo ($v_account_status == '1') ? 'selected' : ''; ?> > Hoạt động</option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <!--End status -->
        <?php
        $obj_value = $dom->xpath('//reason');
        $v_reason = isset($obj_value[0]) ? (string) $obj_value[0] : '';
        ?>
        <div class="Row" id="box-reasion">
            <div class="left-Col" style="width: 10%"><label><?php echo __('reason'); ?><span class="required"> (*)</span>:</label></div>
            <div class="right-Col">
                <textarea name="txt_reason" id="txt_reason" rows="4" style="width: 100%"data-allownull="no" data-validate="text" 
                          data-name="<?php echo __('reason') ?>" 
                          autofocus="autofocus" ><?php echo $v_reason ?></textarea>
            </div>
        </div>
        <!--End reason -->
    </div>
    <div class="button-area">
        <input type="button" name="trash" class="ButtonAccept" value="<?php echo __('update'); ?>" onclick="btn_update_onclick();"/>
        <input type="button" name="back" class="ButtonBack" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick()"/>
    </div>
</form>
<script>
        function btn_back_onclick(){
            $('#sel_status').val('');
            $('#hdn_item_id').val('');
            var f = document.frmMain;
            m = $("#controller").val() + f.hdn_dsp_all_method.value;
            $("#frmMain").attr("action", m);
            f.submit();
        }
        $(document).ready(function() {
            sel_status_onchange($('#sel_status'));
        });
        /**
         * Hiển thị trường nhập lý do khóa tài khoản.
         */
        function sel_status_onchange(selector)
        {

            if ($(selector).val() == '0')
            {
                $('#box-reasion').show();
            }
            else
            {
                $('#box-reasion').hide();
            }
        }
</script>
<?php
$this->template->display('dsp_footer.php');
