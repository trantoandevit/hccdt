<?php
defined('DS') or die();
?>
<?php
 $v_type_name  = isset($arr_all_weblink[0]['C_TYPE_NAME']) ? $arr_all_weblink[0]['C_TYPE_NAME'] : '';
 $title_weblink = isset($title_weblink) ? $title_weblink :  0; 
?>
<div class="widget weblink">
    <?php if($title_weblink == 1): ?>
        <div class="widget-header">
                <h6><?php echo $v_type_name ?></h6>
        </div>
    <?php endif;?>
<select name="sel_weblink" onchange="weblink_onchange(this)">
    <option value="#" style="text-align: center">-- <?php echo __('weblink')?> --</option>
    <?php $n = count($arr_all_weblink); ?>
    <?php for ($i = 0; $i < $n; $i++): ?>
        <?php $item = $arr_all_weblink[$i]; ?>
        <option value="<?php echo $item['C_URL'] ?>" data-newwindow="<?php echo $item['C_NEW_WINDOWN'] ?>"><?php echo $item['C_NAME'] ?></option>
    <?php endfor; ?>
</select>
<?php $const_key = 'WIDGET_WEBLINK_FIRST_INIT'; ?>
<?php if (!defined($const_key)): ?>
    <?php define($const_key, 1); ?>
    <script>
        function weblink_onchange(select_object){
            url = select_object.value;
            new_window = $(select_object).find('option:selected').attr('data-newwindow');
            if(new_window ==1){
                window.open(url, '_blank');
                window.focus();
            }
            else
            {
                window.location = url;
            }
        }
    </script>
<?php endif; ?>
</div>

