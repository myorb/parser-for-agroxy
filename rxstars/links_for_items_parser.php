<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <title>rxstars Zero Step</title>
</head>
<body>

<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
# Setting time and memory limits
ini_set('max_execution_time',0);
ini_set('memory_limit', '128M');

define('AC_DIR', dirname(__FILE__));

    # Including classes
require_once( AC_DIR . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'RollingCurl.class.php');
require_once( AC_DIR . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'AngryCurl.class.php');
require_once( AC_DIR . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'simple_html_dom.php');

$AC = new AngryCurl('callback_function');
$AC->init_console();
//uncomment if you want use proxy
/*$AC->load_proxy_list(
    AC_DIR  . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . 'proxy_list.txt',
    # optional: number of threads
    400,
    # optional: proxy type
    'http',
    # optional: target url to check
    'http://google.com',
    # optional: target regexp to check
    'title>G[o]{2}gle'
);*/
$AC->load_useragent_list( AC_DIR . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . 'useragent_list.txt');

//main CONTEXT BOX *)
$content = array();

$file = 'file.txt';

$domian = 'https://www.rxstars.net/';
$pagesUrl = 'category/reviews/page/';
$totalPages = 119;
$itemUrls = array();

for($i=1;$i<$totalPages;$i++)
   $AC->get($domian.$pagesUrl.$i);

$AC->execute(5);

function callback_function($response, $info, $request)
{
    global $content;
    global $itemUrls;
    global $domian;

if($info['http_code']!==200)
    {
        AngryCurl::add_debug_msg(
            "->\t" .
            $request->options[CURLOPT_PROXY] .
            "\tFAILED\t" .
            $info['http_code'] .
            "\t" .
            $info['total_time'] .
            "\t" .
            $info['url']
        );
    }else{
            $html = str_get_html($response);
            //foreach($html->find('.catalog  .clearfix li') as $div){
                    // $content[] = $div;
            //   var_dump($div->innertext);
            //}
            foreach($html->find('.read-more a') as $el){
                $itemUrls[] = $el->href;
                var_dump( $el->href);
            }
// 	   if(count($content) == 500){
// 		echo "<hr>".count($content);
// 		$array = array_map ("rtrim", $content);
// 		$str = implode ("\n", $array);
// 		file_put_contents ("file.txt", $str);
// //end... if more then 500 rows
//  		die('all done');
		// }
	}
}
echo "<pre> total ";
                var_dump(count($itemUrls));
echo "</pre>";
$str = implode("\n", $itemUrls);
file_put_contents($file, $str);
?>
</body>
</html>
