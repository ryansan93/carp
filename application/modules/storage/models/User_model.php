<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class User_model extends Conf {
	protected $table = 'user';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'USR';
    public $timestamps = false;
}