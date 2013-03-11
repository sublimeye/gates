<?php defined('BASEPATH') or die('No direct script access allowed');

class Authorization extends MY_Controller
{
    var $redirect_auth_url = "backend/users/get_list";
    var $login_url = "backend";

	public function index()
	{
		if(!$this->auth_user->isAuth())
        {
            $this->load->view('backend/login');
        }
        else
        {
            $this->redirect($this->redirect_auth_url);
        }
	}
	
	public function set_limit()
	{
        if(!$this->auth_user->isAuth())
        {
            show_404();
            exit;
        }

		$limit = (int)$this->input->post('limit',TRUE);
		$this->load->library('user_agent');
		
		if($limit > 0 && $limit<=500)
		{
			$this->session->set_userdata(array('limit' =>$limit));

            if($this->agent->is_referral())
            {
                redirect($this->clear_url($this->agent->referrer()));
            }
		}
		
		$this->redirectReferrer('/backend');
	}
	
	public function set_language()
	{
        if(!$this->auth_user->isAuth())
        {
            show_404();
            exit;
        }

		$language = (int)$this->input->post('language',TRUE);

        $this->load->model('language');
        $this->load->library('user_agent');
	
		if($this->language->isLanguage($language))
		{
			$this->session->set_userdata(array('language_id' => $language));

            if($this->agent->is_referral())
            {
                $this->clear_url($this->agent->referrer());
            }
		}
		
		$this->redirectReferrer('/backend');
	}

    public function login()
    {
        $this->load->library('form_validation');

		$this->form_validation->set_rules('login','Логин', 'required|min_length[3]|max_length[15]');
		$this->form_validation->set_rules('password','Пароль','required|min_length[3]|max_length[15]');

		if($this->form_validation->run() != FALSE)
		{
			$login = $this->input->post('login',TRUE);
			$password =$this->input->post('password',TRUE);

			if($this->auth_user->userAuth($login,$password))
			{
				$this->redirect($this->redirect_auth_url);
			}
			else
			{
                $errors = $this->auth_user->getErrors();
                $this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

				$this->redirect($this->login_url);
			}
		}
    }

    public function logout()
    {
        $this->auth_user->userLogOut();
		redirect($this->login_url);
    }
	
}

?>
