<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Express extends CI_Controller {

	function order(){
		$data['current_page'] = "abc";
		$data['sub_page'] = "abc_data_cetak";
		$check = $this->input->get('check');
		if(isset($check)){
			$dt1 = $this->input->get('dt1');
			$dt2 = $this->input->get('dt2');
			$data['check'] = $check;
		}else{
			$data['check'] = '';
			$dt1 = date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days'));
			$dt2 = date('Y-m-d', strtotime(date('Y-m-d'). ' + 2 days'));
		}
		$data['dt1'] = $dt1;
		$data['dt2'] = $dt2;

		$this->expressdb = $this->load->database('expressdb', TRUE);
		$sql = "SELECT * FROM datacetak where tanggal between '$dt1' AND  '$dt2' order by id desc";
		$query = $this->expressdb->query($sql);
		$list =  $query->result();
		$this->expressdb->close();

		if($check=='EXPORT'){
			$this->Mexport->spreadsheet($list,'abc_data_cetak');
			// redirect('/abc_data_cetak.xlsx');
		}
		$data['file_name'] = '/abc_data_cetak.xlsx';
		$data['list'] = $list;
		$this->load->view('app/index_header_template', $data);
		$this->load->view('abc/order_temp', $data);
		$this->load->view('app/index_footer_template', $data);
	}

	function kas(){
		$data['current_page'] = "abc";
		$data['sub_page'] = "abc_kas";
		$check = $this->input->get('check');
		if(isset($check)){
			$dt1 = $this->input->get('dt1');
			$dt2 = $this->input->get('dt2');
			$data['check'] = $check;
		}else{
			$data['check'] = '';
			$dt1 = date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days'));
			$dt2 = date('Y-m-d', strtotime(date('Y-m-d'). ' + 2 days'));
		}
		$data['dt1'] = $dt1;
		$data['dt2'] = $dt2;

		$this->expressdb = $this->load->database('expressdb', TRUE);
		$sql = "WITH
		t1 AS (SELECT * FROM pemasukkan WHERE tanggal BETWEEN '$dt1' AND '$dt2' 
		ORDER BY NoInvoice DESC),
		t2 AS (SELECT NoInvoice, SUM(ActualAmount) AS inv_total FROM datacetak 
		WHERE NoInvoice IN (SELECT DISTINCT(t1.NoInvoice) FROM t1) GROUP BY NoInvoice )
		SELECT * FROM t1
		INNER JOIN t2 ON t2.NoInvoice=t1.NoInvoice";
		$query = $this->expressdb->query($sql);
		$list =  $query->result();
		$this->expressdb->close();

		if($check=='EXPORT'){
			$this->Mexport->spreadsheet($list,'abc_pemasukkan');
			// redirect('/abc_pemasukkan.xlsx');
		}
		$data['file_name'] = '/abc_pemasukkan.xlsx';
		$data['list'] = $list;
		$this->load->view('app/index_header_template', $data);
		$this->load->view('abc/pemasukkan_temp', $data);
		$this->load->view('app/index_footer_template', $data);
	}

	function invDetail(){
		$data['current_page'] = "abc";
		$data['sub_page'] = "abc_data_cetak";
		$invoice_no = $this->input->get('invoice_no');
		

		$this->expressdb = $this->load->database('expressdb', TRUE);
		$sql = "SELECT * FROM datacetak where NoInvoice = '$invoice_no' order by id asc";
		$query = $this->expressdb->query($sql);
		$list =  $query->result();

		$sql2 = "SELECT SUM(ActualAmount) AS ActualAmount FROM datacetak where NoInvoice = '$invoice_no' order by id asc";
		$query2 = $this->expressdb->query($sql2);
		$list2 =  $query2->result();

		$this->expressdb->close();
		$ActualAmount = 0;
		foreach($list2 as $dt){
			$ActualAmount = $dt->ActualAmount;
		}
		$data['list'] = $list;
		$data['ActualAmount'] = $ActualAmount;
		$this->load->view('app/index_header_template_view', $data);
		$this->load->view('abc/order_temp_detail', $data);
		$this->load->view('app/index_footer_template', $data);
	}
	// old

	function pengeluaran_view(){
		$data['current_page'] = "abc";
		$data['sub_page'] = "abc_pengeluaran";
		$check = $this->input->get('check');
		if(isset($check)){
			$dt1 = $this->input->get('dt1');
			$dt2 = $this->input->get('dt2');
			$data['check'] = $check;
		}else{
			$data['check'] = '';
			$dt1 = date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days'));
			$dt2 = date('Y-m-d', strtotime(date('Y-m-d'). ' + 2 days'));
		}
		$data['dt1'] = $dt1;
		$data['dt2'] = $dt2;

		$this->expressdb = $this->load->database('expressdb', TRUE);
		$sql = "SELECT * FROM pengeluaran WHERE tanggal BETWEEN '$dt1' AND '$dt2' ORDER BY id desc";
		$query = $this->expressdb->query($sql);
		$list =  $query->result();
		$this->expressdb->close();

		if($check=='EXPORT'){
			$this->Mexport->spreadsheet($list,'abc_pengeluaran');
			// redirect('/abc_pengeluaran.xlsx');
		}
		$data['file_name'] = '/abc_pengeluaran.xlsx';
		$data['list'] = $list;
		$this->load->view('app/index_header_template', $data);
		$this->load->view('abc/pengeluaran_temp', $data);
		$this->load->view('app/index_footer_template', $data);
	}

	public function index(){
		$this->load->view('welcome_message');
	}

	public function data_cetak(){
		$msg = $this->input->get('msg');
		$data['msg'] = $msg;
		$this->load->view('abc/data_cetak_temp', $data);
	}

	function kas_harian(){
		$msg = $this->input->get('msg');
		$data['msg'] = $msg;
		$this->load->view('abc/kas_harian', $data);
	}

	function pengeluaran(){
		$msg = $this->input->get('msg');
		$data['msg'] = $msg;
		$this->load->view('abc/pengeluaran_temp', $data);
	}

	function uplpoad_data_cetak(){
		set_time_limit(500000000000000000); // 
		$action_data = $this->input->post('action_data');

		if(isset($_POST['import'])){ // Jika user mengklik tombol Import
			// ambil data file
			$namaFile = $_FILES['namafile']['name'];
			$namaSementara = $_FILES['namafile']['tmp_name'];
			$ext = pathinfo($namaFile, PATHINFO_EXTENSION);
			$namabaru = "excel_import.".$ext;
			$dirUpload = "tmp/";
			$terupload = move_uploaded_file($namaSementara, $dirUpload.$namabaru);
			$path = 'tmp/' . $namabaru; // Set tempat menyimpan file tersebut dimana
		  
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			$spreadsheet = $reader->load($path); // Load file yang tadi diupload ke folder tmp
			$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
		
			$numrow = 1;
			$count = count($sheet);
			foreach($sheet as $row){
			  $id = $row['A']; 
			  $Tanggal = $row['B']; 
			  $Customer = $row['C']; 
			  $Deskripsi = $row['D']; 
			  $Bahan = $row['E']; 
			  $Sisi = $row['F']; 
			  $Uk1 = $row['G']; 
			  $X = $row['H']; 
			  $Uk2 = $row['I']; 
			  $M = $row['J']; 
			  $Jumlah = $row['K']; 
			  $Pcs = $row['L']; 
			  $Finishing = $row['M']; 
			  $Desain = $row['N']; 
			  $HargaModal = $row['O']; 
			  $UnitPriceModal = $row['P']; 
			  $TotalModal = $row['Q']; 
			  $Harga = $row['R']; 
			  $UnitPrice = $row['S']; 
			  $Total = $row['T']; 
			  $Balance = $row['U']; 
			  $Dp = $row['V']; 
			  $Ppn = $row['W']; 
			  $ActualAmount = $row['X']; 
			  $SisaBayar = $row['Y']; 
			  $Tgl_DP = $row['Z']; 
			  $Tgl_Lunas = $row['AA']; 
			  $NoInvoice = $row['AB']; 
			  $Status = $row['AC']; 
			  $Validasi = $row['AD']; 
			  $User = $row['AE']; 
			  $Mesin = $row['AF']; 

			  $data = array(
				'id' => $id,
				'Tanggal' => $Tanggal,
				'Customer' => $Customer,
				'Deskripsi' => $Deskripsi,
				'Bahan' => $Bahan,
				'Sisi' => $Sisi,
				'Uk1' => $Uk1,
				'X' => $X,
				'Uk2' => $Uk2,
				'M' => $M,
				'Jumlah' => $Jumlah,
				'Pcs' => $Pcs,
				'Finishing' => $Finishing,
				'Desain' => $Desain,
				'HargaModal' => $HargaModal,
				'UnitPriceModal' => $UnitPriceModal,
				'TotalModal' => $TotalModal,
				'Harga' => $Harga,
				'UnitPrice' => $UnitPrice,
				'Total' => $Total,
				'Balance' => $Balance,
				'Dp' => $Dp,
				'Ppn' => $Ppn,
				'ActualAmount' => $ActualAmount,
				'SisaBayar' => $SisaBayar,
				'Tgl_DP' => $Tgl_DP,
				'Tgl_Lunas' => $Tgl_Lunas,
				'NoInvoice' => $NoInvoice,
				'Status' => $Status,
				'Validasi' => $Validasi,
				'User' => $User,
				'Mesin' => $Mesin,
			);
			$this->expressdb = $this->load->database('expressdb', TRUE);
			// $check = $this->check_data_cetak($id);
			
			if($action_data=='insert'){
				$this->expressdb->insert('datacetak', $data);
			}else{
				$this->expressdb->where('id', $id);
				$this->expressdb->update('datacetak', $data);
				
			}
			$numrow++; 
			 }
			unlink($path); 
		}
		redirect('?msg=success&&com=ABC DATA CETAK&&count='.$count);
	}

	function check_data_cetak($id){
		$this->expressdb = $this->load->database('expressdb', TRUE);
		$sql = "select id from datacetak where id='$id'";
		$query = $this->expressdb->query($sql);
		if($query->num_rows() > 0){
			return 1;
		}else{
			return 2;
		}
	}

	function upload_pengeluaran(){
		set_time_limit(500000000000000000); // 
		if(isset($_POST['import'])){ // Jika user mengklik tombol Import
			// ambil data file
			$namaFile = $_FILES['namafile']['name'];
			$namaSementara = $_FILES['namafile']['tmp_name'];
			$ext = pathinfo($namaFile, PATHINFO_EXTENSION);
			$namabaru = "excel_import.".$ext;
			$dirUpload = "tmp/";
			$terupload = move_uploaded_file($namaSementara, $dirUpload.$namabaru);
			$path = 'tmp/' . $namabaru; // Set tempat menyimpan file tersebut dimana
		  
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			$spreadsheet = $reader->load($path); // Load file yang tadi diupload ke folder tmp
			$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
		
			$numrow = 1;
			$count = count($sheet);
			foreach($sheet as $row){
			  $a = $row['A']; 
			  $b = $row['B']; 
			  $c = $row['C']; 
			  $d = $row['D']; 
			 

			$data = array(
				'id' => $a,
				'Tanggal' => $b,
				'Keterangan' => $c,
				'Jumlah' => $d,
			);
			$this->expressdb = $this->load->database('expressdb', TRUE);
			$check = $this->check_pengeluaran($a);
			if($check==2){
				$this->expressdb->insert('pengeluaran', $data);
			}else{
				$this->expressdb->where('id', $a);
				$this->expressdb->update('pengeluaran', $data);
				
			}
			$numrow++; 
			 }
			unlink($path); 
		}
		redirect('?msg=success&&com=ABC PENGELUARAN&&count='.$count);
	}

	function check_pengeluaran($id){
		$this->expressdb = $this->load->database('expressdb', TRUE);
		$sql = "select id from pengeluaran where id='$id'";
		$query = $this->expressdb->query($sql);
		if($query->num_rows() > 0){
			return 1;
		}else{
			return 2;
		}
	}

	function upload_kas(){
		set_time_limit(500000000000000000); // 
		if(isset($_POST['import'])){ // Jika user mengklik tombol Import
			// ambil data file
			$namaFile = $_FILES['namafile']['name'];
			$namaSementara = $_FILES['namafile']['tmp_name'];
			$ext = pathinfo($namaFile, PATHINFO_EXTENSION);
			$namabaru = "excel_import.".$ext;
			$dirUpload = "tmp/";
			$terupload = move_uploaded_file($namaSementara, $dirUpload.$namabaru);
			$path = 'tmp/' . $namabaru; // Set tempat menyimpan file tersebut dimana
		  
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			$spreadsheet = $reader->load($path); // Load file yang tadi diupload ke folder tmp
			$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
		
			$numrow = 1;
			$count = count($sheet);
			foreach($sheet as $row){
			  $a = $row['A']; 
			  $b = $row['B']; 
			  $c = $row['C']; 
			  $d = $row['D']; 
			  $e = $row['E']; 
			  $f = $row['F']; 
			  $g = $row['G']; 
			  $h = $row['H']; 
			  $i = $row['I']; 
			  $j = $row['J']; 
			 

			$data = array(
				'Tanggal' => $a,
				'NoInvoice' => $b,
				'Customer' => $c,
				'Dp' => $d,
				'SisaBayar' => $e,
				'Saldo' => $f,
				'StatusBayar' => $g,
				'Tgl_DP' => $h,
				'Tgl_Lunas' => $i,
				'Keterangan' => $j,
			);
			$this->expressdb = $this->load->database('expressdb', TRUE);
			$check = $this->check_pemasukan($b);
			if($check==2){
				$this->expressdb->insert('pemasukkan', $data);
			}else{
				$this->expressdb->where('NoInvoice', $b);
				$this->expressdb->update('pemasukkan', $data);
			}
			$numrow++; 
			}
			unlink($path); 
		}
		redirect('?msg=success&&com=ABC KAS HARIAN&&count='.$count);
	}

	function check_pemasukan($id){
		$this->expressdb = $this->load->database('expressdb', TRUE);
		$sql = "select NoInvoice from pemasukkan where NoInvoice='$id'";
		$query = $this->expressdb->query($sql);
		if($query->num_rows() > 0){
			return 1;
		}else{
			return 2;
		}
	}

	function tes(){
		$dateString = '11/8/2023';
		$targetFormat = 'Y-m-d';

		$result = $this->changeDateFormat($dateString, $targetFormat);
		echo $result; // Output: 09-11-2023
	}



	function changeDateFormat($dateString, $targetFormat){
		// List of possible date formats to try
		$formatsToTry = [
			'Y-m-d H:i:s',
			'Y-m-d',
			'm/d/Y H:i:s',
			'm/d/Y',
			'd-m-Y H:i:s',
			'd-m-Y',
			// Add more formats as needed
		];

		$dateTime = null;

		// Try each format until a valid DateTime object is created
		foreach ($formatsToTry as $format) {
			$dateTime = DateTime::createFromFormat($format, $dateString);
			if ($dateTime !== false) {
				break;
			}
		}

		// If a valid DateTime object is created, change the format
		if ($dateTime !== false) {
			$formattedDate = $dateTime->format($targetFormat);
			return $formattedDate;
		} else {
			// Handle the case when none of the formats match
			return 'Invalid date format';
		}
	}

	
	




}
