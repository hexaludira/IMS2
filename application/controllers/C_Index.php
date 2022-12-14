<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//include library phpoffice
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_Index extends CI_Controller {

	function __construct(){
		parent::__construct();
		Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
		$this->load->model('M_Incident');
		$this->load->model('M_Kategori');
		$this->load->model('M_Datatable');
	}

	public function index()
	{
		$this->load->view('V_Incident_List');
	}

	public function incident()
	{
		$data['page'] = 'incident';

		$this->load->view('layout',$data);
	}

	function ambilData()
	{
		$data = $this->M_Incident->getData();
		echo json_encode($data);
	}

	function ambilDataAjax()
	{
		header('Content-Type: application/json');
		$list = $this->M_Datatable->get_datatables();
		$data = array();
		$no = $this->input->post('start');
		//looping data incident
		foreach($list as $data_incident)
		{
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $data_incident->incident_name;
			$row[] = $data_incident->incident_date;
			$row[] = $data_incident->incident_time_begin;
			$row[] = $data_incident->incident_time_end;
			$row[] = $data_incident->incident_location;
			$row[] = $data_incident->incident_detail;
			$row[] = $data_incident->incident_affected;
			$row[] = $data_incident->incident_remark;
			$row[] = $data_incident->incident_status;
			//$row[] = '<a class="btn btn-success btn-sm"><i class="fa fa-edit"></i> </a><a class="btn btn-danger btn-sm "><i class="fa fa-trash"></i> </a>';
			$row[] = '<span><a href="#" class="btn btn-primary btn_edit" data-id="'.$data_incident->incident_id.'">Edit</a> <button data-id="'.$data_incident->incident_id .'" class="btn btn-danger btn_hapus">Hapus</button> <button data-id="'.$data_incident->incident_id .'" class="btn btn-warning btn_tampil_foto">Lihat Foto</button></span>';
			$data[] = $row;
		}
		$output = array(
				"draw" => $this->input->post('draw'),
            "recordsTotal" => $this->M_Datatable->count_all(),
            "recordsFiltered" => $this->M_Datatable->count_filtered(),
            "data" => $data,
		);
		//output to json format
			echo json_encode($output);
        //$this->output->set_output(json_encode($output));
	}

	function hapusData()
	{
		$incident_id = $this->input->post('incident_id');
		$img_name = $this->M_Incident->getImageNameByID($incident_id)->incident_picture_name;
		$data = $this->M_Incident->deleteData($incident_id);
		$glob_find = glob("uploads/".$img_name."*");
		unlink($glob_find[0]);
		// $img_hapus = unlink("uploads/".$img_name.".jpg");
		// if($img_hapus == false){
		// 	unlink("uploads/".$img_name.".png");
		// }
		//$img_hapus = glob('"'.$img_name.'.*'.'"');
		//array_map("unlink", glob('"'.$img_name.'.*'.'"'));
		//unlink("uploads/".glob($img_name.'*'));
		echo json_encode($data);
	}

	function tambahData()
	{
		//$img_name = "";
		$data = $this->input->post();
		// $config['upload_path'] = "./uploads";
		// $config['allowed_types'] = 'gif|jpg|png';
		// $config['encrypt_name'] = TRUE;

			// $this->load->library('upload',$config);

			// if($this->upload->do_upload("incident_picture")){
			// 	$data_image = array('upload_data' => $this->upload->data());

			// // 	//$judul = $this->input->post('judul');
			// 	$img_name = $data_image['upload_data']['file_name'];
			// 	//$result = $this->M_Incident->simpanUpload($img_name);
			// 	//echo json_decode($result);

			// }

		$dataSave = $this->M_Incident->insertData([
				'incident_name' => $data['incident_name'],
				'incident_date' => $data['incident_date'],
				'incident_time_begin' => $data['incident_time_begin'],
				'incident_time_end' => $data['incident_time_end'],
				'incident_location' => $data['incident_location'],
				'incident_detail' => $data['incident_detail'],
				'incident_affected' => $data['incident_affected'],
				'incident_picture_name' => $data['incident_name'].'_'.$data['incident_date'],
				'incident_remark' => $data['incident_remark'],
				'incident_status' => $data['incident_status'],
			]);

		echo json_encode($dataSave);

		
	}

	function cekData(){
		//$list = $this->M_Datatable->get_datatables();
		$data = $this->M_Incident->getDataLast();
		echo $data->incident_name;
		// foreach($data as $data_incident){
		// 	echo $data_incident->incident_name;
		// }
		
	}


	function doUpload(){
		$data = $this->M_Incident->getDataLast();
		//echo $data->incident_name;
		$config['upload_path'] = "./uploads";
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name'] = $data->incident_picture_name;
		//$config['overwrite'] = TRUE;
		//$config['encrypt_name'] = TRUE;

		$this->load->library('upload',$config);
		if($this->upload->do_upload("incident_picture_edit")){
			$data = array('upload_data' => $this->upload->data());

			
			$img_name = $data['upload_data']['file_name'];
			$fileExt = pathinfo($img_name, PATHINFO_EXTENSION);
			echo $img_name;
			//$image = $data['upload_data']['file_name'];

			//$result = $this->M_Incident->simpanUpload($img_name);
			//echo json_encode($result);

		}
	}

	function doUploadEdit(){
		//$haha = $this->input->post('jdskjfhksj');
		$data = $this->input->post();
		$config['upload_path'] = "./uploads";
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name'] = $data['incident_picture_name'];
		$config['overwrite'] = TRUE;

		$this->load->library('upload',$config);
		if($this->upload->do_upload("incident_picture_edit")){
			$data = array('upload_data' => $this->upload->data());

			
			$img_name = $data['upload_data']['file_name'];
			$fileExt = pathinfo($img_name, PATHINFO_EXTENSION);
			echo $img_name;
			//$image = $data['upload_data']['file_name'];

			//$result = $this->M_Incident->simpanUpload($img_name);
			//echo json_encode($result);

		}
	}

	function editData(){
		//$row = array();
		$incident_id = $this->input->post('incident_id');
		$data = $this->M_Incident->getDataByID($incident_id);
		$img_name = $data[0]->incident_picture_name;
		$glob_find = glob("uploads/".$img_name."*");


		if (($img_name != null) && ($glob_find)){
			// $img_name = $glob_find[0];
			$data[0]->incident_picture_preview = $glob_find[0];
			//$data[0]->incident_picture_name = $glob_find[0];

		} else if (($img_name != null) && ($glob_find == null)){
			$data[0]->incident_picture_preview = null;
		} else {
			$data[0]->incident_picture_preview = null;
		}

		//echo $img_name;
		//$data[] = $row;
		echo json_encode($data);
	}

	

	function updateData(){
		//$incident_id = $this->input->post('incident_id');
		$img_name_update;
		$data = $this->input->post();

		// $img_name_old = $data['incident_picture_name'];

		// $img_name_new = $data['incident_name'].'_'.$data['incident_date'];

		// if ($img_name_old == $img_name_new) {
		// 	$img_name_update = $img_name_old;
		// } else {
		// 	// $glob_find = glob("uploads/".$img_name_old."*");

		// 	// rename(, $img_name_new);
		// 	$img_name_update = $img_name_new;
		// }

		//echo $img_name_old;

		$dataAll = [
				'incident_name' => $data['incident_name'],
				'incident_date' => $data['incident_date'],
				'incident_time_begin' => $data['incident_time_begin'],
				'incident_time_end' => $data['incident_time_end'],
				'incident_location' => $data['incident_location'],
				'incident_detail' => $data['incident_detail'],
				'incident_affected' => $data['incident_affected'],
				'incident_picture_name' => $data['incident_picture_name'],
				'incident_remark' => $data['incident_remark'],
				'incident_status' => $data['incident_status'],
		];

		$dataSave = $this->M_Incident->updateData($data['incident_id'],$dataAll);
		echo $data['incident_id'];
		//echo $img_name_update;

		echo json_encode($dataSave);
	}

	//Ambil nama gambar dari DB
	function ambilDataGambar(){
		$incident_id = $this->input->post('incident_id');
		$img_name = $this->M_Incident->getImageNameByID($incident_id)->incident_picture_name;
		$glob_find = glob("uploads/".$img_name."*");
		// if ($img_name != null){
		// 	$glob_find = glob("uploads/".$img_name."*");
		// 	$data = $glob_find[0];
		// 	//echo $data;
		// } else {
		// 	$data = null;
		// 	echo $data;
		// }
		
		if (($img_name != null) && ($glob_find)){
			$data = $glob_find[0];

		} else if (($img_name != null) && ($glob_find == null)){
			$data = null;
		} else {
			$data = null;
		}

		/* Untuk cek ekstensi */
		// if (isset($IMG) && !empty($IMG)) {
		//     $imageType = "png";

		//     if (strpos($IMG, ".png") === false) {
		//         $imageType = "jpg";
		//     }
		// }

		echo json_encode($data);
	}

	function cekEkstensi(){
		$incident_id = "108";
		$data = $this->M_Incident->getImageNameByID($incident_id)->incident_picture_name;

		// $this->load->library('image_lib');
		// $img = "uploads/".$data;
		// $img_ext = $this->image_lib->explode_name($img);

		// echo $img;
		// echo $img_ext['ext'];

		// $path = "uploads/";
		// echo $path . $data .'*';
		$glob_find = glob("uploads/".$data."*");
		echo $glob_find[0];
		// if(unlink($glob_find[0])){
		// 	echo "Berhasil";
		// }
		//print_r(glob("uploads/Kejadian OOO_2022-11-04*"));
		//echo glob($path . $data .'*');

		// if(unlink(realpath(glob($path . $data .'*')))){
		// 	echo "berhasil";
		// }else {
		// 	echo "error";
		// }

		// $incident_id = $this->input->post('incident_id');
		// $data = $this->M_Incident->getImageNameByID($incident_id);

		//echo json_encode($data);
	}

	// function get_kategori(){
	// 	$x['data'] = $this->M_Kategori->get_kategori();
	// 	$this->load->view('V_Kategori',$x);
	// }

	// function get_subkategori(){
	// 	$id = $this->input->post('id');
	// 	$data = $this->M_Kategori->get_subkategori($id);
	// 	echo json_encode($data);
	// }

	function export_to_excel(){
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		//variabel untuk menampung pengaturan style dari header tabel
		$style_col = [
			'font' => ['bold' => true],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // set text di tengah horizontal (center)
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // set text di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // set border top dengan garis tipis
				'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // set border right dengan garis tipis
				'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // set border bottom dengan garis tipis
				'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // set border kiri dengan garis tipis
			]
		];

		//variabel untuk menampung pengaturan style dari isi tabel
		$style_row = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
			],
			'borders' => [
				'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
			]
		];

		$sheet->setCellValue('A1', "Incident List");
		$sheet->mergeCells('A1:J1');
		$sheet->getStyle('A1')->getFont()->setBold(true);
		$sheet->getStyle('A1')->getFont()->setSize(16);

		//Header Table
		$sheet->setCellValue('A3',"No");
		$sheet->setCellValue('B3',"Incident Name");
		$sheet->setCellValue('C3',"Incident Date");
		$sheet->setCellValue('D3',"Incident Time Begin");
		$sheet->setCellValue('E3',"Incident Time End");
		$sheet->setCellValue('F3',"Incident Location");
		$sheet->setCellValue('G3',"Incident Detail");
		$sheet->setCellValue('H3',"Incident Affected");
		$sheet->setCellValue('I3',"Incident Remark");
		$sheet->setCellValue('J3',"Incident Status");

		//Apply style Header
		$sheet->getStyle('A3')->applyFromArray($style_col);
		$sheet->getStyle('B3')->applyFromArray($style_col);
		$sheet->getStyle('C3')->applyFromArray($style_col);
		$sheet->getStyle('D3')->applyFromArray($style_col);
		$sheet->getStyle('E3')->applyFromArray($style_col);
		$sheet->getStyle('F3')->applyFromArray($style_col);
		$sheet->getStyle('G3')->applyFromArray($style_col);
		$sheet->getStyle('H3')->applyFromArray($style_col);
		$sheet->getStyle('I3')->applyFromArray($style_col);
		$sheet->getStyle('J3')->applyFromArray($style_col);

		//Fungsi get_data dari M_Incident
		$incident = $this->M_Incident->getData();

		$no = 1; //untuk penomoran tabel
		$numrow = 4; //set baris pertama untuk isi tabel adalah baris ke-4
		foreach ($incident as $data) { //looping variabel $incident
			$sheet->setCellValue('A'.$numrow, $no);
			$sheet->setCellValue('B'.$numrow, $data->incident_name);
			$sheet->setCellValue('C'.$numrow, $data->incident_date);
			$sheet->setCellValue('D'.$numrow, $data->incident_time_begin);
			$sheet->setCellValue('E'.$numrow, $data->incident_time_end);
			$sheet->setCellValue('F'.$numrow, $data->incident_location);
			$sheet->setCellValue('G'.$numrow, $data->incident_detail);
			$sheet->setCellValue('H'.$numrow, $data->incident_affected);
			$sheet->setCellValue('I'.$numrow, $data->incident_remark);
			$sheet->setCellValue('J'.$numrow, $data->incident_status);

			//apply style ke baris data
			$sheet->getStyle('A'.$numrow)->applyFromArray($style_row);
			$sheet->getStyle('B'.$numrow)->applyFromArray($style_row);
			$sheet->getStyle('C'.$numrow)->applyFromArray($style_row);
			$sheet->getStyle('D'.$numrow)->applyFromArray($style_row);
			$sheet->getStyle('E'.$numrow)->applyFromArray($style_row);
			$sheet->getStyle('F'.$numrow)->applyFromArray($style_row);
			$sheet->getStyle('G'.$numrow)->applyFromArray($style_row);
			$sheet->getStyle('H'.$numrow)->applyFromArray($style_row);
			$sheet->getStyle('I'.$numrow)->applyFromArray($style_row);
			$sheet->getStyle('J'.$numrow)->applyFromArray($style_row);

			$no++;
			$numrow++;
		}

		//set width kolom
		// $sheet->getColumnDimension('A')->setWidth(5);
		// $sheet->getColumnDimension('B')->setWidth(15);
		// $sheet->getColumnDimension('C')->setWidth(10);
		// $sheet->getColumnDimension('D')->setWidth(10);
		// $sheet->getColumnDimension('E')->setWidth(10); 	
		// $sheet->getColumnDimension('F')->setWidth(15);
		// $sheet->getColumnDimension('G')->setWidth(30);
		// $sheet->getColumnDimension('H')->setWidth(10);
		// $sheet->getColumnDimension('I')->setWidth(10);
		// $sheet->getColumnDimension('J')->setWidth(10);

		//$sheet->getDefaultColumnDimension()->setWidth(-1);

		for($i = 'A'; $i<=$sheet->getHighestColumn(); $i++){
			$sheet->getColumnDimension($i)->setAutoSize(true);
		}

		//set height semua kolom
		$sheet->getDefaultRowDimension()->setRowHeight(-1);

		//set orientasi kertas
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

		//set judul file excel
		$sheet->setTitle("Incident List Report");

		//Proses file excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="Incident List Report.xlsx"'); //set nama excelnya
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');

	}
}