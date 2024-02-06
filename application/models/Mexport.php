<?php

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Mexport extends CI_Model{

	function __construct(){
		date_default_timezone_set('Asia/Jakarta');
        parent::__construct();
    }

  

	public function generate($gid){
        $this->sitedb = $this->load->database('sitedb', TRUE);
		$sql = "select _gid,_nik,_name,_dept,_jab,_company_label, _company_name from workplaze.employee_profile('$gid') limit 1";
		$query = $this->sitedb->query($sql);
		$list = $query->result();
        $count = count($list);
        if($count>0){
            foreach($list as $dt){
                $_gid = $dt->_gid;
                $_nik = $dt->_nik;
                $_name = $dt->_name;
                $_dept = $dt->_dept;
                $_jab = $dt->_jab;
                $_company_label = $dt->_company_label;
            }
            $n_exp = explode(" ", $_name);
            // $final_name = strtolower($n_exp[0].'.'.$n_exp[1]); 
            $final_name = strtolower(str_replace(" ",".", $_name)); 
            if($_company_label=='SG'){
                $img = 'logo-02.png';
            }else{
                $img = 'logo-01.png';
            }
            $date = date('M Y', strtotime(date('Y-m-d H:i:s')));
            $new_ref = date('Y').'-'.$_gid;
            // (B) CREATE QR CODE
            $code_n = base64_encode(base64_encode($new_ref).'_'.$date);
            $qr = QrCode::create($final_name."_".$code_n)
            // (B1) CORRECTION LEVEL
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            // (B2) SIZE & MARGIN
            ->setSize(350)
            ->setMargin(5)
            // (B3) COLORS
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
            // (B4) ATTACH LOGO
            $logo = Logo::create("assets/qr-logo/".$img."")
            ->setResizeToWidth(75);
            // (C) OUTPUT QR CODE
            $writer = new PngWriter();
            $result = $writer->write($qr, $logo);
            // $result = $writer->write($qr);
           
            $qr_fix =  $result->getDataUri();
            $arr = array(
                "msg" => 'succcess',
                "data" => $list,
                "qr" => $qr_fix, 
            );
            return $arr;
        }else{
            $arr = array(
                "msg" => 'error',
                "data" => '',
                "qr" => '', 
            );
            return $arr;
        }
    }

    function spreadsheet($myObj,$file){
        // Create a new spreadsheet object
        $spreadsheet = new Spreadsheet();
        // Create a worksheet object
        $worksheet = $spreadsheet->getActiveSheet();
        $array_database = json_decode(json_encode($myObj), true);
        $column_names2 = array_keys($array_database[0]); // header
        $column_names = array_keys($array_database[0]); // contant
        // print_r($column_names); exit;
        foreach ($column_names2 as $i => $column_name2) {
            $worksheet->setCellValue('A1', 'NO');
            if ($i > 24) {
                $final = $column_names2[$i] = 'A'.chr(65+$i+1-26) . 1;
                $last_cells = $column_names2[$i] = 'A'.chr(65+$i+1-26);
            }else if($i > 48 ) {
                $final = $column_names2[$i] = 'B'.chr(65+$i+1-52) . 1;
                $last_cells = $column_names2[$i] = 'B'.chr(65+$i+1-52);
            }else{
                $final =  $column_names2[$i] = chr(65+$i+1) . 1;
                $last_cells =  $column_names2[$i] = chr(65+$i+1);
            }
            $worksheet->setCellValue($final, strtoupper(str_replace("_", " ", $column_name2)));
            $worksheet->getColumnDimension($last_cells)->setAutoSize(true); // for auto width column 
        }
      
        $no = 1;
        $i = 2;
        foreach ($array_database as $row) {
             $worksheet->setCellValue('A'. $i, $no++);
            foreach ($column_names as $key => $column_name) {
                if($key > 24) {
                    $final_2 = 'A'.chr(65+$key+1-26) . ($i);
                }else if($key > 48) {
                    $final_2 = 'B'.chr(65+$key+1-52) . ($i);
                }else{
                    $final_2  = chr(65+$key+1) . ($i);
                }
                $worksheet->setCellValue($final_2, $row[$column_name]);
            }
            $i++;
        }

        $styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
            'font' => [
                'size' => 8.5, // 'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
		];
        $i = $i - 1;
        $worksheet->getStyle('A1:'.$last_cells.''.$i)->applyFromArray($styleArray);
        // Save the spreadsheet to a file
        $writer = new Xlsx($spreadsheet);
        $fileName = $file.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }

    function is_base64($s){
        return !(base64_decode($s, true) === false);
    }




    
	

}