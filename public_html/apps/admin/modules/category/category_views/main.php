
<?php
defined('SERVER_ROOT') or die('No direct script');

$this->template->title = __('category management');
$this->template->display('dsp_header.php');

$v_active_tab = get_post_var('hdn_active_tab',0);
?>
<h2 class="module_title">
    <?php echo __('category'); ?>
</h2>
<div if="right_content">
    <div id="tabs">
        <ul>
          
                <li>
                    <a href="<?php echo $this->get_controller_url() . 'dsp_all_category/' ?>" >
                        <?php echo __('category list') ?>
                    </a>
                </li>
     
                <li>
                    <a href="<?php echo $this->get_controller_url() . 'dsp_featured_category/' ?>" >
                        <?php echo __('featured category') ?>
                    </a>
                </li>
          
        </ul>
    </div>
</div>
<script>
    $('#tabs').tabs(
		{ selected: <?php echo $v_active_tab;?>}
	    ,{select: function(){
            $('#tabs .ui-tabs-panel').html('<center><img src="<?php echo SITE_ROOT ?>public/images/loading.gif"/></center>');
        }
    });
    $('#tabs .ui-tabs-panel').css('min-height', '368px');
    
//    function remove_content()
//    {
//        $('.ui-tabs-panel').html('');
//    }
       
    function reload_current_tab()
    {
        var tabindex = $("#tabs").tabs('option', 'selected');
        $("#tabs").tabs('load',tabindex);
    }
    
    function btn_cache_onclick(){
        m = '<?php echo $this->get_controller_url() ?>write_cache';
        $("#frmMain").attr("action", m);
        frmMain.submit();
    }
</script>
<?php $this->template->display('dsp_footer.php'); ?>