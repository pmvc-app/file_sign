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
    private $_go;
    private $_log;

    function log()
    {
        $data = func_get_args();
        if ($this->_log) {
            echo join('',$data)."\n"; 
        } else {
            call_user_func_array(
                [ $this->_go, 'set'],
                $data
            );
        }
    }

    function index($m, $f)
    {
        $path = $f['path'];
        $pattern = $f['pattern'];
        $exclude = $f['exclude']; 

        $this->_go = $m['dump']; 
        $this->_log = $f['log'];

        $this->log('Run: ', $path);
        $this->log('Pattern: ', $pattern);
        $this->log('log: ', $this->_log ? 'enable' : 'disable');

        $files = PMVC\plug('file_list', [
                'hash' => true,
                'maskKey' => $path,
                'exclude' => $exclude
        ])->ls($path, $pattern);
        ksort($files);
        foreach ($files as $k=>$v) {
            $hash = \PMVC\value($v, ['hash']) ?: '';
            if ($hash) {
                $hash = ','.$hash;
            }
            $this->log($k.$hash);
        }
        return $this->_go;
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
