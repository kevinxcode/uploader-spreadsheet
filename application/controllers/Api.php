<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    function abc_data_cetak(){
        $this->abcdb_real = $this->load->database('abcdb_real', TRUE);
        $now = date('Y-m-d');
        $sql = "SELECT * FROM datacetak WHERE id>34418 AND date(created_date)='$now'  order by id asc ";
		$query = $this->abcdb_real->query($sql);
		$list = $query->result();
		$this->abcdb_real->close();
        
        if(count($list)>0){
            foreach($list as $obj){
                $data = array(
                    'id_dummy' => 'D-'.$obj->id,
                    'Tanggal' => $obj->Tanggal,
                    'Customer' => $obj->Customer,
                    'Deskripsi' => $obj->Deskripsi,
                    'Bahan' => $obj->Bahan,
                    'Sisi' => $obj->Sisi,
                    'Uk1' => $obj->Uk1,
                    'X' => $obj->X,
                    'Uk2' => $obj->Uk2,
                    'M' => $obj->M,
                    'Jumlah' => $obj->Jumlah,
                    'Pcs' => $obj->Pcs,
                    'Finishing' => $obj->Finishing,
                    'Desain' => $obj->Desain,
                    'HargaModal' => $obj->HargaModal,
                    'UnitPriceModal' => $obj->UnitPriceModal,
                    'TotalModal' => $obj->TotalModal,
                    'Harga' => $obj->Harga,
                    'UnitPrice' => $obj->UnitPrice,
                    'Total' => $obj->Total,
                    'Balance' => $obj->Balance,
                    'Dp' => $obj->Dp,
                    'Ppn' => $obj->Ppn,
                    'ActualAmount' => $obj->ActualAmount,
                    'SisaBayar' => $obj->SisaBayar,
                    'Tgl_DP' => $obj->Tgl_DP,
                    'Tgl_Lunas' => $obj->Tgl_Lunas,
                    'NoInvoice' => $obj->NoInvoice,
                    'Status' => $obj->Status,
                    'Validasi' => $obj->Validasi,
                    'User' => $obj->User,
                    'Mesin' => $obj->Mesin,
                );
                $new_id = 'D-'.$obj->id;
                $this->abcdb = $this->load->database('abcdb', TRUE);
                $check = $this->Mdata->check_data_cetak_abc($new_id);
                if($check==2){
                    $this->abcdb->insert('datacetak', $data);
                }else{
                    $this->abcdb->where('id_dummy', $new_id);
                    $this->abcdb->update('datacetak', $data);
                }
            }
            
           
          
        }
        
 
		
    }   

}
