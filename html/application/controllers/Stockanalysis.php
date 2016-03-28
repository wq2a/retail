<?php 
//set php script timeout, 0 to disable
set_time_limit(0); 
date_default_timezone_set('Asia/Chongqing');
class Stockanalysis extends CI_Controller{
	function index()
	{
		$this->db->limit(80);
		//$this->db->where("date!=adate AND his=1");
		$this->db->where("isupdate=1 AND his=1");
		$stockcode_query = $this->db->get('stockcode');
		
		if($stockcode_query->num_rows()<1){
			//echo $text;
			//$this->send_email_('股票分析完成');
		}
		
		foreach($stockcode_query->result() as $stockcode){
			$code = $stockcode->code;
			$codetype = $stockcode->codetype;
			$this->db->where("code='".$code."' AND codetype='".$codetype."'");
			$this->db->order_by('date','desc');
			$this->db->limit(1);
			$stock_query = $this->db->get('stock');
			foreach($stock_query->result() as $stock){
				// set close into stockcode table
				//echo 'close:'.$stock->close;
				$stock_data = array();
				$stock_data['adate'] = $stockcode->date;
				$stock_data['close'] = $stock->close;
				if($stock->close<$stockcode->lowest||$stockcode->lowest==0){
					$this->db->where("code='".$code."' AND codetype='".$codetype."'");
					$this->db->order_by('low','asc');
					$this->db->limit(1);
					$stock_query_low = $this->db->get('stock');
					foreach($stock_query_low->result() as $l){
						//echo 'low:'.$l->low;
						$stock_data['lowest'] = $l->low;
						// set low into stockcode table
					}
				}
				if($stock->close>$stockcode->highest||$stockcode->highest==0){
					$this->db->where("code='".$code."' AND codetype='".$codetype."'");
					$this->db->order_by('high','desc');
					$this->db->limit(1);
					$stock_query_high = $this->db->get('stock');
					foreach($stock_query_high->result() as $h){
						//echo 'high:'.$h->high;
						$stock_data['highest'] = $h->high;
						// set high into stockcode table
					}
				}
				if(isset($stock_data)){
					$stock_data['isupdate'] = 2;
					$where = "code='".$code."' AND codetype='".$codetype."'";
					$this->db->where($where);
					$this->db->update('stockcode', $stock_data);
					print_r($stock_data);
					echo '<br/>';
				}
			}
		}
	}
	
	function reset()
	{
		$stock_data = array();
		$stock_data['isupdate'] = 0;
		$this->db->where('isupdate!=0');
		$this->db->update('stockcode', $stock_data);
		//$this->send_email_('股票重设完成');
	}
	
	function his()
	{
		$this->db->where("his=1");
		$stockcode_query = $this->db->get('stockcode');
		$is_stored = false;
		foreach($stockcode_query->result() as $item){
			
			if(!$is_stored){
				echo $item->date;
				$this->db->where('date',$item->date);
				$temp = $this->db->get('stock_his');
				if($temp->num_rows()>0){
					echo 'already';
					break;
				}else{
					$is_stored = true;
				}
			}
			$his_data = array();
			$his_data['code'] = $item->code;
			$his_data['codetype'] = $item->codetype;
			$his_data['name'] = $item->name;
			$his_data['date'] = $item->date;
			$his_data['highest'] = $item->highest;
			$his_data['lowest'] = $item->lowest;
			$his_data['close'] = $item->close;
			$this->db->insert('stock_his', $his_data);
		}
		//$this->send_email_('股票历史已保存');
		echo 'finish';
	}
	
	function send_email_($message)
	{
		
		echo 's';
		
	}
}
