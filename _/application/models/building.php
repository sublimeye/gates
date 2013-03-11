<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
Building model class
(c) NaBalkone http://www.to.net.ua
@package Models
*/

class Building extends baseModel
{
    var $log_file = 'model_building.log';
    var $model_table = BUILDING_DB_TABLE;
    var $upload_field_name = array('img','swf');
	var $upload_path = array('','');
    var $insert_required_fields = array('town_id','name','url_alias');
    var $additional_image_path = "";
    var $building_plan_image_path = "";
    var $building_place_image_path = "";

    function __construct()
    {
        parent::__construct();

        $this->upload_path['0'] = getenv('DOCUMENT_ROOT').'/user_files/building';
        $this->upload_path['1'] = getenv('DOCUMENT_ROOT').'/user_files/building_swf';

        $this->additional_image_path = getenv('DOCUMENT_ROOT').'/user_files/building_additional_images';
        $this->building_plan_image_path = getenv('DOCUMENT_ROOT').'/user_files/building_plans';
        $this->building_place_image_path = getenv('DOCUMENT_ROOT').'/user_files/building_place_images';
    }

    /**
    *	Add new Building
    *
    * @param	Array
    */

    function add($params)
    {
        (array)$params;
        $insert = array();

        $insert['town_id'] = (array_key_exists('town_id',$params) && !empty($params['town_id'])) ? $params['town_id'] : NULL;
        $insert['name'] = (array_key_exists('name',$params) && !empty($params['name'])) ? $params['name'] : NULL;
        $insert['url_alias'] = (array_key_exists('url_alias',$params) && !empty($params['url_alias'])) ? $params['url_alias'] : NULL;
        $insert['square'] = (array_key_exists('square',$params)) ? $params['square'] : NULL;
        $insert['description'] = (array_key_exists('description',$params)) ? $params['description'] : NULL;
        $insert['content'] = (array_key_exists('content',$params)) ? $params['content'] : NULL;
        $insert['seo_title'] = (array_key_exists('seo_title',$params)) ? $params['seo_title'] : NULL;
        $insert['seo_description'] = (array_key_exists('seo_description',$params)) ? $params['seo_description'] : NULL;
        $insert['seo_keywords'] = (array_key_exists('seo_keywords',$params)) ? $params['seo_keywords'] : NULL;
				$insert['swf_embed'] = (array_key_exists('swf_embed',$params)) ? $params['swf_embed'] : NULL;

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

        if(!$this->isTownExists($insert['town_id']))
        {
            $params['desc'] = "Can not create Building. Town not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_child_element_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->isUrlUnique($insert['url_alias']))
        {
            $params['desc'] = "Can not create Building. Building url not unique";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_url_not_unique',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!empty($_FILES[$this->upload_field_name['0']]['name']))
        {
            $config['upload_path'] = $this->upload_path['0'];
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'gif|jpg|png';

            $upload_result = $this->do_upload($this->upload_field_name['0'],$config);

            if($upload_result !== FALSE)
            {
                $insert[$this->upload_field_name['0']] = $upload_result['file_name'];
                $this->croupImage($this->upload_path['0']."/".$upload_result['file_name'],290,150);
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
        }

        if(!empty($_FILES[$this->upload_field_name['1']]['name']))
        {
            $config['upload_path'] = $this->upload_path['1'];
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'swf';

            $upload_result = $this->do_upload($this->upload_field_name['1'],$config);

            if($upload_result !== FALSE)
            {
                $insert[$this->upload_field_name['1']] = $upload_result['file_name'];
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
        }

        $this->db->insert($this->model_table,$insert);

        if($this->db->_error_message() || !$this->db->insert_id())
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
    *	Update User
    *
    * @param	Int
    * @param	Array
    */

    function update($id,$params)
    {
        (array)$params;
        $update = array();

        if(array_key_exists('town_id',$params) && !empty($params['town_id']))
            $update['town_id'] = $params['town_id'];

        if(array_key_exists('name',$params) && !empty($params['name']))
            $update['name'] = $params['name'];

        if(array_key_exists('url_alias',$params) && !empty($params['url_alias']))
            $update['url_alias'] = $params['url_alias'];

         if(array_key_exists('square',$params))
            $update['square'] = $params['square'];

        if(array_key_exists('description',$params))
            $update['description'] = $params['description'];

        if(array_key_exists('content',$params))
            $update['content'] = $params['content'];

        if(array_key_exists('seo_title',$params))
            $update['seo_title'] = $params['seo_title'];

        if(array_key_exists('seo_description',$params))
            $update['seo_description'] = $params['seo_description'];

         if(array_key_exists('seo_keywords',$params))
            $update['seo_keywords'] = $params['seo_keywords'];

				if(array_key_exists('swf_embed',$params))
            $update['swf_embed'] = $params['swf_embed'];

        if(!(int)$id)
        {
            $params['desc'] = "Can not update Building. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $info = $this->get($id);

        if(!$info)
        {
            $params['desc'] = "Can not update Building. Building does not exist";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_item_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists('town_id',$update) && !$this->isTownExists($update['town_id']))
        {
            $params['desc'] = "Can not update Building. Town not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_child_element_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists('url_alias', $update) && !$this->isUrlUnique($update['url_alias'],0,$id))
        {
            $params['desc'] = "Can not update Building. Url not unique";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_url_not_unique',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists($this->upload_field_name['0'],$_FILES) && !empty($_FILES[$this->upload_field_name['0']]['name']))
        {
            $config['upload_path'] = $this->upload_path['0'];
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'gif|jpg|png';

            $upload_result = $this->do_upload($this->upload_field_name['0'],$config);

            if($upload_result !== FALSE)
            {
                $update[$this->upload_field_name['0']] = $upload_result['file_name'];
                $this->delete_file($id,$this->upload_field_name['0'],FALSE);

                $this->croupImage($this->upload_path['0']."/".$upload_result['file_name'],290,150);
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
        }

        if(array_key_exists($this->upload_field_name['1'],$_FILES) && !empty($_FILES[$this->upload_field_name['1']]['name']))
        {
            $config['upload_path'] = $this->upload_path['1'];
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'swf';

            $upload_result = $this->do_upload($this->upload_field_name['1'],$config);

            if($upload_result !== FALSE)
            {
                $update[$this->upload_field_name['1']] = $upload_result['file_name'];
                $this->delete_file($id,$this->upload_field_name['1'],FALSE);
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
        }

        if(!count($update))
        {
            $params['desc'] = "Can not update Building. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $this->db->where('id',(int)$id);
        $this->db->update($this->model_table,$update);

        if($this->db->_error_message())
        {
            $params['desc'] = "Can not update Building. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        return TRUE;
    }

    function delete($ids)
    {
        if(!empty($ids))
        {
            $ids = (is_array($ids)) ? $ids : array($ids);

            foreach($ids as $id)
            {
                $building_place_images = $this->get_building_place_images($id);

                if(!empty($building_place_images))
                {
                    foreach($building_place_images as $bpi)
                    {
                        $path = $this->building_place_image_path."/".$bpi['img'];

                        if(!empty($bpi['img']) && is_file($path))
                        {
                            unlink($path);
                        }
                    }
                }

                $additional_images = $this->getAdditionalImage($id);

                if(!empty($additional_images))
                {
                    foreach($additional_images as $ai)
                    {
                        $path = $this->additional_image_path."/".$ai['img'];

                        if(!empty($ai['img']) && is_file($path))
                        {
                            unlink($path);
                        }
                    }
                }

                $building_plans = $this->getBuildingPlan($id);

                if(!empty($building_plans))
                {
                    foreach($building_plans as $bp)
                    {
                        $path = $this->building_plan_image_path."/".$bp['img'];

                        if(!empty($bp['img']) && is_file($path))
                        {
                            unlink($path);
                        }
                    }
                }
            }
        }

        return parent::delete($ids);
    }

    function getAdditionalImage($building_id)
    {
        if(!empty($building_id))
        {
            $this->db->select('*')->from(BUILDING_IMAGES_DB_TABLE)->where('building_id',$building_id);
            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return $query->result_array();
            }
        }

        return array();
    }

    function getBuildingPlan($building_id)
    {
        if(!empty($building_id))
        {
            $this->db->select('*')->from(BUILDING_PLANS_DB_TABLE)->where('building_id',$building_id);
            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return $query->result_array();
            }
        }

        return array();
    }

    function get_building_place_images($building_id)
    {
        if(!empty($building_id))
        {
            $this->db->select('img')->from(BUILDING_PLACE_IMAGES_DB_TABLE)->join(BUILDING_PLACES_DB_TABLE,BUILDING_PLACE_IMAGES_DB_TABLE.".building_place_id=".BUILDING_PLACES_DB_TABLE.".id");
            $this->db->where(BUILDING_PLACES_DB_TABLE.'.building_id',$building_id);

            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return $query->result_array();
            }
        }

        return array();
    }

    function isTownExists($town_id)
    {
        if(!empty($town_id))
        {
            $this->db->select('id')->from(TOWN_DB_TABLE)->where('id',$town_id);
            $this->db->limit(1,0);
            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    function getList($params)
    {
        $fields = (array_key_exists('fields', $params)) ? $params['fields'] : "*";
        $where = (array_key_exists('where', $params)) ? $params['where'] : array();
        $page = (array_key_exists('page', $params)) ? (int)$params['page'] : 0;
        $limit = (array_key_exists('limit', $params)) ? (int)$params['limit'] : 0;
        $sort_field = (array_key_exists('sort_field', $params)) ? $params['sort_field'] : "";
        $sort_dir = (array_key_exists('sort_dir', $params)) ? $params['sort_dir'] : "ASC";
        $language_id = (array_key_exists('language_id', $params)) ? $params['language_id'] : NULL;

        $result = array(
            'items' => array(),
            'total' => 0
        );

        $page = ($page >= 0) ? $page : 0;
        $limit_begin = $page * $limit;

        if(!empty($language_id))
        {
            $where[TOWN_DB_TABLE.'.lang_id'] = $language_id;
        }

        $this->db->select($fields)->from($this->model_table);
        $this->db->join(TOWN_DB_TABLE,BUILDING_DB_TABLE.".town_id=".TOWN_DB_TABLE.".id");
        $this->db->where($where);

        if (!empty($sort_field)) {
            $this->db->order_by($sort_field, $sort_dir);
        }

        if(!empty($limit))
        {
            $this->db->limit($limit, $limit_begin);
        }

        $query = $this->db->get();

        if (!$this->db->_error_message()) {
            $result['total'] = $this->getTotal($params);
            $result['items'] = $query->result_array();
        }

        return $result;
    }

    function getTowns($language_id)
    {
        if(!empty($language_id))
        {
            $this->db->from(TOWN_DB_TABLE)->where('lang_id',$language_id);
            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return $query->result_array();
            }
        }

        return array();
    }
}

?>

