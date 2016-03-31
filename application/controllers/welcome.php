<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		// $win_history = new tbWin_history(array('bid_id' => 14, 'bid_type' => 0));
		// $current_prize = (real) @$win_history->prize;
        // $win_history->prize = $current_prize + 100;
		// $win_history->bid_type = 1;
		// $win_history->bid_id = 14;
        // $win_history->save();
	}
	/**
	 * lotto_id:
	 * 	- date
	 */
	function tracking ()
	{
		$lottos = new tbLotto();
		$lottos->get();
		
		$dates = array(
			'2015-05-05',
			'2015-05-06',
			'2015-05-07',
			'2015-05-08',
			'2015-05-09',
			'2015-05-10',
			'2015-05-11',
			'2015-05-12',
		);
		
		$dataset = array();
		foreach ($lottos as $lotto) 
		{
			$lotto_record = array();
			foreach ($dates as $date) 
			{
				$date_record = array();
				$sql = "SELECT * FROM `tbLotto_result_tracking` where lotto_id = {$lotto->id} and dateline > '{$date}' and dateline < '{$date} 23:59:59' order by dateline asc";
				$result = $this->db->query($sql)->result();
				if (($qty = count($result)) > 0)
				{
					$start = $result[0]->dateline;
					$end = end($result)->dateline;
					$date_record = array(
						'start' => $start,
						'start_id' => $result[0]->result_id,
						'end_id' => end($result)->result_id,
						'end' => $end,
						'qty' => $qty
					);
				}
				$lotto_record+= array(
					$date => $date_record
				);	
			}
			$dataset+= array(
				$lotto->name => $lotto_record
			);
		}
		load("tracking_table", array('dates'=>$dates, 'data'=>$dataset));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */