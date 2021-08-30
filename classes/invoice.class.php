<?php
class Invoice
{	
	private $id;//ID в БД
	private $number;// Номер счета
	private $status;// Статус - числовое значение
	
	public $date; //Дата
	public $sale; //Скидка
	public $positions; //Массив ID позиций в счете
	
	private $clPosition; //Объект класса Position
	private $statuses; //Массив возможных статусов
	private $table = 'invoices';
   
    public function __construct() {
	  $this->clPosition = new Position;
	  
	  $statuses = array();
      $query = Db::connect()->query("SELECT * FROM statuses");
	  while ($row = $query->fetch_assoc())
	  {
		 $statuses[$row['id']] = $row['name'];
	  }
  
	  $this->statuses = $statuses;
	  return;
    }
	
	/* Получение счета из БД по номеру или другому полю, например id*/
	
	public function getInvoice($number, $field = 'number'){
		if(!$number) return false;
		
		$number=htmlspecialchars(strip_tags($number));
		
		$query = Db::connect()->query("SELECT * FROM ".$this->table." WHERE ".$field." = '".$number."'");

		if ($query->num_rows > 0){
			$row = $query->fetch_assoc();
			
			$this->id = $row['id'];
			$this->number = $number;
			$this->date = date('d.m.Y', $row['date']);
			$this->status = $row['status'];
			$this->sale = $row['sale'];
			
			$this->positions = $this->clPosition->getPositions($this->id);
		
			return true;
		} else {
			return false;
		}
		
	}
	
	/* Установка номера счета */
	
    public function setNumber($number)
    {
		$number = htmlspecialchars(strip_tags($number));
        $query = Db::connect()->query("UPDATE ".$this->table." SET number = '".$number."' WHERE id = ".$this->id);
		if($query){
			$this->number = $number;
		}
		return;
    }
	
	/*Вывод номера счета*/
	
    public function getNumber()
    {
        return $this->number;
    }
	
	/* Установка статуса */
	
    public function setStatus($status_id)
    {
        $status_id = (int)$status_id;
        $query = Db::connect()->query("UPDATE ".$this->table." SET status = '".$status_id."' WHERE id = ".$this->id);
		if($query){
			$this->status = $status_id;
		}
		return;
    }
	
	/*Вывод статуса*/
	
    public function getStatus()
    {
		if(array_key_exists($this->status, $this->statuses))
			return $this->statuses[$this->status];
		else return $this->status;
    }
	
	/* Установка даты */
	
    public function setDate($date)
    {
        $date = strtotime($date);
        $query = Db::connect()->query("UPDATE ".$this->table." SET date = '".$date."' WHERE id = ".$this->id);
		if($query){
			$this->date = date('d.m.Y', $date);
		}
		return;
    }
	
	/* Установка скидки */
	
    public function setSale($sale)
    {
        $sale = (int)$sale;
        $query = Db::connect()->query("UPDATE ".$this->table." SET sale = '".$sale."' WHERE id = ".$this->id);
		if($query){
			$this->sale = $sale;
		}
		return;
    }
	
	/*Добавление позиции в счет*/
	
    public function addInvoicePosition($name, $sum, $count) 
    {
        return $this->clPosition->addPosition($this->id,$name, $sum, $count);
    }
	
	/*Удаление позиции в счете*/
	
    public function removeInvoicePosition($id)
    {
        return $this->clPosition->removePosition($id);
    }
	
	
	/* Сумма счета с учетом стоимости, количества позиций и скидки на счет в процентах */
	
    public function getSum()
    {
		$sum = 0;
        foreach($this->positions as $position){
			$this->clPosition->getPosition($position);
			$sum += $this->clPosition->sum * $this->clPosition->count;
		}
		if($this->sale){
			$sum = ceil($sum - $this->sale/100*$sum);
		}
		return $sum;
    }
	/*
		Выборка по условиям
		$conditions  = array('date:>' => '20.02.2018', 'status:=' => 1);
		$conditions  = array('date:>' => '20.02.2018', 'status:=' => 'Оплачен');
	*/
	
    public function filter($conditions)
    {
		if(!is_array($conditions)){
			$where = '';
		}
		else{
			foreach($conditions as $condition => $value){
				$condition = explode(':',$condition);
				$condition[0] = trim($condition[0]);
				if($condition[0] == 'date') $value = strtotime($value);
				if($condition[0] == 'status'){
					if(!is_numeric($value) and $val = array_search($value, $this->statuses)){
						$value = $val;
					}
				}
				$where[] = $condition[0]." ".$condition[1]." '".$value."'";
			}
			$where = ' WHERE '.implode(' AND ', $where);
		}
		
		$query = Db::connect()->query("SELECT * FROM ".$this->table.$where);
	    while ($row = $query->fetch_assoc())
	    {
		   $out[] = $row;
	    }
		return $out;
    }
}