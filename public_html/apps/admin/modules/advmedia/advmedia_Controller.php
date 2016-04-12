<?php

defined('DS') or die('no direct access');
define('EXT_SESSION_KEY', 'media_extensions');

class advmedia_Controller extends Controller
{

    public function __construct()
    {
        parent::__construct('admin', 'advmedia');
        Session::init();
        
        if (DEBUG_MODE < 10)
        {
        	$this->model->db->debug = 0;
        }

        //dang nhap
        (Session::get('user_id')) or $this->login_admin();
        Session::check_permission('XEM_DANH_SACH_MEDIA', false) or $this->access_denied();

        $v_lang_id                                   = session::get('session_lang_id');
        $this->view->template->arr_all_lang          = $this->model->qry_all_lang();
        $this->view->template->arr_count_article     = $this->model->gp_qry_count_article();
        $this->view->template->arr_all_grant_website = $this->model->gp_qry_all_website_by_user($v_lang_id);
        $this->model->goback_url                     = $this->view->get_controller_url();
    }

    function main()
    {
        $data['current_dir'] = get_request_var('dir');
        $data['dir_path']    = SERVER_ROOT . 'upload' . DS . $data['current_dir'];
        //folder exist
        if (is_dir($data['dir_path']) == false)
        {
            echo __('this directory has been moved or deleted');
            echo '<br/><a href="' . $this->view->get_controller_url() . '">' . __('click here to return to root') . '</a>';
            return;
        }
        if (!isset($this->extenstions))
        {
            $this->extenstions = strtoupper(EXT_ALL);
        }
        Session::set(EXT_SESSION_KEY, str_replace(' ', '', $this->extenstions));
        $this->view->render('dsp_main', $data);
    }

    /**
     * 
     * @param string $type '', 'image', 'spreadsheet', 'data', 'text', 'video', 'audio', 'compressed'
     */
    function dsp_service($type='')
    {
        $type       = strtoupper($type);
        $const_name = "EXT_$type";
        if (defined($const_name))
        {
            $this->extenstions = strtoupper(constant($const_name));
        }

        $this->view->is_service = true;
        $this->main();
    }

    function get_dir_content()
    {
        $dir_name = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '';
//        if ($dir_name == '')
//        {
//            if (!is_dir(SERVER_ROOT . '/upload/2013'))
//                mkdir(SERVER_ROOT . '/upload/2013');
//            if (!is_dir(SERVER_ROOT . '/upload/2014'))
//                mkdir(SERVER_ROOT . '/upload/2014');
//            echo json_encode(array(
//                'folder' => array(
//                    '2013' => array(
//                        'date' => date('d-m-Y H:i')
//                        , 'path' => DS . '2013'
//                        , 'type' => __('directory'))
//                    , '2014' => array(
//                        'date' => date('d-m-Y H:i')
//                        , 'path' => DS . '2014'
//                        , 'type' => __('directory')))
//                , 'file'   => array()
//            ));
//            return;
//        }
        $dir_path = SERVER_ROOT . 'upload' . DS . $dir_name;

        if (!is_dir($dir_path) && file_exists($dir_path))
        {
            $dir_path = dir_name($dir_path, '\\/');
        }
        if (is_dir($dir_path) == false)
        {
            echo json_encode(array(
                'folder'  => array()
                , 'file'    => array()
                , 'dirpath' => $dir_path
            ));
            return;
        }
        $dir_path .= DS;
        $scan_result        = scandir($dir_path);
        $arr_folder         = array();
        $arr_file           = array();
        $allowed_extensions = explode(',', str_replace(' ', '', Session::get(EXT_SESSION_KEY)));
        foreach ($scan_result as $item)
        {
            $item_info = array();

            //chi xu ly folder va file
            if ($item != '.' && $item != '..')
            {
                $item_info['date'] = date("d/m/Y H:i", filemtime($dir_path . $item));
                $item_info['path'] = $dir_name . DS . $item;
                if (is_dir($dir_path . $item)) //neu la folder
                {
                    $item_info['type'] = __('directory');
                    $arr_folder[$item] = $item_info;
                }
                else //neu la file
                {
                    $item_info['path'] = str_replace('\\', '/', $item_info['path']);
                    $item_info['type'] = strtoupper(substr($item, strrpos($item, '.') + 1));

                    $viewall   = (bool) empty($allowed_extensions);
                    $inallowed = (bool) in_array($item_info['type'], $allowed_extensions);
                    $isindex   = ($item == 'index.htm' or $item == 'index.html') ? true : false;
                    if (($viewall or $inallowed) && !$isindex)
                    {
                        $arr_file[$item] = $item_info;
                    }
                }
            }
        }
        echo json_encode(array('folder' => $arr_folder, 'file'   => $arr_file));
        return;
    }

    function dsp_item_details()
    {
        $path  = isset($_REQUEST['path']) ? $_REQUEST['path'] : '';
        $fname = SERVER_ROOT . 'upload' . DS . $path;
        $furl  = SITE_ROOT . 'upload/' . str_replace('\\', '/', $path);

        if (file_exists($fname) && Session::check_permission('XEM_DANH_SACH_MEDIA', false))
        {
            $data['fname'] = $fname;
            $data['furl']  = $furl;
            $this->view->render('dsp_item_details', $data);
        }
    }

    function create_dir()
    {
        $this->model->create_dir();
        Session::check_permission('THEM_MOI_MEDIA', false) or $this->access_denied();
        $parent_dir = SERVER_ROOT . 'upload' . get_post_var('path', DS, false);
        $dirname    = auto_slug(get_post_var('dirname', 'new folder'));
        $msg        = $this->model->create_dir($parent_dir, $dirname);

        echo json_encode($msg);
    }

    function delete_items()
    {
        $items      = get_post_var('items', array(), false);
        $parent_dir = SERVER_ROOT . 'upload' . DS . get_post_var('parent_dir', '', false);

        $msg = $this->model->delete_items($parent_dir, $items);

        echo json_encode($msg);
    }

    function upload()
    {
        $error              = "";
        $fileElementName    = $_POST['file_element'];
        $folder             = $_POST['folder'];
        $fnam               = $_FILES[$fileElementName]['name'];
        $file_extension     = substr($fnam, strrpos($fnam, '.') + 1);
        $fname_without_ext  = current(explode(".", $fnam));
        $allowed_extensions = explode(',', str_replace(' ', '', Session::get(EXT_SESSION_KEY)));
        $error_code         = 0;
        if (!empty($allowed_extensions) && !in_array(strtoupper($file_extension), $allowed_extensions))
        {
            $error_code = 8;
        }

        if (file_exists($folder) && !is_dir($folder))
        {
            $folder = dirname($folder);
        }

        if (Session::check_permission('THEM_MOI_MEDIA', false) == false)
        {
            $error_code = 7;
        }

        if ($error_code)
        {
            $_FILES[$fileElementName]['error'] = $error_code;
        }

        if (!empty($_FILES[$fileElementName]['error']))
        {
            switch ($_FILES[$fileElementName]['error'])
            {

                case '1':
                    $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'The uploaded file was only partially uploaded';
                    break;
                case '4':
                    $error = 'No file was uploaded.';
                    break;

                case '6':
                    $error = 'Missing a temporary folder';
                    break;
                case '7':
                    $error = 'Failed to write file to disk';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = 'No error code avaiable';
            }
        }
        elseif (empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
        {
            $error = 'No file was uploaded..';
        }
        else
        {

            $fnam = auto_slug($fname_without_ext) . '.' . $file_extension;
            $path = SERVER_ROOT . 'upload' . DS . trim($folder, '/\\') . DS;
            $size = @filesize($_FILES[$fileElementName]['tmp_name']);

            //for security reason, we force to remove uploaded file
            //Use move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $destination) instead.
            //@unlink($_FILES[$fileElementName]['tmp_name']);
            if (file_exists($path . $fnam))
            {
                $fnam = $fname_without_ext . '-' . date('Y.m.d.H.i.s') . '.' . $file_extension;
            }
            move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $path . $fnam);
        }

        $res = new stdClass();

        $res->error    = $error;
        $res->filename = $fnam;
        $res->path     = $path;
        $res->size     = sprintf("%.2fMB", $size / 1048576);
        $res->dt       = date('Y-m-d H:i:s');
        die(json_encode($res));
    }

    function update_db()
    {
        $this->model->update_db();
    }

}
