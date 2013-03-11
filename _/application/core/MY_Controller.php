<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
  Base frontend controller class
  (c) NaBalkone http://www.to.net.ua
  @package Controllers
*/

class MY_Controller extends CI_Controller
{
	var $messages = array();
	var $log_path = '';
	var $log_file = 'Controller.log';
	var $error_log_file_prefix = 'errors';
	var $warning_log_file_prefix = 'warnings';
	var $subject = "debug report";
	var $lang_code = 'ru';
	var $language_id = NULL;
    var $default_language_id = 1;
    var $limit = 20;
	var $auth_only = FALSE;
    var $disabled_remote_call = FALSE;
    var $page_data = array();
    var $header_data = array();
    var $footer_data = array();

	function __construct()
	{
		parent::__construct();

        if($this->disabled_remote_call && (!array_key_exists('REMOTE_ADDR',$_SERVER) || $_SERVER['REMOTE_ADDR'] != '127.0.0.1'))
        {
            show_404();
            exit;
        }

        $this->log_path = BASEPATH.$this->config->item('log_folder');

		if($this->auth_only)
		{
			if(!$this->auth_user->isAuth())
			{
				redirect("/backend");
                exit;
			}
		}

        $active_language = $this->session->userdata('language_id');
        $this->language_id = (!empty($active_language)) ? $active_language : $this->default_language_id;

        $this->limit = ($this->session->userdata('limit') > 0) ? $this->session->userdata('limit') : $this->limit;

        setlocale(LC_ALL, 'ru_RU.UTF-8');
	}

    function createHeader()
    {
        $this->header_data['active_section'] = $this->getVarUrl(1,'backend');
        $this->load->view('backend/header',$this->header_data);
    }

    function createFooter($total = 0,$simple = FALSE)
    {
		$active = $this->getVarUrl(2,'page');

		$total = ($total > 0) ? (int)$total : 0;
        $active = ((int)$active > 0) ? (int)$active : 0;
		$onPage = $this->limit;

		if($total == 0 && $total <= $onPage)
		{
			if($active > 0)
				$active = 0;

			$allPage = 0;
		}
		else
		{
			$allPage = ceil($total/$onPage)-1;
		}

		if($active > $allPage)
			$active = 0;

		$url = $this->uri->segment(1)."/".$this->removeVarUrl(2,'page');
        $par = $this->getQueryVarsUrl();

		$this->footer_data['all_page'] = $allPage;
		$this->footer_data['total_items'] = $total;
		$this->footer_data['url'] = $url;
        $this->footer_data['par'] = $par;
		$this->footer_data['active_page'] = $active;
		$this->footer_data['simple'] = $simple;
		$this->footer_data['limit'] = $this->limit;
		$this->footer_data['language'] = $this->language_id;

		$this->load->view('backend/footer',$this->footer_data);
    }

    function redirectReferrer($alternative_url = NULL)
    {
        $this->load->library('user_agent');

		if($this->agent->is_referral())
		{
			redirect($this->agent->referrer());
            exit;
		}
		else
		{
            if(!empty($alternative_url))
            {
			    redirect($alternative_url);
                exit;
            }
            else
            {
                redirect('/');
            }
		}
    }

    function redirect($url)
    {
        if(!empty($url))
        {
            header('location:/'.$url);
            exit;
        }
    }

	function getDisplayMsg($msg)
	{
		if(is_array($msg) && count($msg))
		{
			$display_str = "";

			foreach($msg as $e)
			{
				$display_str.="<p>".$e['msg']."</p>";
			}

			return $display_str;
		}

		return '';
	}

	function getVarUrl($pos,$varName)
	{
		$url_array = $this->uri->uri_to_assoc($pos);
		$varName = (!empty($varName)) ? mb_strtolower($varName) : NULL;

		if(!empty($varName) && is_array($url_array))
		{
			if(array_key_exists($varName, $url_array))
			{
				return $url_array[$varName];
			}
		}

		return NULL;
	}

	function removeVarUrl($pos,$varName,$params =array())
	{
		$url_array = (empty($params)) ? $this->uri->uri_to_assoc($pos) : $params;

		if(is_array($url_array) && count($url_array) && !empty($varName))
		{
			if(is_scalar($varName))
			{
				$varName = mb_strtolower($varName);

				if(array_key_exists($varName, $url_array))
				{
					unset($url_array[$varName]);
				}
			}

			if(is_array($varName))
			{
				foreach($varName as $vn)
				{
					$vn = mb_strtolower($vn);

					if(array_key_exists($vn, $url_array))
					{
						unset($url_array[$vn]);
					}
				}
			}

			$result = $this->uri->assoc_to_uri($url_array);

			return (!empty($result)) ? $result : $result;
		}

		return "";
	}

	function getVarsUrl($pos)
	{
		$url_array = $this->uri->uri_to_assoc($pos);

		if(is_array($url_array))
		{
			return $this->uri->assoc_to_uri($url_array);
		}

		return NULL;
	}

    function getQueryVarsUrl($clear_empty_values = FALSE)
	{
        if(is_array($_GET))
        {
            $params = array();

            foreach($_GET as $k=>$g)
            {
                if((!empty($k) && is_scalar($k)))
                {
                    if($clear_empty_values)
                    {
                        if(!empty($g))
                            $params[]= $k."=".$g;
                    }
                    else
                    {
                        $params[]= $k."=".$g;
                    }
                }
            }

            if(count($params))
                return "?".implode('&',$params);
        }

		return NULL;
	}

    function removeQueryVarUrl($varName)
	{
        $varName = (!empty($varName)) ? mb_strtolower($varName) : NULL;

        if(!empty($varName))
        {
            if(array_key_exists($varName, $_GET))
            {
                $temp_get = $_GET[$varName];
                unset($_GET[$varName]);

                $result = $this->getQueryVarsUrl(TRUE);
                $_GET[$varName] = $temp_get;

                return $result;
            }
        }

		return  $this->getQueryVarsUrl(TRUE);
	}

    function clear_url($url)
    {
        if(!empty($url))
        {
            $uri = explode('/',$url);
            $get_p = parse_url($url, PHP_URL_QUERY);

            if(is_array($uri))
            {
                foreach($uri as $k=>$u)
                {
                    if($u == 'page')
                    {
                        unset($uri[$k]);
                        unset($uri[$k+1]);
                        break;
                    }
                }

                $url = implode('/',$uri);

                if(!empty($get_p))
                    $url.="?".$get_p;

                return $url;
            }
        }

        return "";
    }

    function getModuleUrl()
    {
        $url_array = $this->uri->segment_array();

        if(is_array($url_array))
        {
            if(count($url_array) > 2)
                $url_array = array_slice($url_array,0,2);

            return implode('/',$url_array);
        }

        return '#';
    }


	/**
	* Return string static block
	*
	* @param	string
	*/

	function getStaticBlock($key)
	{
		$msg = $this->lang->line($key);

		return (!empty($msg)) ? $msg : "";
	}


	/**
	 * 	Send debug message (print_r) to browser
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	*/

	function log_print($var, $msg = "", $return = false)
	{
		if(!is_string($msg))
			$msg = "";

		if(!is_scalar($var))
			$var = print_r($var, TRUE);

		$id = md5(uniqid(""));

		$s = '<div align="left" style="font:bold 11px tahoma;border:1px solid #000000;background-color:#EEEEEE;padding:5px;">' .
				 '<a name="a_' . $id . '"></a>' .
				 '<a href="#a_' . $id . '" style="display:block;float:left" onclick="var _=document.getElementById(\'dump_' . $id . '\').style;if(_.display==\'\'||_.display==\'block\'){_.display=\'none\';this.innerHTML=\'[+]\';}else{_.display=\'block\';this.innerHTML=\'[-]\';};return false">[-]</a>' .
				 htmlspecialchars($msg) . '</div>' .
				 '<div align="left" id="dump_' . $id . '" style="padding-bottom:10px">' .
				 '<pre style="font:11px tahoma;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#FFFFFF;padding:5px;margin:0px;">' .
				 htmlspecialchars($var) . '</pre></div>';

		if($return)
		{
			return $s;
		}
		else
		{
			echo $s;
		}
	}

	/**
	*	Send debug message (print_r) to browser
	*
	* @param	mixed
	* @param	string
	* @param	bool
	*/

	function log_print_r( $var, $msg = "", $return = false )
	{
		if($return)
		{
			return $this->log_print( print_r($var, true), $msg, true );
		}
		else
		{
			$this->log_print( print_r($var, true), $msg );
		}
	}

/**
	*	Send debug message (var_dump) to browser
	*
	* @param	mixed
	* @param	string
	* @param	int
	*/

	function log_dump($var,$msg = "",$return = false)
	{
		if($return)
		{
			return $this->log_print( var_export($var, true), $msg, true );
		}
		else
		{
			$this->log_print( var_export($var, true), $msg );
		}
	}

	/**
	*	Write debug message to file
	*
	* @param	mixed
	* @param	string
	* @param	boolean
	* @param	string
	* @param	int
	*/

  function log_file($var,$msg = "",$is_developer = FALSE,$file_name = "")
  {
		$file_name = (!empty($file_name)) ? $file_name : $this->log_file;
		$log_state = (is_bool($is_developer) && $is_developer)? PROJECT_IN_DEVELOPMENT : $this->config->item('project_state');

		if(($log_state == PROJECT_IN_DEVELOPMENT || $log_state == PROJECT_IN_DEBUGGING) && is_string($file_name) && !empty($file_name))
    {
      $oldu = umask(0);
      $s = "";

			$path = $this->log_path.$file_name;

			clearstatcache( false, $path );

			if(file_exists($path) && is_file($path) && filesize($path) > 0)
			{
					$s.= PHP_EOL;
			}

			$f = fopen( $path, 'a+' );
			flock ($f,LOCK_EX);

      if( function_exists('microtime') )
      {
        list( $msec, $sec ) = explode( " ", microtime() );
        $dt = date("j.m.Y/H:i:s", $sec)."/".$msec;
      }
      else
      {
        $dt = date("j.m.Y/H:i:s")."/0";
      }

      $s .= "==== ".$dt;

      if( is_string($msg) && $msg!="" )
        $s .= " ".$msg;

      $s .= " ====".PHP_EOL;
      $s .= $var;

      if($f)
      {
        fwrite( $f, $s );
        fflush($f);
        flock($f,LOCK_UN);
        fclose($f);
      }

      @chmod( $path,0666);
      umask($oldu);

			return true;
    }

		return false;
	}

	/**
	*	Write debug message to file using print_r output
	*
	* @param	mixed
	* @param	string
	* @param	boolean
	* @param	string
	* $param  Integer
	*/

  function log_file_print_r( $var,$msg = "",$is_developer = FALSE,$file_name = "",$mode = NULL)
  {
		return $this->log_file(print_r($var,true),$msg,$is_developer,$file_name,$mode);
  }

	/**
	* Write debug message to file using var_export output
	*
	* @param	mixed
	* @param	string
	* @param	boolean
	* @param	string
	* @param	Integer
	*/

  function log_file_dump($var,$msg = "",$is_developer = FALSE,$file_name = "",$mode = NULL)
  {
		return $this->log_file(var_export($var,true),$msg,$is_developer,$file_name,$mode);
  }

	/**
	* Write debug message to file using var_dump output
	*
	* @param	mixed
	* @param	string
	* @param	boolean
	* @param	string
	* @param	Integer
	*/

	function log_file_var_dump($var,$msg = "",$is_developer = FALSE,$file_name = "",$mode = NULL)
	{
		ob_start();
	    var_dump($var);
	    $s = ob_get_contents();
		ob_end_clean();

		return $this->log_file($s,$msg,$is_developer,$file_name,$mode);
	}

	/**
	* Send debug message to email
	*
	* @param	mixed
	* @param	string
	* @param	array
	* @param	boolean
	*/

	function log_mail( $var, $msg = "", $mail_params = array(),$is_developer = FALSE)
	{
		$this->load->helper('email');

		$log_state = (is_bool($is_developer) && $is_developer)? PROJECT_IN_DEVELOPMENT : $this->config->item('project_state');

		if($log_state == PROJECT_IN_DEVELOPMENT || $log_state == PROJECT_IN_DEBUGGING)
    {
			if(!count($mail_params))
			{
				$mail_params['to'] = $this->config->item('developers mails');
			}

			if(is_array($mail_params) && array_key_exists('to',$mail_params))
			{
				if(function_exists('microtime'))
					list($tusec, $tsec) = explode(" ", microtime());
				else
				{
					$tsec = time();
					$tusec = 0;
				}

				$s = "==== ".date("j.m.Y/H:i:s",$tsec)."/".$tusec;

				if( is_string($msg) && $msg!="" )
					$s .= " ".$msg;

				$s .= " ====".PHP_EOL;
				$s .= print_r($var, true);
				$headers = "From: log_mail@tendex.su"."\r\n";

				$msg = (!empty($msg)) ? $msg : $this->subject;
				$mail_params['subject'] = (array_key_exists('subject',$mail_params) && !empty($mail_params['subject'])) ? $mail_params['subject'] : $msg;

				if(is_scalar($mail_params['to']) && valid_email($mail_params['to']))
				{
					mail($mail_params['to'], $mail_params['subject'],$s,$headers);
					return TRUE;
				}

				if(is_array($mail_params['to']))
				{
					$send_to_mails = array();

					foreach($mail_params['to'] as $mp)
					{
						if(is_scalar($mp) && valid_email($mp))
							$send_to_mails[] = $mp;
					}

					if(count($mp))
					{
						$send_to_mails_str = implode(', ',$send_to_mails);

						mail($send_to_mails_str, $mail_params['subject'],$s,$headers);

						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}

	/**
	* Set error
	*
	* @param	string
	* @param	int
	* @param	array
	*/

	function set_error($key,$error_level = 0,$params = array(),$is_developer = false)
	{
		$is_set = $this->set_msg($key,MESSAGE_ERROR,$error_level,$params);

		if($is_set)
		{
			if($error_level == DEBUG_LEVEL_CRITICAL)
			{
				$url_redirect = "/";
				$url_redirect = site_url($url_redirect);

				redirect($url_redirect);
			}

			$is_log_error = (is_bool($is_developer) && $is_developer)? true : $this->config->item('log_error');

			if($is_log_error)
			{
				$this->log_error($key,$error_level,$params);
			}
		}

		return $is_set;
	}

	/**
	* Set error with logging file and line of error-point
	*
	* @param	string
	* @param	int
	* @param	array
	*/

	private function log_error($key,$error_level = 0,$params = array())
	{
		$this->log_msg($key,MESSAGE_ERROR,$error_level,$params);
	}

	/**
	* Delete error
	*
	* @param	string
	*/

	function unset_error($key)
	{
		$this->unset_msg($key,MESSAGE_ERROR);
	}

	/**
	* Delete all errors
	*
	*/

	function unset_errors()
	{
		$this->unset_msg_type(MESSAGE_ERROR);
	}

	/**
	* Return error value
	*
	* @param	string
	*/

	function get_error( $key )
	{
		return $this->get_msg($key,MESSAGE_ERROR);
	}


	/**
	* Return list of errors
	*
	*/

	function get_errors()
	{
		return $this->get_msg_type( MESSAGE_ERROR );
	}


	/**
	*	Return last error value
	*
	*/

	function get_last_error()
	{
		if($this->is_errors())
		{
			$errors = $this->get_msg_type( MESSAGE_ERROR );

			return end($errors);
		}

		return NULL;
	}

	/**
	*	Return first error value
	*
	*/

	function get_first_error()
	{
		if($this->is_errors())
		{
			$errors = $this->get_msg_type( MESSAGE_ERROR );
			$error = reset($errors);

			return ($error) ? $error : NULL;
		}

		return NULL;
	}

	/**
	* Return true if error was set
	*
	* @param	string
	*/

	function is_error( $key )
	{
		return $this->is_msg( $key, MESSAGE_ERROR );
	}


	/**
	* Return true if errors exists
	*
	*/
	function is_errors()
	{
		return $this->is_msg_type( MESSAGE_ERROR );
	}



	/**
	* Return errors message string
	*
	* @param	string
	* @param	string
	*/

	private function get_error_message($key,$lang)
	{
		return $this->get_msg_string( $key, MESSAGE_ERROR, $lang );
	}


	/**
	* Set warning
	*
	* @param	string
	* @param	array
	* @param	boolean
	*/

	function set_warning($key,$params = array(),$is_developer = false)
	{
		$is_set = $this->set_msg($key,MESSAGE_WARNING,0,$params);

		if($is_set)
		{
			$is_log_warning = (is_bool($is_developer) && $is_developer)? true : $this->config->item('log_warning');

			if($is_log_warning)
			{
				$this->log_warning($key,$params);
			}
		}

		return $is_set;
	}

	/**
	* Set warning with logging file and line of error-point
	*
	* @param	string
	* @param	array
	*/

	private function log_warning($key,$params = array())
	{
		$this->log_msg($key,MESSAGE_WARNING,0,$params);
	}

	/**
	* Delete warning
	*
	* @param	string
	*/

	function unset_warning($key)
	{
		$this->unset_msg($key,MESSAGE_WARNING);
	}

	/**
	* Delete all warning
	*
	*/

	function unset_warnings()
	{
		$this->unset_msg_type(MESSAGE_WARNING);
	}

	/**
	* Return warning value
	*
	* @param	string
	*/

	function get_warning( $key )
	{
		return $this->get_msg($key,MESSAGE_WARNING);
	}


	/**
	* Return list of warnings
	*
	*/

	function get_warnings()
	{
		return $this->get_msg_type( MESSAGE_WARNING );
	}


	/**
	*	Return last warning value
	*
	*/

	function get_last_warning()
	{
		if($this->is_warnings())
		{
			$warnings = $this->get_msg_type( MESSAGE_WARNING );

			return end($warnings);
		}

		return NULL;
	}

	/**
	*	Return first warning value
	*
	*/

	function get_first_warning()
	{
		if($this->is_warnings())
		{
			$warnings = $this->get_msg_type( MESSAGE_WARNING );
			$warning = reset($warnings);

			return ($warning) ? $warning : NULL;
		}

		return NULL;
	}

	/**
	* Return true if $warning was set
	*
	* @param	string
	*/

	function is_warning( $key )
	{
		return $this->is_msg( $key, MESSAGE_WARNING);
	}


	/**
	* Return true if $warnings exists
	*
	*/

	function is_warnings()
	{
		return $this->is_msg_type( MESSAGE_WARNING );
	}



	/**
	* Return $warning message string
	*
	* @param	string
	* @param	string
	*/

	private function get_warning_message($key,$lang)
	{
		return $this->get_msg_string($key,MESSAGE_WARNING,$lang);
	}


	/**
	* Set message
	*
	* @param	string
	* @param	int
	* @param	int
	* @param	array
	*/

	private function set_msg($key,$msg_type,$msg_level = 0,$params =array())
	{
		if(is_scalar($key) && ($msg_type == MESSAGE_ERROR || $msg_type == MESSAGE_WARNING))
		{
			$msg = array('level' => $msg_level);

			if(is_array($params))
			{
				$msg['description'] = (array_key_exists('desc',$params)) ? $params['desc'] : "";
				$msg['file'] = (array_key_exists('file',$params)) ? $params['file'] : '';
				$msg['line'] = (array_key_exists('line',$params)) ? $params['line'] : 0;
				$msg['class'] = (array_key_exists('class',$params)) ? $params['class'] : '';
				$msg['method'] = (array_key_exists('method',$params)) ? $params['method'] : '';
				$msg['var_name'] = (array_key_exists('var_name',$params)) ? $params['var_name'] : '';
				$msg['var_value'] = (array_key_exists('var_value',$params)) ? $params['var_value'] : '';
				$msg['correct_var_value'] = (array_key_exists('correct_var_value',$params)) ? $params['correct_var_value'] : '';
			}

			$this->messages[ $msg_type ][ $key ] = $msg;

			return TRUE;
		}

		return FALSE;
	}

	/**
	* Delete message
	*
	* @param	string
	* @param	int
	*/

	private function unset_msg( $key, $msg_type )
	{
		if(is_scalar($key) && is_numeric($msg_type) && array_key_exists( $msg_type, $this->messages )
		 && array_key_exists( $key, $this->messages[ $msg_type ] ))
		{
			unset($this->messages[ $msg_type ][ $key ] );
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Delete all messages for specified type
	*
	* @param	int
	*/

	private function unset_msg_type( $msg_type )
	{
		if(is_numeric($msg_type) && array_key_exists( $msg_type, $this->messages ))
		{
			$this->messages[ $msg_type ] = array();
			return TRUE;
		}

		return FALSE;
	}

	/**
	* Return message value
	*
	* @param	string
	* @param	int
	*/

	private function get_msg($key,$msg_type)
	{
		if(is_scalar( $key ) && is_scalar( $msg_type ) && array_key_exists( $msg_type, $this->messages )
			 && array_key_exists( $key, $this->messages[ $msg_type ] ))
		{
			$msg = $this->messages[ $msg_type ][ $key ];
			$msg['msg'] = $this->get_msg_string($key,$msg_type);

			return $msg;
		}

		return NULL;
	}


	/**
	* Return list of messages of specified type
	*
	* @param	int
	*/

	private function get_msg_type($msg_type)
	{
		if(array_key_exists( $msg_type, $this->messages ))
		{
			$msgs = $this->messages[$msg_type];

			foreach($msgs as $k => $ms)
			{
				$msgs[$k]['msg'] = $this->get_msg_string($k,$msg_type);
			}

			return $msgs;
		}

		return array();
	}

	/**
	* Return true if message was set
	*
	* @param	string
	* @param	int
	*/

	private function is_msg($key,$msg_type)
	{
		return ( array_key_exists( $msg_type, $this->messages ) && array_key_exists( $key, $this->messages[ $msg_type ] ) ) ? true : false;
	}

	/**
	* Return true if messages on specified type exists
	*
	* @param	int
	*/

	private function is_msg_type( $msg_type )
	{
		return ( array_key_exists( $msg_type, $this->messages ) && count( $this->messages[ $msg_type ] ) > 0 ) ? true : false;
	}


	/**
	* Return message string
	*
	* @param	string
	* @param	int
	* @param	string
	*/

	public function get_msg_string( $key, $msg_type, $lang = "" )
	{
		$msg_file_name = "";

		if(!is_string($lang) || $lang == "")
		{
			$lang = $this->lang_code;
		}

		if($msg_type == MESSAGE_ERROR)
		{
			$msg_file_name =$this->error_log_file_prefix;
		}

		if($msg_type == MESSAGE_WARNING)
		{
			$msg_file_name =$this->warning_log_file_prefix;
		}

		$lang = mb_strtolower($lang);

		$this->lang->load($msg_file_name,$lang);
		$msg = $this->lang->line($key);

		return (!empty($msg)) ? $msg : "Unresolved message code={$key}";
	}

	/**
	* Set message with logging file and line of error-point
	*
	* @param	string
	* @param	int
	* @param	int
	* @param	array
	*/

	private function log_msg($key,$msg_type,$msg_level = 0,$params = array())
	{
		$msg = array();

		$msg['level'] = $msg_level;
		$msg['description'] = (array_key_exists('desc',$params)) ? $params['desc'] : "";
		$msg['file'] = (array_key_exists('file',$params)) ? $params['file'] : '';
		$msg['line'] = (array_key_exists('line',$params)) ? $params['line'] : 0;
		$msg['class'] = (array_key_exists('class',$params)) ? $params['class'] : '';
		$msg['method'] = (array_key_exists('method',$params)) ? $params['method'] : '';
		$msg['var_name'] = (array_key_exists('var_name',$params)) ? $params['var_name'] : '';
		$msg['var_value'] = (array_key_exists('var_value',$params)) ? $params['var_value'] : '';
		$msg['correct_var_value'] = (array_key_exists('correct_var_value',$params)) ? $params['correct_var_value'] : '';

		$msg['msg'] = $this->get_msg_string($key,$msg_type);

		$msg_str = "";

		foreach($msg as $k => $v)
		{
			$v = ($v === false) ? 0 : $v;

			if(strlen((string)$v) > 0)
			{
				$msg_str.= $k.": ".$v.PHP_EOL;
			}
		}

		if($msg_type == MESSAGE_ERROR)
			$msg_type_name = "ERROR";

		if($msg_type == MESSAGE_WARNING)
			$msg_type_name = "MESSAGE_WARNING";

		switch($msg_level)
		{
			case DEBUG_LEVEL_CUSTOM :
			{
				$msg_level_name = 'CUSTOM';
				break;
			}

			case DEBUG_LEVEL_SQL :
			{
				$msg_level_name = 'SQL';
				break;
			}

			case DEBUG_LEVEL_ROUTE :
			{
				$msg_level_name = 'ROUTE';
				break;
			}

			case DEBUG_LEVEL_CRITICAL :
			{
				$msg_level_name = 'CRITICAL';
				break;
			}
		}

		$this->Get_log_msg_file($msg_type,$msg_level);
	}

	/**
	* Returns the file name for logging
	*
	* @param	Int
	* @param	Int
	*/

	private function Get_log_msg_file($msg_type,$msg_level)
	{
		$file_name = "error.log";
		$msg_type = ($msg_type == MESSAGE_ERROR || $msg_type == MESSAGE_WARNING) ? $msg_type : MESSAGE_ERROR;
		$msg_level = ($msg_level >= DEBUG_LEVEL_CUSTOM && $msg_level <= DEBUG_LEVEL_CRITICAL) ? $msg_level : DEBUG_LEVEL_CUSTOM;

		switch($msg_type)
		{
			case MESSAGE_ERROR :
			{
				$file_name = $this->error_log_file_prefix;
				break;
			}

			case MESSAGE_WARNING :
			{
				$file_name = $this->warning_log_file_prefix;
				break;
			}
		}

		switch($msg_level)
		{
			case DEBUG_LEVEL_CUSTOM :
			{
				$file_name.= '_custom.log';
				break;
			}

			case DEBUG_LEVEL_SQL :
			{
				$file_name.= '_sql.log';
				break;
			}

			case DEBUG_LEVEL_ROUTE :
			{
				$file_name.= '_route.log';
				break;
			}

			case DEBUG_LEVEL_CRITICAL :
			{
				$file_name.= '_critical.log';
				break;
			}
		}

		return $file_name;
	}
}

?>