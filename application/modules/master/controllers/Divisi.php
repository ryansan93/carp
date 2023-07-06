<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Divisi extends Public_Controller
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
			'assets/master/divisi/js/divisi.js'
		));
		$this->add_external_css(array(
			'assets/master/divisi/css/divisi.css'
		));

		$data = $this->includes;

		$data['title_menu'] = 'Master Divisi';

		$content['riwayat'] = $this->riwayat();
		$content['add_form'] = $this->addForm();
		$data['view'] = $this->load->view('master/divisi/index', $content, true);

		$this->load->view($this->template, $data);
	}

	public function getLists()
	{
		$m_conf = new \Model\Storage\Conf();
		$sql = "
			select * from divisi
		";
		$d_conf = $m_conf->hydrateRaw( $sql );

		$data = null;
		if ( $d_conf->count() > 0 ) {
			$data = $d_conf->toArray();
		}

		$content['data'] = $data;
		$html = $this->load->view('master/divisi/list', $content, true);

		echo $html;
	}

	public function loadForm()
    {
        $id = $this->input->get('id');
        $resubmit = $this->input->get('resubmit');

        $html = null;
        if ( !empty($id) && empty($resubmit) ) {
            $html = $this->viewForm($id);
        } else {
            $html = $this->addForm();
        }

        echo $html;
    }

	public function riwayat()
	{
		$content = null;
		$html = $this->load->view('master/divisi/riwayat', $content, true);

		return $html;
	}

	public function addForm()
	{
		$content = null;
		$html = $this->load->view('master/divisi/addForm', $content, true);

		return $html;
	}

	public function viewForm($id)
	{
		$m_conf = new \Model\Storage\Conf();
		$sql = "
			select * from divisi where kode = '".$id."'
		";
		$d_conf = $m_conf->hydrateRaw( $sql );

		$data = null;
		if ( $d_conf->count() > 0 ) {
			$data = $d_conf->toArray()[0];
		}

		$content['data'] = $data;
		$html = $this->load->view('master/divisi/viewForm', $content, true);

		return $html;
	}

	public function save()
	{
		$params = $this->input->post('params');

		try {
			$m_divisi = new \Model\Storage\Divisi_model();

			$kode = $m_divisi->getNextId();

			$m_divisi->kode = $kode;
			$m_divisi->nama = $params['nama'];
			$m_divisi->save();

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
			$m_divisi = new \Model\Storage\Divisi_model();

			$kode = $params['kode'];

			$m_divisi->where('kode', $kode)->update(
				array(
					'nama' => $params['nama']
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
			$m_divisi = new \Model\Storage\Divisi_model();

			$kode = $params['kode'];

			$m_divisi->where('kode', $kode)->delete();

			$this->result['status'] = 1;
			$this->result['message'] = 'Data berhasil di hapus.';
		} catch (Exception $e) {
			$this->result['message'] = $e->getMessage();
		}

		display_json( $this->result );
	}
}