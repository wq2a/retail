<?php 
//set php script timeout, 0 to disable
set_time_limit(0); 
date_default_timezone_set('Asia/Chongqing');
class Stockcurrent extends CI_Controller{
	function index()
	{
		//echo date('Y-m-d H:i:s');
		$year = date('Y');
		$month = date('m')-1;
		$day = date('j')-1;
		$date = strtotime($year.'-'.($month+1).'-'.($day-1));
		
		$date1 = strtotime($year.'-'.($month+1).'-'.($day-2));
		
		$this->load->helper('htmldom_helper');
		
		//$where = "his=1 AND date<'".$date."' AND delete=0";
		//$where = "his=1 AND isupdate=0 AND delete=0";
		$this->db->where("isupdate=0 AND his=1");
		//$this->db->where($where);
		$this->db->limit(100);
		$stockcode_query = $this->db->get('stockcode');
		$code = '000001';
		$type = 'sz';
		$stname = '';
		
		
		foreach ($stockcode_query->result() as $row)
		{
			$code = $row->code;
			$type = $row->codetype;
			$stname = $row->name;
			$insert_result=0;
			$url = 'http://table.finance.yahoo.com/table.csv?s='.$code.'.'.$type.'&a='.$month.'&b='.$day.'&c='.$year;
			$html = file_get_dom($url);
			$html = str_replace("Adj Close","Adj ",$html);
			//$html = str_replace("\r"," ",$html);
			//$html = str_replace("\n"," ",$html);
			$html = str_replace(array('\n','\r')," ",$html);
			$stock = str_getcsv($html," ");
			$count = count($stock);
			//$date1 = strtotime($year.'-'.($month+1).'-'.($day-1));
			for($index=1;$index<$count;$index++){
				$ss = explode(',',$stock[$index]);
				if(strtotime($ss[0])>$date1){
					$stock_data = array();
					$stock_data['code'] = $code;
					$stock_data['codetype'] = $type;
					$stock_data['name'] = $stname;
					$stock_data['open'] = $ss[1];
					$stock_data['high'] = $ss[2];
					$stock_data['low'] = $ss[3];
					$stock_data['close'] = $ss[4];
					$stock_data['volume'] = $ss[5];
					$stock_data['adj'] = $ss[6];
					$stock_data['date'] = trim($ss[0]);
					$where = "code='".$stock_data['code']."' AND date='".$stock_data['date']."' AND codetype='".$stock_data['codetype']."'";
					$this->db->where($where);
					$stock_query = $this->db->get('stock');
					if($stock_query->num_rows()<1){
						$this->db->insert('stock', $stock_data);
						$insert_result++;
						
					}
				}
			}
			echo $stname.$insert_result;
			if($insert_result>0){
				$data = array(
					'date' => $date,
					'isupdate' => 1
				);
			
				$where = "code='".$code."' AND codetype='".$type."'";
				$this->db->where($where);
				$this->db->update('stockcode', $data);
			}else{
				$data = array(
					'delete' => '1',
					'isupdate' => 1
				);
			
				$where = "code='".$code."' AND codetype='".$type."'";
				$this->db->where($where);
				$this->db->update('stockcode', $data);
			}
			
		}
	}
}