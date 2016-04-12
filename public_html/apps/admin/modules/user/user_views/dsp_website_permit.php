<?php
$this->load_xml('xml_user_permission.xml');
echo $this->render_form_display_single();

//Anh sach chuyen muc cua chuyen trang
//var_dump($arr_all_category);
?>
<table class="none-border-table panel_table" border="0" style="width: 100%" cellpadding="0" cellspacing="0">
    <tbody>
        <tr class="panel_color">
            <td colspan="4" height="25px"><span class="@css">Phân công tác nghiệp
                    trên các chuyên mục</span></td>
        </tr>
        <?php
        for ($i = 0; $i < count($arr_all_category); $i++):
            $item = $arr_all_category[$i];
            $v_category_id = $item['PK_CATEGORY'];
            $v_name = $item['C_NAME'];
            $v_status = $item['C_STATUS'];
            $v_order = $item['C_ORDER'];
            $v_internal_order = $item['C_INTERNAL_ORDER'];
            //$v_default = $item['C_DEFAULT'];
            $v_parent = $item['FK_PARENT'];
            $v_indent = strlen($v_internal_order) / 3 - 1;
            $v_indent_text = '';
            for ($j = 0; $j < $v_indent; $j++) {
                $v_indent_text .= ' -- ';
            }
            ?>
            <tr class="xslgridrow">
                <td>
                    <table border="1" class="adminlist">
                        <tr>
                            <td class="text_check" width="98%">
                                <label for="cat_<?php echo $v_category_id ?>"><?php echo $v_indent_text . $v_name ?></label>
                            </td>
                            <td align="top" style="width: 1%">
                                <input type="checkbox" id="cat_<?php echo $v_category_id ?>" 
                                       data-name="<?php echo $v_name; ?>" 
                                       data-id  ="<?php echo $v_category_id; ?>" 
                                       data-category="yes"
                                       data-xml="yes" data-doc="" 
                                       <?php echo ($v_status == 0) ? ' disabled' : ''; ?>
                                       />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
<?php endfor; ?>
    </tbody>
</table>
