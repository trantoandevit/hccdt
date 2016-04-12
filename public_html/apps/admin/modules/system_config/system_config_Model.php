<?php
defined('DS') or die('no direct access');

class system_config_Model extends Model{
    function __construct()
    {
        parent::__construct();
    }
    
    function update_options()
    {
        $xml = (get_post_var('XmlData', '', false));
        $controller = get_post_var('controller');
        if($xml == '')
        {
            $this->exec_done($controller);
        }
        $dbkey = OPT_SYSCFG;
        if(DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "
                If(Exists(Select PK_OPTION From t_ps_option Where C_OPTION_KEY = ?))
                    Update t_ps_option Set C_OPTION_VALUE = ? where C_OPTION_KEY = ?
                Else
                    Insert Into t_ps_option(C_OPTION_KEY, C_OPTION_VALUE) Values(?, ?)
                ";
            $params = array(
                $dbkey
                ,$xml, $dbkey
                ,$dbkey, $xml
            );
            $this->db->Execute($stmt, $params);
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select COUNT(PK_OPTION) From t_ps_option Where C_OPTION_KEY = ?";
            $v_count = $this->db->getOne($stmt,array($dbkey));
            if($v_count > 0)
            {
                $stmt = "Update t_ps_option Set C_OPTION_VALUE = ? where C_OPTION_KEY = ?";
                $this->db->Execute($stmt, array($xml,$dbkey));
            }
            else 
            {
                $stmt = "Insert Into t_ps_option(C_OPTION_KEY, C_OPTION_VALUE) Values(?, ?)";
                $this->db->Execute($stmt, array($dbkey,$xml));
            }
        }
        
        if($this->db->errorNo() == 0)
        {
            $this->exec_done($controller);
        }
        else{
            $this->exec_fail($controller, __('update failed'));
        }
    }
    
    function qry_data()
    {
        $stmt = "Select C_OPTION_VALUE From t_ps_option Where C_OPTION_KEY = ?";
        $params = array(OPT_SYSCFG);
       return xml_add_declaration($this->db->getOne($stmt, $params));
      
    }
}
?>
