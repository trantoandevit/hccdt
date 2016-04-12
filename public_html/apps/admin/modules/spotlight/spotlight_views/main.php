<?php
defined('DS') or die('no direct access');
$this->template->title = __('spotlight');
$this->template->display('dsp_header.php');
$n                     = count($arr_all_position);
?>
<h2 class="module_title"><?php echo __('spotlight') ?></h2>
<div id="tabs">
    <ul>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php
            $item   = $arr_all_position[$i];
            $v_id   = $item['PK_SPOTLIGHT_POSITION'];
            $v_url  = $this->get_controller_url() . 'dsp_single_position/' . $v_id;
            $v_name = $item['C_NAME'];
            ?>
            <li>
                    <span 
                        style="margin-top:6px;margin-right: 3px;right: 0;display: block;display: none;"
                        class="ui-icon ui-icon-close" onclick="item_delete_onclick(this);"
                        data-id="<?php echo $v_id; ?>"
                        > 
                    </span>
                <a href="<?php echo $v_url ?>">
                    <?php echo $v_name ?>
                </a>
            </li>
        <?php endfor; ?>
            <li>
                <a href="<?php echo $this->get_controller_url() . 'dsp_single_position/' ?>">
                    +
                </a>
            </li>
    </ul>
</div>
<script>
    $('#tabs').tabs({
        select: function(){
            $('#tabs div[id!="add_new"]').html("");
             $('#tabs .ui-tabs-panel').html('<center><img src="<?php echo SITE_ROOT ?>public/images/loading.gif"/></center>');
        }
    });
    $('#tabs .ui-tabs-panel').css('min-height', '368px');
   
    function item_delete_onclick(span_obj){
        if(!confirm("<?php echo __('are you sure to delete all selected object?') ?>"))
        {
            return;
        }
        var $pos_id = $(span_obj).attr('data-id');
        $.ajax({
            type: 'post',
            url: "<?php echo $this->get_controller_url() ?>delete_position/",
            data: {'position_id': $pos_id},
            success: function(){
                var panelId = $( span_obj ).parent().find("a").attr("href");
                $( span_obj ).closest( "li" ).remove();
                $( panelId ).remove();
                tabs.tabs( "refresh" );
            }
        });
    }
    
    function reload_current_tab()
    {
        var current_index = $("#tabs").tabs("option","selected");
        $("#tabs").tabs('load',current_index);
    }
</script>
<?php $this->template->display('dsp_footer.php'); ?>

