<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Carp_model extends Conf{
	protected $table = 'carp';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'CARP';
	public $timestamps = false;

	public function getNextKode(){
		$id = $this->whereRaw("SUBSTRING(".$this->primaryKey.",1,4) = '".$this->kodeTable."'")
					->selectRaw("concat('".$this->kodeTable."',
						case 
							when char_length(substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1) = 1 THEN 
								concat('0000', substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1) 
							when char_length(substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1) = 2 THEN 
								concat('000', substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1) 
							when char_length(substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1) = 3 THEN 
								concat('00', substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1) 
							when char_length(substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1) = 4 THEN 
								concat('0', substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1) 
							when char_length(substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1) = 5 THEN 
								substring( COALESCE(MAX(".$this->primaryKey."),'00000'),5,5)+1
						end) as nextId")
					->first();

		return $id->nextId;
	}
}
