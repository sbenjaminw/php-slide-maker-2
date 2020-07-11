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
	
	//	Instance of render engine.
	$re = new RenderEngine(
		$_FILES["upldFiles"],
		$textcontent,
		$bg
	);
		
	$re->MakeSVG();
	
?>
