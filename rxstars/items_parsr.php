<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <title>lal</title>
</head>
<body>

<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
# Setting time and memory limits
ini_set('max_execution_time',0);
ini_set('memory_limit', '128M');

define('AC_DIR', dirname(__FILE__));

    # Including classes
require_once( AC_DIR . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'RollingCurl.class.php');
require_once( AC_DIR . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'AngryCurl.class.php');
require_once( AC_DIR . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'simple_html_dom.php');


function read_and_delete_first_line($filename) {
  $file = file($filename);
  $output = $file[0];
  unset($file[0]);
  file_put_contents($filename, $file);
  return $output;
}


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
$itemsFile = 'items.txt';
$domian = 'http://www.pharmacyreviewer.com';
$pagesUrls = 'http://www.pharmacyreviewer.com/reviews/all-online-pharmacies/Page-';

$itemUrls = array();
$handle = fopen($file, "r");
if ($handle) {
    $i = 0;
    while (($i<3) && (($line = fgets($handle)) !== false)) {
    // while ((($line = fgets($handle)) !== false)) {
        // process the line read.
        $itemUrls[] = $line;
        $i++;
    }

    fclose($handle);
} else {
    // error opening the file.
} 

var_dump($itemUrls);
foreach ($itemUrls as $url) {
   $AC->get($url);
}
// for($i=2;$i<49;$i++)
//    $AC->get('http://www.pharmacyreviewer.com/reviews/all-online-pharmacies/Page-'.$i);

$AC->execute();

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
            $tempCntent['Pharmacy'] = array();

            foreach($html->find('div.item') as $article) {
                $item['title']          = trim($article->find('.contentheading', 0)->children(0)->plaintext);
                $item['image']          = trim($article->find('.contentheading', 0)->children(1)->plaintext);
                $item['image_large']          = $domian.trim($article->find('img.jrMediaPhoto', 0)->src);
                //jrListingMainImage
                $item['site_link']      = trim($article->find('.redirectUrl a', 0)->plaintext);
                $item['annotation']     = trim($article->find('.jrCustomFields', 0)->plaintext);
                $item['body']           = trim($article->find('.jrListingFulltext', 0)->plaintext);
                $articles[]['Pharmacy'] = $item;
            }

            // foreach($html->find('.contentheading') as $el){
            //     $tempCntent['title']=trim($el->children(0)->plaintext);
            //     $tempCntent['image']=trim($el->children(1)->plaintext);
            // }
            // foreach($html->find('.redirectUrl a') as $el){
            //     $tempCntent['site_link']=trim($el->plaintext);
            // }
            // foreach ($html->find('.jrCustomFields') as $el) {
            //     $tempCntent['annotation']=trim($el->plaintext);
            //     # code...
            // }
            // foreach ($html->find('.jrListingFulltext') as $el) {
            //     $tempCntent['body']=$tempCntent['annotation'].'<br>'.trim($el->plaintext);
            //     # code...
            // }
            var_dump($articles);
            //.contentheading span[itemprop]
            $content[] = $tempCntent;

//     if(count($content) == 500){
//      echo "<hr>".count($content);
//      $array = array_map ("rtrim", $content);
//      $str = implode ("\n", $array);
//      file_put_contents ("file.txt", $str);
// //end... if more then 500 rows 
//          die('all done');
        // }    
    }
}
// foreach ($content as $value) {
//     $jsone[] = json_encode($value);
// }
// $str = implode("\n", $jsone);
// file_put_contents($itemsFile, $str);
// $image = file_get_contents('http://www.pharmacyreviewer.com/media/reviews/photos/thumbnail/300x300s/05/a2/8e/55_alldaychemist.com_1182544477.jpg');
// file_put_contents('image.jpg', $image);
?>

</body>
</html>
