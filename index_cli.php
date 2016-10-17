<?php
ini_set("memory_limit","256M");
$b = new \PMVC\MappingBuilder();
${_INIT_CONFIG}[_CLASS] = 'FileSign';
${_INIT_CONFIG}[_INIT_BUILDER] = $b;
$b->addAction('index', [
    _FORM => 'FileSignVerify'
]);

\PMVC\plug('view_cli',[
    'flush'=>true
]);

class FileSign extends \PMVC\Action
{
    function index($m, $f)
    {
        $path = $f['path'];
        $pattern = $f['pattern'];
        $exclude = $f['exclude']; 

        $go = $m['dump'];
        $go->set('Run', $path);
        $go->set('Pattern', $pattern);
        $files = PMVC\plug('file_list', [
                'hash' => true,
                'maskKey' => $path,
                'exclude' => $exclude
        ])->ls($path, $pattern);
        foreach ($files as $k=>$v) {
            $hash = $v['hash'] ?: '';
            if ($hash) {
                $hash = ','.$hash;
            }
            $go->set($k.$hash);
        }
        return $go;
    }
}

class FileSignVerify extends PMVC\ActionForm
{
    function validate()
    {
        $inputPath = $this['path'];
        if (is_dir($inputPath)) {
            $path = $inputPath;
        } else {
            $inputPathInfo = pathinfo($inputPath);
            $dir = \PMVC\value($inputPathInfo, ['dirname']);
            if (in_array($dir, ['.', '/'])) {
                $dir = null;
            }
            $path = $dir;
            $pattern = $inputPathInfo['basename'];
        }
        if ( empty($path) || !realpath($path) ) {
            return !trigger_error('No path found.');
        } else {
            if(empty($pattern)){
                $pattern='*';
            }
            $this['path'] = $path;
            $this['pattern'] = $pattern;
            return true;
        }
    }
}
