<?php
	
	//	The stage, image and text objects
	require_once("./src/svg_stage.php");
	require_once("./src/svg_image.php");
	require_once("./src/svg_text.php");
	
	//	The svg writer
	require_once("./src/svg_writer.php");
	
	//	The text file reader.
	//require_once("./src/svg_word_reader.php");
	
	//	The file upload handler.
	require_once("./src/svg_uploadhandler.php");
	
	
	/*	The render engine class
		Generates the SVGs and then converts them into PNG files. */
	class RenderEngine {
		
		//	The actual files sent 
		private $filesSent;
		
		//	The text content of the slides 
		private $textContent;
		
		//	The title slide 
		private $slideBG;
		
		//	Where to put user uploaded images.
		private $userUploadedImageLocation;
		
		//	The types of files to allow.
		private $allowedTypes;
		
		//	Secondary file names.
		private $secondaryFileNames;
		
		//	Are there multiple images?
		private $containsMoreThanOneImage;
		
		
		//	First, determine if the files sent are correct.
		public function __construct($files, $text, $bg){
			
			//	The types of images to allow.
			$this->allowedTypes = array(
				"image/png", 
				"image/gif", 
				"image/jpg"
			);
			
			$this->filesSent = $files;
			
			$this->userUploadedImageLocation = "D:\\Software\\WAMPServer\\www\\projects\\slide-generator\\images\\tmp_img\\";
			
			$this->textContent = explode("\r\n", $text);
			$this->slideBG = $bg;
			$this->secondaryFileNames = array();
			$this->containsMoreThanOneImage = false;
				
		}
		
		
		/*	Determine valid secondary image file.
			If $fileStr contains something like "@img-pics1.png" , determine if it has a 
			a valid file extension. */
		public function SecondaryImageFinder($fileStr){
			
			//	Flag to return. By default is false.
			$isvalid = false;
			
			//	These are the allowed file types.
			$allowedFileTypes = array("png", "jpg", "gif");
				
			if(strpos($fileStr, "@img-") > -1){
				
				//	Explode the string 
				$exploded = explode("@img-", $fileStr);
				
				/*	Loop through allowed file types.
					If there is a match to the allowed file type, 
					then sest the is valid flag to true. */
				foreach($allowedFileTypes as $ftype){
					if(strpos($exploded[1], $ftype)){
						$isvalid = true;
						array_push($this->secondaryFileNames, $exploded[1]);
					}
				}
				
			}
			
			return $isvalid;
			
		}
		
		//	Generate the SVG.
		public function MakeSVG(){
			
			//	The slides will have this name and the counter added on to them.
			$svgFileName = "svgsGeneratedByPhp";
			$svgCounter = 0;
			
			//	Instance of file upload handler
			$uploadHandler = new FileUploadHandler();
			
			if($uploadHandler->UploadFileChecker($_FILES) != false){
				//	Upload the files.
				$uploadHandler->UploadFileTempServer($_FILES);
			}
			
			//	Check each sentance if there are more than one image.
			foreach($this->textContent as $sentance){
				if(!$this->SecondaryImageFinder($sentance) == false){
					$this->containsMoreThanOneImage = true;
				}
			}
			
			//	Start generating the SVGs
			foreach($this->textContent as $sentance){
				
				//	The file name, plus the counter.
				$namePlusSVG = "/images/svg/" . $svgFileName . $svgCounter . ".svg";
			
				//	Set the stage.
				$svgstage = new SVGStage(800, 1024, $namePlusSVG);
				$svgStartTag = $svgstage->GenerateOpenString();
				
				$bannerOBJ = "";
				$bannerTag = "";
				
				$slideImages = "";
				
				$containsText = false;
				
				//	If the string contains @ symbol, then it is an image.
				if($this->SecondaryImageFinder($sentance) == false){
					
					$containsText = true;
					
					$bannerPath = "D:\\Software\\WAMPServer\\www\\projects\\slide-generator\\images\\tmp_img\\" . $this->slideBG;
					
					$bannerOBJ = new SVGImage(1024, 300, 0, 0, $bannerPath);
					$bannerTag = $bannerOBJ->GenerateImageString();
					
					//	The text.
					$svgtext = new SVGText(100, 450, $sentance, 50);
					$textTag = $svgtext->GenerateTextString();
					
				} else {
					
					//$bannerPath = "http://localhost/projects/slide-generator/images/tmp_img/" . $this->slideBG;
					$bannerPath = "D:\\Software\\WAMPServer\\www\\projects\\slide-generator\\images\\tmp_img\\" . $this->slideBG;
					
					
					$bannerOBJ = new SVGImage(1024, 300, 0, 0, $bannerPath);
					$bannerTag = $bannerOBJ->GenerateImageString();
					
					//	Check if the secondary file matches the sentance.
					foreach($this->secondaryFileNames as $secondaryFile){
						
						$atPlusFile = "@img-" . $secondaryFile;
						
						if($sentance == $atPlusFile){
							
							$siPath = "D:\\Software\\WAMPServer\\www\\projects\\slide-generator\\images\\tmp_img\\" . $secondaryFile;
							
							//$siPath = "http://localhost/projects/slide-generator/images/tmp_img/" . $secondaryFile;
							$secondaryImage = new SVGImage(600, 800, 200, 100, $siPath);
							$slideImages = $secondaryImage->GenerateImageString();
							
							break;
							
						}
						
					}
					
				}
				
				$svgEnd = $svgstage->GenerateCloseString();
				
				$totalSvg = "";
				
				if($containsText == false){
					$totalSvg = $svgStartTag . $slideImages . $bannerTag . $svgEnd;
				} else {
					
					$totalSvg = $svgStartTag . $bannerTag . $textTag . $svgEnd;
				}
				
				$namePlusSVG = "D:\\Software\\WAMPServer\\www\\projects\\slide-generator\\images\\svg\\" . $svgFileName . $svgCounter . ".svg";
				
				//	SVG Writer Object
				$svgwriter = new SVGWriter($namePlusSVG, $totalSvg);
				
				//	Write the SVG file.
				$svgwriter->WriteFile($namePlusSVG, $totalSvg);
				
				$svgCounter++;
				
			}
			
			$this->PNGFileConversion();
			
		}
		
		//	Converts SVG to PNGs
		public function PNGFileConversion(){
			
			//var_dump($this->fileArray);
			
			$fileArray = array();
			
			$svgLocation = "D:\\Software\\WAMPServer\\www\\projects\\slide-generator\\images\\svg\\";
			
			if(is_dir($svgLocation)) {
				
				if($location = opendir($svgLocation)) {
										
					while(($file = readdir($location)) !== false) {
						
						if(strlen($file) > 3){
							
							$newFile = explode(".", $file);
							
							array_push($fileArray, $newFile[0]);
							
						}
						
					}
					
					closedir($location);
					
				}
			}
			
			//var_dump($fileArray);
			//$shellcmds = array();
			
			foreach($fileArray as $file2){
					
				$cmd =  '"D:\Software\Inkscape\inkscape.exe" -z -f "' . "D:\\Software\\WAMPServer\\www\\projects\\slide-generator\\images\\svg\\" . $file2 . '.svg" -w 1024 -j -e "' . "D:\\Software\\WAMPServer\\www\\projects\\slide-generator\\images\\png\\" . $file2 . '.png"';
				
				echo $cmd;
				echo "<br/><br/>";
				
				shell_exec($cmd);
				
			}
			
		}
		
	}
?>