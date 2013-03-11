<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
Building model class
(c) NaBalkone http://www.to.net.ua
@package Models
*/

class Building_plans extends baseModel
{
    var $log_file = 'model_building_plans.log';
    var $model_table = BUILDING_PLANS_DB_TABLE;
    var $upload_field_name = array('img');
	var $upload_path = array('');
    var $insert_required_fields = array('building_id','name');
    var $relative_field_name = 'building_id';

    function __construct()
    {
        parent::__construct();

        $this->upload_path['0'] = getenv('DOCUMENT_ROOT').'/user_files/building_plans';
    }

    /**
    *	Add new Building Image
    *
    * @param	Array
    */

    function add($params)
    {
        $insert['building_id'] = (array_key_exists('building_id',$params) && !empty($params['building_id'])) ? $params['building_id'] : NULL;
        $insert['name'] = (array_key_exists('name',$params) && !empty($params['name'])) ? $params['name'] : NULL;
        $insert['description'] = (array_key_exists('description',$params)) ? $params['description'] : NULL;
        $insert['sort_order'] = (array_key_exists('sort_order',$params)) ? $params['sort_order'] : 0;

        if(!$this->not_empty($insert,$this->insert_required_fields))
        {
            $params['desc'] = "Can not create Building. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->isBuildingExists($insert['building_id']))
        {
            $params['desc'] = "Can not create Building Image. Building not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_child_element_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $config['upload_path'] = $this->upload_path['0'];
        $config['encrypt_name'] = TRUE;
        $config['allowed_types'] = 'gif|jpg|png';

        $upload_result = $this->do_upload($this->upload_field_name['0'],$config);

        if($upload_result !== FALSE)
        {
            $insert[$this->upload_field_name['0']] = $upload_result['file_name'];

            copy($this->upload_path['0']."/".$upload_result['file_name'],$this->upload_path['0']."/middle_".$upload_result['file_name']);
            $this->croupImage($this->upload_path['0']."/middle_".$upload_result['file_name'],434,284);
        }
        else
        {
            $params['msg'] = $this->upload->display_errors();
            $params['desc'] = "Can not upload file.";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('custom',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $fix_sort_order = $insert['sort_order'];
		$insert['sort_order'] = $this->getLastSortOrder(NULL,$insert['building_id']) + 1;

        $this->db->insert($this->model_table,$insert);

        $id = $this->db->insert_id();

        if(!empty($id) && $fix_sort_order != $insert['sort_order'])
		    $this->SetSortOrder($id,$fix_sort_order,$insert['building_id']);

        if($this->db->_error_message() || !$id)
        {
            $params['desc'] = "Can not create Building. Run SQL query failed";
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
    *	Update Building Plan
    *
    * @param	Array
    */

    function update($id,$params)
    {
        (array)$params;
         $update = array();

        if(array_key_exists('name',$params) && !empty($params['name']))
            $update['name'] = $params['name'];

        if(array_key_exists('description',$params) && $params['description'])
            $update['description'] = $params['description'];

        $info = $this->get($id);

        if(!count($info))
        {
            $params['desc'] = "Can not create Building plan. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists($this->upload_field_name[0],$_FILES) && !empty($_FILES[$this->upload_field_name[0]]['name']))
		{
            $config['upload_path'] = $this->upload_path['0'];
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'gif|jpg|png';

            $upload_result = $this->do_upload($this->upload_field_name['0'],$config);

            if($upload_result !== FALSE)
            {
                $update[$this->upload_field_name['0']] = $upload_result['file_name'];
                $this->delete_file($id,$this->upload_field_name['0'],FALSE);

                copy($this->upload_path['0']."/".$upload_result['file_name'],$this->upload_path['0']."/middle_".$upload_result['file_name']);
                $this->croupImage($this->upload_path['0']."/middle_".$upload_result['file_name'],434,284);
            }
        }

        $this->db->where('id',$id)->update($this->model_table,$update);

        if(array_key_exists('sort_order',$params))
        {
            $update['sort_order'] = $params['sort_order'];

            if($update['sort_order'] != $info['sort_order'])
                $this->SetSortOrder($id,$update['sort_order'],$info['building_id']);
        }

        if($this->db->_error_message() || !$id)
        {
            $params['desc'] = "Can not update Building plan. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        return TRUE;

    }
    
    function isBuildingExists($building_id)
    {
        if(!empty($building_id))
        {
            $this->db->select('id')->from(BUILDING_DB_TABLE)->where('id',$building_id);
            $this->db->limit(1,0);
            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return TRUE;
            }
        }

        return FALSE;
    }
}

?>

