<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
Database library class
(c) NaBalkone http://www.to.net.ua
@package Library
*/

class Auth_user 
{
	var $ci = NULL;

	function __construct()
	{
		$this->ci =& get_instance();
	}
	
	public function userAuth($login,$password)
	{
        $this->ci->load->model('user');

		$login = (!empty($login)) ? mysql_real_escape_string($login) : NULL;
		$password = (!empty($password)) ? $password : NULL;

        return $this->ci->user->Auth($login,$password);
	}

	public function getErrors()
	{
		return $this->ci->user->get_errors();
	}
	
	public function userLogOut()
	{
		if($this->isAuth())
		{
			$this->ci->session->unset_userdata('user_auth');
			
			return TRUE;
		}
		
		return FALSE;
	}

	public function getUserAuth()
	{
		if($this->isAuth())
		{
			return $this->ci->session->userdata('user_auth');
		}
		
		return NULL;
	}
	
	public function getUserVar($var)
	{
		if($this->isAuth() && !empty($var) && is_scalar($var))
		{
			$user = $this->getUserAuth();
			
			if(is_array($user) && count($user))
			{
				if(array_key_exists($var,$user))
					return $user[$var];
			}
		}
		
		return FALSE;
	}

	public function getUserId()
	{
		$user_data = $this->ci->session->userdata('user_auth');

		if(is_array($user_data) && array_key_exists('id',$user_data))
		{
			return $user_data['id'];
		}
		
		return NULL;
	}
	
	public function isAuth()
	{
	    if($this->getUserId())
		    return TRUE;

        return FALSE;
	}
	
}

?>
