
<?php
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('synthesis','jquery.treeview');
$VIEW_DATA['arr_script'] = array('jquery.treeview');
  
$arr_all_sitemap = isset($arr_all_menu_position['sitemap'])?$arr_all_menu_position['sitemap']:array(); 
?>
<?php $this->render('dsp_header', $VIEW_DATA, $this->theme_code); ?>
<div class="clear" style="height: 10px"></div>
<div class="col-md-12 content">
    <div class='col-md-3 block'>
        <?php $n = isset($arr_all_widget_position['widget_left']) ? count($arr_all_widget_position['widget_left']) : 0; ?>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php echo $arr_all_widget_position['widget_left'][$i]['C_CONTENT'] ?>
        <?php endfor; ?>
    </div>
    <div class='col-md-9 block' style="min-height: 200px">
        <div class="div-synthesis" style='margin-top: 0px' >
            <div class="div_title_bg-title-top"></div>
            <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                     <label><?php echo __('has sitemap')?></label>
                </div>
                <div class="title-border-right"></div>
            </div>
           
            <div style='margin: 10px'>
                <div id="sidetreecontrol">
                    <a href="?#"><?php echo __('miniature')?></a> | <a href="?#"><?php echo __('view all')?></a>
                </div>
                <ul id="tree">
                    <?php
                    $v_current_index = 0;
                    $v_selected_menu = -1;
                    for ($i = 0; $i < count($arr_all_sitemap); $i++):
                        $row_menu = $arr_all_sitemap[$i];
                        $v_menu_id = $row_menu['PK_MENU'];
                        $v_internal_order = $row_menu['C_INTERNAL_ORDER'];
                        $v_level_index = strlen($v_internal_order) / 3 - 1;
                        $v_url = $row_menu['C_URL'];
                        $v_name = $row_menu['C_NAME'];
                        $v_title = $v_name;

                        $v_current_class = '';


                        $v_cat_id = get_xml_value(simplexml_load_string($row_menu['C_VALUE']), "//item[@data='1' and @type='category']/id");

                        if (isset($_GET['category_id']) == TRUE && $_GET['category_id'] == $v_cat_id) {
                            $v_current_class = ' current';
                            if ($v_level_index == 0) {
                                $v_selected_menu = $v_current_index;
                            }
                        } else {
                            $v_current_class = '';
                        }

                        if (isset($arr_all_menu_position['sitemap'][$i - 1]['C_INTERNAL_ORDER'])) {
                            $v_internal_order_pre = $arr_all_menu_position['sitemap'][$i - 1]['C_INTERNAL_ORDER'];
                        } else {
                            $v_internal_order_pre = '000';
                        }

                        if ($v_level_index == 0) {
                            $v_current_index++;
                        }
                        $v_level_pre = strlen($v_internal_order_pre) / 3 - 1;
                        ?>
                        <?php if ($v_level_pre == $v_level_index && $i != 0): ?>
                        </li>
                        <?php elseif ($v_level_pre < $v_level_index): ?>
                        <ul>
                            <?php
                        elseif ($v_level_pre > $v_level_index):
                            echo "</li>";
                            for ($n = 0; $n < ($v_level_pre - $v_level_index); $n++) {
                                echo "</ul>";
                                echo "</li>";
                            }
                            ?>
                        <?php endif; ?>

                        <li >
                            <a href="<?php echo $v_url; ?>" title="<?php echo $v_title; ?>">
                        <?php echo $v_name; ?> 
                            </a>
                    <?php endfor; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="clear" style="height: 10px"></div>
    <script>
    $(function() {
            $("#tree").treeview({
                    collapsed: true,
                    animated: 200,
                    control:"#sidetreecontrol",
                    persist: "location"
            });
    })
		
</script>
<?php
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
