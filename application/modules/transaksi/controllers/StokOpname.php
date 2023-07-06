<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StokOpname extends Public_Controller {

    private $pathView = 'transaksi/stok_opname/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    public function index($segment=0)
    {
        if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(array(
                "assets/select2/js/select2.min.js",
                "assets/transaksi/stok_opname/js/stok-opname.js"
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/stok_opname/css/stok-opname.css"
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Stok Opname';

            $r_content['gudang'] = $this->getGudang();
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', $r_content, TRUE);

            // Load Indexx
            $data['title_menu'] = 'Stok Opname';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getGudang()
    {
        $m_gudang = new \Model\Storage\Gudang_model();
        $d_gudang = $m_gudang->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_gudang->count() > 0 ) {
            $data = $d_gudang->toArray();
        }

        return $data;
    }

    public function getGroupItem()
    {
        $m_gi = new \Model\Storage\GroupItem_model();
        $d_gi = $m_gi->orderBy('nama', 'asc')->get();

        $data_gi = null;
        if ( $d_gi->count() > 0 ) {
            $data_gi = $d_gi->toArray();
        }

        return $data_gi;
    }

    public function getItem()
    {
        $m_item = new \Model\Storage\Item_model();
        $d_item = $m_item->with(['satuan'])->orderBy('nama', 'asc')->get();

        $data_item = null;
        if ( $d_item->count() > 0 ) {
            $data_item = $d_item->toArray();
        }

        return $data_item;
    }

    public function loadForm()
    {
        $id = $this->input->get('id');
        $resubmit = $this->input->get('resubmit');

        $html = null;
        if ( !empty($id) && empty($resubmit) ) {
            $html = $this->viewForm($id);
        } else if ( !empty($id) && !empty($resubmit) ) {
            $html = $this->editForm($id);
        } else {
            $html = $this->addForm();
        }

        echo $html;
    }

    public function getListItem()
    {
        $params = $this->input->get('params');

        $tanggal = $params['tanggal'];
        $gudang_kode = $params['gudang_kode'];
        $group_item = $params['group_item'];

        $sql_group_item = null;
        if ( !empty($group_item) ) {
            $sql_group_item = "where gi.kode in ('".implode("', '", $group_item)."')";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                i.kode,
                i.nama,
                gi.nama as nama_group,
                s.harga,
                s.jumlah
            from item i
            right join
                group_item gi
                on
                    i.group_kode = gi.kode
            left join
                (
                    select s.gudang_kode, s.item_kode, sum(s.jumlah) as jumlah, sh.harga from stok s
                    right join
                        (
                            select top 1 * from stok_tanggal where gudang_kode = '".$gudang_kode."' and tanggal <= GETDATE() order by tanggal desc
                        ) st
                        on
                            s.id_header = st.id
                    left join
                        stok_harga sh
                        on
                            sh.id_header = st.id and
                            sh.item_kode = s.item_kode
                    group by
                        s.gudang_kode, 
                        s.item_kode,
                        sh.harga
                ) s
                on
                    i.kode = s.item_kode
            ".$sql_group_item."
            order by
                i.nama asc
        ";
        $d_item = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_item->count() > 0 ) {
            $d_item = $d_item->toArray();


            $idx = 0;
            foreach ($d_item as $k_item => $v_item) {
                $m_satuan = new \Model\Storage\ItemSatuan_model();
                $d_satuan = $m_satuan->where('item_kode', $v_item['kode'])->get();

                $data[ $idx ] = $v_item;
                $data[ $idx ]['satuan'] = ($d_satuan->count() > 0) ? $d_satuan->toArray() : null;

                // $key = $v_item['kode'];

                // $data[ $key ] = $v_item;
                // $data[ $key ]['satuan'][] = array(
                //     'satuan' => $v_item['satuan'],
                //     'pengali' => $v_item['pengali']
                // );

                $idx++;
            }
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'listItem', $content, true);

        echo $html;
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $gudang_kode = $params['gudang_kode'];

        $m_so = new \Model\Storage\StokOpname_model();
        $sql = "
            select 
                so.id, 
                so.kode_stok_opname, 
                so.tanggal, 
                g.nama 
            from stok_opname so
            right join
                gudang g
                on
                    so.gudang_kode = g.kode_gudang
            where
                so.tanggal between '".$start_date."' and '".$end_date."' and
                g.kode_gudang in ('".implode("', '", $gudang_kode)."')
            order by
                so.tanggal desc,
                g.nama asc
        ";
        $d_so = $m_so->hydrateRaw( $sql );

        $data = null;
        if ( $d_so->count() > 0 ) {
            $data = $d_so->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function addForm()
    {
        $content['group_item'] = $this->getGroupItem();
        // $content['item'] = $this->getItem();
        $content['gudang'] = $this->getGudang();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function viewForm($id)
    {
        $m_so = new \Model\Storage\StokOpname_model();
        $d_so = $m_so->where('id', $id)->with(['detail', 'gudang'])->first();

        $data = null;
        if ( $d_so ) {
            $data = $d_so->toArray();
        }

        $content['akses'] = $this->hakAkses;
        // $content['item'] = $this->getItem();
        // $content['gudang'] = $this->getGudang();
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_so = new \Model\Storage\StokOpname_model();

            $kode_stok_opname = $m_so->getNextIdRibuan();

            $m_so->tanggal = $params['tanggal'];
            $m_so->gudang_kode = $params['gudang_kode'];
            $m_so->kode_stok_opname = $kode_stok_opname;
            $m_so->save();

            foreach ($params['list_item'] as $k_li => $v_li) {
                $m_sod = new \Model\Storage\StokOpnameDet_model();
                $m_sod->id_header = $m_so->id;
                $m_sod->item_kode = $v_li['item_kode'];
                $m_sod->satuan = $v_li['satuan'];
                $m_sod->pengali = $v_li['pengali'];
                $m_sod->jumlah = $v_li['jumlah'];
                $m_sod->harga = $v_li['harga'];
                $m_sod->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_so, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['content'] = array('kode' => $kode_stok_opname);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function hitungStokOpname()
    {
        $params = $this->input->post('params');

        try {
            $kode = $params['kode'];

            $m_conf = new \Model\Storage\Conf();

            $tgl_transaksi = null;
            $gudang = null;
            $barang = null;

            $sql_tgl_dan_gudang = "
                select so.* from stok_opname so
                where
                    so.kode_stok_opname = '".$kode."'
            ";
            $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
            if ( $d_tgl_dan_gudang->count() > 0 ) {
                $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
                $tgl_transaksi = $d_tgl_dan_gudang['tanggal'];
                $gudang = $d_tgl_dan_gudang['gudang_kode'];
            }

            $sql_barang = "
                select so.tanggal, sod.item_kode from stok_opname_det sod
                right join
                    stok_opname so
                    on
                        so.id = sod.id_header
                where
                    so.kode_stok_opname = '".$kode."' and
                    sod.jumlah > 0
                group by
                    so.tanggal,
                    sod.item_kode
            ";
            $d_barang = $m_conf->hydrateRaw( $sql_barang );
            if ( $d_barang->count() > 0 ) {
                $d_barang = $d_barang->toArray();

                foreach ($d_barang as $key => $value) {
                    $barang[] = $value['item_kode'];
                }
            }

            $sql = "EXEC sp_hitung_stok_by_barang @barang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($barang))))."', @tgl_transaksi = '".$tgl_transaksi."', @gudang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($gudang))))."'";

            $d_conf = $m_conf->hydrateRaw($sql);

            // $conf = new \Model\Storage\Conf();
            // $sql = "EXEC sp_stok_opname @kode = '$kode'";

            // $d_conf = $conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function tes()
    {
        $kode = 'SO23060001';

        $m_conf = new \Model\Storage\Conf();

        $tgl_transaksi = null;
        $gudang = null;
        $barang = null;

        $sql_tgl_dan_gudang = "
            select so.* from stok_opname so
            where
                so.kode_stok_opname = '".$kode."'
        ";
        $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
        if ( $d_tgl_dan_gudang->count() > 0 ) {
            $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
            $tgl_transaksi = $d_tgl_dan_gudang['tanggal'];
            $gudang = $d_tgl_dan_gudang['gudang_kode'];
        }

        $sql_barang = "
            select so.tanggal, sod.item_kode from stok_opname_det sod
            right join
                stok_opname so
                on
                    so.id = sod.id_header
            where
                so.kode_stok_opname = '".$kode."' and
                sod.jumlah > 0
            group by
                so.tanggal,
                sod.item_kode
        ";
        $d_barang = $m_conf->hydrateRaw( $sql_barang );
        if ( $d_barang->count() > 0 ) {
            $d_barang = $d_barang->toArray();

            foreach ($d_barang as $key => $value) {
                $barang[] = $value['item_kode'];
            }
        }

        cetak_r($tgl_transaksi);
        cetak_r($gudang);
        cetak_r($barang);
    }

    public function injekStokOpname()
    {
        $data = array(
            array(
                'kode_barang' => 'BRG2302001', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 320000.00
            ),
            array(
                'kode_barang' => 'BRG2302002', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 110000.00
            ),
            array(
                'kode_barang' => 'BRG2302003', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 30000.00
            ),
            array(
                'kode_barang' => 'BRG2302004', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 85000.00
            ),
            array(
                'kode_barang' => 'BRG2302006', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302007', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302008', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 45000.00
            ),
            array(
                'kode_barang' => 'BRG2302010', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 260000.00
            ),
            array(
                'kode_barang' => 'BRG2302011', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 265000.00
            ),
            array(
                'kode_barang' => 'BRG2302012', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 265000.00
            ),
            array(
                'kode_barang' => 'BRG2302013', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 156000.00
            ),
            array(
                'kode_barang' => 'BRG2302014', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 52500.00
            ),
            array(
                'kode_barang' => 'BRG2302015', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 85000.00
            ),
            array(
                'kode_barang' => 'BRG2302016', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 66000.00
            ),
            array(
                'kode_barang' => 'BRG2302017', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 31428.57
            ),
            array(
                'kode_barang' => 'BRG2302018', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302019', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 2500.00
            ),
            array(
                'kode_barang' => 'BRG2302021', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 42000.00
            ),
            array(
                'kode_barang' => 'BRG2302022', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 14000.00
            ),
            array(
                'kode_barang' => 'BRG2302024', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 56000.00
            ),
            array(
                'kode_barang' => 'BRG2302025', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 20000.00
            ),
            array(
                'kode_barang' => 'BRG2302026', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302028', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 312000.00
            ),
            array(
                'kode_barang' => 'BRG2302029', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 38500.00
            ),
            array(
                'kode_barang' => 'BRG2302030', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 110000.00
            ),
            array(
                'kode_barang' => 'BRG2302031', 
                'satuan' => 'EKOR', 
                'jumlah' => 0, 
                'harga' => 775000.00
            ),
            array(
                'kode_barang' => 'BRG2302032', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 28000.00
            ),
            array(
                'kode_barang' => 'BRG2302033', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 14500.00
            ),
            array(
                'kode_barang' => 'BRG2302037', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 65000.00
            ),
            array(
                'kode_barang' => 'BRG2302038', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 32000.00
            ),
            array(
                'kode_barang' => 'BRG2302039', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 27500.00
            ),
            array(
                'kode_barang' => 'BRG2302042', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 39000.00
            ),
            array(
                'kode_barang' => 'BRG2302041', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 32000.00
            ),
            array(
                'kode_barang' => 'BRG2302043', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 16000.00
            ),
            array(
                'kode_barang' => 'BRG2302045', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 37000.00
            ),
            array(
                'kode_barang' => 'BRG2302044', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 39000.00
            ),
            array(
                'kode_barang' => 'BRG2302046', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 42727.00
            ),
            array(
                'kode_barang' => 'BRG2302550', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302048', 
                'satuan' => 'EKOR', 
                'jumlah' => 0, 
                'harga' => 75000.00
            ),
            array(
                'kode_barang' => 'BRG2302049', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 130000.00
            ),
            array(
                'kode_barang' => 'BRG2302050', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 41000.00
            ),
            array(
                'kode_barang' => 'BRG2302051', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 47500.00
            ),
            array(
                'kode_barang' => 'BRG2302052', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 14550.00
            ),
            array(
                'kode_barang' => 'BRG2302055', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 91700.00
            ),
            array(
                'kode_barang' => 'BRG2302056', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 14250.00
            ),
            array(
                'kode_barang' => 'BRG2302057', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 81800.00
            ),
            array(
                'kode_barang' => 'BRG2302058', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 66900.00
            ),
            array(
                'kode_barang' => 'BRG2302059', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 67000.00
            ),
            array(
                'kode_barang' => 'BRG2302060', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 53500.00
            ),
            array(
                'kode_barang' => 'BRG2302061', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302062', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302063', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 14000.00
            ),
            array(
                'kode_barang' => 'BRG2302064', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302065', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 11500.00
            ),
            array(
                'kode_barang' => 'BRG2302067', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 16000.00
            ),
            array(
                'kode_barang' => 'BRG2302068', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 15250.00
            ),
            array(
                'kode_barang' => 'BRG2302070', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 24000.00
            ),
            array(
                'kode_barang' => 'BRG2302069', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 22000.00
            ),
            array(
                'kode_barang' => 'BRG2302071', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 19500.00
            ),
            array(
                'kode_barang' => 'BRG2302072', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 128750.00
            ),
            array(
                'kode_barang' => 'BRG2302066', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13200.00
            ),
            array(
                'kode_barang' => 'BRG2302073', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 280000.00
            ),
            array(
                'kode_barang' => 'BRG2302074', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 9000.00
            ),
            array(
                'kode_barang' => 'BRG2302076', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 198000.00
            ),
            array(
                'kode_barang' => 'BRG2302077', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302078', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 146000.00
            ),
            array(
                'kode_barang' => 'BRG2302080', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13500.00
            ),
            array(
                'kode_barang' => 'BRG2302081', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 19000.00
            ),
            array(
                'kode_barang' => 'BRG2302083', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 47500.00
            ),
            array(
                'kode_barang' => 'BRG2302084', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 28000.00
            ),
            array(
                'kode_barang' => 'BRG2302086', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 20000.00
            ),
            array(
                'kode_barang' => 'BRG2302092', 
                'satuan' => 'EKOR', 
                'jumlah' => 0, 
                'harga' => 28000.00
            ),
            array(
                'kode_barang' => 'BRG2302094', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 186500.00
            ),
            array(
                'kode_barang' => 'BRG2302095', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 27500.00
            ),
            array(
                'kode_barang' => 'BRG2302546', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 1600.00
            ),
            array(
                'kode_barang' => 'BRG2302096', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 30500.00
            ),
            array(
                'kode_barang' => 'BRG2302097', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 24500.00
            ),
            array(
                'kode_barang' => 'BRG2302100', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 20000.00
            ),
            array(
                'kode_barang' => 'BRG2302099', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 38000.00
            ),
            array(
                'kode_barang' => 'BRG2302101', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 37000.00
            ),
            array(
                'kode_barang' => 'BRG2302102', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 8000.00
            ),
            array(
                'kode_barang' => 'BRG2302103', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 95000.00
            ),
            array(
                'kode_barang' => 'BRG2302106', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302105', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 46000.00
            ),
            array(
                'kode_barang' => 'BRG2302107', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 4500.00
            ),
            array(
                'kode_barang' => 'BRG2302109', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 88000.00
            ),
            array(
                'kode_barang' => 'BRG2302111', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 49000.00
            ),
            array(
                'kode_barang' => 'BRG2302112', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 62000.00
            ),
            array(
                'kode_barang' => 'BRG2302113', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 72100.00
            ),
            array(
                'kode_barang' => 'BRG2302114', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 14750.00
            ),
            array(
                'kode_barang' => 'BRG2302115', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 53400.00
            ),
            array(
                'kode_barang' => 'BRG2302116', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 17182.00
            ),
            array(
                'kode_barang' => 'BRG2302117', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 47000.00
            ),
            array(
                'kode_barang' => 'BRG2302118', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 57500.00
            ),
            array(
                'kode_barang' => 'BRG2302119', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 18750.00
            ),
            array(
                'kode_barang' => 'BRG2302120', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 17500.00
            ),
            array(
                'kode_barang' => 'BRG2302121', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 80000.00
            ),
            array(
                'kode_barang' => 'BRG2302123', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 1540909.00
            ),
            array(
                'kode_barang' => 'BRG2302563', 
                'satuan' => 'CAN', 
                'jumlah' => 0, 
                'harga' => 3901.02
            ),
            array(
                'kode_barang' => 'BRG2302564', 
                'satuan' => 'CAN', 
                'jumlah' => 0, 
                'harga' => 3916.43
            ),
            array(
                'kode_barang' => 'BRG2302126', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 86000.00
            ),
            array(
                'kode_barang' => 'BRG2302128', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 75000.00
            ),
            array(
                'kode_barang' => 'BRG2302130', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 22450.00
            ),
            array(
                'kode_barang' => 'BRG2302132', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 86205.00
            ),
            array(
                'kode_barang' => 'BRG2302133', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 19000.00
            ),
            array(
                'kode_barang' => 'BRG2302135', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 87000.00
            ),
            array(
                'kode_barang' => 'BRG2302137', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 68000.00
            ),
            array(
                'kode_barang' => 'BRG2302558', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 96500.00
            ),
            array(
                'kode_barang' => 'BRG2302140', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 49000.00
            ),
            array(
                'kode_barang' => 'BRG2302141', 
                'satuan' => 'EKOR', 
                'jumlah' => 0, 
                'harga' => 75000.00
            ),
            array(
                'kode_barang' => 'BRG2302142', 
                'satuan' => 'EKOR', 
                'jumlah' => 0, 
                'harga' => 55000.00
            ),
            array(
                'kode_barang' => 'BRG2302144', 
                'satuan' => 'EKOR', 
                'jumlah' => 0, 
                'harga' => 75000.00
            ),
            array(
                'kode_barang' => 'BRG2302145', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 95000.00
            ),
            array(
                'kode_barang' => 'BRG2302146', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 85000.00
            ),
            array(
                'kode_barang' => 'BRG2302147', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 220000.00
            ),
            array(
                'kode_barang' => 'BRG2302148', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 145000.00
            ),
            array(
                'kode_barang' => 'BRG2302149', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 120000.00
            ),
            array(
                'kode_barang' => 'BRG2302151', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 85000.00
            ),
            array(
                'kode_barang' => 'BRG2302152', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 85000.00
            ),
            array(
                'kode_barang' => 'BRG2302153', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 75000.00
            ),
            array(
                'kode_barang' => 'BRG2302154', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 24000.00
            ),
            array(
                'kode_barang' => 'BRG2302155', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 62500.00
            ),
            array(
                'kode_barang' => 'BRG2302156', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 23750.00
            ),
            array(
                'kode_barang' => 'BRG2302157', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 105000.00
            ),
            array(
                'kode_barang' => 'BRG2302158', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 481000.00
            ),
            array(
                'kode_barang' => 'BRG2302159', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302160', 
                'satuan' => 'IKAT', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302161', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302162', 
                'satuan' => 'PACK', 
                'jumlah' => 0, 
                'harga' => 20000.00
            ),
            array(
                'kode_barang' => 'BRG2302163', 
                'satuan' => 'IKAT', 
                'jumlah' => 0, 
                'harga' => 3000.00
            ),
            array(
                'kode_barang' => 'BRG2302164', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302165', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 100000.00
            ),
            array(
                'kode_barang' => 'BRG2302166', 
                'satuan' => 'KG', 
                'jumlah' => 0, 
                'harga' => 37500.00
            ),
            array(
                'kode_barang' => 'BRG2302167', 
                'satuan' => 'KG', 
                'jumlah' => 0, 
                'harga' => 17500.00
            ),
            array(
                'kode_barang' => 'BRG2302168', 
                'satuan' => 'KG', 
                'jumlah' => 0, 
                'harga' => 8500.00
            ),
            array(
                'kode_barang' => 'BRG2302169', 
                'satuan' => 'IKAT', 
                'jumlah' => 0, 
                'harga' => 2000.00
            ),
            array(
                'kode_barang' => 'BRG2302170', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13000.00
            ),
            array(
                'kode_barang' => 'BRG2302171', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10500.00
            ),
            array(
                'kode_barang' => 'BRG2302172', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 3000.00
            ),
            array(
                'kode_barang' => 'BRG2302173', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 8000.00
            ),
            array(
                'kode_barang' => 'BRG2302174', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302175', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302176', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302177', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12500.00
            ),
            array(
                'kode_barang' => 'BRG2302565', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 13000.00
            ),
            array(
                'kode_barang' => 'BRG2302178', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 156250.00
            ),
            array(
                'kode_barang' => 'BRG2302592', 
                'satuan' => 'PACK', 
                'jumlah' => 0, 
                'harga' => 31450.00
            ),
            array(
                'kode_barang' => 'BRG2302182', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 8000.00
            ),
            array(
                'kode_barang' => 'BRG2302566', 
                'satuan' => 'BTL', 
                'jumlah' => 0, 
                'harga' => 4000.00
            ),
            array(
                'kode_barang' => 'BRG2302183', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 11500.00
            ),
            array(
                'kode_barang' => 'BRG2302184', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 33500.00
            ),
            array(
                'kode_barang' => 'BRG2302187', 
                'satuan' => 'PACK', 
                'jumlah' => 0, 
                'harga' => 118967.00
            ),
            array(
                'kode_barang' => 'BRG2302547', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 22000.00
            ),
            array(
                'kode_barang' => 'BRG2302190', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302593', 
                'satuan' => 'PACK', 
                'jumlah' => 0, 
                'harga' => 23912.50
            ),
            array(
                'kode_barang' => 'BRG2302567', 
                'satuan' => 'BTL', 
                'jumlah' => 0, 
                'harga' => 22525.00
            ),
            array(
                'kode_barang' => 'BRG2302197', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 40000.00
            ),
            array(
                'kode_barang' => 'BRG2302198', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 26000.00
            ),
            array(
                'kode_barang' => 'BRG2302200', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 33000.00
            ),
            array(
                'kode_barang' => 'BRG2302202', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12500.00
            ),
            array(
                'kode_barang' => 'BRG2302203', 
                'satuan' => 'PACK', 
                'jumlah' => 0, 
                'harga' => 26000.00
            ),
            array(
                'kode_barang' => 'BRG2302204', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 90000.00
            ),
            array(
                'kode_barang' => 'BRG2302205', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 140000.00
            ),
            array(
                'kode_barang' => 'BRG2302208', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 47000.00
            ),
            array(
                'kode_barang' => 'BRG2302207', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 50000.00
            ),
            array(
                'kode_barang' => 'BRG2302209', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 36000.00
            ),
            array(
                'kode_barang' => 'BRG2302210', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302212', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 50000.00
            ),
            array(
                'kode_barang' => 'BRG2302213', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 125000.00
            ),
            array(
                'kode_barang' => 'BRG2302214', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 265000.00
            ),
            array(
                'kode_barang' => 'BRG2302215', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 55000.00
            ),
            array(
                'kode_barang' => 'BRG2302216', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 19000.00
            ),
            array(
                'kode_barang' => 'BRG2302217', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 7000.00
            ),
            array(
                'kode_barang' => 'BRG2302218', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 16000.00
            ),
            array(
                'kode_barang' => 'BRG2302219', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 1450.00
            ),
            array(
                'kode_barang' => 'BRG2302220', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 14000.00
            ),
            array(
                'kode_barang' => 'BRG2302222', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 37000.00
            ),
            array(
                'kode_barang' => 'BRG2302223', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 16000.00
            ),
            array(
                'kode_barang' => 'BRG2302224', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 285000.00
            ),
            array(
                'kode_barang' => 'BRG2302225', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 55000.00
            ),
            array(
                'kode_barang' => 'BRG2302227', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13750.00
            ),
            array(
                'kode_barang' => 'BRG2302229', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 40000.00
            ),
            array(
                'kode_barang' => 'BRG2302232', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 65000.00
            ),
            array(
                'kode_barang' => 'BRG2302557', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302236', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302238', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 19000.00
            ),
            array(
                'kode_barang' => 'BRG2302239', 
                'satuan' => 'KG', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302241', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 65000.00
            ),
            array(
                'kode_barang' => 'BRG2302242', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 30000.00
            ),
            array(
                'kode_barang' => 'BRG2302243', 
                'satuan' => 'EKOR', 
                'jumlah' => 0, 
                'harga' => 1487550.00
            ),
            array(
                'kode_barang' => 'BRG2302244', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 8000.00
            ),
            array(
                'kode_barang' => 'BRG2302245', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 18000.00
            ),
            array(
                'kode_barang' => 'BRG2302246', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 240000.00
            ),
            array(
                'kode_barang' => 'BRG2302247', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 37500.00
            ),
            array(
                'kode_barang' => 'BRG2302250', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 8500.00
            ),
            array(
                'kode_barang' => 'BRG2302251', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 24000.00
            ),
            array(
                'kode_barang' => 'BRG2302253', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 15250.00
            ),
            array(
                'kode_barang' => 'BRG2302254', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 19500.00
            ),
            array(
                'kode_barang' => 'BRG2302255', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12500.00
            ),
            array(
                'kode_barang' => 'BRG2302259', 
                'satuan' => 'KG', 
                'jumlah' => 0, 
                'harga' => 12500.00
            ),
            array(
                'kode_barang' => 'BRG2302260', 
                'satuan' => 'G', 
                'jumlah' => 0, 
                'harga' => 46500.00
            ),
            array(
                'kode_barang' => 'BRG2302261', 
                'satuan' => 'ikat', 
                'jumlah' => 0, 
                'harga' => 3500.00
            ),
            array(
                'kode_barang' => 'BRG2302262', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302264', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 32500.00
            ),
            array(
                'kode_barang' => 'BRG2302265', 
                'satuan' => 'KG', 
                'jumlah' => 0, 
                'harga' => 11666.94
            ),
            array(
                'kode_barang' => 'BRG2302266', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 22000.00
            ),
            array(
                'kode_barang' => 'BRG2302268', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302269', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302267', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13500.00
            ),
            array(
                'kode_barang' => 'BRG2302270', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302271', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 23000.00
            ),
            array(
                'kode_barang' => 'BRG2302272', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 160000.00
            ),
            array(
                'kode_barang' => 'BRG2302273', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 55000.00
            ),
            array(
                'kode_barang' => 'BRG2302274', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 22000.00
            ),
            array(
                'kode_barang' => 'BRG2302275', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302276', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 8000.00
            ),
            array(
                'kode_barang' => 'BRG2302277', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 6000.00
            ),
            array(
                'kode_barang' => 'BRG2302278', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 9000.00
            ),
            array(
                'kode_barang' => 'BRG2302279', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302281', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302282', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 77000.00
            ),
            array(
                'kode_barang' => 'BRG2302283', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 92000.00
            ),
            array(
                'kode_barang' => 'BRG2302285', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302287', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 24500.00
            ),
            array(
                'kode_barang' => 'BRG2302289', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 22500.00
            ),
            array(
                'kode_barang' => 'BRG2302290', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302292', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 224000.00
            ),
            array(
                'kode_barang' => 'BRG2302294', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 71309.00
            ),
            array(
                'kode_barang' => 'BRG2302296', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302297', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 30000.00
            ),
            array(
                'kode_barang' => 'BRG2302295', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 6500.00
            ),
            array(
                'kode_barang' => 'BRG2302298', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302302', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 24000.00
            ),
            array(
                'kode_barang' => 'BRG2302303', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302304', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 34000.00
            ),
            array(
                'kode_barang' => 'BRG2302589', 
                'satuan' => 'PACK', 
                'jumlah' => 0, 
                'harga' => 27900.00
            ),
            array(
                'kode_barang' => 'BRG2302306', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 8000.00
            ),
            array(
                'kode_barang' => 'BRG2302307', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 85000.00
            ),
            array(
                'kode_barang' => 'BRG2302308', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302309', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 7000.00
            ),
            array(
                'kode_barang' => 'BRG2302311', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 45000.00
            ),
            array(
                'kode_barang' => 'BRG2302312', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 6000.00
            ),
            array(
                'kode_barang' => 'BRG2302313', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 1100.00
            ),
            array(
                'kode_barang' => 'BRG2302314', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 30000.00
            ),
            array(
                'kode_barang' => 'BRG2302315', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 95000.00
            ),
            array(
                'kode_barang' => 'BRG2302316', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 55000.00
            ),
            array(
                'kode_barang' => 'BRG2302317', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 7500.00
            ),
            array(
                'kode_barang' => 'BRG2302319', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 1800.00
            ),
            array(
                'kode_barang' => 'BRG2302320', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 88288.00
            ),
            array(
                'kode_barang' => 'BRG2302321', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 81818.00
            ),
            array(
                'kode_barang' => 'BRG2302559', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 22000.00
            ),
            array(
                'kode_barang' => 'BRG2302323', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 31250.00
            ),
            array(
                'kode_barang' => 'BRG2302325', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 30000.00
            ),
            array(
                'kode_barang' => 'BRG2302326', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 18500.00
            ),
            array(
                'kode_barang' => 'BRG2302327', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 5000.00
            ),
            array(
                'kode_barang' => 'BRG2302328', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 115000.00
            ),
            array(
                'kode_barang' => 'BRG2302331', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 131000.00
            ),
            array(
                'kode_barang' => 'BRG2302333', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13500.00
            ),
            array(
                'kode_barang' => 'BRG2302334', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 110000.00
            ),
            array(
                'kode_barang' => 'BRG2302336', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 4000.00
            ),
            array(
                'kode_barang' => 'BRG2302338', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13500.00
            ),
            array(
                'kode_barang' => 'BRG2302340', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 80000.00
            ),
            array(
                'kode_barang' => 'BRG2302345', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302346', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 108162.90
            ),
            array(
                'kode_barang' => 'BRG2302348', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 14575.00
            ),
            array(
                'kode_barang' => 'BRG2302349', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 31000.00
            ),
            array(
                'kode_barang' => 'BRG2302548', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 32000.00
            ),
            array(
                'kode_barang' => 'BRG2302352', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 5000.00
            ),
            array(
                'kode_barang' => 'BRG2302353', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 55000.00
            ),
            array(
                'kode_barang' => 'BRG2302354', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 11000.00
            ),
            array(
                'kode_barang' => 'BRG2302356', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 120000.00
            ),
            array(
                'kode_barang' => 'BRG2302362', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 52500.00
            ),
            array(
                'kode_barang' => 'BRG2302363', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 60000.00
            ),
            array(
                'kode_barang' => 'BRG2302364', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 74500.00
            ),
            array(
                'kode_barang' => 'BRG2302365', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 20750.00
            ),
            array(
                'kode_barang' => 'BRG2302367', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 75000.00
            ),
            array(
                'kode_barang' => 'BRG2302368', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 110000.00
            ),
            array(
                'kode_barang' => 'BRG2302370', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302372', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302371', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 20000.00
            ),
            array(
                'kode_barang' => 'BRG2302376', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 1545.00
            ),
            array(
                'kode_barang' => 'BRG2302377', 
                'satuan' => 'SISIR', 
                'jumlah' => 0, 
                'harga' => 31500.00
            ),
            array(
                'kode_barang' => 'BRG2302378', 
                'satuan' => 'SISIR', 
                'jumlah' => 0, 
                'harga' => 30000.00
            ),
            array(
                'kode_barang' => 'BRG2302379', 
                'satuan' => 'SISIR', 
                'jumlah' => 0, 
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302380', 
                'satuan' => 'SSR', 
                'jumlah' => 0, 
                'harga' => 85000.00
            ),
            array(
                'kode_barang' => 'BRG2302381', 
                'satuan' => 'TANDON', 
                'jumlah' => 0, 
                'harga' => 85000.00
            ),
            array(
                'kode_barang' => 'BRG2302382', 
                'satuan' => 'SISIR', 
                'jumlah' => 0, 
                'harga' => 45000.00
            ),
            array(
                'kode_barang' => 'BRG2302383', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 5000.00
            ),
            array(
                'kode_barang' => 'BRG2302384', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 165000.00
            ),
            array(
                'kode_barang' => 'BRG2302385', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 22400.00
            ),
            array(
                'kode_barang' => 'BRG2302386', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 31982.00
            ),
            array(
                'kode_barang' => 'BRG2302574', 
                'satuan' => 'BTL', 
                'jumlah' => 0, 
                'harga' => 5433.33
            ),
            array(
                'kode_barang' => 'BRG2302555', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 42500.00
            ),
            array(
                'kode_barang' => 'BRG2302387', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 95000.00
            ),
            array(
                'kode_barang' => 'BRG2302390', 
                'satuan' => 'SLICE', 
                'jumlah' => 0, 
                'harga' => 42000.00
            ),
            array(
                'kode_barang' => 'BRG2302556', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 450000.00
            ),
            array(
                'kode_barang' => 'BRG2302394', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13000.00
            ),
            array(
                'kode_barang' => 'BRG2302397', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 27500.00
            ),
            array(
                'kode_barang' => 'BRG2302585', 
                'satuan' => 'PACK', 
                'jumlah' => 0, 
                'harga' => 29100.00
            ),
            array(
                'kode_barang' => 'BRG2302401', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 49500.00
            ),
            array(
                'kode_barang' => 'BRG2302405', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 19000.00
            ),
            array(
                'kode_barang' => 'BRG2302409', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 48750.00
            ),
            array(
                'kode_barang' => 'BRG2302411', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 52500.00
            ),
            array(
                'kode_barang' => 'BRG2302412', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 55000.00
            ),
            array(
                'kode_barang' => 'BRG2302413', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 60000.00
            ),
            array(
                'kode_barang' => 'BRG2302414', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 27000.00
            ),
            array(
                'kode_barang' => 'BRG2302415', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10500.00
            ),
            array(
                'kode_barang' => 'BRG2302416', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302417', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302418', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 26000.00
            ),
            array(
                'kode_barang' => 'BRG2302419', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 172500.00
            ),
            array(
                'kode_barang' => 'BRG2302420', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12500.00
            ),
            array(
                'kode_barang' => 'BRG2302421', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302423', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 16000.00
            ),
            array(
                'kode_barang' => 'BRG2302422', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 95000.00
            ),
            array(
                'kode_barang' => 'BRG2302424', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302426', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 9000.00
            ),
            array(
                'kode_barang' => 'BRG2302428', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302427', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 7000.00
            ),
            array(
                'kode_barang' => 'BRG2302429', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 0.00
            ),
            array(
                'kode_barang' => 'BRG2302430', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 600000.00
            ),
            array(
                'kode_barang' => 'BRG2302431', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 2500000.00
            ),
            array(
                'kode_barang' => 'BRG2302432', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 125000.00
            ),
            array(
                'kode_barang' => 'BRG2302433', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 175000.00
            ),
            array(
                'kode_barang' => 'BRG2302434', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 19000.00
            ),
            array(
                'kode_barang' => 'BRG2302435', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 68500.00
            ),
            array(
                'kode_barang' => 'BRG2302436', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 92700.00
            ),
            array(
                'kode_barang' => 'BRG2302439', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12500.00
            ),
            array(
                'kode_barang' => 'BRG2302442', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12500.00
            ),
            array(
                'kode_barang' => 'BRG2302446', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 160000.00
            ),
            array(
                'kode_barang' => 'BRG2302447', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 87000.00
            ),
            array(
                'kode_barang' => 'BRG2302448', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302449', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 17000.00
            ),
            array(
                'kode_barang' => 'BRG2302450', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 22000.00
            ),
            array(
                'kode_barang' => 'BRG2302452', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 62000.00
            ),
            array(
                'kode_barang' => 'BRG2302399', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 3500.00
            ),
            array(
                'kode_barang' => 'BRG2302460', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 3500.00
            ),
            array(
                'kode_barang' => 'BRG2302461', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 4800.00
            ),
            array(
                'kode_barang' => 'BRG2302462', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 145000.00
            ),
            array(
                'kode_barang' => 'BRG2302464', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 23000.00
            ),
            array(
                'kode_barang' => 'BRG2302463', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 34000.00
            ),
            array(
                'kode_barang' => 'BRG2302465', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 20000.00
            ),
            array(
                'kode_barang' => 'BRG2302466', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 51000.00
            ),
            array(
                'kode_barang' => 'BRG2302467', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 8500.00
            ),
            array(
                'kode_barang' => 'BRG2302468', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 20000.00
            ),
            array(
                'kode_barang' => 'BRG2302471', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 4000.00
            ),
            array(
                'kode_barang' => 'BRG2302470', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 4000.00
            ),
            array(
                'kode_barang' => 'BRG2302473', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302474', 
                'satuan' => 'PCS', 
                'jumlah' => 0, 
                'harga' => 600.00
            ),
            array(
                'kode_barang' => 'BRG2302475', 
                'satuan' => 'PAX', 
                'jumlah' => 0, 
                'harga' => 4500.00
            ),
            array(
                'kode_barang' => 'BRG2302476', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 290000.00
            ),
            array(
                'kode_barang' => 'BRG2302478', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 28000.00
            ),
            array(
                'kode_barang' => 'BRG2302481', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 28000.00
            ),
            array(
                'kode_barang' => 'BRG2302482', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 61273.00
            ),
            array(
                'kode_barang' => 'BRG2302487', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 8800.00
            ),
            array(
                'kode_barang' => 'BRG2302488', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302551', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 45000.00
            ),
            array(
                'kode_barang' => 'BRG2302491', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 5000.00
            ),
            array(
                'kode_barang' => 'BRG2302492', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 7500.00
            ),
            array(
                'kode_barang' => 'BRG2302493', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302494', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 14863.64
            ),
            array(
                'kode_barang' => 'BRG2302495', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 14500.00
            ),
            array(
                'kode_barang' => 'BRG2302496', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 285000.00
            ),
            array(
                'kode_barang' => 'BRG2302497', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13500.00
            ),
            array(
                'kode_barang' => 'BRG2302498', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 13000.00
            ),
            array(
                'kode_barang' => 'BRG2302499', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 45000.00
            ),
            array(
                'kode_barang' => 'BRG2302500', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 105000.00
            ),
            array(
                'kode_barang' => 'BRG2302501', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 9333.33
            ),
            array(
                'kode_barang' => 'BRG2302503', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 125000.00
            ),
            array(
                'kode_barang' => 'BRG2302504', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 243000.00
            ),
            array(
                'kode_barang' => 'BRG2302505', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 32000.00
            ),
            array(
                'kode_barang' => 'BRG2302506', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302507', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 20000.00
            ),
            array(
                'kode_barang' => 'BRG2302554', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 32500.00
            ),
            array(
                'kode_barang' => 'BRG2302510', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 110000.00
            ),
            array(
                'kode_barang' => 'BRG2302511', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 90000.00
            ),
            array(
                'kode_barang' => 'BRG2302512', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 60000.00
            ),
            array(
                'kode_barang' => 'BRG2302513', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 130000.00
            ),
            array(
                'kode_barang' => 'BRG2302514', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 130000.00
            ),
            array(
                'kode_barang' => 'BRG2302515', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 145000.00
            ),
            array(
                'kode_barang' => 'BRG2302517', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 110000.00
            ),
            array(
                'kode_barang' => 'BRG2302518', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 65000.00
            ),
            array(
                'kode_barang' => 'BRG2302519', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 138000.00
            ),
            array(
                'kode_barang' => 'BRG2302520', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 115000.00
            ),
            array(
                'kode_barang' => 'BRG2302521', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 162000.00
            ),
            array(
                'kode_barang' => 'BRG2302522', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 175000.00
            ),
            array(
                'kode_barang' => 'BRG2302523', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 150000.00
            ),
            array(
                'kode_barang' => 'BRG2302526', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 76000.00
            ),
            array(
                'kode_barang' => 'BRG2302527', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 115000.00
            ),
            array(
                'kode_barang' => 'BRG2302528', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 162500.00
            ),
            array(
                'kode_barang' => 'BRG2302529', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 65000.00
            ),
            array(
                'kode_barang' => 'BRG2302530', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 85000.00
            ),
            array(
                'kode_barang' => 'BRG2302531', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 80000.00
            ),
            array(
                'kode_barang' => 'BRG2302532', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 65000.00
            ),
            array(
                'kode_barang' => 'BRG2302533', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 67000.00
            ),
            array(
                'kode_barang' => 'BRG2302534', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 178000.00
            ),
            array(
                'kode_barang' => 'BRG2302535', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 64000.00
            ),
            array(
                'kode_barang' => 'BRG2302536', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 45000.00
            ),
            array(
                'kode_barang' => 'BRG2302537', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 130000.00
            ),
            array(
                'kode_barang' => 'BRG2302538', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 250000.00
            ),
            array(
                'kode_barang' => 'BRG2302539', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302540', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 82205.00
            ),
            array(
                'kode_barang' => 'BRG2302541', 
                'satuan' => 'ML', 
                'jumlah' => 0, 
                'harga' => 41850.00
            ),
            array(
                'kode_barang' => 'BRG2302544', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 17500.00
            ),
            array(
                'kode_barang' => 'BRG2302543', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 10000.00
            ),
            array(
                'kode_barang' => 'BRG2302553', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 109033.00
            ),
            array(
                'kode_barang' => 'BRG2302577', 
                'satuan' => 'BTL', 
                'jumlah' => 0, 
                'harga' => 4197.67
            ),
            array(
                'kode_barang' => 'BRG2302578', 
                'satuan' => 'BTL', 
                'jumlah' => 0, 
                'harga' => 4197.67
            ),
            array(
                'kode_barang' => 'BRG2302579', 
                'satuan' => 'BTL', 
                'jumlah' => 0, 
                'harga' => 4197.67
            ),
            array(
                'kode_barang' => 'BRG2302545', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 40000.00
            ),
            array(
                'kode_barang' => 'BRG2302549', 
                'satuan' => 'GRAM', 
                'jumlah' => 0, 
                'harga' => 35000.00
            ),
        );

        $keterangan_barang = '';
        $idx_barang_tidak_ditemukan = 0;
        foreach ($data as $k_data => $v_data) {
            $m_item = new \Model\Storage\Item_model();
            $d_item = $m_item->where('kode', $v_data['kode_barang'])->first();

            $m_is = new \Model\Storage\ItemSatuan_model();
            $d_is = $m_is->where('item_kode', $v_data['kode_barang'])->where('satuan', $v_data['satuan'])->first();

            if ( !$d_item || !$d_is ) {
                cetak_r('KODE : '.$v_data['kode_barang']);
                cetak_r('KODE : '.$v_data['kode_barang'].' | SATUAN : '.$v_data['satuan']);

                if ( $keterangan_barang != '' ) {
                    $keterangan_barang .= '<br>';
                }
                if ( !$d_item ) {
                    $keterangan_barang .= 'KODE : '.$v_data['kode_barang'];
                }
                if ( !$d_item ) {
                    $keterangan_barang .= 'KODE : '.$v_data['kode_barang'].' | SATUAN : '.$v_data['satuan'];
                }

                $idx_barang_tidak_ditemukan++;
            } else {
                $data[ $k_data ]['pengali'] = $d_is->pengali;
            }
        }

        if ( $idx_barang_tidak_ditemukan > 0 ) {
            $keterangan_barang .= '<br>List barang yang tidak ada di program.';

            echo $keterangan_barang;
        } else {
            // cetak_r('lengkap');
            $m_so = new \Model\Storage\StokOpname_model();

            $kode_stok_opname = $m_so->getNextIdRibuan();

            $m_so->tanggal = '2023-07-01';
            $m_so->gudang_kode = 'GDG-PUSAT';
            $m_so->kode_stok_opname = $kode_stok_opname;
            $m_so->save();

            foreach ($data as $k_li => $v_li) {
                $m_sod = new \Model\Storage\StokOpnameDet_model();
                $m_sod->id_header = $m_so->id;
                $m_sod->item_kode = $v_li['kode_barang'];
                $m_sod->satuan = $v_li['satuan'];
                $m_sod->pengali = $v_li['pengali'];
                $m_sod->jumlah = $v_li['jumlah'];
                $m_sod->harga = $v_li['harga'];
                $m_sod->save();
            }

            $kode = $kode_stok_opname;

            $m_conf = new \Model\Storage\Conf();

            $tgl_transaksi = null;
            $gudang = null;
            $barang = null;

            $sql_tgl_dan_gudang = "
                select so.* from stok_opname so
                where
                    so.kode_stok_opname = '".$kode."'
            ";
            $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
            if ( $d_tgl_dan_gudang->count() > 0 ) {
                $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
                $tgl_transaksi = $d_tgl_dan_gudang['tanggal'];
                $gudang = $d_tgl_dan_gudang['gudang_kode'];
            }

            $sql_barang = "
                select so.tanggal, sod.item_kode from stok_opname_det sod
                right join
                    stok_opname so
                    on
                        so.id = sod.id_header
                where
                    so.kode_stok_opname = '".$kode."' and
                    sod.jumlah > 0
                group by
                    so.tanggal,
                    sod.item_kode
            ";
            $d_barang = $m_conf->hydrateRaw( $sql_barang );
            if ( $d_barang->count() > 0 ) {
                $d_barang = $d_barang->toArray();

                foreach ($d_barang as $key => $value) {
                    $barang[] = $value['item_kode'];
                }
            }

            $sql = "EXEC sp_hitung_stok_by_barang @barang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($barang))))."', @tgl_transaksi = '".$tgl_transaksi."', @gudang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($gudang))))."'";

            $d_conf = $m_conf->hydrateRaw($sql);
        }
    }
}