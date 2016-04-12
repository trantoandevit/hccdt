<?php
defined('DS') or die('no direct access');
$this->template->title = __('sticky');
$this->template->display('dsp_header.php');
$controller            = $this->get_controller_url();

?>
<?php
echo $this->hidden('controller', $this->get_controller_url());
echo $this->hidden('hdn_delete_method', 'delete_sticky');
echo $this->hidden('hdn_insert_method', 'insert_sticky');
?>
<h2 class="module_title"><?php echo __('sticky') ?></h2>
<div id="tabs">
    <ul>
        <li>
            <a href="<?php echo $controller . 'dsp_all_sticky/1' ?>">
                <?php echo __('homepage sticky') ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $controller . 'dsp_all_sticky/0' ?>">
                <?php echo __('category sticky') ?>
            </a>
        </li>
<!--        <li> Tin noi bat trong ngay ko su dung
            <a href="<?php echo $controller . 'dsp_all_sticky/2' ?>">
                <?php echo __('breaking news') ?>
            </a>
        </li>-->
    </ul>
</div>


<script>
    
    $('#tabs').tabs({
        select: function(){
            $('#tabs .ui-tabs-panel').html('');
            $('#tabs .ui-tabs-panel').html('<center><img src="<?php echo SITE_ROOT ?>public/images/loading.gif"/></center>');
        }
    });
    $('#tabs .ui-tabs-panel').css('min-height', '368px');
    
    function load_current_tab()
    {
        var current_index = $("#tabs").tabs("option","selected");
        if(current_index == 1 && current_index != 'undefined')
        {
            sel_category_onchange();
        }
        else
        {
            $("#tabs").tabs('load',current_index);
        }
    }
</script>

<?php
$this->template->display('dsp_footer.php');
?>