<?php
/**
 * Database helper & User Management
 */
class DB {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (!self::$instance) {
            try {
                self::$instance = new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                    DB_USER, DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                     PDO::ATTR_EMULATE_PREPARES => false]
                );
            } catch (PDOException $e) {
                die(json_encode(['status' => false, 'msg' => 'DB connection failed: ' . $e->getMessage()]));
            }
        }
        return self::$instance;
    }

    public static function query(string $sql, array $params = []): PDOStatement {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array {
        return self::query($sql, $params)->fetch() ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array {
        return self::query($sql, $params)->fetchAll();
    }
}

/**
 * User Management
 */
class UserManager {
    public static function install(): void {
        DB::getInstance()->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) UNIQUE NOT NULL,
                `password` VARCHAR(255) NOT NULL,
                `email` VARCHAR(100) DEFAULT '',
                `role` ENUM('superadmin','admin','viewer') DEFAULT 'viewer',
                `allowed_paths` TEXT DEFAULT NULL COMMENT 'JSON array of allowed root paths',
                `allowed_sites` TEXT DEFAULT NULL COMMENT 'JSON array of site IDs, NULL=all',
                `allowed_dbs` TEXT DEFAULT NULL COMMENT 'JSON array of db IDs, NULL=all',
                `can_upload` TINYINT(1) DEFAULT 1,
                `can_delete` TINYINT(1) DEFAULT 0,
                `can_edit_files` TINYINT(1) DEFAULT 1,
                `can_manage_sites` TINYINT(1) DEFAULT 0,
                `can_manage_dbs` TINYINT(1) DEFAULT 0,
                `api_key` VARCHAR(64) DEFAULT NULL COMMENT 'Personal API key for REST access',
                `last_login` DATETIME DEFAULT NULL,
                `login_ip` VARCHAR(45) DEFAULT NULL,
                `is_active` TINYINT(1) DEFAULT 1,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `audit_logs` (
                `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL,
                `username` VARCHAR(50) NOT NULL,
                `action` VARCHAR(100) NOT NULL,
                `target` TEXT DEFAULT NULL,
                `detail` TEXT DEFAULT NULL,
                `ip` VARCHAR(45) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user (`user_id`),
                INDEX idx_created (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `sessions` (
                `session_id` VARCHAR(128) PRIMARY KEY,
                `user_id` INT NOT NULL,
                `ip` VARCHAR(45),
                `user_agent` VARCHAR(255),
                `expires_at` DATETIME NOT NULL,
                INDEX idx_user (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Buat superadmin jika belum ada
        $exists = DB::fetch("SELECT id FROM users WHERE role='superadmin' LIMIT 1");
        if (!$exists) {
            $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
            DB::query(
                "INSERT INTO users (username, password, role, can_delete, can_manage_sites, can_manage_dbs) VALUES (?,?,?,1,1,1)",
                ['admin', $hash, 'superadmin']
            );
        }
    }

    public static function login(string $username, string $password, string $ip = ''): ?array {
        $user = DB::fetch("SELECT * FROM users WHERE username=? AND is_active=1", [$username]);
        if (!$user || !password_verify($password, $user['password'])) return null;

        DB::query("UPDATE users SET last_login=NOW(), login_ip=? WHERE id=?", [$ip, $user['id']]);
        self::log($user['id'], $user['username'], 'login', null, null, $ip);
        return $user;
    }

    public static function getById(int $id): ?array {
        return DB::fetch("SELECT * FROM users WHERE id=? AND is_active=1", [$id]);
    }

    public static function getAll(): array {
        return DB::fetchAll("SELECT id, username, email, role, can_upload, can_delete, can_edit_files, can_manage_sites, can_manage_dbs, allowed_paths, allowed_sites, allowed_dbs, last_login, login_ip, is_active, created_at FROM users ORDER BY role, username");
    }

    public static function create(array $data): bool {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $apiKey = bin2hex(random_bytes(32));
        DB::query("INSERT INTO users (username, password, email, role, allowed_paths, allowed_sites, allowed_dbs, can_upload, can_delete, can_edit_files, can_manage_sites, can_manage_dbs, api_key) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [
            $data['username'], $hash, $data['email'] ?? '',
            $data['role'] ?? 'viewer',
            isset($data['allowed_paths']) ? json_encode($data['allowed_paths']) : null,
            isset($data['allowed_sites']) ? json_encode($data['allowed_sites']) : null,
            isset($data['allowed_dbs']) ? json_encode($data['allowed_dbs']) : null,
            $data['can_upload'] ?? 1,
            $data['can_delete'] ?? 0,
            $data['can_edit_files'] ?? 1,
            $data['can_manage_sites'] ?? 0,
            $data['can_manage_dbs'] ?? 0,
            $apiKey,
        ]);
        return true;
    }

    public static function update(int $id, array $data): bool {
        $fields = [];
        $vals = [];
        $allowed = ['email','role','can_upload','can_delete','can_edit_files','can_manage_sites','can_manage_dbs','is_active','allowed_paths','allowed_sites','allowed_dbs'];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $fields[] = "$f=?";
                $vals[] = in_array($f, ['allowed_paths','allowed_sites','allowed_dbs']) && is_array($data[$f])
                    ? json_encode($data[$f]) : $data[$f];
            }
        }
        if (!empty($data['password'])) {
            $fields[] = "password=?";
            $vals[] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        if (!$fields) return false;
        $vals[] = $id;
        DB::query("UPDATE users SET " . implode(',', $fields) . " WHERE id=?", $vals);
        return true;
    }

    public static function delete(int $id): bool {
        DB::query("DELETE FROM users WHERE id=? AND role!='superadmin'", [$id]);
        return true;
    }

    public static function log(int $userId, string $username, string $action, ?string $target, ?string $detail, string $ip = ''): void {
        DB::query("INSERT INTO audit_logs (user_id, username, action, target, detail, ip) VALUES (?,?,?,?,?,?)",
            [$userId, $username, $action, $target, $detail, $ip]);
    }

    public static function getLogs(int $limit = 100, int $userId = 0): array {
        if ($userId) {
            return DB::fetchAll("SELECT * FROM audit_logs WHERE user_id=? ORDER BY created_at DESC LIMIT $limit", [$userId]);
        }
        return DB::fetchAll("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT $limit");
    }
}

/**
 * Auth helper
 */
class Auth {
    public static function check(): ?array {
        if (empty($_SESSION['user_id'])) return null;
        if (empty($_SESSION['expires_at']) || $_SESSION['expires_at'] < time()) {
            self::logout();
            return null;
        }
        return $_SESSION['user'] ?? null;
    }

    public static function require(): array {
        $user = self::check();
        if (!$user) {
            if (self::isAjax()) {
                http_response_code(401);
                echo json_encode(['status' => false, 'msg' => 'Unauthenticated']);
                exit;
            }
            header('Location: /index.php?page=login');
            exit;
        }
        return $user;
    }

    public static function setUser(array $user): void {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
        $_SESSION['expires_at'] = time() + SESSION_LIFETIME;
        session_regenerate_id(true);
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
        }
        session_destroy();
    }

    public static function can(array $user, string $permission): bool {
        if ($user['role'] === 'superadmin') return true;
        return !empty($user[$permission]);
    }

    public static function canAccessPath(array $user, string $path): bool {
        if ($user['role'] === 'superadmin') return true;
        if (empty($user['allowed_paths'])) return true;
        $allowed = is_string($user['allowed_paths']) ? json_decode($user['allowed_paths'], true) : $user['allowed_paths'];
        if (empty($allowed)) return true;
        foreach ($allowed as $ap) {
            if (strpos(realpath($path) ?: $path, $ap) === 0) return true;
        }
        return false;
    }

    public static function csrf(): string {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    public static function verifyCsrf(string $token): bool {
        return hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token);
    }

    public static function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
