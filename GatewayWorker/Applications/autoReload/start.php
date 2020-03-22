<?php
use Workerman\Worker;
use Workerman\Lib\Timer;

// watch Applications catalogue

// worker
$webDirSource = dirname(dirname(dirname(__FILE__)));
$worker = new Worker();
$worker->name = 'FileMonitor';
$worker->reloadable = false;
$worker->onWorkerStart = function()
{
    global $webDirSource;
    Timer::add(300, 'check_files_remove', $webDirSource);
};

// check files func
function check_files_remove($webDirSource)
{
//    $files = [];
//    $dateTime = date("Ymd",strtotime("-1 day"));
//    if(is_dir($webDirSource)) {
//        if($files = scandir($webDirSource)) {
//            $files = array_slice($files,2);
//            foreach($files as $file) {
//                if ($file < $dateTime) {
//                    rmdir($webDirSource  . $file);
//                }
//            }
//        }
//    }

    $pdo = new PDO('sqlite:' . $webDirSource . '/sql');
    $data = $pdo->query("select * from file_info where addtime < '".(time() - 60*60*2+5)."'")->fetchAll();
    foreach($data as $item){
        if(is_file($item['path'])){
            unlink($item['path']);
        }
        $pdo->exec("delete from file_info where id=".$item['id']);
    }
    $pdo = null;
    return;
}