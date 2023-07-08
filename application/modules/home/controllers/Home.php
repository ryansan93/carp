<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Public_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->add_external_js(array(
            "assets/chart/chart.js",
            "assets/home/js/home.js",
        ));
        $this->add_external_css(array(
        	"assets/home/css/home.css",
        ));

		$data = $this->includes;

		$data['title_menu'] = 'Dashboard';

		$content = null;
		$data['view'] = $this->load->view('home/dashboard', $content, true);

		$this->load->view($this->template, $data);
	}

	public function getStage()
    {
    	$data = array(
    		'Closed',
			'Open',
			'Re-Open',
			'Responded',
			'Verified',
			'Voided'
    	);

    	return $data;
    }

    public function getStatus()
    {
    	$data = array(
    		'Canceled',
			'Closed',
			'Open'
    	);

    	return $data;
    }

	public function getDataStatus()
	{
		$status = $this->getStatus();

		$list_status = [];

		$idx = 0;
		foreach ($status as $key => $value) {
			$m_conf = new \Model\Storage\Conf();
			$sql = "
				select 
					count(c.status) as jumlah
				from
					carp c
				where
					c.status = '".$value."'
				group by
					c.status
			";
			$d_conf = $m_conf->hydrateRaw( $sql );

			$list_status[$idx]['nama'] = $value;
			if ( $d_conf->count() > 0 ) {
				$d_conf = $d_conf->toArray()[0];
				$list_status[$idx]['total'] = $d_conf['jumlah'];
			} else {
				$list_status[$idx]['total'] = 0;
			}

			$idx++;
		}

		if ( !empty($list_status) ) {
			$this->result['status'] = 1;
			$this->result['content'] = array(
				'list_status' => $list_status
			);
		} else {
			$this->result['status'] = 0;
		}

		display_json( $this->result );
	}

	public function getDataStage()
	{
		$stage = $this->getStage();

		$color = array(
			'#ffcdb8',
			'#fff8b8',
			'#c5ffb8',
			'#b8ffeb',
			'#b8c3ff',
			'#f0b8ff'
		);
		$list_stage = [];

		$idx = 0;
		foreach ($stage as $key => $value) {
			$m_conf = new \Model\Storage\Conf();
			$sql = "
				select 
					count(c.stage) as jumlah
				from
					carp c
				where
					c.stage = '".$value."'
				group by
					c.stage
			";
			$d_conf = $m_conf->hydrateRaw( $sql );

			$list_stage[$idx]['nama'] = $value;
			$list_stage[$idx]['color'] = $color[ $idx ];
			if ( $d_conf->count() > 0 ) {
				$d_conf = $d_conf->toArray()[0];
				$list_stage[$idx]['total'] = $d_conf['jumlah'];
			} else {
				$list_stage[$idx]['total'] = 0;
			}

			$idx++;
		}

		if ( !empty($list_stage) ) {
			$content['data'] = $list_stage;
			$html = $this->load->view('home/stageChart', $content, true);

			$this->result['status'] = 1;
			$this->result['content'] = array(
				'list_stage' => $html
			);
		} else {
			$this->result['status'] = 0;
		}

		display_json( $this->result );
	}
}