<?php
/**
 * AAPanel API Client
 * Wrapper resmi untuk AAPanel REST API
 */
class AAPanelAPI {
    private $url;
    private $key;
    private $token;
    private $timeout = 30;

    public function __construct(string $url = '', string $key = '') {
        $this->url = rtrim($url ?: AAPANEL_URL, '/');
        $this->key  = $key  ?: AAPANEL_KEY;
        $this->token = md5(md5($this->key) . time()); // token sementara, akan di-refresh
    }

    // =============================================
    // CORE REQUEST
    // =============================================
    private function request(string $path, array $data = [], string $method = 'POST'): array {
        $timestamp = time();
        $token = md5(md5($this->key) . $timestamp);

        $data['request_token'] = $token;
        $data['request_time']  = $timestamp;

        $url = $this->url . $path;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER         => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_COOKIE         => 'request_token=' . $token,
        ]);

        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            return ['status' => false, 'msg' => "cURL error: $error", 'data' => null];
        }
        if ($httpCode !== 200) {
            return ['status' => false, 'msg' => "HTTP $httpCode", 'data' => null];
        }

        $decoded = json_decode($response, true);
        if ($decoded === null) {
            return ['status' => false, 'msg' => 'Invalid JSON response', 'raw' => $response];
        }
        return $decoded;
    }

    // =============================================
    // SYSTEM
    // =============================================
    public function getSystemInfo(): array {
        return $this->request('/system?action=GetSystemTotal');
    }
    public function getDiskInfo(): array {
        return $this->request('/system?action=GetDiskInfo');
    }
    public function getNetWork(): array {
        return $this->request('/system?action=GetNetWork');
    }

    // =============================================
    // FILE MANAGER
    // =============================================
    public function getDir(string $path = '/', int $page = 1, int $perPage = 100, string $search = ''): array {
        return $this->request('/files?action=GetDir', [
            'path'    => $path,
            'p'       => $page,
            'perPage' => $perPage,
            'showRow' => $perPage,
            'search'  => $search,
            'toPath'  => $path,
        ]);
    }
    public function getFileContent(string $path): array {
        return $this->request('/files?action=GetFileBody', ['path' => $path]);
    }
    public function saveFileContent(string $path, string $content, string $encoding = 'utf-8'): array {
        return $this->request('/files?action=SaveFileBody', [
            'path'     => $path,
            'data'     => $content,
            'encoding' => $encoding,
        ]);
    }
    public function createFile(string $path): array {
        return $this->request('/files?action=NewFile', ['path' => $path]);
    }
    public function createDir(string $path): array {
        return $this->request('/files?action=AddDir', ['path' => $path]);
    }
    public function deleteFile(string $path): array {
        return $this->request('/files?action=DeleteFile', ['path' => $path]);
    }
    public function renameFile(string $src, string $dst): array {
        return $this->request('/files?action=ReName', ['path' => $src, 'newname' => basename($dst)]);
    }
    public function copyFile(string $src, string $dst): array {
        return $this->request('/files?action=CopyFile', ['sfile' => $src, 'dfile' => $dst, 'type' => 'cp']);
    }
    public function moveFile(string $src, string $dst): array {
        return $this->request('/files?action=CopyFile', ['sfile' => $src, 'dfile' => $dst, 'type' => 'mv']);
    }
    public function getFilePermission(string $path): array {
        return $this->request('/files?action=GetFileAccess', ['path' => $path]);
    }
    public function setFilePermission(string $path, string $mode, string $user, bool $recursive = false): array {
        return $this->request('/files?action=SetFileAccess', [
            'path'      => $path,
            'access'    => $mode,
            'user'      => $user,
            'proup'     => $user,
            'recursive' => $recursive ? 1 : 0,
        ]);
    }
    public function compressFiles(array $files, string $toPath, string $filename, string $type = 'zip'): array {
        return $this->request('/files?action=Compress', [
            'files'    => json_encode($files),
            'type'     => $type,
            'name'     => $filename,
            'path'     => $toPath,
            'toPath'   => $toPath,
        ]);
    }
    public function decompressFile(string $file, string $toPath, string $password = ''): array {
        return $this->request('/files?action=UnCompress', [
            'sfile'    => $file,
            'dfile'    => $toPath,
            'type'     => pathinfo($file, PATHINFO_EXTENSION),
            'password' => $password,
        ]);
    }
    public function getDownloadUrl(string $path): string {
        $timestamp = time();
        $token = md5(md5($this->key) . $timestamp);
        return $this->url . '/files?action=DownloadFile&path=' . urlencode($path)
            . '&request_token=' . $token . '&request_time=' . $timestamp;
    }

    // =============================================
    // WEBSITES
    // =============================================
    public function getSites(int $page = 1, int $perPage = 100, string $search = ''): array {
        return $this->request('/data?action=getData', [
            'table'  => 'sites',
            'limit'  => $perPage,
            'p'      => $page,
            'search' => $search,
        ]);
    }
    public function getSiteInfo(int $siteId): array {
        return $this->request('/site?action=GetSite', ['id' => $siteId]);
    }
    public function createSite(array $params): array {
        return $this->request('/site?action=AddSite', $params);
    }
    public function deleteSite(int $siteId, string $webName, bool $deleteFiles = false, bool $deleteDb = false): array {
        return $this->request('/site?action=DeleteSite', [
            'id'      => $siteId,
            'webname' => $webName,
            'ftp'     => 0,
            'database'=> $deleteDb ? 1 : 0,
            'path'    => $deleteFiles ? 1 : 0,
        ]);
    }
    public function stopSite(int $siteId, string $webName): array {
        return $this->request('/site?action=SiteStop', ['id' => $siteId, 'name' => $webName]);
    }
    public function startSite(int $siteId, string $webName): array {
        return $this->request('/site?action=SiteStart', ['id' => $siteId, 'name' => $webName]);
    }
    public function getSiteDomain(int $siteId): array {
        return $this->request('/domain?action=GetDomain', ['id' => $siteId, 'pid' => 0]);
    }
    public function addDomain(int $siteId, string $webName, string $domain): array {
        return $this->request('/domain?action=AddDomain', [
            'id'      => $siteId,
            'webname' => $webName,
            'domain'  => $domain,
        ]);
    }
    public function deleteDomain(int $siteId, string $webName, string $domain, int $port): array {
        return $this->request('/domain?action=DelDomain', [
            'id'      => $siteId,
            'webname' => $webName,
            'domain'  => $domain,
            'port'    => $port,
        ]);
    }
    public function getSiteConf(string $webName): array {
        return $this->request('/site?action=GetSiteConf', ['name' => $webName]);
    }
    public function setSiteConf(string $webName, array $params): array {
        $params['name'] = $webName;
        return $this->request('/site?action=SetSiteConf', $params);
    }
    public function getSiteRewrite(string $webName): array {
        return $this->request('/site?action=GetSiteRewrite', ['siteName' => $webName]);
    }
    public function getSslInfo(int $siteId, string $webName): array {
        return $this->request('/site?action=GetSSL', ['id' => $siteId, 'siteName' => $webName]);
    }
    public function setSSL(int $siteId, string $webName, string $key, string $crt): array {
        return $this->request('/site?action=SetSSL', [
            'id'       => $siteId,
            'siteName' => $webName,
            'key'      => $key,
            'csr'      => $crt,
            'type'     => 1,
            'autoWild' => 0,
        ]);
    }
    public function closeSSL(int $siteId, string $webName): array {
        return $this->request('/site?action=CloseSSLConf', [
            'updateOf'=> 1,
            'id'      => $siteId,
            'siteName'=> $webName,
        ]);
    }
    public function applyFreeSSL(int $siteId, string $webName, array $domains): array {
        return $this->request('/acme?action=apply_cert_api', [
            'domains' => json_encode($domains),
            'auth_type'=> 'http',
            'auth_to'  => $siteId,
            'auto_wildcard' => 0,
        ]);
    }
    public function getSiteLog(string $webName, string $type = 'access'): array {
        return $this->request('/site?action=GetSiteLogs', [
            'siteName' => $webName,
            'type'     => $type,
        ]);
    }
    public function getSiteBackups(int $siteId): array {
        return $this->request('/data?action=getData', [
            'table'  => 'backup',
            'limit'  => 5,
            'p'      => 1,
            'where'  => "pid='" . $siteId . "'",
        ]);
    }
    public function createBackup(int $siteId): array {
        return $this->request('/site?action=ToBackup', ['id' => $siteId]);
    }
    public function deleteBackup(int $backupId): array {
        return $this->request('/site?action=DelBackup', ['id' => $backupId]);
    }
    public function getPhpVersionList(): array {
        return $this->request('/site?action=GetPHPVersion');
    }
    public function getSitePhpVersion(string $webName): array {
        return $this->request('/site?action=GetSitePHPVersion', ['siteName' => $webName]);
    }
    public function setSitePhpVersion(string $webName, string $version): array {
        return $this->request('/site?action=SetSitePHPVersion', [
            'siteName'   => $webName,
            'phpVersion' => $version,
        ]);
    }

    // =============================================
    // DATABASE / MYSQL
    // =============================================
    public function getDatabases(int $page = 1, int $perPage = 100, string $search = ''): array {
        return $this->request('/data?action=getData', [
            'table'  => 'databases',
            'limit'  => $perPage,
            'p'      => $page,
            'search' => $search,
        ]);
    }
    public function createDatabase(string $dbName, string $dbUser, string $dbPass, string $address = '%', string $remark = ''): array {
        return $this->request('/database?action=AddDatabase', [
            'name'    => $dbName,
            'username'=> $dbUser,
            'password'=> $dbPass,
            'address' => $address,
            'codeing' => 'utf8mb4',
            'remark'  => $remark,
        ]);
    }
    public function deleteDatabase(int $dbId, string $dbName): array {
        return $this->request('/database?action=DeleteDatabase', ['id' => $dbId, 'name' => $dbName]);
    }
    public function resetDbPassword(int $dbId, string $dbName, string $dbUser, string $newPass): array {
        return $this->request('/database?action=ResDatabaseUser', [
            'id'       => $dbId,
            'name'     => $dbName,
            'username' => $dbUser,
            'password' => $newPass,
        ]);
    }
    public function backupDatabase(int $dbId): array {
        return $this->request('/database?action=ToBackup', ['id' => $dbId]);
    }
    public function getDatabaseBackups(int $dbId): array {
        return $this->request('/data?action=getData', [
            'table' => 'backup',
            'limit' => 5,
            'p'     => 1,
            'where' => "pid='" . $dbId . "' AND type='2'",
        ]);
    }
    public function getPhpMyAdminUrl(): string {
        $timestamp = time();
        $token = md5(md5($this->key) . $timestamp);
        return $this->url . '/phpMyAdmin/?request_token=' . $token . '&request_time=' . $timestamp;
    }

    // =============================================
    // FTP
    // =============================================
    public function getFtpList(int $page = 1, int $perPage = 100, string $search = ''): array {
        return $this->request('/data?action=getData', [
            'table'  => 'ftps',
            'limit'  => $perPage,
            'p'      => $page,
            'search' => $search,
        ]);
    }
    public function createFtp(string $user, string $pass, string $path): array {
        return $this->request('/ftp?action=AddUser', [
            'username' => $user,
            'password' => $pass,
            'path'     => $path,
        ]);
    }
    public function deleteFtp(int $id, string $user): array {
        return $this->request('/ftp?action=DeleteUser', ['id' => $id, 'username' => $user]);
    }

    // =============================================
    // CRON JOBS
    // =============================================
    public function getCronList(): array {
        return $this->request('/cron?action=GetCron');
    }
    public function createCron(array $params): array {
        return $this->request('/cron?action=AddCron', $params);
    }
    public function deleteCron(int $id): array {
        return $this->request('/cron?action=DelCron', ['id' => $id]);
    }

    // =============================================
    // FIREWALL
    // =============================================
    public function getFirewallRules(): array {
        return $this->request('/firewall?action=FirewallList');
    }
    public function addFirewallRule(string $type, string $port, string $source = '', string $strategy = 'accept'): array {
        return $this->request('/firewall?action=AddAcceptPort', [
            'port'     => $port,
            'type'     => $type, // TCP/UDP
            'source'   => $source,
            'strategy' => $strategy,
        ]);
    }
    public function deleteFirewallRule(string $port, string $type): array {
        return $this->request('/firewall?action=DelAcceptPort', ['port' => $port, 'type' => $type]);
    }

    // =============================================
    // SSL / LETS ENCRYPT
    // =============================================
    public function getCertList(): array {
        return $this->request('/ssl?action=GetSSLList');
    }
}
