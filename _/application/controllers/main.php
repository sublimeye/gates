<?php defined('BASEPATH') or die('No direct script access allowed');

class Main extends MY_controller
{
    var $log_file = "main.log";

    function index()
    {
        $this->load->model('news');

        $this->page_data['base_url'] = $this->config->item('base_url');
        $this->page_data['news'] = $this->news->getNews();
        $this->load->view('main',$this->page_data);
    }

    function get_news()
    {
        $id = $this->input->post('id',TRUE);

        $result = array('status' => 0, 'data' => array());

        if(empty($id))
        {
            echo json_encode($result);
            exit;
        }

        $this->load->model('news');

        $this->page_data = $this->news->getWhere(array('id' => $id, 'enabled' => 1),array('name','content',"DATE_FORMAT(date_add,'%d %M %Y') AS date_show"));

        $result['data'] = $this->load->view('open_news',$this->page_data,TRUE);
        $result['status'] = 1;

        echo json_encode($result);
    }
}



 
