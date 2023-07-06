<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Divisi_model extends Conf {
	protected $table = 'divisi';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'DIV';
    public $timestamps = false;
}