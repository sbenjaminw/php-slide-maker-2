<?php
	
	//	This file determines if a file is valid
	//	And then uploads it to server
	class FileUploadHandler {
		
		//	Used for checking $_FILES.
		private $allowedFileTypes;
		
		//	Used for checking @img-1.etc 
		private $allowedFileStringTypes;
		
		//	The location of where to put the user-provided image.
		public $userUploadedImageLocation;
		
		public function __construct(){
			
			$this->allowedFileTypes = array(
				"image/png", 
				"image/gif", 
				"image/jpg"
			);
			
			$this->allowedFileStringTypes = array(
				"png", 
				"jpg", 
				"gif"
			);
			
			$this->userUploadedImageLocation = "D:\\Software\\WAMPServer\\www\\projects\\slide-generator\\images\\tmp_img\\";
			
		}
		
		//	Takes the files and then uploads them to server.
		public function UploadFileTempServer($files){
			
			/*	Loop through the name of the files sent, if the slideBG 
				matches the name then it is the background slide. Move the 
				file. */
			for($i = 0; $i != count($files["upldFiles"]["tmp_name"]); $i++){

				move_uploaded_file(
					$files["upldFiles"]["tmp_name"][$i],
					$this->userUploadedImageLocation . $files["upldFiles"]["name"][$i]
				);
				
			}
			
		}
		
		//	Determine if the file types are allowed.
		public function UploadFileChecker($files){
			
			$isValidUploadFileType = false;
			
			//	For each uploaded file, check the path extention. If it is in the 
			//	array, then change valid flag to true. otherwise, break.
			foreach($files["upldFiles"]["type"] as $file){
				
				//if (!in_array($ext, $allowed_types)) {
				if (!in_array($file, $this->allowedFileTypes)) {
					break;
				} else {
					$isValidUploadFileType = true;
				}
				
			}
			
			return $isValidUploadFileType;
			
		}
		

	
	}
	
?>