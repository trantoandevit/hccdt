<?php
if (!defined('SERVER_ROOT'))
{
	exit('No direct script access allowed');
}
?>

    </div>
    <!--End bg-border-right-->
    </div>
    <!--End bg-border left-->
</div>
<!--end wrap-container-->
    <div class="col-md-12 block">
        <div class="footer-menu">
            <label style="width: 10px">&nbsp;</label>
            <?php $arr_all_menu_position['menu_footer'] = isset($arr_all_menu_position['menu_footer']) ? $arr_all_menu_position['menu_footer'] : array(); ?>
            <?php for ($i = 0; $i < count($arr_all_menu_position['menu_footer']); $i++):
                    $row_menu = $arr_all_menu_position['menu_footer'][$i];
                    $v_url    = $row_menu['C_URL'];
                    $v_name   = $row_menu['C_NAME'];
                    ?>
                    <a href="<?php echo $v_url; ?>" title="">
                        <?php echo $v_name; ?> 
                        <?php echo ($i == (count($arr_all_menu_position['menu_footer']) - 1))?'':'|';?>
                    </a>
                    
            <?php endfor; ?>
            <div class="footer-right">
                <?php $n = isset($arr_all_widget_position['widget_footer_menu']) ? count($arr_all_widget_position['widget_footer_menu']) : 0; ?>
                <?php for ($i = 0; $i < $n; $i++): ?>
                    <?php echo $arr_all_widget_position['widget_footer_menu'][$i]['C_CONTENT'] ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <div class="col-md-12 footer-info" >
        <?php require_once __DIR__.'/dsp_content_footer.php';?>       
    </div>
</body>
</html>
