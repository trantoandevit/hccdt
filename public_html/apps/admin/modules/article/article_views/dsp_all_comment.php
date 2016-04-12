<?php defined('DS') or die('no direct access') ?>
<form name="frmComment" id="frmComment" action="" method="POST">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', '0');
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_comment');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_comment');
    echo $this->hidden('hdn_update_method', 'update_comment');
    echo $this->hidden('hdn_delete_method', 'delete_comment');
    ?>

    <div style="overflow-y: scroll;height: 500px;">
        <?php
        $this->load_xml('dsp_all_comment.xml');
        echo $this->render_form_display_all($arr_all_comment);
        ?>
    </div>
    <?php echo $this->paging2($arr_all_comment); ?>



</form>

<script>
    window.frmComment.submit = function(){
        $.ajax({
            type: 'post',
            url: '<?php echo $this->get_controller_url() . 'dsp_all_comment/' . $v_id; ?>',
            data: $('#frmComment').serialize(),
            success: function(obj){
                $('#frmComment').parent().html(obj);
            }
        });
        return false;
    }
</script>