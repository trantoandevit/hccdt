<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>
<?php
//header
$this->template->title = __('website manager');
$this->template->display('dsp_header.php');
?>
<h2 class="module_title"><?php echo __('website manager');?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_website');
    echo $this->hidden('hdn_dsp_all_method','dsp_all_website');
    echo $this->hidden('hdn_update_method','update_website');
    echo $this->hidden('hdn_delete_method','delete_website');
    ?>

    <?php
    $this->load_xml('xml_dsp_all_website.xml');
    //$check_premission = session::check_permission('SUA_CHUYEN_TRANG',FALSE);
    //echo session::check_permission('SUA_CHUYEN_TRANG');
    echo $this->render_form_display_all($arr_all_website);

    //Phan trang
    echo $this->paging2($arr_all_website);
    ?>

    <div class="button-area">
	    <input type="button" name="addnew" class="ButtonAdd" value="<?php echo __('add new');?>" onclick="btn_addnew_onclick();"/>
	    <input type="button" name="trash" class="ButtonDelete" value="<?php echo __('delete');?>" onclick="btn_delete_onclick();"/>
    </div>
</form>
<?php $this->template->display('dsp_footer.php');