<?php
namespace Model\Storage;
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Conf extends Eloquent
{
	public $timestamps = false;
	public function __construct(){
	}

	public function getCurrConnection(){
		// return $this->getConnection();
		return $this->getConnection('default');
	}

	public static function factory($nama_class){
		$new_class = '\Model\\Storage\\'.$nama_class;
		return new $new_class;
	}

	public function getDate() {
		return $this->hydrateRaw('select CURRENT_TIMESTAMP() waktu, left(cast(CURRENT_TIMESTAMP() as date),10) tanggal, left(cast(CURRENT_TIMESTAMP() as time),8) jam', array())->first()->toArray();
	}

	public function getNextIdentity()
	{
		$next_id = $this -> max($this->primaryKey) + 1;
		return $next_id;
	}

	public function getNextId(){
		$id = $this->whereRaw("SUBSTRING(".$this->primaryKey.",4,4) = concat( cast(right(year(current_timestamp),2) as char(2)), LPAD(MONTH(current_timestamp), 2, '0') )")
					->selectRaw("concat('".$this->kodeTable."',right(year(current_timestamp),2),LPAD(MONTH(current_timestamp), 2, '0'),case when char_length(substring( COALESCE(MAX(".$this->primaryKey."),'000'),8,3)+1) = 1 THEN concat('00', substring( COALESCE(MAX(".$this->primaryKey."),'000'),8,3)+1) when char_length(substring( COALESCE(MAX(".$this->primaryKey."),'000'),8,3)+1) = 2 THEN concat('0', substring( COALESCE(MAX(".$this->primaryKey."),'000'),8,3)+1) when char_length(substring( COALESCE(MAX(".$this->primaryKey."),'000'),8,3)+1) = 3 THEN substring( COALESCE(MAX(".$this->primaryKey."),'000'),8,3)+1 end) as nextId")
					->first();

		return $id->nextId;
	}
}