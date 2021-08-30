<?php
class Position
{
	private $id; //ID в БД
	private $invoice_id; //Номер счета
	public $name; //Название позиции
	public $count; //Количество в счете
	public $sum; //Стоимость одного элемента в позиции
	
	private $table = 'positions';

   
    public function __construct() {
      
    }
	
	/* Получение списка позиций из БД по ID счета*/
	
	public function getPositions($invoice_id){
		$invoice_id = (int)$invoice_id;
		
		$positions = array();
		$query = Db::connect()->query("SELECT id FROM ".$this->table." WHERE invoice_id = ".$invoice_id);
		while ($row = $query->fetch_assoc())
		{
			$positions[] = $row['id'];
		}
		return $positions;
	}
	
	/* Получение позиции из БД по ID*/
	
	public function getPosition($id){
		$id = (int)$id;
		$positions = array();
		$query = Db::connect()->query("SELECT * FROM ".$this->table." WHERE id = ".$id);
		if ($query->num_rows > 0){
			$row = $query->fetch_assoc();
			
			$this->id = $row['id'];
			$this->invoice_id = $row['invoice_id'];
			$this->name = $row['name'];
			$this->count = $row['count'];
			$this->sum = $row['sum'];
	
			return true;
		} else {
			return false;
		}
		
		return $positions;
	}
	
	/* Добавление позиции*/
	
	public function addPosition($invoice_id, $name, $sum, $count){
		$invoice_id = (int)$invoice_id;
		$name=htmlspecialchars(strip_tags($name));
		$sum = (int)$sum;
		$count = (int)$count;
		
		$query = Db::connect()->query("INSERT INTO ".$this->table." SET invoice_id=".$invoice_id.", name='".$name."', sum=".$sum.", count=".$count);
		
		if($query) return true;
		else return false;
	}
	
	/* Удаление позиции*/
	
	public function removePosition($id){
		$id = (int)$id;
		
		$query = Db::connect()->query("DELETE FROM ".$this->table." WHERE id=".$id);
		
		if($query) return true;
		else return false;
	}
   

}