<?php

class Session
{

    function __construct()
    {
        
    }

    public static function init()
    {
        @session_start();
    }

    public static function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    public static function destroy()
    {
        @session_destroy();
    }
    public static function session_unset($key = '')
    {
        if(trim($key) != '' && isset($_SESSION[$key]))
        {
              unset($_SESSION[$key]);
        }
    }
    public static function check_permission($code_function, $user_permission_on_website = TRUE)
    {
        if ($_SESSION['is_admin'] == 1)
        {
            return TRUE;
        }
        else
        {
            if ($user_permission_on_website == TRUE)
            {
                //Xet tren tung chuyen trang rieng
                //var_dump($_SESSION['arr_all_grant_function_code']);exit;
                $user_function = replace_bad_char($_SESSION['session_website_id']) . "::" . $code_function;
                return in_array($user_function, $_SESSION['arr_all_grant_function_code']);
            }
            else
            {
                //Quyen tren tat ca cac chuyen trang
                //var_dump($_SESSION['arr_all_function_code']);exit;
                return in_array($code_function, $_SESSION['arr_all_grant_function_without_web']);
            }
        }
    }

}

Class Cookie
{

    function __construct()
    {
        
    }

    public static function set($key, $val, $time = '')
    {
        if (!$time)
        {
            setcookie($key, $val, time() + 3600, SITE_ROOT);
        }
        else
        {
            setcookie($key, $val, $time, SITE_ROOT);
        }
    }

    public static function get($key, $default_value = '')
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default_value;
        ;
    }

    public static function destroy($key = '')
    {
        if ($key != '')
        {
            unset($_COOKIE[$key]);
        }
        else
        {
            $_COOKIE = array();
        }
    }

}