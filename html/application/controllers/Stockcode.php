<?php 
//set php script timeout, 0 to disable
//set_time_limit(0); 
class Stockcode extends CI_Controller{
	function index()
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
						'his' => array(
                                                 'type' => 'INT',
                                                 'constraint' => 7,
												 'unsigned' => TRUE
                                          ),
                        'date' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20'
                                          )
                );
				
		$this->dbforge->add_field($fields);
		$this->dbforge->create_table('stockcode',TRUE);
		
		$this->load->helper('htmldom_helper');
		$url = 'http://quote.eastmoney.com/stock_list.html';
		$html = file_get_dom($url);
		$quote = $html('div#quotesearch',0);
		$u = $quote('ul');
		foreach($u[0]('li') as $i){
			$str = iconv('GB2312','UTF-8', $i->getPlainText().'<br/>');
			preg_match('/(?P<name>.*)\((?P<code>.*)\)/', $str, $matches);
			//echo $matches['name'].' '.$matches['code'].'<br/>';
			if(isset($matches['code'])&&$matches['code']>='600000'&&$matches['code']<'900000'){
				$stock_data = array();
				$stock_data['code'] = $matches['code'];
				$stock_data['name'] = $matches['name'];
				$stock_data['codetype'] = 'ss';
				$stock_data['his'] = 0;
				$where = "code='".$stock_data['code']."' AND name='".$stock_data['name']."'";
				$this->db->where($where);
				$stock_query = $this->db->get('stockcode');
				if($stock_query->num_rows()<1){
					$this->db->insert('stockcode', $stock_data); 
				}
			}
		}
		foreach($u[1]('li') as $i){
			$str = iconv('GB2312','UTF-8', $i->getPlainText());
			preg_match('/(?P<name>.*)\((?P<code>.*)\)/', $str, $matches);
			//echo $matches['name'].' '.$matches['code'].'<br/>';
			if(isset($matches['code'])&&!($matches['code']>='030000'&&$matches['code']<'300000')){
				$stock_data = array();
				$stock_data['code'] = $matches['code'];
				$stock_data['name'] = $matches['name'];
				$stock_data['codetype'] = 'sz';
				$stock_data['his'] = 0;
				$where = "code='".$stock_data['code']."' AND name='".$stock_data['name']."'";
				$this->db->where($where);
				$stock_query = $this->db->get('stockcode');
				if($stock_query->num_rows()<1){
					$this->db->insert('stockcode', $stock_data); 
				}
			}
		}
	}
}