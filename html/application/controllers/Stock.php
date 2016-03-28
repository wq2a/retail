<?php 
//set php script timeout, 0 to disable
//set_time_limit(0); 
class Stock extends My_Controller{
	
	function __Construct()
	{
		parent::__construct();
		$data['navigation'] = get_class();
		$this->load->vars($data);
	}
	
	function home()
	{
		$this->index();
	}
	
	function index()
	{

		$this->load->library('pagination');

		$config['base_url'] = base_url().'/index.php/stock/index';
		$this->db->where("highest<100 AND delete<10 AND his>0");
		$config['total_rows'] = $this->db->get('stockcode')->num_rows();
		$config['per_page'] = 88;
		$config['num_links'] = 30;
		$config['full_tag_open'] = '<div id="pagination">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);
		$this->db->where("highest<100 AND delete<10  AND his>0");
		$this->db->order_by('((highest-close)/highest)','desc');
		$data['records'] = $this->db->get('stockcode', $config['per_page'], $this->uri->segment(3));
		
		// 加载页面
		$data['main_containt'] = 'stock/homepage';
		$this->load->view('includes/template',$data);
		
	}
	
	// 自选
	function my()
	{

		$this->load->library('pagination');

		$config['base_url'] = base_url().'/index.php/stock/index';
		$this->db->where("highest<100 AND delete<10 AND his>0 AND mystock=1");
		$config['total_rows'] = $this->db->get('stockcode')->num_rows();
		$config['per_page'] = 88;
		$config['num_links'] = 30;
		$config['full_tag_open'] = '<div id="pagination">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);
		$this->db->where("highest<100 AND delete<10  AND his>0  AND mystock=1");
		$this->db->order_by('((highest-close)/highest)','desc');
		$data['records'] = $this->db->get('stockcode', $config['per_page'], $this->uri->segment(3));
		
		// 加载页面
		$data['main_containt'] = 'stock/homepage';
		$this->load->view('includes/template',$data);
		
	}
	
	function searchstock($stock)
	{
		if($stock!='s_null')
		{
			$temp = "highest<100 AND delete<10 AND his>0";
			$this->load->library('pagination');

			$config['base_url'] = base_url().'/index.php/stock/searchstock/'.$stock;
			$stock = str_replace('s_','',$stock);
			$this->db->where($temp);
			$this->db->like('code',$stock);
			$this->db->or_like('name',$stock);
			$config['total_rows'] = $this->db->get('stockcode')->num_rows();
			$config['per_page'] = 88;
			$config['num_links'] = 30;
			$config['full_tag_open'] = '<div id="pagination">';
			$config['full_tag_close'] = '</div>';
			$this->pagination->initialize($config);
			//$this->db->where("highest<100 AND delete<10  AND his>0  AND mystock=1");
			$this->db->where($temp);
			$this->db->like('code',$stock);
			$this->db->or_like('name',$stock);
			$this->db->order_by('((highest-close)/highest)','desc');
			$data['records'] = $this->db->get('stockcode', $config['per_page'], $this->uri->segment(3));
			$data['s'] = $stock;
			
			// 加载页面
			$data['main_containt'] = 'stock/homepage';
			$this->load->view('includes/template',$data);
		}else{
			$this->index();
		}
	}
	
	function stock_kline($code,$codetype)
	{
		$this->db->order_by('date','asc');
		$this->db->where('code',trim($code));
		$this->db->where('codetype',trim($codetype));
		$stock_q = $this->db->get('stock');
		$temp = '';
		foreach($stock_q->result() as $item){
			//echo $item->name;
			$t = explode('-',$item->date);
			//echo $t[1].'/'.$t[2].'/'.$t[0];
			$temp .="'".$t[1].'/'.$t[2].'/'.$t[0]." 16:00:00',".$item->open.','.$item->high.','.$item->low.','.$item->close.';';
		}
		$temp .= ']';
		echo str_replace(';]','',$temp);
	}
	
	function stock_analysis($code,$codetype)
	{
		//$this->db->limit($num);
		$this->db->order_by("date", "asc");
		$this->db->where('code',trim($code));
		$this->db->where('codetype',trim($codetype));
		$stock_q = $this->db->get('stock_his');
		$temp = '[[';
		$index=0;
		foreach($stock_q->result() as $item){
			//$temp .= '['.$index.','.(number_format((100*($item->close-$item->lowest)/$item->lowest), 2, '.', '')-$r1).'],';
			$temp .= '['.$index.','.$item->highest.'],';
			$index++;
		}
		$temp .= '],[';
		$index=0;
		foreach($stock_q->result() as $item){
			//$temp .= '['.$index.','.(number_format((100*($item->highest-$item->close)/$item->highest), 2, '.', '')-$g1).'],';
			$temp .= '['.$index.','.$item->close.'],';
			$index++;
		}
		$temp .= '],[';
		$index=0;
		foreach($stock_q->result() as $item){
			$temp .= '['.$index.','.$item->lowest.'],';
			$index++;
		}
		
		$temp .=']]';
		echo str_replace(',]',']',$temp);
	}
	
	function news()
	{
		$this->load->library('pagination');

		$config['base_url'] = base_url().'/index.php/stock/news';
		//$this->db->where("highest<100 AND delete<10 AND his>0");
		$config['total_rows'] = $this->db->get('top_news_list')->num_rows();
		$config['per_page'] = 10;
		$config['num_links'] = 10;
		$config['full_tag_open'] = '<div id="pagination">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);
		//$this->db->where("highest<100 AND delete<10  AND his>0");
		$this->db->order_by('news_id','desc');
		$data['records'] = $this->db->get('top_news_list', $config['per_page'], $this->uri->segment(3));
		
		// 加载页面
		$data['main_containt'] = 'stock/news';
		$this->load->view('includes/template',$data);
		
	}
	
	function delete()
	{
		if($this->input->post('ajax')=='1')
		{
			$success=false;
			$data = array(
				'his' => 0,
				'date' => '',
				'delete' => 0,
				'highest' => 0,
				'lowest' => 0,
				'close' => 0,
				'adate' => ''
			);
			$where = "code='".$this->input->post('code')."' AND codetype='".$this->input->post('codetype')."'";
			$this->db->where($where);
			$success = $this->db->update('stockcode', $data); 
			
			$where2 = "code='".$this->input->post('code')."' AND codetype='".$this->input->post('codetype')."'";
			$this->db->where($where2);
				//$this->db->where('name', $i->name);
			$this->db->delete('stock');
			//}
			
			if($success)
			{
				echo '成功';
			}
		}
	}
	
	function mystock()
	{
		if($this->input->post('ajax')=='1')
		{
			$success=false;
			$data = array();
			if($this->input->post('mystock')==0){
				$data['mystock'] = 1;
			}else{
				$data['mystock'] = 0;
			}
			$where = "code='".$this->input->post('code')."' AND codetype='".$this->input->post('codetype')."'";
			$this->db->where($where);
			$success = $this->db->update('stockcode', $data); 
			
			if($success)
			{
				echo '成功';
			}
		}
	}
	
	/*
	function delok()
	{
		$data = array(
			'date' => '',
			'delete' => 0,
			'highest' => 0,
			'lowest' => 0,
			'close' => 0,
			'adate' => '',
		);
		$this->db->where("his=0");
		$success = $this->db->update('stockcode', $data);
		
		$this->db->where("his=0");
		$stock_q = $this->db->get('stockcode'); 
		foreach($stock_q->result() as $i){
			echo $i->name;
			$this->db->where('name', $i->name);
			$this->db->delete('stock'); 
		}
	}*/
	
	function source()
	{
		$this->load->dbforge();
		$fields = array(
                        'code' => array(
												 'type' => 'VARCHAR',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                          ),
						'codetype' => array(
												 'type' => 'VARCHAR',
                                                 'constraint' => '10',
                                                 'unsigned' => TRUE,
                                          ),
						'name' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
												 'unsigned' => TRUE
                                          ),
						'sname' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
												 'unsigned' => TRUE
                                          ),
						'open' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
												 'unsigned' => TRUE
                                          ),
						'high' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
												 'unsigned' => TRUE
                                          ),
						'low' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
												 'unsigned' => TRUE
                                          ),
						'close' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
												 'unsigned' => TRUE
                                          ),
						'volume' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '30',
												 'unsigned' => TRUE
                                          ),
						'adj' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
												 'unsigned' => TRUE
                                          ),
                        'date' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20'
                                          )
                );
				
		$this->dbforge->add_field($fields);
		$this->dbforge->create_table('stock',TRUE);
		
		$this->load->helper('htmldom_helper');
		for($ii=0;$ii<1;$ii++){
		$this->db->where("his=0");
		$this->db->limit(1);
		$stockcode_query = $this->db->get('stockcode');
		$code = '000001';// = $stockcode_query->code;
		$type = 'sz';//$stockcode_query->codetype;
		$stname = '';
		foreach ($stockcode_query->result() as $row)
		{
			$code = $row->code;
			$type = $row->codetype;
			$stname = $row->name;
			
		}
		$insert_result=0;
		$url = 'http://table.finance.yahoo.com/table.csv?s='.$code.'.'.$type;
		$html = file_get_dom($url);
		$html = str_replace("Adj Close","Adj ",$html);
		$html = str_replace("\r"," ",$html);
		$html = str_replace("\n"," ",$html);
		$stock = str_getcsv($html," ");
		$count = count($stock);
		$date1 = strtotime("2014-8-7");
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
					$stock_data['date'] = $ss[0];
					$where = "code='".$stock_data['code']."' AND date='".$stock_data['date']."' AND codetype='".$stock_data['codetype']."'";
					$this->db->where($where);
					$stock_query = $this->db->get('stock');
					if($stock_query->num_rows()<1){
						$this->db->insert('stock', $stock_data);
						$insert_result++;
					}
				}
			}
			echo 'finish';
			echo $insert_result;
			$data = array(
				'his' => 1
			);
			$where = "code='".$code."' AND codetype='".$type."'";
			$this->db->where($where);
			$this->db->update('stockcode', $data); 
		
		}
	}
}