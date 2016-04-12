<?php
defined('DS') or die();
?>
<div class="widget widget-statistic <?php echo $widget_style ?>" data-code="weblink">
    <div class="widget-header">
        <h6><?php echo __('statistic') ?></h6>
    </div>
    <div class="widget-content">
        <table style="border:none;border-spacing: 0px;">
            <colgroup>
                <col width="40%">
                <col width="60%">
            </colgroup>
            <tr>
                <td><?php echo __('viewing') ?>:</td>
                <td>
                    <h4 class="blue" style="margin:0;">&nbsp;<script>var ref = (''+document.referrer+'');document.write('<script src="http://freehostedscripts.net/ocounter.php?site=ID2833473&&r=' + ref + '"><\/script>');</script><?php //echo $stats_online; ?></h4>
                </td>
            </tr>
            <tr>
                <td><?php echo __('web counter') ?>:</td>
                <td><h4 class="blue" style="margin:0;">&nbsp;<?php echo $stats_all; ?></h4></td>
            </tr>
        </table>
    </div>
</div>