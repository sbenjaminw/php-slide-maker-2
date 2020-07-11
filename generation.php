<?php

	require_once("render_engine.php");

	if(!isset($_FILES)){
		header("location:http://localhost/projects/slide-generator/index.html");
	}
	
	if(!isset($_POST)){
		header("location:http://localhost/projects/slide-generator/index.html");
	}
	
	$textcontent = $_POST["txtContents"];
	$bg = $_POST["txtTitleSlide"];
	
	//var_dump($_FILES["upldFiles"]);
	//echo "sent!";
		
	//	Instance of render engine.
	$re = new RenderEngine(
		$_FILES["upldFiles"],
		$textcontent,
		$bg
	);
		
	$re->MakeSVG();
	
		


/*
	if(isset($_POST["txtContents"])){
		
		if(isset($_POST["upldFiles"])){
			
			$validflag = CheckValidFileTypes();
			//$strip_files = StripFiles();
			
			if($validflag == true){
				
				if(strlen($_POST["txtContents"]) > 0){
					
					//	Split the text into sentances.
					$sentances = preg_split('/\r\n|[\r\n]/', $_POST["txtContents"]);
									
					$re = new RenderEngine($sentances, $_POST["upldFiles"], $_POST["txtTitleSlide"]);
					$re->MakeSVG();
					
				}
				
			}
			
			
		}
		
	} else {
		
		header("location:http://localhost/projects/automating-svg/index.html");
		
	}	
	
	//	Checks for valid file types.
	function CheckValidFileTypes(){
		
		//	The end flag. By default is false.
		$valid_files = false;
		
		//	Allowed file types.
		$allowed_types = array("png", "gif", "jpg");
			
		//	For each uploaded file, check the path extention. If it is in the 
		//	array, then change valid flag to true. otherwise, break.
		foreach($_POST["upldFiles"] as $file){
			
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			
			if (!in_array($ext, $allowed_types)) {
				
				break;
			
			} else {
				
				$valid_files = true;
				
			}
			
		}
		
		//	If the valid flag is true, then generate the files. 
		//	If not, redirect.
		if($valid_files == true){
			
			return true;
			
		} else {
			
			header("location:http://localhost/projects/automating-svg/index.html");
			
		}
	
	}
	

*/
	
?>