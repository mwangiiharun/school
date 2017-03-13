<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update extends Admin_Controller {
/*
| -----------------------------------------------------
| PRODUCT NAME: 	INILABS SCHOOL MANAGEMENT SYSTEM
| -----------------------------------------------------
| AUTHOR:			INILABS TEAM
| -----------------------------------------------------
| EMAIL:			info@inilabs.net
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY INILABS IT
| -----------------------------------------------------
| WEBSITE:			http://inilabs.net
| -----------------------------------------------------
*/
	function __construct() {
		parent::__construct();
		$language = $this->session->userdata('lang');
		$this->load->model("update_m");
		$this->lang->load('update', $language);
		if(config_item('demo')) {
			$this->session->set_flashdata('error', 'In demo update module is disable!');
			redirect(base_url('dashboard/index'));
		}
	}

	public function index()
	{

		if ($_POST) {
			$data = $this->input->post('data');
			// dd($data);
			if(isset($data['filename'])) {
				$file = APPPATH . 'libraries/upgrade/'.$data['filename'].'.php';
				if(file_exists($file) && is_file($file)) {
					@include_once($file);
				}
			}

	        echo base_url('signin/signout');

		} else {
			$this->data["subview"] = "update/index";
			$this->load->view('_layout_main', $this->data);
		}

	}

	public function upload() {
		if($_FILES["file"]['name'] !="") {
			$filename = $_FILES['file']['name'];

			$config['upload_path'] = "./uploads/images";
			$config['allowed_types'] = "zip";
			$config['file_name'] = $filename;
			$config['max_size'] = '1102400';
			$this->load->library('upload', $config);
			if(!$this->upload->do_upload("file")) {
				$error = $this->upload->display_errors();
				echo $error;
			} else {
				$data = array("upload_data" => $this->upload->data());
				$file_path = $config['upload_path'].'/'.$filename;
				if (file_exists($file_path)) {
				    $zip = new ZipArchive;
					if ($zip->open($file_path) === TRUE) {
					    $zip->extractTo($config['upload_path']);
					    $zip->close();

					    $explode = explode('.', $file_path);
					    $path = '.'.$explode[1].'/';
					    // exec('chmod -R 777 '.$path);


					    $destination = FCPATH;
						$destination = rtrim($destination,'/');
						// dd($destination);
					    $this->smartCopy($path , $destination);
					    $string = file_get_contents($path.'inilabs.json');
						$json_a = json_decode($string, true);

						echo $string;


					} else {
					    echo 'failed';
					    echo "Extract zip files failed.";
					}
				} else {
					echo "File not found.";
				}

			}
		} else {
			echo 'File missing or check your upload file MB permission of php.ini file';
		}
	}


    function smartCopy($source, $dest, $options=array('folderPermission'=>0777,'filePermission'=>0777)) {
        $result=false;

        if (is_file($source)) {
            if ($dest[strlen($dest)-1]=='/') {
                if (!file_exists($dest)) {
                    cmfcDirectory::makeAll($dest,$options['folderPermission'],true);
                }
                $__dest=$dest."/".basename($source);
            } else {
                $__dest=$dest;
            }
            $result=copy($source, $__dest);
            @chmod($__dest,$options['filePermission']);

        } elseif(is_dir($source)) {
            if ($dest[strlen($dest)-1]=='/') {
                if ($source[strlen($source)-1]=='/') {
                    //Copy only contents
                } else {
                    //Change parent itself and its contents
                    $dest=$dest.basename($source);
                    @mkdir($dest);
                    @chmod($dest,$options['filePermission']);
                }
            } else {
                if ($source[strlen($source)-1]=='/') {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    @chmod($dest,$options['filePermission']);
                } else {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    @chmod($dest,$options['filePermission']);
                }
            }

            $dirHandle=opendir($source);
            while($file=readdir($dirHandle))
            {
                if($file!="." && $file!="..")
                {
                     if(!is_dir($source."/".$file)) {
                        $__dest=$dest."/".$file;
                    } else {
                        $__dest=$dest."/".$file;
                    }
                    $result=$this->smartCopy($source."/".$file, $__dest, $options);
                }
            }
            closedir($dirHandle);

        } else {
            $result=false;
        }
        return $result;
    }

}
