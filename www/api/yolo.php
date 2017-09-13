
<?php
//Shorten
define('DS',DIRECTORY_SEPARATOR);
$run_cmd = './darknet detector train cfg/voc.data cfg/yolo-voc.cfg backup/yolo-voc.backup';
$http_base_path = '/var/www/html/';
$input_images_relative_path = 'yolo/inputimages';
$output_images_relative_path = 'yolo/outputimages';
$input_images_base_path = $http_base_path.$input_images_relative_path;
$output_images_base_path = $http_base_path.$output_images_relative_path;
#$hostname = $_SERVER['SERVER_ADDR'];
$hostname='localhost';
$port = $_SERVER['SERVER_PORT'];
$input_images_web_path = 'http://'.$hostname.":".$port.'/'.$input_images_relative_path;
$output_images_web_path = 'http://'.$hostname.":".$port.'/'.$output_images_relative_path;
/**
 * Check exec and demo.py command
 */
if( ! function_exists("exec")){
	$response['error']= 'Error: php exec not available, safe mode?';
	respond($response);
}
if( empty(run('python2 --version 2>&1')) ){
	$response['error']= 'Error: python2 command not found, is it installed and in your PATH?';
	respond($response);
}
if( ! file_exists('/app/Yolo/darknet') ){
	$response['error']= 'Error:  /app/Yolo/darknet not found, please make sure this is installed';
	respond($response);
}
/**
 * Check POSTED data.
 */
if( empty($_POST['image']) ){
	$response['error']= 'Error: No image data recieved. Please send a base64 encoded image';
	respond($response);
}
/**
 * Check POSTED data.
 */
if( empty($_POST['imagename']) ){
	$response['error']= 'Error: No imagename data recieved. Please send name of image file';
	respond($response);
}
/**
 * Save image to disk (tmp)
 */
$curtime = date('Ymd-Hisu');
$imagename = $_POST['imagename'];
$inputimage_full_path = $input_images_base_path.DS.$curtime.'-'.$imagename;
$outputimage_full_path = $output_images_base_path.DS.$curtime.'-output-'.$imagename;
$inputimage_full_web_path = $input_images_web_path.DS.$curtime.'-'.$imagename;
$outputimage_full_web_path = $output_images_web_path.DS.$curtime.'-output-'.$imagename;
if( ! file_put_contents($inputimage_full_path, base64_decode($_POST['image']) ) ){
	$response['error']= "Error: Failed saving image to disk path ($inputimage_full_path), please check webserver permissions.";
	respond($response);
}
/**
 * Run demo.py command on image
 */
$result = run($run_cmd.' '.$inputimage_full_path.' '.$outputimage_full_path);
/**
 * Remove image
 */
//unlink('tmp'.DS.'check.jpg');
/**
 * Check result.
 */
if( empty( $result[0] ) ){
	$response['error']= 'Error: frcnn returned no result';
	respond($response);
}
array_push($result, 'input_image_path='.$inputimage_full_web_path);
array_push($result, 'output_image_path='.$outputimage_full_web_path);
//Add results to response
foreach ($result as &$value) {
	if ($value != '') {
		$arr1 = explode('=',$value);
		$resultmap[$arr1[0]] = $arr1[1];
	}
}
$response['data'] =  $resultmap;
//Respond with results
respond($response);
/**
 * Aux functions
 */
//Sets headers and responds json
function respond($response){
	header('Access-Control-Allow-Origin: *');
	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: application/json');
	echo json_encode($response);
	exit;
}
//Runs command and returns output
function run($command){
	$output = array();
	exec($command,$output);
	return $output;
}
?>
