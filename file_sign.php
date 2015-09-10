<?php
/**
 * usage
 * #for test
 * ./convmv.php --path=/yourpath
 * #for run
 * ./convmv.php --path=/yourpath --notest
 */
ini_set("memory_limit","256M");
include_once('vendor/autoload.php');
PMVC\Load::plug();
$params = PMVC\plug('cmd')->commands($argv);

$mypath = $params['path'];
$exclude = $params['exclude'];

if(is_dir($mypath)){
    $path = $mypath;
}else{
    $mypath = pathinfo($mypath);
    $path = $mypath['dirname'];
    $pattern = $mypath['basename'];
}

if( empty($path) || !realpath($path) )
{
    exit('No path found'."\n");
}
if(empty($pattern)){
    $pattern='*';
}
echo "Run in ".$path."\n";
echo "File pattern: ".$pattern."\n";
$files = PMVC\plug('file_list',array(
        'hash' => true,
        'prefix' => $path,
        'exclude' => $exclude
    ))->ls($path,$pattern);
ksort($files);
foreach ($files as $k=>$f) {
    $hash = $f['hash'] ?: '';
    if ($hash) {
        $hash = ','.$hash;
    }
    echo $k.$hash."\n";
}

