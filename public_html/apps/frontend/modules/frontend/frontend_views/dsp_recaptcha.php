<?php
defined('DS') or die('no direct access');
?>
<head>
    <title><?php echo __('please enter captcha') ?></title>
</head>
<body>
    <form action="#" onsubmit="modal_onsubmit();">
        <?php echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY); ?>
        <input type="button" class="ButtonAccept" onClick="modal_onsubmit();" value="<?php echo __('confirm') ?>"/>
        <input type="button" class="ButtonCancel" onClick="window.parent.hidePopWin(false)" value="<?php echo __('cancel') ?>"/>
    </form>
    <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
    <script>
        function modal_onsubmit(){
            $a = {
                'recaptcha_challenge_field' : $('#recaptcha_challenge_field').val()
                ,'recaptcha_response_field' : $('#recaptcha_response_field').val()
            };
            returnVal = $a;
            window.parent.hidePopWin(true);
            return false;
        }
    </script>
</body>
