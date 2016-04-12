<?php
defined('DS') or die('no direct access');
$this->template->title = __('system config');
$this->template->display('dsp_header.php');
?>
<h2 class="module_title"><?php echo __('system config') ?></h2>
<form method="post" name="frmMain" id="frmMain" action="#">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_dsp_all_method', 'main');
    echo $this->hidden('hdn_update_method', 'update_options');

    echo $this->hidden('hdn_item_id', '1');
    echo $this->hidden('XmlData', $xml_data);
    ?>

    <!-- XML data -->
    <?php
    $v_xml_file_name = 'options.xml';
    if ($v_xml_file_name != '')
    {
        $this->load_xml($v_xml_file_name);
        echo $this->render_form_display_single();
    }
    ?>
    <!-- Button -->
    <div class="button-area">
        <input type="button" name="update" class="ButtonAccept" value="<?php echo __('update'); ?>" onclick="btn_update_onclick();"/>
    </div>
</form>
<script>
    $(document).ready(function() {
        //Fill data
        var formHelper = new DynamicFormHelper('','',document.frmMain);
        formHelper.BindXmlData();
    });
</script>
<?php $this->template->display('dsp_footer.php') ?>
