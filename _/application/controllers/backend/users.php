<?php defined('BASEPATH') or die('No direct script access allowed');

class Users extends MY_Controller
{
    var $auth_only = TRUE;
    var $enabled_sort_fields = array('id','name','email');

    public function get_list()
	{
        $this->createHeader();
        $this->load->model('user');

        $page = (!is_null($this->getVarUrl(2,'page'))) ? (int)$this->getVarUrl(2,'page') : 0;
        $sort_fields = (!is_null($this->getVarUrl(2,'sort'))) ? $this->getVarUrl(2,'sort') : NULL;
        $sort_dir = (!is_null($this->getVarUrl(2,'type'))) ? $this->getVarUrl(2,'type') : NULL;

        $params['sort_field'] = (in_array($sort_fields,$this->enabled_sort_fields)) ? $sort_fields : 'name';
		$params['sort_dir'] = ($sort_dir == 'asc' || $sort_dir == 'desc') ? $sort_dir : 'desc';
		$params['page'] = $page;
		$params['limit'] = $this->limit;
        $params['fields'] = array('id','name','email','enabled');

        $users = $this->user->getList($params);

        $this->page_data['users'] = $users['items'];
        $this->page_data['sort'] = $params['sort_field'];
		$this->page_data['sort_type'] = $params['sort_dir'];
		$this->page_data['sort_type_next'] = ($params['sort_dir'] == 'asc') ? 'desc' : 'asc';
		$this->page_data['url'] = $this->getModuleUrl()."/get_list";

        $this->load->view('backend/users/users',$this->page_data);
        $this->createFooter($users['total']);
	}

    public function add()
    {
        $this->createHeader();

        $add_data = $this->session->flashdata('add_data');

		if(is_array($add_data) && count($add_data))
			$this->page_data = $add_data;

        $this->page_data['url_action'] = $this->getModuleUrl()."/action_add";
        $this->page_data['url'] = $this->getModuleUrl()."/get_list";

        $this->load->view('backend/users/users_form',$this->page_data);
        $this->createFooter(0,TRUE);
    }

    public function edit()
    {
        $this->createHeader();

        $id = (int)$this->getVarUrl(2,'id');
        $this->load->model('user');

        $user = $this->user->get($id);

        if(!empty($user))
        {
            $this->page_data = $user;
            $add_data = $this->session->flashdata('add_data');

            if(is_array($add_data) && count($add_data))
                $this->page_data = $add_data;

            $this->page_data['url_action'] = $this->getModuleUrl()."/action_edit/id/".$id;
            $this->page_data['url'] = $this->getModuleUrl()."/get_list";

            $this->load->view('backend/users/users_form',$this->page_data);
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
		$this->load->model('user');

		$this->form_validation->set_rules('name','Имя','required|min_length[3]|max_length[200]');
        $this->form_validation->set_rules('email','E-mail','required|min_length[3]|max_length[200]|valid_email');
        $this->form_validation->set_rules('password','Пароль','required|min_length[3]|max_length[200]');

		$params['name'] = $this->input->post('name',TRUE);
		$params['email'] = $this->input->post('email',TRUE);
		$params['password'] = $this->input->post('password',TRUE);
		$params['enabled'] = $this->input->post('enabled',TRUE);

		$this->session->set_flashdata('add_data',$params);

		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('errors',$errors);

			$this->redirectReferrer();
		}

		$this->user->add($params);

		if($this->user->is_errors())
		{
			$errors = $this->user->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->user->is_warnings())
		{
			$warnings = $this->user->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirect($this->getModuleUrl()."/get_list");
    }

    public function action_edit()
    {
        $id = (int)$this->getVarUrl(2,'id');

        $this->load->library('form_validation');
		$this->load->model('user');

		$this->form_validation->set_rules('name','Имя','required|min_length[3]|max_length[200]');
        $this->form_validation->set_rules('email','E-mail','required|min_length[3]|max_length[200]|valid_email');
        $this->form_validation->set_rules('password','Пароль','required|min_length[3]|max_length[200]');

		$params['name'] = $this->input->post('name',TRUE);
		$params['email'] = $this->input->post('email',TRUE);
		$params['password'] = $this->input->post('password',TRUE);
		$params['enabled'] = $this->input->post('enabled',TRUE);

		$this->session->set_flashdata('edit_data',$params);

		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('errors',$errors);

			$this->redirectReferrer();
		}

		$this->user->update($id,$params);

		if($this->user->is_errors())
		{
			$errors = $this->user->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->user->is_warnings())
		{
			$warnings = $this->user->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirect($this->getModuleUrl()."/edit/id/".$id);
    }

    function delete()
    {
        $this->load->model('user');
		$items_delete = $this->input->post('items');

		if(is_array($items_delete) && count($items_delete))
		{
			$this->user->delete($items_delete);
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

		if($this->user->is_errors())
		{
			$errors = $this->user->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));
		}

		if($this->user->is_warnings())
		{
			$warnings = $this->user->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}
				
		$this->redirectReferrer();
    }
}

?>
