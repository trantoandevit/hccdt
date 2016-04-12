<?php
defined('DS') or die('no direct access');
$this->template->title = __('widget');
$this->template->display('dsp_header.php');
?>
<style>
    table td{
        border: none;
        text-align: left;
    }
</style>
<div id="right_content">
    <h2 class="module_title"><?php echo __('widget'); ?></h2>
    <div >
        <div style="width: 67%;margin-right: 1%;float:left;" class="available-widgets">
            <div class="ui-widget-header ui-corner-top">
                <h4><?php echo __('available widgets'); ?></h4>
            </div>
            <div class="ui-widget-content" style="padding:10px;padding-right: 0px;min-height: 500px;float:left;">
                <?php
                $n                     = count($arr_all_widget);
                $v_dragable            =  'widget-drag';
                ?>
                <?php for ($i = 0; $i < $n; $i++): ?>
                    <?php
                    $item      = $arr_all_widget[$i];
                    $v_name    = __($item['C_NAME']);
                    $v_code    = $item['C_CODE'];
                    $v_summary = $item['C_SUMMARY'];
                    $v_form    = $item['C_FORM'];
                    ?>
                    <div class="ui-widget widget-drag" data-id="0" data-code="<?php echo $v_code ?>">
                        <div class="ui-widget-header ui-state-default ui-corner-top">
                            <span 
                                class="ui-icon ui-icon-arrow-2-n-s widget-arrow ui-corner-top" 
                                onclick="collaspe_form(this);" style="display: none;"
                                >
                            </span>
                            <h4 style=""><b><?php echo $v_name; ?></b></h4>
                        </div>
                        <?php if ($item['C_FORM']): ?>
                            <div class="ui-widget-content">
                                <?php echo $item['C_FORM'] ?>
                            </div>
                        <?php endif; ?>
                        <div class="widget-summary">
                            <?php echo $v_summary ?></br>
                        </div>
                    </div>
                <?php
                    if($i%2 != 0 )
                    {
                        echo '<div class="clear" style="width:100%;"></div>';
                    }
                ?>
                <?php endfor; ?>
            </div>
            <h2></h2>
            <div class="ui-widget-header ui-corner-top">
                <h4><?php echo __('widget tools'); ?></h4>
            </div>
            <div class="ui-widget-content" style="padding:10px;min-height: 200px;text-align: center">
                <div style="display: inline-block;margin:0 auto;width:50%;text-align: center;text-align: center;">
                    <h4 style="margin: 0px;">
                        <table style="display: inline-block;">
                            <tr>
                                <td><?php echo __('total cached file') ?>:</td>
                                <td><?php echo $cached_file ?></td>
                            </tr>
                            <tr>
                                <?php
                                $file_size      = $cached_file_size;
                                $file_size_unit = 'kB';
                                if ($cached_file_size < 1024)
                                {
                                    $file_size      = $cached_file_size;
                                    $file_size_unit = 'Bytes';
                                }
                                elseif ($cached_file_size < (1024 * 1024))
                                {
                                    $file_size      = intval($cached_file_size / 1024);
                                    $file_size_unit = 'kB';
                                }
                                else
                                {
                                    $file_size      = intval($cached_file_size / (1024 * 1024));
                                    $file_size_unit = 'MB';
                                }
                                ?>
                                <td><?php echo __('total cached size') ?>:</td>
                                <td><?php echo $file_size ?>&nbsp;<?php echo $file_size_unit ?></td>
                            </tr>
                        </table>
                    </h4>
                    <img 
                        height="128" width="128" id="recycle"
                        src="<?php echo SITE_ROOT ?>public/images/recycle.png"
                        title="<?php echo __('click to clean trash and refresh cached data') ?>"
                        onclick="recycle_onclick()"
                        />
                </div>
            </div>
        </div>
        <div style="width: 32%;float:left;" class="">
            <?php if (!empty($arr_all_position)): ?>
                <?php foreach ($arr_all_position as $v_pos_code => $arr_current_widget): ?>
                    <div class="ui-widget">
                        <div class="ui-widget-header ui-corner-top">
                            <span class="ui-icon ui-icon-arrow-2-n-s widget-arrow ui-corner-top" onclick="collaspe_form(this);"></span>
                            <h4><?php echo $v_pos_code; ?></h4>
                        </div>

                        <div class="ui-widget-content sortable" data-position="<?php echo $v_pos_code; ?>">
                            <?php if (!empty($arr_current_widget)): ?>
                                <?php foreach ($arr_current_widget as $arr_single_widget): ?>
                                    <?php
                                    $v_widget_id   = $arr_single_widget['PK_WEBSITE_THEME_WIDGET'];
                                    $v_widget_code = $arr_single_widget['C_WIDGET_CODE'];
                                    $v_widget_form = $arr_single_widget['C_FORM'];
                                    $v_widget_name = __($arr_single_widget['C_NAME']);
                                    ?>
                                    <div 
                                        class="ui-widget widget-drag" 
                                        data-id="<?php echo $v_widget_id ?>" 
                                        data-code="<?php echo $v_widget_code ?>"
                                        >
                                        <div class="ui-widget-header ui-state-default ui-corner-top">
                                            <?php if ($v_widget_form): ?>
                                                <span class="ui-icon ui-icon-arrow-2-n-s widget-arrow ui-corner-top" 
                                                      onclick="collaspe_form(this);"
                                                      >
                                                </span>
                                            <?php endif; ?>
                                            <h4 style=""><b><?php echo $v_widget_name; ?></b></h4>
                                        </div>
                                        <?php if ($v_widget_form): ?>
                                            <div class="ui-widget-content">
                                                <?php echo $v_widget_form ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    </br>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        init_dragable();
        init_sortable();
        
    });
    
    function init_dragable(){
        $dragable = 'true';
        if($dragable){
            $('.available-widgets .widget-drag').draggable({
                connectToSortable: ".sortable",
                helper: "clone",
                scroll: true,
                start: function(e, ui){
                    $(this).width('250');
                }
            });
        }
    }
    
    function recycle_onclick(){
        v_url = '<?php echo $this->get_controller_url() ?>' + 'recycle_cache';
        $('#recycle').css('opacity', '0.6');
        $.ajax({
            type: 'post'
            ,url: v_url
            ,success: function(returnVal){
               $('#recycle').css('opacity', '1');
               window.location.reload();
            }
        });
    }
    
    function update_widget($ui_item){
        if($ui_item){
            $v_loading = "<?php echo SITE_ROOT ?>public/images/loading.gif";
            $v_loading = '<img height="16" class="img-loading" width="16" src="'+ $v_loading +'"/>';
            $($ui_item).find('.widget-button').append($v_loading);
            $v_code = $($ui_item).attr('data-code');
            $v_new_order = $($ui_item).index();
            $v_position = $($ui_item).parents('.sortable').attr('data-position');
            $v_widget_id = $($ui_item).attr('data-id');
            $v_param = $($ui_item).find('form').serializeObject();
            $url = "<?php echo $this->get_controller_url(); ?>" + 'update_widget';
            
            $.ajax({
                type: 'post',
                url: $url,
                data: {
                    'widget_id': $v_widget_id,
                    'code': $v_code,
                    'new_order': $v_new_order,
                    'position': $v_position,
                    'param': $v_param
                },
                success: function(returnID){
                    $($ui_item).attr('data-id', returnID.toString());
                    $('.img-loading:first').remove();
                }
            });
        }
    }

    function init_sortable(){
        $(".sortable").sortable({
            placeholder: 'placeholder',
            cancel: ".widget-drag .ui-widget-content, .widget-drag span",
            receive: function(e, ui) { sortableIn = 1; },
            over: function(e, ui) { sortableIn = 1; },
            out: function(e, ui) { sortableIn = 0;},
            beforeStop: function(e, ui) {
                if (sortableIn == 0)
                {
                    remove_widget(ui.item);
                }
                else
                {
                
                    update_widget(ui.item);
                    var header = $(ui.item).find('.ui-widget-header');
                    if( $(ui.item).attr('data-id') == 0 )
                    {
                    
                        $(ui.item).find('.widget-summary').remove();
                        if($(ui.item).find('.ui-widget-content').length > 0){
                            $(ui.item).find('span:first').show();
                            $(ui.item).find('.ui-widget-content').slideToggle('fast');
                        }
                    }
                }          
            }
        });
    }
    
    function collaspe_form($span_collapse)
    {
        $($span_collapse).closest('.ui-widget').find('.ui-widget-content:first').slideToggle('fast');
    }
    
    function remove_widget($ui_item){
        $v_id = $($ui_item).attr('data-id');
        if($v_id){
            $.ajax({
                type: 'post',
                url: "<?php echo $this->get_controller_url() ?>" + 'remove_widget',
                data: {'widget_id': $v_id},
                success: function(){
                    $($ui_item).remove();
                }
            });
        }
    }
    
    function cancel_widget($btn_cancel)
    {
        $($btn_cancel).parents('.ui-widget-content:first').slideToggle('fast');
    }
    
    $.fn.serializeObject = function()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
</script>
<?php $this->template->display('dsp_footer.php') ?>
