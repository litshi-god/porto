<?php
/**
 * FINAL FULL API HANDLER (ALL CASES - STABLE)
 */

// ==========================
// OUTPUT BUFFER
// ==========================
ob_start();

// ==========================
// LOAD CORE
// ==========================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/api/AAPanelAPI.php';

// ==========================
// CLEAN OUTPUT (ANTI JSON ERROR)
// ==========================
ob_clean();

header('Content-Type: application/json; charset=utf-8');

// ==========================
// JSON RESPONSE HELPER
// ==========================
function res($data, int $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// ==========================
// INIT
// ==========================
$user   = Auth::check();
$action = $_REQUEST['action'] ?? '';
$api    = new AAPanelAPI();
$ip     = $_SERVER['REMOTE_ADDR'] ?? '';

// ==========================
// PUBLIC
// ==========================
if ($action === 'csrf_token') {
    res(['status'=>true,'token'=>Auth::csrf()]);
}

if ($action === 'login') {
    doLogin();
}

// ==========================
// AUTH CHECK
// ==========================
if (!$user) {
    res(['status'=>false,'msg'=>'Unauthenticated','redirect'=>'/admin'], 401);
}

// ==========================
// CSRF CHECK
// ==========================
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','HEAD','OPTIONS'])) {
    $csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!Auth::verifyCsrf($csrf)) {
        res(['status'=>false,'msg'=>'Invalid CSRF'],403);
    }
}

// ==========================
// ROUTER
// ==========================
try {

switch ($action) {

// ==========================
// AUTH
// ==========================
case 'logout':
    Auth::logout();
    res(['status'=>true]);

// ==========================
// DASHBOARD
// ==========================
case 'dashboard':
    res([
        'status'=>true,
        'data'=>[
            'system'=>$api->getSystemInfo(),
            'disk'=>$api->getDiskInfo(),
            'network'=>$api->getNetWork()
        ]
    ]);

// ==========================
// FILE MANAGER
// ==========================
case 'file.list':
    $path = $_GET['path'] ?? '/www/wwwroot';
    if (!Auth::canAccessPath($user,$path)) res(['status'=>false,'msg'=>'Access denied']);
    res($api->getDir($path,(int)($_GET['p']??1),100,$_GET['search']??''));

case 'file.content':
    $path = $_GET['path'] ?? '';
    if (!$path) res(['status'=>false,'msg'=>'Path required']);
    if (!Auth::canAccessPath($user,$path)) res(['status'=>false,'msg'=>'Access denied']);
    res($api->getFileContent($path));

case 'file.save':
    if (!Auth::can($user,'can_edit_files')) res(['status'=>false,'msg'=>'Permission denied']);
    $path=$_POST['path']??'';
    if (!Auth::canAccessPath($user,$path)) res(['status'=>false,'msg'=>'Access denied']);
    res($api->saveFileContent($path,$_POST['content']??''));

case 'file.create':
    if (!Auth::can($user,'can_upload')) res(['status'=>false,'msg'=>'Permission denied']);
    $path=$_POST['path']??'';
    if (!Auth::canAccessPath($user,$path)) res(['status'=>false,'msg'=>'Access denied']);
    res($api->createFile($path));

case 'file.mkdir':
    if (!Auth::can($user,'can_upload')) res(['status'=>false,'msg'=>'Permission denied']);
    $path=$_POST['path']??'';
    if (!Auth::canAccessPath($user,$path)) res(['status'=>false,'msg'=>'Access denied']);
    res($api->createDir($path));

case 'file.delete':
    if (!Auth::can($user,'can_delete')) res(['status'=>false,'msg'=>'No permission']);
    $path=$_POST['path']??'';
    if (!Auth::canAccessPath($user,$path)) res(['status'=>false,'msg'=>'Access denied']);
    res($api->deleteFile($path));

case 'file.rename':
    if (!Auth::can($user,'can_edit_files')) res(['status'=>false,'msg'=>'Permission denied']);
    res($api->renameFile($_POST['src']??'',$_POST['dst']??''));

case 'file.copy':
    res($api->copyFile($_POST['src']??'',$_POST['dst']??''));

case 'file.move':
    if (!Auth::can($user,'can_edit_files')) res(['status'=>false,'msg'=>'Permission denied']);
    res($api->moveFile($_POST['src']??'',$_POST['dst']??''));

case 'file.compress':
    res($api->compressFiles(
        json_decode($_POST['files']??'[]',true),
        $_POST['to_path']??'',
        $_POST['name']??'archive',
        $_POST['type']??'zip'
    ));

case 'file.decompress':
    res($api->decompressFile($_POST['file']??'',$_POST['to_path']??'',$_POST['password']??''));

case 'file.chmod':
    res($api->setFilePermission(
        $_POST['path']??'',
        $_POST['mode']??'755',
        $_POST['owner']??'www',
        !empty($_POST['recursive'])
    ));

case 'file.download_url':
    res(['status'=>true,'url'=>$api->getDownloadUrl($_GET['path']??'')]);

// ==========================
// SITES
// ==========================
case 'sites.list':
    res($api->getSites((int)($_GET['p']??1),20,$_GET['search']??''));

case 'sites.create':
    res($api->createSite($_POST));

case 'sites.delete':
    res($api->deleteSite((int)$_POST['id'],$_POST['name'],!empty($_POST['delete_files']),!empty($_POST['delete_db'])));

case 'sites.stop':
    res($api->stopSite((int)$_POST['id'],$_POST['name']));

case 'sites.start':
    res($api->startSite((int)$_POST['id'],$_POST['name']));

case 'sites.domain.list':
    res($api->getSiteDomain((int)$_GET['id']));

case 'sites.domain.add':
    res($api->addDomain((int)$_POST['id'],$_POST['name'],$_POST['domain']));

case 'sites.domain.delete':
    res($api->deleteDomain((int)$_POST['id'],$_POST['name'],$_POST['domain'],(int)($_POST['port']??80)));

case 'sites.ssl.info':
    res($api->getSslInfo((int)$_GET['id'],$_GET['name']));

case 'sites.ssl.set':
    res($api->setSSL((int)$_POST['id'],$_POST['name'],$_POST['key'],$_POST['crt']));

case 'sites.ssl.apply':
    res($api->applyFreeSSL((int)$_POST['id'],$_POST['name'],explode("\n",$_POST['domains'])));

case 'sites.ssl.close':
    res($api->closeSSL((int)$_POST['id'],$_POST['name']));

case 'sites.php_version':
    res($_SERVER['REQUEST_METHOD']==='POST'
        ? $api->setSitePhpVersion($_POST['name'],$_POST['version'])
        : $api->getSitePhpVersion($_GET['name'])
    );

case 'sites.php_list':
    res($api->getPhpVersionList());

case 'sites.backup.list':
    res($api->getSiteBackups((int)$_GET['id']));

case 'sites.backup.create':
    res($api->createBackup((int)$_POST['id']));

case 'sites.backup.delete':
    res($api->deleteBackup((int)$_POST['id']));

case 'sites.logs':
    res($api->getSiteLog($_GET['name'],$_GET['type']??'access'));

// ==========================
// DATABASE
// ==========================
case 'db.list':
    res($api->getDatabases((int)($_GET['p']??1),20,$_GET['search']??''));

case 'db.create':
    res($api->createDatabase(
        $_POST['name'],
        $_POST['username'],
        $_POST['password'],
        $_POST['address']??'%',
        $_POST['remark']??''
    ));

case 'db.delete':
    res($api->deleteDatabase((int)$_POST['id'],$_POST['name']));

case 'db.reset_password':
    res($api->resetDbPassword((int)$_POST['id'],$_POST['name'],$_POST['username'],$_POST['password']));

case 'db.backup':
    res($api->backupDatabase((int)$_POST['id']));

case 'db.phpmyadmin':
    res(['status'=>true,'url'=>$api->getPhpMyAdminUrl()]);

// ==========================
// USERS
// ==========================
case 'users.list':
    res(['status'=>true,'data'=>UserManager::getAll()]);

case 'users.create':
    UserManager::create($_POST);
    res(['status'=>true]);

case 'users.update':
    UserManager::update((int)$_POST['id'],$_POST);
    res(['status'=>true]);

case 'users.delete':
    UserManager::delete((int)$_POST['id']);
    res(['status'=>true]);

// ==========================
// AUDIT
// ==========================
case 'audit.list':
    res(['status'=>true,'data'=>UserManager::getLogs(200,(int)($_GET['user_id']??0))]);

// ==========================
default:
    res(['status'=>false,'msg'=>'Unknown action'],404);

}

} catch (Throwable $e) {

    error_log($e->getMessage());

    res([
        'status'=>false,
        'msg'=>'Server error'
    ],500);
}

// ==========================
// LOGIN
// ==========================
function doLogin() {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        res(['status'=>false,'msg'=>'Isi semua field']);
    }

    $user = UserManager::login($username,$password);

    if (!$user || empty($user['username'])) {
        res(['status'=>false,'msg'=>'Login gagal']);
    }

    Auth::setUser($user);

    res([
        'status'=>true,
        'user'=>[
            'id'=>$user['id'],
            'username'=>$user['username'],
            'role'=>$user['role']
        ]
    ]);
}
