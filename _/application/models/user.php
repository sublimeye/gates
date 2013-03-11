<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
User model class
(c) NaBalkone http://www.to.net.ua
@package Models
*/

class User extends baseModel
{
    var $log_file = 'model_users.log';
    var $model_table = USERS_DB_TABLE;
    var $insert_required_fields = array('name','password','email');

    function __construct()
    {
        parent::__construct();
    }

    /**
    *	Add new User
    *
    * @param	Array
    */

    function add($params)
    {
        (array)$params;
        $insert = array();

        $insert['name'] = (array_key_exists('name',$params) && !empty($params['name'])) ? $params['name'] : NULL;
        $insert['password'] = (array_key_exists('password',$params) && !empty($params['password'])) ? md5($params['password']) : NULL;
        $insert['email'] = (array_key_exists('email',$params) && !empty($params['email'])) ? $params['email'] : NULL;
        $insert['enabled'] = (array_key_exists('enabled',$params) && $this->isStatus($params['enabled'])) ? $params['enabled'] : STATUS_ENABLED;

        if(!$this->not_empty($insert,$this->insert_required_fields))
        {
            $params['desc'] = "Can not create User. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->isLoginUnique($insert['email']))
        {
            $params['desc'] = "Can not create User. User login not unique";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_user_login_not_unique',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $this->db->insert($this->model_table,$insert);

        if($this->db->_error_message() || !$this->db->insert_id())
        {
            $params['desc'] = "Can not create User. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        return TRUE;
    }

    /**
    *	Update User
    *
    * @param	Int
    * @param	Array
    */

    function update($id,$params)
    {
        (array)$params;
        $update = array();

        if(array_key_exists('name',$params) && !empty($params['name']))
            $update['name'] = $params['name'];

        if(array_key_exists('password',$params) && !empty($params['password']))
            $update['password'] = $params['password'];

        if(array_key_exists('email',$params) && !empty($params['email']))
            $update['email'] = $params['email'];

        if(array_key_exists('enabled',$params) && $this->isStatus($params['enabled']))
            $update['enabled'] = $params['enabled'];

        if(!count($update) || !(int)$id)
        {
            $params['desc'] = "Can not update User. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->get($id,'id'))
        {
            $params['desc'] = "Can not update User. User does not exist";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_item_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists('password',$update))
        {
            $update['password'] = md5($update['password']);
        }

        if(array_key_exists('email', $update) && !$this->isLoginUnique($update['email'],$id))
        {
            $params['desc'] = "Can not update User. Login not unique";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;
            $params['var_name'] = '$update["login"]';
            $params['var_value'] = $update["email"];

            $this->set_error('error_user_login_not_unique',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $this->db->where('id',(int)$id);
        $this->db->update($this->model_table,$update);

        if($this->db->_error_message())
        {
            $params['desc'] = "Can not update User. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        return TRUE;
    }


    /**
    *	Check the uniqueness of the user's login
    *
    * @param	String
    * @param	Int
    */

    function isLoginUnique($login,$id=0)
    {
        if(!empty($login))
        {
            $where = array('email' => $login);

            if((int)$id > 0)
                $where['id !='] = (int)$id;

            $user = $this->getWhere($where,'id');

            if(!$user)
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    function Auth($login,$password)
    {
        if(!empty($login) && !empty($password))
        {
            $this->db->select('*')->from($this->model_table);
            $this->db->where("BINARY email = '".$login."' AND password = '".md5($password)."' AND enabled=".STATUS_ENABLED ,NULL,FALSE)->limit(1,0);
            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                $user_info['user_auth'] = $query->row_array();
                $this->session->set_userdata($user_info);

                return TRUE;
            }
            else
            {
                $params['desc'] = "User Auth error. Invalid login or password";
                $params['file'] = __FILE__;
                $params['line'] = __LINE__;
                $params['class'] = __CLASS__;
                $params['method'] = __METHOD__;

                $this->set_error('error_invalid_login_or_password',DEBUG_LEVEL_CUSTOM,$params);

                return FALSE;
            }
        }

        $params['desc'] = "User Auth error. Username or password not set";
        $params['file'] = __FILE__;
        $params['line'] = __LINE__;
        $params['class'] = __CLASS__;
        $params['method'] = __METHOD__;

        $this->set_error('error_username_or_password_not_set',DEBUG_LEVEL_CUSTOM,$params);

        return FALSE;
    }
}

?>

