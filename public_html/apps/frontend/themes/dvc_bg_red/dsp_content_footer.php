<div class="footer-content" style="width: 530px;margin: 0 auto">
         <?php
        $v_unit_name = get_system_config_value(CFGKEY_UNIT_NAME);
        if(trim($v_unit_name) != '')
        {
            echo '<strong style="font-size: 16px">Cơ quan chủ quản: '.$v_unit_name. '</strong><br>';    
        }
        $v_copyright = get_system_config_value(CFGKEY_UNIT_COPYRIGHT);
        if(trim($v_copyright) != '')
        {
            echo '<b class="copyright">Chịu trách nhiệm nội dung: '.$v_copyright. '</b><br/>';    
        }
        $v_address  = get_system_config_value(CFGKEY_UNIT_ADD);
        if(trim($v_address) != '')
        {
            echo '<b >Địa chỉ: </b> '.$v_address.'<br/>';
        }
        $v_phone  = get_system_config_value(CFGKEY_UNIT_PHONE);
        if(trim($v_phone) != '')
        {
            echo '<b >Điện thoại: </b> '.$v_phone.'';
        }

        $v_fax  = get_system_config_value(CFGKEY_UNIT_FAX);
        if(trim($v_phone) != '')
        {
            echo ' - <b >Số Fax: </b> '.$v_fax;
        }
        $v_email = get_system_config_value(CFGKEY_UNIT_EMAIL);
        if(trim($v_email) != '')
        {
            echo '<br/><b >Email: </b> '.$v_email.'';
        }
        $v_website = get_system_config_value(CFGKEY_UNIT_WEBSITE);
        if(trim($v_website) != '')
        {
            echo ' - <b >Website: </b> '.$v_website.'<br/>';
        }
        ?>
</div>