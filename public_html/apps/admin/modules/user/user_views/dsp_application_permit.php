<?php
$this->load_xml('xml_user_permission.xml');
echo $this->render_form_display_single();

//Danh sach chuyen muc cua chuyen trang
?>

<table class="none-border-table panel_table" border="0" style="width: 100%" cellpadding="0" cellspacing="0">
	<tbody>
		<tr class="panel_color">
			<td colspan="4" height="25px"><span class="@css">Phân công tác nghiệp
					trên các chuyên mục</span></td>
		</tr>
		<?php for($i=1;$i<=10;$i++):?>
		<tr class="xslgridrow">
			<td>
				<table border="1" class="adminlist">
					<tr>
						<td class="text_check" width="98%">
						    <?php if ($i % 3 == 0){ echo '--';}?>
						    <?php if ($i % 4 == 0){ echo '----';}?>
						    <label for="cat_<?php echo $i?>">Tên chuyên mục <?php echo $i?></label>
					    </td>
						<td align="top" style="width: 1%">
						    <input type="checkbox" id="cat_<?php echo $i?>" data-name="Tên chuyên mục <?php echo $i?>" data-xml="yes" data-doc="">
					    </td>
					</tr>
				</table>
			</td>
		</tr>
		<?php endfor; ?>
	</tbody>
</table>
