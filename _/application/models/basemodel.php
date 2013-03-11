<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
  Base model class
  (c) NaBalkone http://www.to.net.ua
  @package Models
*/

class baseModel extends CI_Model
{
    var $messages = array();
    var $log_path = '';
    var $log_file = 'baseModel.log';
    var $error_log_file_prefix = 'errors';
    var $warning_log_file_prefix = 'warnings';
    var $subject = "debug report";
    var $lang_code = '';
    var $limit = 20;
    var $relative_field_name = '';
    var $upload_path = array();
    var $upload_field_name = array();

    function baseModel()
    {
        parent::__construct();

        $this->log_path = BASEPATH . $this->config->item('log_folder');
        $this->lang_code = 'ru';
    }

    function setModelLang($lang_code)
    {
        $this->lang_code = $lang_code;
        $this->lang->load('vars', $this->lang_code);
    }

    /**
     * Return string static block
     *
     * @param    string
     */

    function getStaticBlock($key)
    {
        $msg = $this->lang->line($key);

        return (!empty($msg)) ? $msg : "";
    }

    /**
     *     Send debug message (print_r) to browser
     *
     * @param    mixed
     * @param    string
     * @param    bool
     */

    function log_print($var, $msg = "", $return = false)
    {
        if (!is_string($msg))
            $msg = "";

        if (!is_scalar($var))
            $var = print_r($var, TRUE);

        $id = md5(uniqid(""));

        $s = '<div align="left" style="font:bold 11px tahoma;border:1px solid #000000;background-color:#EEEEEE;padding:5px;">' .
             '<a name="a_' . $id . '"></a>' .
             '<a href="#a_' . $id . '" style="display:block;float:left" onclick="var _=document.getElementById(\'dump_' . $id . '\').style;if(_.display==\'\'||_.display==\'block\'){_.display=\'none\';this.innerHTML=\'[+]\';}else{_.display=\'block\';this.innerHTML=\'[-]\';};return false">[-]</a>' .
             htmlspecialchars($msg) . '</div>' .
             '<div align="left" id="dump_' . $id . '" style="padding-bottom:10px">' .
             '<pre style="font:11px tahoma;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#FFFFFF;padding:5px;margin:0px;">' .
             htmlspecialchars($var) . '</pre></div>';

        if ($return) {
            return $s;
        }
        else
        {
            echo $s;
        }
    }


    function getList($params)
    {
        $fields = (array_key_exists('fields', $params)) ? $params['fields'] : "*";
        $where = (array_key_exists('where', $params)) ? $params['where'] : array();
        $page = (array_key_exists('page', $params)) ? (int)$params['page'] : 0;
        $limit = (array_key_exists('limit', $params)) ? (int)$params['limit'] : 0;
        $sort_field = (array_key_exists('sort_field', $params)) ? $params['sort_field'] : "";
        $sort_dir = (array_key_exists('sort_dir', $params)) ? $params['sort_dir'] : "ASC";

        $result = array(
            'items' => array(),
            'total' => 0
        );

        $page = ($page >= 0) ? $page : 0;
        $limit = ($limit >= 1 && $limit <= 1000) ? $limit : 1000;
        $limit_begin = $page * $limit;

        $this->db->select($fields)->from($this->model_table);
        $this->db->where($where);

        if (!empty($sort_field)) {
            $this->db->order_by($sort_field, $sort_dir);
        }

        $this->db->limit($limit, $limit_begin);

        $query = $this->db->get();

        if (!$this->db->_error_message()) {
            $result['total'] = $this->getTotal($params);
            $result['items'] = $query->result_array();
        }

        return $result;
    }

    function get($id, $fields = "*")
    {
        (int)$id;

        if($id){
            $this->db->select($fields);
            $this->db->from($this->model_table);
            $this->db->where(array('id' => $id));
            $this->db->limit(1, 0);

            $query = $this->db->get();

            if (!$this->db->_error_message() && $query->num_rows()) {
                return $query->row_array();
            }
        }

        return FALSE;
    }

    function getByField($field, $value, $fields = "*")
    {
        if (!empty($field) && !empty($value)) {
            $this->db->select($fields);
            $this->db->from($this->model_table);
            $this->db->where(array($field => $value));
            $this->db->limit(1, 0);

            $query = $this->db->get();

            if (!$this->db->_error_message() && $query->num_rows()) {
                return $query->row_array();
            }
        }

        return FALSE;
    }

    function getWhere($where, $fields = "*")
    {
        if (!empty($fields) && !empty($where)) {
            $this->db->select($fields);
            $this->db->from($this->model_table);
            $this->db->where($where);
            $this->db->limit(1, 0);

            $query = $this->db->get();

            if (!$this->db->_error_message() && $query->num_rows()) {
                return $query->row_array();
            }
        }

        return FALSE;
    }

    function getTotal($params)
    {
        $where = (array_key_exists('where', $params)) ? $params['where'] : array();

        $this->db->from($this->model_table);
        $this->db->where($where);

        $total = $this->db->count_all_results();

        return $total;
    }

    function delete($ids)
    {
        if (!empty($ids)) {
            $ids = (is_scalar($ids)) ? array($ids) : $ids;

            if (is_array($ids) && count($ids)) {
                if (!empty($this->upload_path) && !empty($this->upload_field_name)) {
                    foreach ($ids as $id)
                    {
                        foreach($this->upload_field_name as $ufn)
                        {
                            $this->delete_file($id,$ufn, FALSE);
                        }
                    }
                }

                $this->db->where_in('id', $ids)->delete($this->model_table);

                return TRUE;
            }
        }

        return FALSE;
    }

    function not_empty($array, $keys)
    {
        if (is_array($array) && count($array) && is_array($keys) &&
            count($keys) <= count($array)
        ) {
            foreach ($keys as $k)
            {
                if (!array_key_exists($k, $array) || empty($array[$k]))
                    return FALSE;
            }

            return TRUE;
        }

        return FALSE;
    }

    function isStatus($status)
    {
        if ($status == STATUS_ENABLED || $status == STATUS_DISABLED) {
            return TRUE;
        }

        return FALSE;
    }

    function isUrlUnique($url, $lang_id = 0, $id = 0)
    {
        if (!empty($url) && is_scalar($url)) {
            $where = array('url_alias' => mb_strtolower($url));

            $this->db->select('id')->from($this->model_table);
            $this->db->limit(1, 0);

            if (!empty($id))
                $where['id !='] = $id;

            if (!empty($lang_id))
                $where['lang_id'] = $lang_id;

            $this->db->where($where);
            $query = $this->db->get();

            if (!$this->db->_error_message($query) && !$query->num_rows()) {
                return TRUE;
            }
        }

        return FALSE;
    }

    function isParentExists($parent_id, $lang_id = NULL)
    {
        if(is_null($parent_id))
            return TRUE;

        if ($parent_id > 0) {
            $this->db->select('id')->from($this->model_table);
            $this->db->where(array('id' => $parent_id));
            $this->db->limit(1, 0);

            if (!empty($lang_id)) {
                $this->db->where('lang_id', $lang_id);
            }

            $query = $this->db->get();

            if (!$this->db->_error_message() && $query->num_rows()) {
                return TRUE;
            }
        }

        return FALSE;
    }

    function croupImage($img,$width,$height)
    {
        if(is_file($img) && is_writable($img))
        {
            $this->load->library('image_lib');

            $size = getimagesize($img);

            if($size['1'] < $size['0'])
            {
                $img_proportion = $size['1'] / $size['0'];
                $new_width = floor($height / $img_proportion);

                if($new_width < $width)
                {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $img;
                    $config['width'] = $width;
                    $config['height'] = $height;
                    $config['maintain_ratio'] = TRUE;
                    $config['master_dim'] = 'width';

                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                    $this->image_lib->clear();

                    $new_size = getimagesize($img);
                    $new_height = $new_size['1'];

                    $config_croup['image_library'] = 'gd2';
                    $config_croup['source_image'] = $img;
                    $config_croup['width'] = $width;
                    $config_croup['height'] = $height;
                    $config_croup['y_axis'] = floor(($new_height - $height) / 2);
                    $config_croup['maintain_ratio'] = FALSE;

                    $this->image_lib->initialize($config_croup);
                    $this->image_lib->crop();
                }
                else
                {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $img;
                    $config['width'] = $width;
                    $config['height'] = $height;
                    $config['maintain_ratio'] = TRUE;
                    $config['master_dim'] = 'height';

                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                    $this->image_lib->clear();

                    $config_croup['image_library'] = 'gd2';
                    $config_croup['source_image'] = $img;
                    $config_croup['width'] = $width;
                    $config_croup['height'] = $height;
                    $config_croup['x_axis'] = floor(($new_width - $width) / 2);
                    $config_croup['maintain_ratio'] = FALSE;

                    $this->image_lib->initialize($config_croup);
                    $this->image_lib->crop();
                }
            }

            if($size['0'] < $size['1'])
            {
                $img_proportion = $size['0'] / $size['1'];
                $new_height = $width / $img_proportion;

                if($new_height < $height)
                {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $img;
                    $config['width'] = $width;
                    $config['height'] = $height;
                    $config['maintain_ratio'] = TRUE;
                    $config['master_dim'] = 'height';

                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                    $this->image_lib->clear();

                    $new_size = getimagesize($img);
                    $new_width = $new_size['0'];

                    $config_croup['image_library'] = 'gd2';
                    $config_croup['source_image'] = $img;
                    $config_croup['width'] = $width;
                    $config_croup['height'] = $height;
                    $config_croup['x_axis'] = floor(($new_width - $width) / 2);
                    $config_croup['maintain_ratio'] = FALSE;

                    $this->image_lib->initialize($config_croup);
                    $this->image_lib->crop();
                }
                else
                {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $img;
                    $config['width'] = $width;
                    $config['height'] = $height;
                    $config['maintain_ratio'] = TRUE;
                    $config['master_dim'] = 'width';

                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                    $this->image_lib->clear();

                    $config_croup['image_library'] = 'gd2';
                    $config_croup['source_image'] = $img;
                    $config_croup['width'] = $width;
                    $config_croup['height'] = $height;
                    $config_croup['y_axis'] = floor(($new_height - $height) / 2);
                    $config_croup['maintain_ratio'] = FALSE;

                    $this->image_lib->initialize($config_croup);
                    $this->image_lib->crop();
                }
            }
        }
    }


    function isLangExists($lang_id)
    {
        if(!empty($lang_id))
        {
            $this->db->select('id')->from(LANGUAGES_DB_TABLE);
            $this->db->where('id',(int)$lang_id);
            $this->db->limit(1,0);

            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
	*	Get node childs recursive
	*
	* @param	Int
	* @param	String
	*/

	function getTreeBranch($id)
	{
		static $result;

		if($id > 0)
		{
			$this->db->select('*')->from($this->model_table)->where('id',$id)->limit(1,0);
			$query = $this->db->get();

			if(!$this->db->_error_message() && $query->num_rows())
			{
				$result[$id] = $query->row_array();

				$this->db->select('*')->from($this->model_table)->where($this->relative_field_name,$id)->order_by('sort_order','ASC');
				$query = $this->db->get();

				if(!$this->db->_error_message() && $query->num_rows())
				{
					$childs = $query->result_array();

					foreach($childs as $c)
					{
						$result[$c['id']] = $c;
						$this->getTreeBranch($c['id']);
					}
				}

				return $result;
			}
		}

		return array();
	}


	/**
	*	Get tree node recursive
	*
	* @param	Int
	* @param	String
	*/

	function getTree($language_id)
	{
		if($language_id > 0)
		{
			$this->db->select('*')->from($this->model_table)->where(array($this->relative_field_name." IS NULL" => "", 'lang_id' => $language_id),NULL,FALSE)->order_by('sort_order','ASC');
			$query = $this->db->get();

			if(!$this->db->_error_message() && $query->num_rows())
			{
				$result = $query->result_array();
				$tree = array();

				foreach($result as $r)
				{
					$branch =$this->getTreeBranch($r['id']);

					if(is_array($branch) && count($branch))
					{
						foreach($branch as $b)
						{
							$tree[$b['id']] = $b;
						}
					}
				}
				$this->log_file_print_r($tree);
				return $tree;
			}
		}
		return array();
	}


    function validateParent($id, $parent_id)
    {
        if (is_null($parent_id)) {
            return TRUE;
        }

        $branch = $this->getTreeBranch($id);

        if (is_array($branch) && count($branch)) {
            if (!array_key_exists($parent_id, $branch)) {
                return TRUE;
            }
        }
        else
        {
            return TRUE;
        }

        return FALSE;
    }

    /**
     *    Compaction positions sorting
     *
     * @param    Int
     * @param    String
     * @param    Mixed
     */

    private function packing_sort_order($lang_id = NULL, $relative_value = NULL)
    {
        $this->db->select("SUM(sort_order) AS sum_sort_order, COUNT(sort_order) AS count_items")->from($this->model_table);

        if (!empty($this->relative_field_name)) {
            if (is_null($relative_value))
                $this->db->where("{$this->relative_field_name} IS NULL");
            else
                $this->db->where($this->relative_field_name, $relative_value);
        }

        if (!empty($lang_id)) {
            $this->db->where('lang_id', $lang_id);
        }

        $this->db->order_by('sort_order', 'ASC');
        $query = $this->db->get();

        if (!$this->db->_error_message() && $query->num_rows()) {
            $fix_info = $query->row();

            $sum_sort_order = $fix_info->sum_sort_order;
            $count_items = $fix_info->count_items;
            $is_fix = 0;

            for ($i = 0; $i < $count_items; $i++)
            {
                $is_fix += $i;
            }

            if ($is_fix != $sum_sort_order || ($sum_sort_order == 0 && $count_items > 1)) {
                $this->db->select(array('id', 'sort_order'))->from($this->model_table);

                if (!empty($lang_id)) {
                    $this->db->where('lang_id', $lang_id);
                }

                if (!empty($this->relative_field_name)) {
                    if (is_null($relative_value))
                        $this->db->where("{$this->relative_field_name} IS NULL");
                    else
                        $this->db->where($this->relative_field_name, $relative_value);
                }

                $this->db->order_by('sort_order', 'ASC');
                $query = $this->db->get();

                if (!$this->db->_error_message() && $query->num_rows()) {
                    $list = $query->result_array();
                    $move_pos = 0;

                    for ($i = 0; $i < count($list); $i++)
                    {
                        $this->db->where('id', $list[$i]['id']);
                        $this->db->update($this->model_table, array('sort_order' => $move_pos));
                        $move_pos++;
                    }

                    return TRUE;
                }
            }
        }
        else
        {
            $params['desc'] = "Can not set sort order. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed', DEBUG_LEVEL_SQL, $params);
        }

        return FALSE;
    }

    /**
     *    Get last position sort order
     *
     * @param    Int
     * @param    String
     * @param    Mixed
     */

    function getLastSortOrder($lang_id = NULL, $relative_value = NULL)
    {
        $this->db->select("MAX(`sort_order`) AS max_sorting")->from($this->model_table);

        if (!empty($lang_id)) {
            $this->db->where('lang_id', $lang_id);
        }

        if (!empty($this->relative_field_name)) {
            if (is_null($relative_value))
                $this->db->where("{$this->relative_field_name} IS NULL");
            else
                $this->db->where($this->relative_field_name, $relative_value);
        }

        $query = $this->db->get();

        if (!$this->db->_error_message() && $query->num_rows() > 0) {
            $max_sort = $query->row();
            $max_sort_value = (!is_null($max_sort->max_sorting)) ? $max_sort->max_sorting : -1;

            return $max_sort_value;
        }

        return 0;
    }

    /**
     *    Set item sort order
     *
     * @param    Int
     * @param    Int
     * @param    String
     */

    function SetSortOrder($id, $position, $relative_value = NULL)
    {
        $id = ($id > 0) ? (int)$id : 0;
        $position = ($position >= 0) ? (int)$position : NULL;

        if ($id > 0 && !is_null($position) && $position >= 0) {
            $this->db->select('*')->from($this->model_table)->where('id', $id);

            if (!empty($relative_value) && !empty($this->relative_field_name)) {
                $this->db->select($this->relative_field_name);
            }

            $this->db->limit(1, 0);
            $query = $this->db->get();

            if (!$this->db->_error_message() && $query->num_rows() > 0) {
                $row = $query->row_array();

                $item_position = $row['sort_order'];

                if (!empty($relative_value) && !empty($this->relative_field_name))
                    $relative_value = $row[$this->relative_field_name];
                else
                    $relative_value = NULL;

                $lang_id = NULL;

                if (array_key_exists('lang_id', $row)) {
                    $lang_id = $row['lang_id'];
                }

                $is_fix = $this->packing_sort_order($lang_id, $relative_value);

                if ($is_fix) {
                    $params['desc'] = "Can not update field sort order. Structure is destroyed, you must seal";
                    $params['file'] = __FILE__;
                    $params['line'] = __LINE__;
                    $params['class'] = __CLASS__;
                    $params['method'] = __METHOD__;

                    $this->set_warning('warning_packing_sort_order', $params);

                    $this->db->select(array('sort_order'))->from($this->model_table)->where('id', $id);

                    if (!empty($this->relative_field_name)) {
                        if (is_null($relative_value))
                            $this->db->where("{$this->relative_field_name} IS NULL");
                        else
                            $this->db->where($this->relative_field_name, $relative_value);
                    }

                    $this->db->limit(1, 0);
                    $query = $this->db->get();

                    if (!$this->db->_error_message() && $query->num_rows() > 0) {
                        $item_position = $query->row();
                        $item_position = $item_position->sort_order;
                    }
                }

                if ($position != $item_position && $position <= $this->getLastSortOrder($lang_id, $relative_value)) {
                    $this->db->select(array('id', 'sort_order'))->from($this->model_table);

                    if ($item_position < $position) {
                        $begin_pos = $item_position;

                        $this->db->where(array('sort_order >=' => $item_position, 'sort_order <=' => $position));
                    }
                    else
                    {
                        $begin_pos = $position;

                        $this->db->where(array('sort_order >=' => $position, 'sort_order <=' => $item_position));
                    }

                    if (!empty($this->relative_field_name)) {
                        if (is_null($relative_value))
                            $this->db->where("{$this->relative_field_name} IS NULL");
                        else
                            $this->db->where($this->relative_field_name, $relative_value);
                    }

                    $this->db->order_by('sort_order', 'ASC');
                    $query = $this->db->get();

                    if (!$this->db->_error_message() && $query->num_rows() > 0) {
                        $move_pos = $begin_pos;
                        $last_move_pos = -1;
                        $list = $query->result_array();
                        $n = count($list);

                        if ($n) {
                            for ($i = 0; $i < $n; $i++)
                            {
                                if ($last_move_pos > -1) {
                                    $move_pos = $last_move_pos;
                                    $last_move_pos = -1;
                                }

                                if ($item_position > $position) {
                                    if ($list[$i]['id'] != $id) {
                                        $move_pos = $move_pos + 1;
                                    }
                                    else
                                    {
                                        $last_move_pos = $move_pos;
                                        $move_pos = $begin_pos;
                                    }
                                }
                                else
                                {
                                    if ($list[$i]['id'] != $id) {
                                        $move_pos = $list[$i]['sort_order'] - 1;
                                    }
                                    else
                                    {
                                        $last_move_pos = $move_pos;
                                        $move_pos = $position;
                                    }
                                }

                                $this->db->where('id', $list[$i]['id']);
                                $this->db->update($this->model_table, array('sort_order' => $move_pos));
                            }

                            $this->db->where('id', $id);
                            $this->db->update($this->model_table, array('sort_order' => $position));
                        }
                    }
                    else
                    {
                        $params['desc'] = "Can not set sort order. Run SQL query failed";
                        $params['file'] = __FILE__;
                        $params['line'] = __LINE__;
                        $params['class'] = __CLASS__;
                        $params['method'] = __METHOD__;

                        $this->set_error('error_run_sql_failed', DEBUG_LEVEL_SQL, $params);
                    }
                }
                else
                {
                    $params['desc'] = "Can not set sort order. Incorrect parameters";
                    $params['file'] = __FILE__;
                    $params['line'] = __LINE__;
                    $params['class'] = __CLASS__;
                    $params['method'] = __METHOD__;

                    $this->set_warning('warning_sort_position_bad', $params);
                }
            }
            else
            {
                $params['desc'] = "Can not set sort order. Run SQL query failed";
                $params['file'] = __FILE__;
                $params['line'] = __LINE__;
                $params['class'] = __CLASS__;
                $params['method'] = __METHOD__;

                $this->set_warning('error_run_sql_failed', DEBUG_LEVEL_SQL, $params);
            }
        }
        else
        {
            $params['desc'] = "Can not set sort order. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params', DEBUG_LEVEL_CUSTOM, $params);
        }
    }

    /**
     *    Send debug message (print_r) to browser
     *
     * @param    mixed
     * @param    string
     * @param    bool
     */

    function log_print_r($var, $msg = "", $return = false)
    {
        if ($return) {
            return $this->log_print(print_r($var, true), $msg, true);
        }
        else
        {
            $this->log_print(print_r($var, true), $msg);
        }
    }

    /**
     *    Send debug message (var_dump) to browser
     *
     * @param    mixed
     * @param    string
     * @param    int
     */

    function log_dump($var, $msg = "", $return = false)
    {
        if ($return) {
            return $this->log_print(var_export($var, true), $msg, true);
        }
        else
        {
            $this->log_print(var_export($var, true), $msg);
        }
    }

    /**
     *    Write debug message to file
     *
     * @param    mixed
     * @param    string
     * @param    boolean
     * @param    string
     * @param    int
     */

    function log_file($var, $msg = "", $is_developer = FALSE, $file_name = "")
    {
        $file_name = (!empty($file_name)) ? $file_name : $this->log_file;
        $log_state = (is_bool($is_developer) && $is_developer) ? PROJECT_IN_DEVELOPMENT
                : $this->config->item('project_state');

        if (($log_state == PROJECT_IN_DEVELOPMENT || $log_state == PROJECT_IN_DEBUGGING) && is_string($file_name) && !empty($file_name)) {
            $oldu = umask(0);
            $s = "";

            $path = $this->log_path . $file_name;

            clearstatcache(false, $path);

            if (file_exists($path) && is_file($path) && filesize($path) > 0) {
                $s .= PHP_EOL;
            }

            $f = fopen($path, 'a+');
            flock($f, LOCK_EX);

            if (function_exists('microtime')) {
                list($msec, $sec) = explode(" ", microtime());
                $dt = date("j.m.Y/H:i:s", $sec) . "/" . $msec;
            }
            else
            {
                $dt = date("j.m.Y/H:i:s") . "/0";
            }

            $s .= "==== " . $dt;

            if (is_string($msg) && $msg != "")
                $s .= " " . $msg;

            $s .= " ====" . PHP_EOL;
            $s .= $var;

            if ($f) {
                fwrite($f, $s);
                fflush($f);
                flock($f, LOCK_UN);
                fclose($f);
            }

            @chmod($path, 0666);
            umask($oldu);

            return true;
        }

        return false;
    }

    /**
     *    Write debug message to file using print_r output
     *
     * @param    mixed
     * @param    string
     * @param    boolean
     * @param    string
     * $param  Integer
     */

    function log_file_print_r($var, $msg = "", $is_developer = FALSE, $file_name = "", $mode = NULL)
    {
        return $this->log_file(print_r($var, true), $msg, $is_developer, $file_name, $mode);
    }

    /**
     * Write debug message to file using var_export output
     *
     * @param    mixed
     * @param    string
     * @param    boolean
     * @param    string
     * @param    Integer
     */

    function log_file_dump($var, $msg = "", $is_developer = FALSE, $file_name = "", $mode = NULL)
    {
        return $this->log_file(var_export($var, true), $msg, $is_developer, $file_name, $mode);
    }

    /**
     * Write debug message to file using var_dump output
     *
     * @param    mixed
     * @param    string
     * @param    boolean
     * @param    string
     * @param    Integer
     */

    function log_file_var_dump($var, $msg = "", $is_developer = FALSE, $file_name = "", $mode = NULL)
    {
        ob_start();
        var_dump($var);
        $s = ob_get_contents();
        ob_end_clean();

        return $this->log_file($s, $msg, $is_developer, $file_name, $mode);
    }

    /**
     * Send debug message to email
     *
     * @param    mixed
     * @param    string
     * @param    array
     * @param    boolean
     */

    function log_mail($var, $msg = "", $mail_params = array(), $is_developer = FALSE)
    {
        $this->load->helper('email');

        $log_state = (is_bool($is_developer) && $is_developer) ? PROJECT_IN_DEVELOPMENT
                : $this->config->item('project_state');

        if ($log_state == PROJECT_IN_DEVELOPMENT || $log_state == PROJECT_IN_DEBUGGING) {
            if (!count($mail_params)) {
                $mail_params['to'] = $this->config->item('developers mails');
            }

            if (is_array($mail_params) && array_key_exists('to', $mail_params)) {
                if (function_exists('microtime'))
                    list($tusec, $tsec) = explode(" ", microtime());
                else
                {
                    $tsec = time();
                    $tusec = 0;
                }

                $s = "==== " . date("j.m.Y/H:i:s", $tsec) . "/" . $tusec;

                if (is_string($msg) && $msg != "")
                    $s .= " " . $msg;

                $s .= " ====" . PHP_EOL;
                $s .= print_r($var, true);
                $headers = "From: log_mail@tendex.su" . "\r\n";

                $msg = (!empty($msg)) ? $msg : $this->subject;
                $mail_params['subject'] = (array_key_exists('subject', $mail_params) && !empty($mail_params['subject']))
                        ? $mail_params['subject'] : $msg;

                if (is_scalar($mail_params['to']) && valid_email($mail_params['to'])) {
                    mail($mail_params['to'], $mail_params['subject'], $s, $headers);
                    return TRUE;
                }

                if (is_array($mail_params['to'])) {
                    $send_to_mails = array();

                    foreach ($mail_params['to'] as $mp)
                    {
                        if (is_scalar($mp) && valid_email($mp))
                            $send_to_mails[] = $mp;
                    }

                    if (count($mp)) {
                        $send_to_mails_str = implode(', ', $send_to_mails);

                        mail($send_to_mails_str, $mail_params['subject'], $s, $headers);

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
     * @param    string
     * @param    int
     * @param    array
     */

    function set_error($key, $error_level = 0, $params = array(), $is_developer = false)
    {
        $is_set = $this->set_msg($key, MESSAGE_ERROR, $error_level, $params);

        if ($is_set) {
            $is_log_error = (is_bool($is_developer) && $is_developer) ? true : $this->config->item('log_error');

            if ($is_log_error) {
                $this->log_error($key, $error_level, $params);
            }

            if ($error_level == DEBUG_LEVEL_CRITICAL) {
                $url_redirect = $this->config->item('url_critical_error');
                $url_redirect = site_url($url_redirect);

                redirect($url_redirect);
            }
        }

        return $is_set;
    }

    /**
     * Set error with logging file and line of error-point
     *
     * @param    string
     * @param    int
     * @param    array
     */

    private function log_error($key, $error_level = 0, $params = array())
    {
        $this->log_msg($key, MESSAGE_ERROR, $error_level, $params);
    }

    /**
     * Delete error
     *
     * @param    string
     */

    function unset_error($key)
    {
        $this->unset_msg($key, MESSAGE_ERROR);
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
     * @param    string
     */

    function get_error($key)
    {
        return $this->get_msg($key, MESSAGE_ERROR);
    }


    /**
     * Return list of errors
     *
     */

    function get_errors()
    {
        return $this->get_msg_type(MESSAGE_ERROR);
    }


    /**
     *    Return last error value
     *
     */

    function get_last_error()
    {
        if ($this->is_errors()) {
            $errors = $this->get_msg_type(MESSAGE_ERROR);

            return end($errors);
        }

        return NULL;
    }

    /**
     *    Return first error value
     *
     */

    function get_first_error()
    {
        if ($this->is_errors()) {
            $errors = $this->get_msg_type(MESSAGE_ERROR);
            $error = reset($errors);

            return ($error) ? $error : NULL;
        }

        return NULL;
    }

    /**
     * Return true if error was set
     *
     * @param    string
     */

    function is_error($key)
    {
        return $this->is_msg($key, MESSAGE_ERROR);
    }


    /**
     * Return true if errors exists
     *
     */
    function is_errors()
    {
        return $this->is_msg_type(MESSAGE_ERROR);
    }


    /**
     * Return errors message string
     *
     * @param    string
     * @param    string
     */

    private function get_error_message($key, $lang)
    {
        return $this->get_msg_string($key, MESSAGE_ERROR, $lang);
    }


    /**
     * Set warning
     *
     * @param    string
     * @param    array
     * @param    boolean
     */

    function set_warning($key, $params = array(), $is_developer = false)
    {
        $is_set = $this->set_msg($key, MESSAGE_WARNING, 0, $params);

        if ($is_set) {
            $is_log_warning = (is_bool($is_developer) && $is_developer) ? true : $this->config->item('log_warning');

            if ($is_log_warning) {
                $this->log_warning($key, $params);
            }
        }

        return $is_set;
    }

    /**
     * Set warning with logging file and line of error-point
     *
     * @param    string
     * @param    array
     */

    private function log_warning($key, $params = array())
    {
        $this->log_msg($key, MESSAGE_WARNING, 0, $params);
    }

    /**
     * Delete warning
     *
     * @param    string
     */

    function unset_warning($key)
    {
        $this->unset_msg($key, MESSAGE_WARNING);
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
     * @param    string
     */

    function get_warning($key)
    {
        return $this->get_msg($key, MESSAGE_WARNING);
    }


    /**
     * Return list of warnings
     *
     */

    function get_warnings()
    {
        return $this->get_msg_type(MESSAGE_WARNING);
    }


    /**
     *    Return last warning value
     *
     */

    function get_last_warning()
    {
        if ($this->is_warnings()) {
            $warnings = $this->get_msg_type(MESSAGE_WARNING);

            return end($warnings);
        }

        return NULL;
    }

    /**
     *    Return first warning value
     *
     */

    function get_first_warning()
    {
        if ($this->is_warnings()) {
            $warnings = $this->get_msg_type(MESSAGE_WARNING);
            $warning = reset($warnings);

            return ($warning) ? $warning : NULL;
        }

        return NULL;
    }

    /**
     * Return true if $warning was set
     *
     * @param    string
     */

    function is_warning($key)
    {
        return $this->is_msg($key, MESSAGE_WARNING);
    }


    /**
     * Return true if $warnings exists
     *
     */

    function is_warnings()
    {
        return $this->is_msg_type(MESSAGE_WARNING);
    }


    /**
     * Return $warning message string
     *
     * @param    string
     * @param    string
     */

    private function get_warning_message($key, $lang)
    {
        return $this->get_msg_string($key, MESSAGE_WARNING, $lang);
    }


    /**
     * Set message
     *
     * @param    string
     * @param    int
     * @param    int
     * @param    array
     */

    private function set_msg($key, $msg_type, $msg_level = 0, $params = array())
    {
        if (is_scalar($key) && ($msg_type == MESSAGE_ERROR || $msg_type == MESSAGE_WARNING)) {
            $msg = array('level' => $msg_level);

            if (is_array($params)) {
                $msg['description'] = (array_key_exists('desc', $params)) ? $params['desc'] : "";
                $msg['file'] = (array_key_exists('file', $params)) ? $params['file'] : '';
                $msg['line'] = (array_key_exists('line', $params)) ? $params['line'] : 0;
                $msg['class'] = (array_key_exists('class', $params)) ? $params['class'] : '';
                $msg['method'] = (array_key_exists('method', $params)) ? $params['method'] : '';
                $msg['var_name'] = (array_key_exists('var_name', $params)) ? $params['var_name'] : '';
                $msg['var_value'] = (array_key_exists('var_value', $params)) ? $params['var_value'] : '';
                $msg['correct_var_value'] = (array_key_exists('correct_var_value', $params))
                        ? $params['correct_var_value'] : '';
                $msg['msg'] = (array_key_exists('msg', $params)) ? $params['msg'] : '';
                $msg['dynamic_variable'] = (array_key_exists('dynamic_variable', $params)) ? $params['dynamic_variable']
                        : '';
            }

            $this->messages[$msg_type][$key] = $msg;

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Delete message
     *
     * @param    string
     * @param    int
     */

    private function unset_msg($key, $msg_type)
    {
        if (is_scalar($key) && is_numeric($msg_type) && array_key_exists($msg_type, $this->messages)
            && array_key_exists($key, $this->messages[$msg_type])
        ) {
            unset($this->messages[$msg_type][$key]);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Delete all messages for specified type
     *
     * @param    int
     */

    private function unset_msg_type($msg_type)
    {
        if (is_numeric($msg_type) && array_key_exists($msg_type, $this->messages)) {
            $this->messages[$msg_type] = array();
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Return message value
     *
     * @param    string
     * @param    int
     */

    private function get_msg($key, $msg_type)
    {
        if (is_scalar($key) && is_scalar($msg_type) && array_key_exists($msg_type, $this->messages)
            && array_key_exists($key, $this->messages[$msg_type])
        ) {
            $msg = $this->messages[$msg_type][$key];
            $msg['msg'] = $this->get_msg_string($key, $msg_type);

            return $msg;
        }

        return NULL;
    }


    /**
     * Return list of messages of specified type
     *
     * @param    int
     */

    private function get_msg_type($msg_type)
    {
        if (array_key_exists($msg_type, $this->messages)) {
            $msgs = $this->messages[$msg_type];

            foreach ($msgs as $k => $ms)
            {
                if (!array_key_exists('msg', $msgs[$k]) || (array_key_exists('msg', $msgs[$k]) && empty($msgs[$k]['msg'])))
                    $msgs[$k]['msg'] = $this->get_msg_string($k, $msg_type);

                if (array_key_exists('dynamic_variable', $msgs[$k]))
                    $msgs[$k]['msg'] = sprintf($msgs[$k]['msg'], $msgs[$k]['dynamic_variable']);

            }
            return $msgs;
        }

        return array();
    }

    /**
     * Return true if message was set
     *
     * @param    string
     * @param    int
     */

    private function is_msg($key, $msg_type)
    {
        return (array_key_exists($msg_type, $this->messages) && array_key_exists($key, $this->messages[$msg_type]))
                ? true : false;
    }

    /**
     * Return true if messages on specified type exists
     *
     * @param    int
     */

    private function is_msg_type($msg_type)
    {
        return (array_key_exists($msg_type, $this->messages) && count($this->messages[$msg_type]) > 0) ? true : false;
    }


    /**
     * Return message string
     *
     * @param    string
     * @param    int
     * @param    string
     */

    function get_msg_string($key, $msg_type, $lang = "")
    {
        $msg_file_name = "";

        if (!is_string($lang) || $lang == "") {
            $lang = $this->lang_code;
        }

        if ($msg_type == MESSAGE_ERROR) {
            $msg_file_name = $this->error_log_file_prefix;
        }

        if ($msg_type == MESSAGE_WARNING) {
            $msg_file_name = $this->warning_log_file_prefix;
        }

        $lang = mb_strtolower($lang);

        $this->lang->load($msg_file_name, $lang);
        $msg = $this->lang->line($key);

        return (!empty($msg)) ? $msg : "Unresolved message code={$key}";
    }

    /**
     * Set message with logging file and line of error-point
     *
     * @param    string
     * @param    int
     * @param    int
     * @param    array
     */

    private function log_msg($key, $msg_type, $msg_level = 0, $params = array())
    {
        $msg = array();

        $msg['level'] = $msg_level;
        $msg['description'] = (array_key_exists('desc', $params)) ? $params['desc'] : "";
        $msg['file'] = (array_key_exists('file', $params)) ? $params['file'] : '';
        $msg['line'] = (array_key_exists('line', $params)) ? $params['line'] : 0;
        $msg['class'] = (array_key_exists('class', $params)) ? $params['class'] : '';
        $msg['method'] = (array_key_exists('method', $params)) ? $params['method'] : '';
        $msg['var_name'] = (array_key_exists('var_name', $params)) ? $params['var_name'] : '';
        $msg['var_value'] = (array_key_exists('var_value', $params)) ? $params['var_value'] : '';
        $msg['correct_var_value'] = (array_key_exists('correct_var_value', $params)) ? $params['correct_var_value']
                : '';

        if (!(array_key_exists('msg', $params)))
            $msg['msg'] = $this->get_msg_string($key, $msg_type);
        else
            $msg['msg'] = $params['msg'];

        $msg_str = "";

        foreach ($msg as $k => $v)
        {
            $v = ($v === false) ? 0 : $v;

            if (strlen((string)$v) > 0) {
                $msg_str .= $k . ": " . $v . PHP_EOL;
            }
        }

        if ($msg_type == MESSAGE_ERROR)
            $msg_type_name = "ERROR";

        if ($msg_type == MESSAGE_WARNING)
            $msg_type_name = "MESSAGE_WARNING";

        switch ($msg_level)
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

        $file_name = $this->Get_log_msg_file($msg_type, $msg_level);
        $this->log_file($msg_str, $msg_type_name . " " . $msg_level_name . " " . $key, true, $file_name, LOG_FILE_APPEND);
    }

    /**
     * Returns the file name for logging
     *
     * @param    Int
     * @param    Int
     */

    private function Get_log_msg_file($msg_type, $msg_level)
    {
        $file_name = "error.log";
        $msg_type = ($msg_type == MESSAGE_ERROR || $msg_type == MESSAGE_WARNING) ? $msg_type : MESSAGE_ERROR;
        $msg_level = ($msg_level >= DEBUG_LEVEL_CUSTOM && $msg_level <= DEBUG_LEVEL_CRITICAL) ? $msg_level
                : DEBUG_LEVEL_CUSTOM;

        switch ($msg_type)
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

        switch ($msg_level)
        {
            case DEBUG_LEVEL_CUSTOM :
                {
                $file_name .= '_custom.log';
                break;
                }

            case DEBUG_LEVEL_SQL :
                {
                $file_name .= '_sql.log';
                break;
                }

            case DEBUG_LEVEL_ROUTE :
                {
                $file_name .= '_route.log';
                break;
                }

            case DEBUG_LEVEL_CRITICAL :
                {
                $file_name .= '_critical.log';
                break;
                }
        }

        return $file_name;
    }

    /**
     * Upload file
     *
     * @param    String
     * @param    Array
     */

    function do_upload($field_name, $config)
    {
        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($field_name)) {
            return FALSE;
        }
        else
        {
            return $this->upload->data();
        }
    }

    function delete_file($item, $field, $update_table = TRUE, $path = NULL)
    {
        $index = array_search($field,$this->upload_field_name);

        if($index !== FALSE)
        {
            $path = (empty($path)) ? $this->upload_path[$index] : $path;

            if ((int)$item > 0 && !empty($field) && !empty($path)) {
                $item_data = $this->get($item);

                if (is_array($item_data) && count($item_data))
                {
                    if (array_key_exists($field, $item_data))
                    {
                        if (!empty($item_data[$field]))
                        {
                            if (is_writable($path) && is_file($path . "/" . $item_data[$field]))
                            {
                                unlink($path . "/" . $item_data[$field]);

                                if(is_file($path . "/middle_" . $item_data[$field]))
                                {
                                   unlink($path . "/middle_" . $item_data[$field]);
                                }

                                if(is_file($path . "/small_" . $item_data[$field]))
                                {
                                   unlink($path . "/small_" . $item_data[$field]);
                                }

                                if($update_table)
                                    $this->db->where('id', $item)->update($this->model_table, array($field => NULL));

                                return TRUE;
                            }
                            else
                            {
                                $params['desc'] = "Can not delete file. File not exist or no permission to write";
                                $params['file'] = __FILE__;
                                $params['line'] = __LINE__;
                                $params['class'] = __CLASS__;
                                $params['method'] = __METHOD__;

                                $this->set_error('error_file_not_exist', DEBUG_LEVEL_CUSTOM, $params);
                            }
                        }
                    }
                    else
                    {
                        $params['desc'] = "Can not delete file. Field name not exist";
                        $params['file'] = __FILE__;
                        $params['line'] = __LINE__;
                        $params['class'] = __CLASS__;
                        $params['method'] = __METHOD__;

                        $this->set_error('error_field_name_not_exist', DEBUG_LEVEL_CUSTOM, $params);
                    }
                }
                else
                {
                    $params['desc'] = "Can not delete file. Parent item does not exist";
                    $params['file'] = __FILE__;
                    $params['line'] = __LINE__;
                    $params['class'] = __CLASS__;
                    $params['method'] = __METHOD__;

                    $this->set_error('error_item_not_exist', DEBUG_LEVEL_CUSTOM, $params);
                }
            }
            else
            {
                $params['desc'] = "Can not delete file. Incorrect parameters";
                $params['file'] = __FILE__;
                $params['line'] = __LINE__;
                $params['class'] = __CLASS__;
                $params['method'] = __METHOD__;

                $this->set_error('error_invalid_params', DEBUG_LEVEL_CUSTOM, $params);
            }
        }
        else
        {
            $params['desc'] = "Can not delete file. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params', DEBUG_LEVEL_CUSTOM, $params);
        }

        return FALSE;
    }

}

?>
