<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <title>rxstars Step Two</title>
</head>
<body>

<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1);
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

$file = 'file.txt';

$handle = fopen($file, "r");
if ($handle) {
    $i = 0;
    // while (($i<3) && (($line = fgets($handle)) !== false)) {
    while ((($line = fgets($handle)) !== false)) {
        $AC->get(trim($line));
        $i++;
    }

    fclose($handle);
} else {
    echo "error opening the file.";
}

$AC->execute(2);

function callback_function($response, $info, $request)
{
  static $ok = 0;
  static $er = 0;
  if($info['http_code']!==200)
      {
          AngryCurl::add_debug_msg(
              "->\t" .
              ++$er .
              "\tFAILED\t" .
              $info['http_code'] .
              "\t" .
              $info['total_time'] .
              "\t" .
              $info['url']
          );
      }else{
          AngryCurl::add_debug_msg(
              "->\t" .
              ++$ok .
              "\t__OK__\t" .
              $info['http_code'] .
              "\t" .
              $info['total_time'] .
              "\t" .
              $info['url']
          );
              $html = str_get_html($response);
              if (isset($html->find('article')) && !empty($html->find('article'))) {
                foreach ($html->find('article') as $article) {
                  $item['source']         = $info['url'];
                  if (!empty($article)) {
                    $item['title']          = trim($article->find('.entry-title',0)->innertext);
                    $script = $article->getElementsByTagName('script',0);
                    if(isset($script->innertext) && !empty($script)){
                      preg_match('/var myurl = "(.*)";/', $script->innertext, $matches);
                      $item['url']      = isset($matches[1])?$matches[1]:'';
                    }
                    $item['image']          = trim($article->find('.thumbnail', 0)->find('img',0)->src);
                    $item['annotation']     = trim($article->find('.entry-content', 0)->find('p',0)->plaintext);
                    $item['body']           = trim($article->find('.entry-content', 0)->innertext);
                      // $item['body']           = $article->plaintext;
                      // $item['html']           = $article->innertext;
                  }
                $json = json_encode(['Pharmacy'=>$item],JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                file_put_contents('import2.txt', $json."\n",FILE_APPEND| LOCK_EX);
              // echo "<br>";
              }
            }


      }
}
?>

</body>
</html>
