<?php defined('BASEPATH') or die('No direct script access allowed');

class Publications extends MY_Controller
{
    var $auth_only = TRUE;
    var $enabled_sort_fields = array('id','name','url_alias');

    public function get_list()
	{
        $this->createHeader();
        $this->load->model('news');

        $page = (!is_null($this->getVarUrl(2,'page'))) ? (int)$this->getVarUrl(2,'page') : 0;
        $sort_fields = (!is_null($this->getVarUrl(2,'sort'))) ? $this->getVarUrl(2,'sort') : NULL;
        $sort_dir = (!is_null($this->getVarUrl(2,'type'))) ? $this->getVarUrl(2,'type') : NULL;

        $params['sort_field'] = (in_array($sort_fields,$this->enabled_sort_fields)) ? $sort_fields : 'name';
		$params['sort_dir'] = ($sort_dir == 'asc' || $sort_dir == 'desc') ? $sort_dir : 'desc';
		$params['page'] = $page;
		$params['limit'] = $this->limit;
        $params['fields'] = array('id','name','url_alias','enabled');
        $params['where'] = array('lang_id' => $this->language_id);

        $news = $this->news->getList($params);

        $this->page_data['news'] = $news['items'];
        $this->page_data['sort'] = $params['sort_field'];
		$this->page_data['sort_type'] = $params['sort_dir'];
		$this->page_data['sort_type_next'] = ($params['sort_dir'] == 'asc') ? 'desc' : 'asc';
		$this->page_data['url'] = $this->getModuleUrl()."/get_list";

        $this->load->view('backend/publications/news',$this->page_data);
        $this->createFooter($news['total']);
	}

    public function add()
    {
        $this->createHeader();

        $add_data = $this->session->flashdata('add_data');

        if(is_array($add_data) && count($add_data))
			$this->page_data = $add_data;

        $this->page_data['url_action'] = $this->getModuleUrl()."/action_add";
        $this->page_data['url'] = $this->getModuleUrl()."/get_list";

        $this->load->view('backend/publications/news_form',$this->page_data);
        $this->createFooter(0,TRUE);

    }

    public function edit()
    {
        $this->createHeader();

        $id = (int)$this->getVarUrl(2,'id');
        $this->load->model('news');

        $news = $this->news->get($id);

        if(!empty($news))
        {
            $this->page_data = $news;
            $add_data = $this->session->flashdata('add_data');

            if(is_array($add_data) && count($add_data))
                $this->page_data = $add_data;

            $this->page_data['url_action'] = $this->getModuleUrl()."/action_edit/id/".$id;
            $this->page_data['url'] = $this->getModuleUrl()."/get_list";

            $this->load->view('backend/publications/news_form',$this->page_data);
            $this->createFooter(0,TRUE);
        }
        else
        {
            show_404();
        }
    }

    public function action_add()
    {
        $this->load->library('form_validation');
		$this->load->model('news');

		$this->form_validation->set_rules('name','Имя','required|min_length[3]|max_length[200]');
        $this->form_validation->set_rules('url_alias','URL Alias','required|min_length[3]|max_length[200]');

		$params['name'] = $this->input->post('name',TRUE);
		$params['lang_id'] = $this->language_id;
        $params['url_alias'] = $this->input->post('url_alias',TRUE);
        $params['description'] = $this->input->post('description',TRUE);
        $params['content'] = $this->input->post('content',TRUE);
        $params['seo_title'] = $this->input->post('seo_title',TRUE);
        $params['seo_description'] = $this->input->post('seo_description',TRUE);
        $params['seo_keywords'] = $this->input->post('seo_keywords',TRUE);
		$params['enabled'] = $this->input->post('enabled',TRUE);

		$this->session->set_flashdata('add_data',$params);

		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('errors',$errors);

			$this->redirectReferrer();
		}

		$this->news->add($params);

		if($this->news->is_errors())
		{
			$errors = $this->news->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->news->is_warnings())
		{
			$warnings = $this->news->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirect($this->getModuleUrl()."/get_list");
    }

    public function action_edit()
    {
        $id = (int)$this->getVarUrl(2,'id');

        $this->load->library('form_validation');
		$this->load->model('news');

		$this->form_validation->set_rules('name','Имя','required|min_length[3]|max_length[200]');
        $this->form_validation->set_rules('url_alias','URL Alias','required|min_length[3]|max_length[200]');

		$params['name'] = $this->input->post('name',TRUE);
		$params['lang_id'] = $this->language_id;
        $params['url_alias'] = $this->input->post('url_alias',TRUE);
        $params['description'] = $this->input->post('description',TRUE);
        $params['content'] = $this->input->post('content',TRUE);
        $params['seo_title'] = $this->input->post('seo_title',TRUE);
        $params['seo_description'] = $this->input->post('seo_description',TRUE);
        $params['seo_keywords'] = $this->input->post('seo_keywords',TRUE);
		$params['enabled'] = $this->input->post('enabled',TRUE);

        $this->session->set_flashdata('edit_data',$params);

		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('errors',$errors);

			$this->redirectReferrer();
		}

		$this->news->update($id,$params);

		if($this->news->is_errors())
		{
			$errors = $this->news->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->news->is_warnings())
		{
			$warnings = $this->news->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirect($this->getModuleUrl()."/edit/id/".$id);
    }

    function delete()
    {
        $this->load->model('news');
		$items_delete = $this->input->post('items');

		if(is_array($items_delete) && count($items_delete))
		{
			$this->news->delete($items_delete);
		}
		else
		{
			$params['desc'] = "Can not delete. Not selected items";
			$params['file'] = __FILE__;
			$params['line'] = __LINE__;
			$params['class'] = __CLASS__;
			$params['method'] = __METHOD__;

			$this->set_warning('warning_not_selected_item',$params);
		}

		if($this->news->is_errors())
		{
			$errors = $this->news->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));
		}

		if($this->news->is_warnings())
		{
			$warnings = $this->news->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}
				
		$this->redirectReferrer();
    }
}

?>
