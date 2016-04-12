<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<?php
$arr_url       = explode('/', $_GET['url']);
$v_menu_active = isset($arr_url[1]) ? $arr_url[1] : '';
?>
<?php if ($v_menu_active != ''): ?>
    <?php if ($this->dsp_side_bar): ?>
        </div>
        </div>
    <?php else: ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
</div>
<!-- end .grid_16 -->

<div class="clear">&nbsp;</div>

<div class="grid_24 ">
     <?php
        $v_unit_name = defined('_CONST_UNIT_NAME_ERRORS') ? _CONST_UNIT_NAME_ERRORS : '';
    ?>
    <div id="footer">Phần mềm Cổng thông tin <?php echo $v_unit_name; ?></div>
</div>

</div>
<!-- class="container_24" -->
</body>
</html>
