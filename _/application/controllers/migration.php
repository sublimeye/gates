<?php defined('BASEPATH') or die('No direct script access allowed');

class migration extends MY_controller
{
    var $log_file = "migration.log";
    var $enabled_ips = array('127.0.0.1');

    function __construct()
    {
        parent::__construct();

        if(!in_array($_SERVER['REMOTE_ADDR'],$this->enabled_ips))
        {
            show_404();
        }
    }

    function index()
    {
        $this->load->library('migration');

        $this->load->view('migration_start');
        $this->migration->current();
    }
}



 
