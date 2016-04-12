<?php
defined('DS') or die('no direct access');
?>
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
<link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
<div class='widget widget-poll <?php echo $v_widget_style ?>' data-code='poll'>
    <div class='widget-header'>
        <h6><?php echo $v_widget_name ?></h6>
    </div>
    <div class='widget-content'>
        <?php if (!empty($arr_single_poll)): ?>
            <?php
            $v_poll_name = $arr_single_poll['C_NAME'];
            $ck_begin    = $arr_single_poll['CK_BEGIN_DATE'];
            $ck_end      = $arr_single_poll['CK_END_DATE'];
            $v_disable   = Cookie::get('WIDGET_POLL_' . $v_poll_id) ? 'disabled' : ''; 
        ?>

            <p class='widget-content-title'><?php echo $v_poll_name ?></p>

            <form>
                <input type='hidden' id="hdn_poll_id" name='hdn_poll_id' value='<?php echo $v_poll_id ?>'/>
                <input type='hidden' name='hdn_answer_id' value=''/>
                <?php $n = count($arr_all_opt); ?>
                <?php
                for ($i = 0; $i < $n; $i++):
                    ?>
                    <?php
                    $item = $arr_all_opt[$i];
                    $v_opt_val = $item['PK_POLL_DETAIL'];
                    $v_opt_answer = $item['C_ANSWER'];
                    ?>
                    <label>
                        <input type="radio" name='rad_widget_poll_<?php echo $v_index ?>' value='<?php echo $v_opt_val ?>' onclick="this.form.hdn_answer_id.value=this.value"/>
                        <?php echo $v_opt_answer ?></br>
                    </label>

                <?php endfor; ?>
                
                <?php if ($ck_begin < 0 or $ck_end < 0): ?>
                    <?php echo __('this poll is expired'); ?>
                <?php else: ?>
                    <a class="vote" href="javascript:void(0)" onclick="btn_vote_onclick(this);" >
                        <span>
                            <?php echo $v_disable ? __('thank you for voting') : __('vote') ?>
                        </span>
                    </a>
                <?php endif; ?>
                &nbsp;&nbsp;
                <a class="a_poll_result" href='javascript:void(0)'  onclick="dsp_poll_result(this)">
                    <?php echo __('see result') ?>
                </a>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php if (!defined('WIDGET_POLL')): ?>
    <?php define('WIDGET_POLL', 1); ?>
    <div id="widget_poll_modal" title="" name="widget_poll_modal" style="display: none;overflow: hidden; margin: 0 auto"></div>

    <script>
        $(document).ready(function(){
            jQuery.browser = {};
                 (function () {
                     jQuery.browser.msie = false;
                     jQuery.browser.version = 0;
                     if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
                         jQuery.browser.msie = true;
                         jQuery.browser.version = RegExp.$1;
                     }
                 })();
        })
        function dsp_poll_result($a_obj)
        {
            var v_id = $($a_obj).parents('form:first').find('#hdn_poll_id').val();
            var url = "<?php echo $this->get_controller_url() ?>" + 'dsp_poll_result/' + v_id;
            $('#widget_poll_modal').html('<iframe src="'+ url +'" style="width:100%;height:100%;border:none;"></iframe>').dialog({
                width: 600,
                height: 300,
                modal: true,
                title: '<?php echo __('poll result') ?>'
            });
            
        }
        function btn_vote_onclick($btn_obj){
            var cookie = document.cookie;
            var aid = $($btn_obj).parents('form:first').find('[name=hdn_answer_id]').val();   
            var pid = $($btn_obj).parents('form:first').find('[name=hdn_poll_id]').val();  
            if(!aid || !pid){
//              alert('<?php echo __('please choose answer!')?>')
                return;
            }
            if(cookie.match('WIDGET_POLL_'+pid) != null)
            {
                dsp_poll_result($btn_obj);
                return;
            }
            
            var url= "<?php echo $this->get_controller_url() ?>" + 'handle_widget/';
            url += '&code=poll';
            url += '&pid=' + pid;
            url += '&aid=' + aid;
            $('#widget_poll_modal').html('<iframe src="'+ url +'" style="width:100%;height:100%;border:none;"></iframe>').dialog({
                width: 600,
                height: 300,
                modal: true,
                title: '<?php echo __('please enter captcha') ?>'
            });
        }
        function close_widget_poll_model(){
            $('#widget_poll_modal').dialog('close');
        }
                                
    </script>
<?php endif; ?>