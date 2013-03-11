<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Adddefaultuser extends CI_Migration {
	
	public function up()
	{	
		if($this->db->table_exists(USERS_DB_TABLE))
		{
            $insert['name'] = "Default";
            $insert['email'] = "nan@to.net.ua";
            $insert['password'] = md5('nabalkoneqwedsa');
            $insert['enabled'] = 1;
            
            $this->db->insert(USERS_DB_TABLE,$insert);
		}
	}

	public function down()
	{
	}
}
