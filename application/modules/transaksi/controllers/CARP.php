<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CARP extends Public_Controller
{
	private $url;
	function __construct()
	{
		parent::__construct();
		$this->url = $this->current_base_uri;
	}

	public function index()
	{
		$url = $this->current_uri;

		$this->add_external_js(array(
			"assets/select2/js/select2.min.js",
			"assets/transaksi/carp/js/carp.js"
		));
		$this->add_external_css(array(
			"assets/select2/css/select2.min.css",
			"assets/transaksi/carp/css/carp.css"
		));

		$data = $this->includes;

		$data['title_menu'] = 'Master CARP';

		$content['riwayat'] = $this->riwayat();
		$content['add_form'] = $this->addForm();
		$data['view'] = $this->load->view('transaksi/carp/index', $content, true);

		$this->load->view($this->template, $data);
	}

	public function getLists()
	{
		$m_conf = new \Model\Storage\Conf();
		$sql = "
			select 
				c.created_date,
				c.kode,
				c.kategori,
				c.due_date,
				c.status_date,
				c.stage,
				c.status,
				i_user.nama as inama_user,
				r_user.nama as rnama_user,
				i_divisi.nama as inama_divisi,
				r_divisi.nama as rnama_divisi,
				i_branch.nama as inama_branch,
				r_branch.nama as rnama_branch,
				v_user.nama as vnama
			from carp c
			left join
				user i_user
				on
					i_user.kode = c.initiator_user_kode
			left join
				user r_user
				on
					r_user.kode = c.recipient_user_kode
			left join
				divisi i_divisi
				on
					i_divisi.kode = c.initiator_divisi_kode
			left join
				divisi r_divisi
				on
					r_divisi.kode = c.recipient_divisi_kode
			left join
				branch i_branch
				on
					i_branch.kode = c.initiator_branch_kode
			left join
				branch r_branch
				on
					r_branch.kode = c.recipient_branch_kode
			left join
				user v_user
				on
					v_user.kode = c.verified_user_kode
		";
		$d_conf = $m_conf->hydrateRaw( $sql );

		$data = null;
		if ( $d_conf->count() > 0 ) {
			$data = $d_conf->toArray();
		}

		$content['data'] = $data;
		$html = $this->load->view('transaksi/carp/list', $content, true);

		echo $html;
	}

	public function loadForm()
    {
        $id = $this->input->get('id');
        $resubmit = $this->input->get('resubmit');

        $html = null;
        if ( !empty($id) && !empty($resubmit) ) {
        	$html = $this->editForm($id);
        } else if ( !empty($id) && empty($resubmit) ) {
            $html = $this->viewForm($id);
        } else {
            $html = $this->addForm();
        }

        echo $html;
    }

    public function getUser()
    {
    	$m_conf = new \Model\Storage\Conf();
		$sql = "
			select * from user order by nama asc
		";
		$d_conf = $m_conf->hydrateRaw( $sql );

		$data = null;
		if ( $d_conf->count() > 0 ) {
			$data = $d_conf->toArray();
		}

		return $data;
    }

    public function getDivisi()
    {
    	$m_conf = new \Model\Storage\Conf();
		$sql = "
			select * from divisi order by nama asc
		";
		$d_conf = $m_conf->hydrateRaw( $sql );

		$data = null;
		if ( $d_conf->count() > 0 ) {
			$data = $d_conf->toArray();
		}

		return $data;
    }

    public function getBranch()
    {
    	$m_conf = new \Model\Storage\Conf();
		$sql = "
			select * from branch order by nama asc
		";
		$d_conf = $m_conf->hydrateRaw( $sql );

		$data = null;
		if ( $d_conf->count() > 0 ) {
			$data = $d_conf->toArray();
		}

		return $data;
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

	public function riwayat()
	{
		$content = null;
		$html = $this->load->view('transaksi/carp/riwayat', $content, true);

		return $html;
	}

	public function addForm()
	{
		$content['user'] = $this->getUser();
		$content['divisi'] = $this->getDivisi();
		$content['branch'] = $this->getBranch();
		$content['stage'] = $this->getStage();
		$content['status'] = $this->getStatus();
		$html = $this->load->view('transaksi/carp/addForm', $content, true);

		return $html;
	}

	public function viewForm($id)
	{
		$m_conf = new \Model\Storage\Conf();
		$sql = "
			select 
				c.created_date,
				c.kode,
				c.kategori,
				c.due_date,
				c.status_date,
				c.stage,
				c.status,
				i_user.nama as inama_user,
				r_user.nama as rnama_user,
				i_divisi.nama as inama_divisi,
				r_divisi.nama as rnama_divisi,
				i_branch.nama as inama_branch,
				r_branch.nama as rnama_branch,
				v_user.nama as vnama
			from carp c
			left join
				user i_user
				on
					i_user.kode = c.initiator_user_kode
			left join
				user r_user
				on
					r_user.kode = c.recipient_user_kode
			left join
				divisi i_divisi
				on
					i_divisi.kode = c.initiator_divisi_kode
			left join
				divisi r_divisi
				on
					r_divisi.kode = c.recipient_divisi_kode
			left join
				branch i_branch
				on
					i_branch.kode = c.initiator_branch_kode
			left join
				branch r_branch
				on
					r_branch.kode = c.recipient_branch_kode
			left join
				user v_user
				on
					v_user.kode = c.verified_user_kode 
			where 
				c.kode = '".$id."'
		";
		$d_conf = $m_conf->hydrateRaw( $sql );

		$data = null;
		if ( $d_conf->count() > 0 ) {
			$data = $d_conf->toArray()[0];
		}

		$content['data'] = $data;
		$html = $this->load->view('transaksi/carp/viewForm', $content, true);

		return $html;
	}

	public function editForm($id)
	{
		$m_conf = new \Model\Storage\Conf();
		$sql = "
			select c.* from carp c
			where 
				c.kode = '".$id."'
		";
		$d_conf = $m_conf->hydrateRaw( $sql );

		$data = null;
		if ( $d_conf->count() > 0 ) {
			$data = $d_conf->toArray()[0];
		}

		$content['data'] = $data;
		$content['user'] = $this->getUser();
		$content['divisi'] = $this->getDivisi();
		$content['branch'] = $this->getBranch();
		$content['stage'] = $this->getStage();
		$content['status'] = $this->getStatus();
		$html = $this->load->view('transaksi/carp/editForm', $content, true);

		return $html;
	}

	public function save()
	{
		$params = $this->input->post('params');

		try {
			$m_carp = new \Model\Storage\Carp_model();
			$now = $m_carp->getDate();

			$dt1 = strtotime($now['tanggal']);
			$due_date = date('Y-m-d', strtotime("+1 month", $dt1));

			$kode = $m_carp->getNextKode();

			$m_carp->kode = $kode;
			$m_carp->created_date = $now['tanggal'];
			$m_carp->kategori = $params['kategori'];
			$m_carp->initiator_user_kode = $params['initiator_user'];
			$m_carp->initiator_divisi_kode = $params['initiator_divisi'];
			$m_carp->initiator_branch_kode = $params['initiator_branch'];
			$m_carp->recipient_user_kode = $params['recipient_user'];
			$m_carp->recipient_divisi_kode = $params['recipient_divisi'];
			$m_carp->recipient_branch_kode = $params['recipient_branch'];
			$m_carp->verified_user_kode = isset($params['verified_by']) ? $params['verified_by'] : null;
			$m_carp->due_date = $due_date;
			$m_carp->effectiveness = null;
			$m_carp->status_date = $now['tanggal'];
			$m_carp->stage = $params['stage'];
			$m_carp->status = $params['status'];
			$m_carp->save();

			$this->result['status'] = 1;
			$this->result['message'] = 'Data berhasil di simpan.';
		} catch (Exception $e) {
			$this->result['message'] = $e->getMessage();
		}

		display_json( $this->result );
	}

	public function edit()
	{
		$params = $this->input->post('params');
		
		try {
			$m_carp = new \Model\Storage\Carp_model();
			$now = $m_carp->getDate();

			$kode = $params['kode'];

			$m_carp->where('kode', $kode)->update(
				array(
					'kategori' => $params['kategori'],
					'initiator_user_kode' => $params['initiator_user'],
					'initiator_divisi_kode' => $params['initiator_divisi'],
					'initiator_branch_kode' => $params['initiator_branch'],
					'recipient_user_kode' => $params['recipient_user'],
					'recipient_divisi_kode' => $params['recipient_divisi'],
					'recipient_branch_kode' => $params['recipient_branch'],
					'verified_user_kode' => isset($params['verified_by']) ? $params['verified_by'] : null,
					'status_date' => $now['tanggal'],
					'stage' => $params['stage'],
					'status' => $params['status'],
				)
			);

			$this->result['status'] = 1;
			$this->result['message'] = 'Data berhasil di edit.';
		} catch (Exception $e) {
			$this->result['message'] = $e->getMessage();
		}

		display_json( $this->result );
	}

	public function delete()
	{
		$params = $this->input->post('params');
		
		try {
			$m_carp = new \Model\Storage\Carp_model();

			$kode = $params['kode'];

			$m_carp->where('kode', $kode)->delete();

			$this->result['status'] = 1;
			$this->result['message'] = 'Data berhasil di hapus.';
		} catch (Exception $e) {
			$this->result['message'] = $e->getMessage();
		}

		display_json( $this->result );
	}

	public function tes()
	{
		$m_carp = new \Model\Storage\Carp_model();

		$kode = $m_carp->getNextKode();

		cetak_r( $kode );
	}
}