<?php
class main_Controller extends Controller {

    function __construct()
    {
        parent::__construct('admin', 'main');

        Lang::load_lang('lang_vi');
    }

    public function main()
    {
        echo __('Admin dashboard!');
        echo __('Một từ tiếng việt');
    }

}