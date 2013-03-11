<?php defined('BASEPATH') or die('No direct script access allowed');

class Towns extends MY_controller
{
    var $log_file = "towns.log";
    var $menu_items = array('city','infrastructure','map','building');

    function horse()
    {
        $this->load->model('town');
        $this->load->model('news');

        $id = 2;
        $section = $this->uri->segment(3);
        $place_id = $this->uri->segment(4);
        
        $town = $this->town->get($id);

        if(!in_array($section,$this->menu_items))
        {
            show_404();
        }

        $this->page_data = $town;
        $this->page_data['base_url'] = $this->config->item('base_url');
        $this->page_data['links'] = array();
        $this->page_data['news'] = $this->news->getNews();

        if(!empty($town))
        {
            $this->page_data['content_name'] = $this->page_data['name'];

            if($section == 'building')
            {
                if(empty($place_id))
                {
                    show_404();
                }

                $this->load->model('building_places');
                $this->load->model('building');

                $place_data = $this->building_places->get($place_id);

                if(empty($place_data))
                {
                    show_404();
                }

                $building = $this->building->get($place_data['building_id']);

                $this->page_data['seo_title'] = $building['seo_title'];
                $this->page_data['seo_description'] = $building['seo_description'];
                $this->page_data['seo_keywords'] = $building['seo_keywords'];
                $this->page_data['description'] = $building['content'];
                $this->page_data['content_name'] = $building['name'];
            }

            $this->load->model('building_places');
            $building_places = $this->building_places->getBuildingPlaces($id,TRUE);

            if(is_array($building_places))
            {
				$this->load->model('building_place_images');
                $links = array();

                foreach($building_places as $i=>$bpi)
                {
                    if(!empty($bpi['img']))
                    {
                        $building_places[$i]['img'] =  $this->page_data['base_url']."user_files/building/".$bpi['img'];
                    }

                    $links[] = array(
                        'href' => $this->page_data['base_url']."towns/horse/building/".$bpi['id']."/description",
                        'name' => $bpi['name'],
                        'id' => $bpi['id']
                    );

                    $building_places[$i]['params'] = unserialize($bpi['params']);

                    #$building_place_images = $this->building_images->getImages($bpi['building_id'],4);
                    $building_place_images = $this->building_place_images->getImages($bpi['id'],4);
                    $building_places[$i]['images'] = $building_place_images;
                }
            }

            $this->page_data['links'] = $links;
            $this->page_data['building_places'] = json_encode($building_places);
            $this->load->view('town_horse',$this->page_data);
        }
        else
        {
            show_404();
        }
    }

    function get_building_data()
    {
        $place_id = $this->input->post('alias',TRUE);
        $section = $this->input->post('section',TRUE);
        
        $result = array('status' => 1, 'data' => array());

        if(empty($place_id))
        {
            echo json_encode($result);
            exit;
        }

        $this->load->model('building');
        $this->load->model('building_images');
        $this->load->model('building_places');
        $this->load->model('building_plans');

        $this->page_data['section'] = (int)$section;

        $place_data = $this->building_places->get($place_id);

        if($place_data)
        {
            $this->page_data['building'] = $this->building->get($place_data['building_id']);

            $building_place_images = $this->building_places->get_building_place_images($place_id);
            $this->page_data['building']['place_images'] = $building_place_images;

            $params['fields'] = array('id','name','description','sort_order','img');
            $params['sort_field'] = 'sort_order';
            $params['sort_dir'] = 'asc';
            $params['where'] = array('building_id' => $this->page_data['building']['id']);

            $building_plans = $this->building_plans->getList($params);
            $this->page_data['building']['plans'] = $building_plans['items'];

            $town_url_alias = ($this->page_data['building']['town_id'] == 2) ? 'horse' : 'alpiyka';

            $this->page_data['url'] =  $this->config->item('base_url')."towns/".$town_url_alias."/building/".$place_id;
            $result['data'] = $this->load->view('open_building',$this->page_data,TRUE);
        }

        echo json_encode($result);
    }

    function alpiyka()
    {
        $this->load->model('town');
        $this->load->model('news');

        $id = 1;
        $section = $this->uri->segment(3);
        $place_id = $this->uri->segment(4);

        $town = $this->town->get($id);

        if(!in_array($section,$this->menu_items))
        {
            show_404();
        }

        $this->page_data = $town;
        $this->page_data['base_url'] = $this->config->item('base_url');
        $this->page_data['links'] = array();
        $this->page_data['news'] = $this->news->getNews();

        if(!empty($town))
        {
            $this->page_data['content_name'] = $this->page_data['name'];

            if($section == 'building')
            {
                if(empty($place_id))
                {
                    show_404();
                }

                $this->load->model('building_places');
                $this->load->model('building');

                $place_data = $this->building_places->get($place_id);

                if(empty($place_data))
                {
                    show_404();
                }

                $building = $this->building->get($place_data['building_id']);
                
                $this->page_data['seo_title'] = $building['seo_title'];
                $this->page_data['seo_description'] = $building['seo_description'];
                $this->page_data['seo_keywords'] = $building['seo_keywords'];
                $this->page_data['description'] = $building['content'];
                $this->page_data['content_name'] = $building['name'];
            }

            $this->load->model('building_places');
            $building_places = $this->building_places->getBuildingPlaces($id,TRUE);

            if(is_array($building_places))
            {
                $this->load->model('building_images');
								$this->load->model('building_place_images');
                $links = array();

                foreach($building_places as $i=>$bpi)
                {
                    if(!empty($bpi['img']))
                    {
                        $building_places[$i]['img'] =  $this->page_data['base_url']."user_files/building/".$bpi['img'];
                    }

                    $links[] = array(
                        'href' => $this->page_data['base_url']."towns/alpiyka/building/".$bpi['id']."/description",
                        'name' => $bpi['name'],
                        'id' => $bpi['id']
                    );

                    $building_places[$i]['params'] = unserialize($bpi['params']);

                    $building_place_images = $this->building_place_images->getImages($bpi['id'],4);
                    $building_places[$i]['images'] = $building_place_images;
                }
            }

            $this->page_data['links'] = $links;
            $this->page_data['building_places'] = json_encode($building_places);
            $this->load->view('town_alpiyka',$this->page_data);
        }
        else
        {
            show_404();
        }
    }
}



 
