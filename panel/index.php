<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AAPanel Manager</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%2320a97f'/><text x='7' y='23' font-size='18' fill='white' font-weight='bold'>A</text></svg>">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --bg:#0f1117;--bg2:#161b22;--bg3:#1c2333;--bg4:#21262d;
  --border:#30363d;--border2:#3d444d;
  --text:#e6edf3;--text2:#8b949e;--text3:#6e7681;
  --green:#238636;--green-h:#2ea043;--green-light:#1a7f37;
  --blue:#1f6feb;--blue-light:#388bfd;
  --red:#da3633;--red-h:#f85149;
  --yellow:#d29922;--yellow-light:#e3b341;
  --purple:#8957e5;
  --radius:6px;
}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;font-size:13px;background:var(--bg);color:var(--text);height:100vh;overflow:hidden;display:flex;flex-direction:column}

/* LOGIN */
#login-screen{position:fixed;inset:0;background:var(--bg);display:flex;align-items:center;justify-content:center;z-index:9999}
.login-box{background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:40px 36px;width:360px}
.login-logo{display:flex;align-items:center;gap:10px;margin-bottom:28px;justify-content:center}
.login-logo-icon{width:40px;height:40px;background:#20a97f;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#fff}
.login-logo h1{font-size:20px;font-weight:600}
.login-logo span{color:#20a97f}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12px;color:var(--text2);margin-bottom:5px}
.form-group input{width:100%;background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:8px 12px;color:var(--text);font-size:13px;outline:none;transition:border .2s}
.form-group input:focus{border-color:var(--blue-light);box-shadow:0 0 0 3px rgba(31,111,235,.15)}
.btn-login{width:100%;background:var(--green);border:none;border-radius:var(--radius);padding:10px;color:#fff;font-size:14px;font-weight:500;cursor:pointer;margin-top:8px;transition:background .2s}
.btn-login:hover{background:var(--green-h)}
.login-err{color:var(--red-h);font-size:12px;text-align:center;margin-top:10px;display:none}

/* TOPBAR */
#topbar{height:48px;background:var(--bg2);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 16px;gap:12px;flex-shrink:0}
.top-logo{display:flex;align-items:center;gap:8px;font-weight:600;font-size:14px;color:var(--text);text-decoration:none}
.top-logo .icon{width:26px;height:26px;background:#20a97f;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff}
.top-logo span{color:#20a97f}
.top-sep{width:1px;height:20px;background:var(--border);margin:0 4px}
.top-nav{display:flex;gap:2px;flex:1}
.nav-btn{padding:5px 12px;border-radius:var(--radius);cursor:pointer;color:var(--text2);font-size:12px;font-weight:500;border:none;background:none;transition:all .15s;display:flex;align-items:center;gap:5px}
.nav-btn:hover{background:var(--bg3);color:var(--text)}
.nav-btn.active{background:var(--bg3);color:var(--text)}
.nav-btn svg{width:14px;height:14px;flex-shrink:0}
.top-right{display:flex;align-items:center;gap:8px;margin-left:auto}
.user-chip{display:flex;align-items:center;gap:6px;padding:4px 10px;background:var(--bg3);border-radius:20px;cursor:pointer;border:1px solid var(--border)}
.user-chip .avatar{width:20px;height:20px;border-radius:50%;background:var(--purple);display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;font-weight:600}
.user-chip .uname{font-size:12px;color:var(--text)}
.user-chip .role-badge{font-size:10px;padding:1px 5px;border-radius:3px;background:rgba(139,87,229,.2);color:var(--purple)}

/* LAYOUT */
#app{display:flex;flex:1;overflow:hidden}
#sidebar{width:200px;background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;overflow-y:auto;flex-shrink:0}
.sidebar-section{padding:8px 0;border-bottom:1px solid var(--border)}
.sidebar-label{padding:6px 14px 3px;font-size:10px;color:var(--text3);font-weight:600;letter-spacing:.7px;text-transform:uppercase}
.sidebar-item{display:flex;align-items:center;gap:7px;padding:6px 14px;cursor:pointer;color:var(--text2);font-size:12px;border-left:2px solid transparent;transition:all .1s}
.sidebar-item:hover{background:var(--bg3);color:var(--text)}
.sidebar-item.active{background:var(--bg4);color:#fff;border-left-color:#20a97f}
.sidebar-item svg{width:14px;height:14px;flex-shrink:0}
.disk-info{padding:10px 14px}
.disk-label{font-size:10px;color:var(--text3);margin-bottom:5px;display:flex;justify-content:space-between}
.disk-bar{height:4px;background:var(--bg4);border-radius:2px;overflow:hidden}
.disk-fill{height:100%;border-radius:2px;background:#20a97f;transition:width 1s}

/* CONTENT */
#content{flex:1;overflow:auto;display:flex;flex-direction:column;min-width:0}
.page{display:none;flex-direction:column;height:100%}
.page.active{display:flex}

/* TOOLBAR */
.toolbar{padding:8px 12px;background:var(--bg2);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:6px;flex-wrap:wrap;flex-shrink:0}
.breadcrumb{display:flex;align-items:center;gap:4px;flex:1;min-width:0;overflow:hidden;white-space:nowrap}
.breadcrumb-seg{color:var(--blue-light);cursor:pointer;font-size:12px;max-width:120px;overflow:hidden;text-overflow:ellipsis}
.breadcrumb-seg:hover{text-decoration:underline}
.breadcrumb-sep{color:var(--text3);font-size:11px}
.btn{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:var(--radius);cursor:pointer;font-size:12px;font-weight:500;border:1px solid;transition:all .15s;white-space:nowrap}
.btn svg{width:13px;height:13px}
.btn-primary{background:var(--green);border-color:var(--green);color:#fff}.btn-primary:hover{background:var(--green-h)}
.btn-default{background:var(--bg3);border-color:var(--border2);color:var(--text)}.btn-default:hover{background:var(--bg4)}
.btn-danger{background:transparent;border-color:var(--red);color:var(--red-h)}.btn-danger:hover{background:rgba(218,54,51,.1)}
.btn-blue{background:var(--blue);border-color:var(--blue);color:#fff}.btn-blue:hover{background:#1a60d1}
.btn-sm{padding:3px 8px;font-size:11px}
.btn:disabled{opacity:.5;cursor:not-allowed}
.search-inp{padding:5px 10px;background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);color:var(--text);font-size:12px;width:160px;outline:none}
.search-inp:focus{border-color:var(--blue-light)}

/* TABLE */
.table-wrap{flex:1;overflow:auto}
table{width:100%;border-collapse:collapse}
thead th{padding:7px 12px;text-align:left;font-size:11px;font-weight:600;color:var(--text2);background:var(--bg2);border-bottom:1px solid var(--border);position:sticky;top:0;white-space:nowrap;z-index:2}
thead th:first-child{width:36px;padding-left:10px}
tbody tr{border-bottom:1px solid var(--border);cursor:pointer;transition:background .1s}
tbody tr:hover{background:var(--bg3)}
tbody tr.selected{background:rgba(31,111,235,.1)}
td{padding:6px 12px;font-size:12px;color:var(--text);vertical-align:middle}
td:first-child{padding-left:10px}
.file-name{display:flex;align-items:center;gap:7px}
.file-ico{font-size:15px;width:20px;text-align:center;flex-shrink:0}
.fname{color:var(--text)}
.fname.dir{color:var(--blue-light);font-weight:500}
.mono{font-family:'SFMono-Regular',Consolas,monospace;font-size:11px}
.badge{display:inline-flex;align-items:center;padding:1px 6px;border-radius:3px;font-size:10px;font-weight:500}
.badge-green{background:rgba(35,134,54,.2);color:#3fb950}
.badge-red{background:rgba(218,54,51,.15);color:var(--red-h)}
.badge-blue{background:rgba(31,111,235,.15);color:var(--blue-light)}
.badge-yellow{background:rgba(210,153,34,.15);color:var(--yellow-light)}
.badge-gray{background:var(--bg3);color:var(--text2)}
.muted{color:var(--text3);font-size:11px}
.actions{display:flex;align-items:center;gap:4px;opacity:0;transition:opacity .1s}
tr:hover .actions{opacity:1}

/* STATUS BAR */
.statusbar{padding:4px 12px;background:var(--bg2);border-top:1px solid var(--border);font-size:11px;color:var(--text2);display:flex;justify-content:space-between;flex-shrink:0}

/* MODAL */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);display:none;align-items:center;justify-content:center;z-index:1000}
.modal-overlay.show{display:flex}
.modal{background:var(--bg2);border:1px solid var(--border2);border-radius:10px;padding:24px;min-width:360px;max-width:680px;width:90%;max-height:85vh;overflow-y:auto}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.modal-title{font-size:15px;font-weight:600}
.modal-close{background:none;border:none;color:var(--text2);font-size:18px;cursor:pointer;padding:2px 6px;line-height:1;border-radius:3px}
.modal-close:hover{background:var(--bg3);color:var(--text)}
.form-row{margin-bottom:14px}
.form-row label{display:block;font-size:11px;color:var(--text2);margin-bottom:5px;font-weight:500}
.form-row input,.form-row select,.form-row textarea{width:100%;background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:7px 10px;color:var(--text);font-size:12px;outline:none;font-family:inherit}
.form-row input:focus,.form-row select:focus,.form-row textarea:focus{border-color:var(--blue-light);box-shadow:0 0 0 3px rgba(31,111,235,.1)}
.form-row textarea{resize:vertical;min-height:80px}
.form-row select option{background:var(--bg3)}
.modal-footer{display:flex;gap:8px;justify-content:flex-end;margin-top:18px;padding-top:14px;border-top:1px solid var(--border)}
.checkbox-row{display:flex;align-items:center;gap:6px;margin-bottom:8px;cursor:pointer}
.checkbox-row input{width:auto}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}

/* EDITOR */
#editor-wrap{display:flex;flex-direction:column;height:100%}
#editor-toolbar{display:flex;align-items:center;gap:8px;padding:8px 12px;background:var(--bg2);border-bottom:1px solid var(--border);flex-shrink:0}
#editor-path{font-size:12px;color:var(--text2);flex:1;font-family:monospace}
#editor-textarea{flex:1;background:var(--bg);color:var(--text);border:none;padding:16px;font-family:'SFMono-Regular',Consolas,monospace;font-size:13px;line-height:1.7;resize:none;outline:none;width:100%}

/* DASHBOARD */
.dashboard{padding:20px;overflow-y:auto}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:20px}
.stat-card{background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:16px}
.stat-label{font-size:11px;color:var(--text3);margin-bottom:6px;font-weight:500;text-transform:uppercase;letter-spacing:.5px}
.stat-value{font-size:22px;font-weight:600;color:var(--text)}
.stat-sub{font-size:11px;color:var(--text3);margin-top:3px}
.stat-bar{margin-top:10px;height:4px;background:var(--bg4);border-radius:2px;overflow:hidden}
.stat-bar-fill{height:100%;border-radius:2px;background:#20a97f}

/* PAGINATION */
.pagination{display:flex;align-items:center;gap:4px;padding:8px 12px;border-top:1px solid var(--border);flex-shrink:0;background:var(--bg2)}
.page-btn{padding:4px 10px;border-radius:var(--radius);cursor:pointer;font-size:12px;background:var(--bg3);border:1px solid var(--border);color:var(--text);transition:all .1s}
.page-btn:hover{background:var(--bg4)}
.page-btn.active{background:var(--blue);border-color:var(--blue);color:#fff}
.page-btn:disabled{opacity:.4;cursor:not-allowed}
.page-info{font-size:11px;color:var(--text3);margin-left:auto}

/* NOTIFICATION */
#notif{position:fixed;top:56px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:6px}
.toast{background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius);padding:10px 16px;font-size:12px;display:flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(0,0,0,.4);animation:slideIn .2s ease}
.toast.success{border-left:3px solid #3fb950}
.toast.error{border-left:3px solid var(--red-h)}
.toast.info{border-left:3px solid var(--blue-light)}
@keyframes slideIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}

/* CONTEXT MENU */
#ctx-menu{position:fixed;background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius);padding:4px 0;z-index:2000;min-width:160px;display:none;box-shadow:0 8px 24px rgba(0,0,0,.5)}
.ctx-item{padding:6px 14px;cursor:pointer;font-size:12px;color:var(--text);display:flex;align-items:center;gap:8px}
.ctx-item:hover{background:var(--bg3)}
.ctx-item.danger{color:var(--red-h)}
.ctx-item svg{width:13px;height:13px}
.ctx-sep{height:1px;background:var(--border);margin:3px 0}

/* LOADER */
.loading{display:flex;align-items:center;justify-content:center;padding:40px;color:var(--text3);gap:10px;font-size:13px}
.spinner{width:18px;height:18px;border:2px solid var(--border2);border-top-color:#20a97f;border-radius:50%;animation:spin .6s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* EMPTY STATE */
.empty{text-align:center;padding:48px;color:var(--text3)}
.empty svg{width:40px;height:40px;margin:0 auto 12px;display:block;opacity:.3}

/* PHPMYADMIN IFRAME */
#pma-frame{flex:1;border:none;width:100%;background:#fff}

/* RESPONSIVE */
@media(max-width:768px){
  #sidebar{display:none}
  .form-grid{grid-template-columns:1fr}
}

/* TOGGLE */
.toggle{position:relative;display:inline-flex;width:36px;height:20px}
.toggle input{opacity:0;width:0;height:0}
.toggle-slider{position:absolute;inset:0;background:var(--bg4);border-radius:10px;cursor:pointer;transition:.3s}
.toggle-slider:before{position:absolute;content:'';height:14px;width:14px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.toggle input:checked+.toggle-slider{background:#20a97f}
.toggle input:checked+.toggle-slider:before{transform:translateX(16px)}
</style>
</head>
<body>

<!-- LOGIN -->
<div id="login-screen">
  <div class="login-box">
    <div class="login-logo">
      <div class="login-logo-icon">A</div>
      <h1>AAPanel <span>Manager</span></h1>
    </div>
    <div class="form-group">
      <label>Username</label>
      <input type="text" id="login-user" placeholder="Masukkan username" autocomplete="username">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" id="login-pass" placeholder="Masukkan password" autocomplete="current-password">
    </div>
    <button class="btn-login" id="login-btn" onclick="doLogin()">Masuk</button>
    <div class="login-err" id="login-err"></div>
  </div>
</div>

<!-- TOPBAR -->
<div id="topbar" style="display:none">
  <a class="top-logo" href="#">
    <div class="icon">A</div>
    AAPanel <span>Manager</span>
  </a>
  <div class="top-sep"></div>
  <div class="top-nav" id="top-nav">
    <button class="nav-btn active" onclick="showPage('dashboard')" data-page="dashboard">
      <svg viewBox="0 0 16 16" fill="currentColor"><path d="M2 2h5v5H2V2zm0 7h5v5H2V9zm7-7h5v5H9V2zm0 7h5v5H9V9z"/></svg>Dashboard
    </button>
    <button class="nav-btn" onclick="showPage('files')" data-page="files">
      <svg viewBox="0 0 16 16" fill="currentColor"><path d="M1 3.5A1.5 1.5 0 012.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0115 5.5v7a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 011 12.5v-9z"/></svg>File Manager
    </button>
    <button class="nav-btn" onclick="showPage('sites')" data-page="sites">
      <svg viewBox="0 0 16 16" fill="currentColor"><path d="M8 0a8 8 0 100 16A8 8 0 008 0zM1.5 8.5A6.5 6.5 0 018 1.5v13a6.5 6.5 0 01-6.5-6z"/></svg>Website
    </button>
    <button class="nav-btn" onclick="showPage('databases')" data-page="databases">
      <svg viewBox="0 0 16 16" fill="currentColor"><ellipse cx="8" cy="4" rx="6" ry="2.5"/><path d="M2 4v2c0 1.38 2.686 2.5 6 2.5S14 7.38 14 6V4M2 8v2c0 1.38 2.686 2.5 6 2.5S14 11.38 14 10V8M2 12v2c0 1.38 2.686 2.5 6 2.5S14 15.38 14 14v-2"/></svg>Database
    </button>
    <button class="nav-btn" id="nav-users" onclick="showPage('users')" data-page="users" style="display:none">
      <svg viewBox="0 0 16 16" fill="currentColor"><path d="M8 8a3 3 0 100-6 3 3 0 000 6zM3 14a5 5 0 0110 0H3z"/></svg>Users
    </button>
    <button class="nav-btn" id="nav-logs" onclick="showPage('logs')" data-page="logs" style="display:none">
      <svg viewBox="0 0 16 16" fill="currentColor"><path d="M3 2.5a.5.5 0 01.5-.5h9a.5.5 0 010 1h-9a.5.5 0 01-.5-.5zm0 4a.5.5 0 01.5-.5h9a.5.5 0 010 1h-9a.5.5 0 01-.5-.5zm0 4a.5.5 0 01.5-.5h5a.5.5 0 010 1h-5a.5.5 0 01-.5-.5z"/></svg>Audit Log
    </button>
  </div>
  <div class="top-right">
    <div class="user-chip" id="user-chip">
      <div class="avatar" id="user-avatar">A</div>
      <span class="uname" id="user-name">-</span>
      <span class="role-badge" id="user-role">-</span>
    </div>
    <button class="btn btn-default btn-sm" onclick="doLogout()">Keluar</button>
  </div>
</div>

<!-- NOTIFICATIONS -->
<div id="notif"></div>
<!-- CONTEXT MENU -->
<div id="ctx-menu"></div>

<!-- APP -->
<div id="app" style="display:none">
  <!-- SIDEBAR -->
  <div id="sidebar">
    <div class="sidebar-section">
      <div class="sidebar-label">File Manager</div>
      <div class="sidebar-item active" onclick="navigateTo('/www/wwwroot')" id="sb-wwwroot">
        <svg viewBox="0 0 16 16" fill="currentColor"><path d="M1 3.5A1.5 1.5 0 012.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0115 5.5v7a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 011 12.5v-9z"/></svg>wwwroot
      </div>
      <div class="sidebar-item" onclick="navigateTo('/www/backup')">
        <svg viewBox="0 0 16 16" fill="currentColor"><path d="M1 3.5A1.5 1.5 0 012.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0115 5.5v7a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 011 12.5v-9z"/></svg>Backup
      </div>
      <div class="sidebar-item" onclick="navigateTo('/www/server/nginx')">
        <svg viewBox="0 0 16 16" fill="currentColor"><path d="M1 3.5A1.5 1.5 0 012.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0115 5.5v7a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 011 12.5v-9z"/></svg>Nginx Conf
      </div>
      <div class="sidebar-item" onclick="navigateTo('/')">
        <svg viewBox="0 0 16 16" fill="currentColor"><path d="M2 2h5v5H2V2zm0 7h5v5H2V9zm7-7h5v5H9V2zm0 7h5v5H9V9z"/></svg>Root /
      </div>
    </div>
    <div class="sidebar-section" id="sb-disk-section">
      <div class="sidebar-label">Disk</div>
      <div class="disk-info" id="disk-info">
        <div class="disk-label">
          <span id="disk-used">-</span>
          <span id="disk-total">-</span>
        </div>
        <div class="disk-bar"><div class="disk-fill" id="disk-fill" style="width:0%"></div></div>
      </div>
    </div>
  </div>

  <!-- CONTENT AREA -->
  <div id="content">

    <!-- DASHBOARD PAGE -->
    <div class="page active" id="page-dashboard">
      <div class="dashboard" id="dashboard-content">
        <div class="loading"><div class="spinner"></div> Memuat data server...</div>
      </div>
    </div>

    <!-- FILE MANAGER PAGE -->
    <div class="page" id="page-files">
      <div class="toolbar" id="fm-toolbar">
        <button class="btn btn-default btn-sm" onclick="fmBack()">← Kembali</button>
        <div class="breadcrumb" id="fm-breadcrumb"></div>
        <input class="search-inp" id="fm-search" placeholder="Cari file..." onkeydown="if(event.key==='Enter')fmLoad()">
        <button class="btn btn-primary btn-sm" onclick="modalNewFile()">+ File</button>
        <button class="btn btn-primary btn-sm" onclick="modalNewDir()">+ Folder</button>
        <button class="btn btn-default btn-sm" id="btn-upload" onclick="triggerUpload()">↑ Upload</button>
        <button class="btn btn-default btn-sm" onclick="modalCompress()">Compress</button>
        <button class="btn btn-default btn-sm" onclick="fmRefresh()">↻</button>
        <input type="file" id="upload-input" multiple style="display:none" onchange="uploadFiles(this.files)">
      </div>
      <div class="table-wrap">
        <table id="fm-table">
          <thead>
            <tr>
              <th><input type="checkbox" id="fm-chk-all" onchange="toggleAllFiles(this)"></th>
              <th>Nama</th><th>Ukuran</th><th>Izin</th><th>Pemilik</th><th>Diubah</th><th></th>
            </tr>
          </thead>
          <tbody id="fm-tbody"></tbody>
        </table>
      </div>
      <div class="statusbar">
        <span id="fm-status">-</span>
        <span id="fm-sel-status"></span>
      </div>
    </div>

    <!-- EDITOR PAGE -->
    <div class="page" id="page-editor">
      <div id="editor-wrap">
        <div id="editor-toolbar">
          <button class="btn btn-default btn-sm" onclick="closeEditor()">← Kembali</button>
          <span id="editor-path">-</span>
          <button class="btn btn-primary btn-sm" onclick="saveFile()">💾 Simpan</button>
        </div>
        <textarea id="editor-textarea" spellcheck="false"></textarea>
      </div>
    </div>

    <!-- WEBSITES PAGE -->
    <div class="page" id="page-sites">
      <div class="toolbar">
        <button class="btn btn-primary btn-sm" onclick="modalNewSite()">+ Tambah Website</button>
        <input class="search-inp" id="sites-search" placeholder="Cari domain..." onkeydown="if(event.key==='Enter')loadSites()">
        <button class="btn btn-default btn-sm" onclick="loadSites()">↻ Refresh</button>
        <div style="margin-left:auto;font-size:11px;color:var(--text3)" id="sites-count"></div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Domain</th><th>Path</th><th>PHP</th><th>Status</th><th>SSL</th><th>Dibuat</th><th>Aksi</th>
          </tr></thead>
          <tbody id="sites-tbody"></tbody>
        </table>
      </div>
      <div class="pagination" id="sites-paging"></div>
    </div>

    <!-- DATABASES PAGE -->
    <div class="page" id="page-databases">
      <div class="toolbar">
        <button class="btn btn-primary btn-sm" onclick="modalNewDb()">+ Tambah Database</button>
        <input class="search-inp" id="db-search" placeholder="Cari database..." onkeydown="if(event.key==='Enter')loadDbs()">
        <button class="btn btn-default btn-sm" onclick="loadDbs()">↻ Refresh</button>
        <button class="btn btn-blue btn-sm" onclick="openPhpMyAdmin()">phpMyAdmin ↗</button>
        <div style="margin-left:auto;font-size:11px;color:var(--text3)" id="db-count"></div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Database</th><th>User</th><th>Akses</th><th>Encoding</th><th>Keterangan</th><th>Dibuat</th><th>Aksi</th>
          </tr></thead>
          <tbody id="db-tbody"></tbody>
        </table>
      </div>
      <div class="pagination" id="db-paging"></div>
    </div>

    <!-- PHPMA PAGE -->
    <div class="page" id="page-phpmyadmin">
      <div class="toolbar">
        <button class="btn btn-default btn-sm" onclick="showPage('databases')">← Kembali</button>
        <span style="font-size:12px;color:var(--text2)">phpMyAdmin</span>
        <button class="btn btn-blue btn-sm" onclick="openPmaNewTab()">Buka di Tab Baru ↗</button>
      </div>
      <iframe id="pma-frame" src="about:blank"></iframe>
    </div>

    <!-- USERS PAGE -->
    <div class="page" id="page-users">
      <div class="toolbar">
        <button class="btn btn-primary btn-sm" onclick="modalNewUser()">+ Tambah User</button>
        <button class="btn btn-default btn-sm" onclick="loadUsers()">↻ Refresh</button>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Username</th><th>Role</th><th>Upload</th><th>Delete</th><th>Kelola Site</th><th>Kelola DB</th><th>Path Dibatasi</th><th>Login Terakhir</th><th>Status</th><th>Aksi</th>
          </tr></thead>
          <tbody id="users-tbody"></tbody>
        </table>
      </div>
    </div>

    <!-- AUDIT LOG PAGE -->
    <div class="page" id="page-logs">
      <div class="toolbar">
        <button class="btn btn-default btn-sm" onclick="loadLogs()">↻ Refresh</button>
        <span style="font-size:11px;color:var(--text3)">200 log terbaru</span>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Waktu</th><th>User</th><th>Aksi</th><th>Target</th><th>IP</th>
          </tr></thead>
          <tbody id="logs-tbody"></tbody>
        </table>
      </div>
    </div>

  </div><!-- /content -->
</div><!-- /app -->

<!-- MODALS -->
<div class="modal-overlay" id="modal-overlay" onclick="if(event.target===this)closeModal()">

  <!-- Generic modal -->
  <div class="modal" id="modal-main">
    <div class="modal-header">
      <span class="modal-title" id="modal-title">Modal</span>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <div id="modal-body"></div>
    <div class="modal-footer" id="modal-footer"></div>
  </div>

</div>

<script>
// =============================================
// STATE
// =============================================
const S = {
  user: null,
  csrf: '',
  currentPage: 'dashboard',
  fm: { path: '/www/wwwroot', history: [], selected: [] },
  sites: { page: 1, total: 0, list: [] },
  dbs: { page: 1, total: 0, list: [] },
  editor: { path: '', origPath: '' },
  pmaUrl: '',
};

// =============================================
// UTILITIES
// =============================================
function api(action, data={}, method='POST') {
  const opts = { method, headers: {'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':S.csrf} };
  if (method==='POST') {
    opts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
    opts.body = new URLSearchParams({action, ...data}).toString();
  }
  const url = method==='GET' ? `api.php?action=${action}&${new URLSearchParams(data)}` : 'api.php';
  if (method==='POST') opts.body = new URLSearchParams({action, ...data}).toString();
  return fetch(url, opts).then(r=>r.json()).catch(e=>({status:false, msg:e.message}));
}

function toast(msg, type='info', dur=3500) {
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.textContent = msg;
  document.getElementById('notif').appendChild(el);
  setTimeout(()=>el.remove(), dur);
}

function fmtSize(b) {
  if (!b) return '-';
  const u = ['B','KB','MB','GB','TB'];
  let i=0; b=+b;
  while(b>=1024&&i<4){b/=1024;i++}
  return b.toFixed(i?1:0)+' '+u[i];
}

function fmtDate(ts) {
  if (!ts) return '-';
  return new Date(ts*1000).toLocaleDateString('id-ID',{year:'numeric',month:'short',day:'2-digit',hour:'2-digit',minute:'2-digit'});
}

function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function closeModal() { document.getElementById('modal-overlay').classList.remove('show'); }
function openModal(title, bodyHtml, footerHtml='') {
  document.getElementById('modal-title').textContent = title;
  document.getElementById('modal-body').innerHTML = bodyHtml;
  document.getElementById('modal-footer').innerHTML = footerHtml;
  document.getElementById('modal-overlay').classList.add('show');
}

function showPage(name) {
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.nav-btn').forEach(b=>b.classList.remove('active'));
  const pg = document.getElementById('page-'+name);
  if (pg) pg.classList.add('active');
  const nb = document.querySelector(`[data-page="${name}"]`);
  if (nb) nb.classList.add('active');
  S.currentPage = name;

  if (name==='dashboard') loadDashboard();
  else if (name==='files') { showPage2('files'); fmLoad(); }
  else if (name==='sites') { showPage2('sites'); loadSites(); }
  else if (name==='databases') { showPage2('databases'); loadDbs(); }
  else if (name==='users') { showPage2('users'); loadUsers(); }
  else if (name==='logs') { showPage2('logs'); loadLogs(); }
}

function showPage2(name) {
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  const pg = document.getElementById('page-'+name);
  if(pg) pg.classList.add('active');
}

// =============================================
// AUTH
// =============================================
async function doLogin() {
  const u = document.getElementById('login-user').value.trim();
  const p = document.getElementById('login-pass').value;
  const btn = document.getElementById('login-btn');
  const err = document.getElementById('login-err');
  if (!u||!p) { err.textContent='Isi semua field'; err.style.display='block'; return; }
  btn.textContent='Masuk...'; btn.disabled=true;
  const r = await fetch('api.php?action=login', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({action:'login', username:u, password:p})
  }).then(r=>r.json()).catch(e=>({status:false,msg:e.message}));
  btn.textContent='Masuk'; btn.disabled=false;
  if (r.status) {
    S.user = r.user;
    S.csrf = await getCsrf();
    initUI();
  } else {
    err.textContent = r.msg||'Login gagal';
    err.style.display = 'block';
  }
}

async function getCsrf() {
  // Ambil dari session via endpoint kecil
  const r = await fetch('api.php?action=csrf_token', {headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).catch(()=>({token:''}));
  return r.token || '';
}

function doLogout() {
  api('logout').then(()=>{
    S.user = null;
    document.getElementById('login-screen').style.display='flex';
    document.getElementById('topbar').style.display='none';
    document.getElementById('app').style.display='none';
    document.getElementById('login-pass').value='';
  });
}

function initUI() {
  document.getElementById('login-screen').style.display='none';
  document.getElementById('topbar').style.display='flex';
  document.getElementById('app').style.display='flex';

  const u = S.user;
  document.getElementById('user-name').textContent = u.username;
  document.getElementById('user-role').textContent = u.role;
  document.getElementById('user-avatar').textContent = u.username[0].toUpperCase();

  if (u.role==='superadmin') {
    document.getElementById('nav-users').style.display='';
    document.getElementById('nav-logs').style.display='';
  }
  showPage('dashboard');
}

// =============================================
// DASHBOARD
// =============================================
async function loadDashboard() {
  const c = document.getElementById('dashboard-content');
  c.innerHTML = '<div class="loading"><div class="spinner"></div>Memuat data...</div>';
  const r = await api('dashboard', {}, 'GET');
  if (!r.status) { c.innerHTML = `<div class="empty">Gagal memuat: ${esc(r.msg)}</div>`; return; }

  const sys = r.data?.system || {};
  const mem = sys.mem || {};
  const cpu = sys.cpu || {};
  const load = sys.load || {};
  const disks = r.data?.disk?.data || [];

  let diskHtml = disks.map(d=>`
    <div class="stat-card">
      <div class="stat-label">${esc(d.path||'/')}</div>
      <div class="stat-value">${fmtSize((d.used||0)*1024*1024*1024)}</div>
      <div class="stat-sub">dari ${fmtSize((d.size||0)*1024*1024*1024)} — ${esc(d.inodes||'')} inodes</div>
      <div class="stat-bar"><div class="stat-bar-fill" style="width:${d.percent||0}%"></div></div>
    </div>`).join('');

  c.innerHTML = `
    <h2 style="font-size:15px;font-weight:600;margin-bottom:14px;color:var(--text)">Overview Server</h2>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">CPU</div>
        <div class="stat-value">${cpu.used||0}%</div>
        <div class="stat-sub">${cpu.cpuNum||1} core — load ${load['1min']||0}</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:${cpu.used||0}%"></div></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Memory</div>
        <div class="stat-value">${fmtSize((mem.memUsed||0)*1024)}</div>
        <div class="stat-sub">dari ${fmtSize((mem.memTotal||0)*1024)}</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:${mem.memRealUsed&&mem.memTotal?Math.round(mem.memRealUsed/mem.memTotal*100):0}%;background:#1f6feb"></div></div>
      </div>
      ${diskHtml}
    </div>
    <p style="font-size:11px;color:var(--text3);margin-top:4px">Terakhir diperbarui: ${new Date().toLocaleTimeString('id-ID')}</p>`;

  // Update sidebar disk
  if (disks[0]) {
    const d = disks[0];
    document.getElementById('disk-used').textContent = fmtSize((d.used||0)*1024*1024*1024);
    document.getElementById('disk-total').textContent = fmtSize((d.size||0)*1024*1024*1024);
    document.getElementById('disk-fill').style.width = (d.percent||0)+'%';
  }
}

// =============================================
// FILE MANAGER
// =============================================
async function fmLoad(path, page=1) {
  if (path) S.fm.path = path;
  const tbody = document.getElementById('fm-tbody');
  tbody.innerHTML = '<tr><td colspan="7"><div class="loading"><div class="spinner"></div>Memuat...</div></td></tr>';
  renderBreadcrumb();
  const search = document.getElementById('fm-search').value;
  const r = await api('file.list', {path: S.fm.path, p: page, search}, 'GET');
  if (!r.status) {
    tbody.innerHTML = `<tr><td colspan="7"><div class="empty">Error: ${esc(r.msg)}</div></td></tr>`;
    return;
  }
  const files = r.data || r.DATA || [];
  document.getElementById('fm-status').textContent = `${files.length} item di ${S.fm.path}`;
  S.fm.selected = [];
  renderFileTable(files);
}

function renderFileTable(files) {
  const tbody = document.getElementById('fm-tbody');
  if (!files.length) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty">Folder kosong</div></td></tr>';
    return;
  }
  tbody.innerHTML = files.map(f=>{
    const isDir = f.IsDir || f.is_dir || f.type==='d';
    const name = f.filename || f.name || f.Filename;
    const size = isDir ? '—' : fmtSize(f.FileSize || f.size || 0);
    const perm = f.Perms || f.perms || f.mode || '';
    const owner = f.User || f.user || '';
    const mtime = f.Mtime || f.mtime || f.time || 0;
    const ico = isDir ? '📁' : getFileIcon(name);
    return `<tr oncontextmenu="showCtx(event,'${esc(name)}',${isDir})" ondblclick="${isDir?`navigateTo('${esc(S.fm.path+'/'+name)}')`:`openEditor('${esc(S.fm.path+'/'+name)}')`}">
      <td><input type="checkbox" class="fm-chk" value="${esc(S.fm.path+'/'+name)}" onchange="updateSel(this)"></td>
      <td><div class="file-name"><span class="file-ico">${ico}</span><span class="fname${isDir?' dir':''}">${esc(name)}</span></div></td>
      <td class="muted">${size}</td>
      <td class="mono">${esc(perm)}</td>
      <td><span class="badge badge-gray">${esc(owner)}</span></td>
      <td class="muted">${fmtDate(mtime)}</td>
      <td><div class="actions">
        ${isDir ? `<button class="btn btn-default btn-sm" onclick="navigateTo('${esc(S.fm.path+'/'+name)}')">Buka</button>` : `<button class="btn btn-default btn-sm" onclick="openEditor('${esc(S.fm.path+'/'+name)}')">Edit</button>`}
        <button class="btn btn-default btn-sm" onclick="downloadFile('${esc(S.fm.path+'/'+name)}')">↓</button>
        <button class="btn btn-danger btn-sm" onclick="deleteFile('${esc(S.fm.path+'/'+name)}','${esc(name)}')">✕</button>
      </div></td>
    </tr>`;
  }).join('');
}

function getFileIcon(name) {
  const ext = name.split('.').pop().toLowerCase();
  const icons = {php:'🐘',html:'🌐',htm:'🌐',js:'🟨',css:'🎨',json:'{}',xml:'📄',sql:'🗃',zip:'📦',tar:'📦',gz:'📦',rar:'📦','7z':'📦',jpg:'🖼',jpeg:'🖼',png:'🖼',gif:'🖼',svg:'🖼',mp4:'🎬',mp3:'🎵',pdf:'📕',txt:'📝',sh:'⚡',env:'🔧',conf:'⚙',ini:'⚙',log:'📋'};
  return icons[ext] || '📄';
}

function renderBreadcrumb() {
  const bc = document.getElementById('fm-breadcrumb');
  const parts = S.fm.path.split('/').filter(Boolean);
  let html = `<span class="breadcrumb-seg" onclick="navigateTo('/')">/</span>`;
  let cur = '';
  parts.forEach(p => {
    cur += '/' + p;
    const cp = cur;
    html += `<span class="breadcrumb-sep">›</span><span class="breadcrumb-seg" onclick="navigateTo('${esc(cp)}')">${esc(p)}</span>`;
  });
  bc.innerHTML = html;
}

function navigateTo(path) {
  S.fm.history.push(S.fm.path);
  showPage2('files');
  document.querySelectorAll('.nav-btn').forEach(b=>b.classList.remove('active'));
  document.querySelector('[data-page="files"]')?.classList.add('active');
  fmLoad(path);
}

function fmBack() {
  if (S.fm.history.length) { fmLoad(S.fm.history.pop()); }
  else toast('Sudah di root', 'info');
}

function fmRefresh() { fmLoad(); }

function updateSel(el) {
  if (el.checked) S.fm.selected.push(el.value);
  else S.fm.selected = S.fm.selected.filter(v=>v!==el.value);
  document.getElementById('fm-sel-status').textContent = S.fm.selected.length ? `${S.fm.selected.length} terpilih` : '';
}

function toggleAllFiles(el) {
  document.querySelectorAll('.fm-chk').forEach(c=>{
    c.checked = el.checked;
    updateSel(c);
  });
}

// ---- Context Menu ----
function showCtx(e, name, isDir) {
  e.preventDefault();
  const path = S.fm.path + '/' + name;
  const menu = document.getElementById('ctx-menu');
  menu.style.display = 'block';
  menu.style.left = e.pageX + 'px';
  menu.style.top = e.pageY + 'px';
  menu.innerHTML = `
    ${!isDir ? `<div class="ctx-item" onclick="openEditor('${esc(path)}')">✏️ Edit</div>` : ''}
    ${isDir ? `<div class="ctx-item" onclick="navigateTo('${esc(path)}')">📂 Buka Folder</div>` : ''}
    <div class="ctx-item" onclick="downloadFile('${esc(path)}')">⬇️ Download</div>
    <div class="ctx-item" onclick="modalRename('${esc(path)}','${esc(name)}')">✏️ Rename</div>
    <div class="ctx-item" onclick="modalChmod('${esc(path)}')">🔑 Permissions</div>
    <div class="ctx-sep"></div>
    <div class="ctx-item danger" onclick="deleteFile('${esc(path)}','${esc(name)}')">🗑 Hapus</div>`;
}
document.addEventListener('click', ()=>{ document.getElementById('ctx-menu').style.display='none'; });

// ---- File Actions ----
async function openEditor(path) {
  S.editor.path = path;
  showPage2('editor');
  document.querySelectorAll('.nav-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById('editor-path').textContent = path;
  document.getElementById('editor-textarea').value = 'Memuat...';
  const r = await api('file.content', {path}, 'GET');
  if (r.status !== false) {
    document.getElementById('editor-textarea').value = r.data || r.DATA || '';
  } else {
    toast('Gagal memuat file: ' + r.msg, 'error');
  }
}

function closeEditor() {
  showPage2('files');
  document.querySelector('[data-page="files"]')?.classList.add('active');
}

async function saveFile() {
  const content = document.getElementById('editor-textarea').value;
  const r = await api('file.save', {path: S.editor.path, content});
  if (r.status !== false) toast('File disimpan', 'success');
  else toast('Gagal simpan: ' + r.msg, 'error');
}

async function deleteFile(path, name) {
  if (!confirm(`Hapus "${name}"?\nTindakan ini tidak dapat dibatalkan.`)) return;
  const r = await api('file.delete', {path});
  if (r.status !== false) { toast(`${name} dihapus`, 'success'); fmRefresh(); }
  else toast('Gagal hapus: ' + r.msg, 'error');
}

async function downloadFile(path) {
  const r = await api('file.download_url', {path}, 'GET');
  if (r.status && r.url) window.open(r.url, '_blank');
  else toast('Gagal generate URL: ' + r.msg, 'error');
}

function triggerUpload() { document.getElementById('upload-input').click(); }

async function uploadFiles(files) {
  for (const file of files) {
    const fd = new FormData();
    fd.append('action', 'file.upload');
    fd.append('path', S.fm.path + '/' + file.name);
    fd.append('file', file);
    fd.append(CSRF_TOKEN_NAME, S.csrf);
    // Upload via direct AAPanel URL (bypass API for file upload)
    toast(`Mengupload ${file.name}...`, 'info');
    // Note: upload langsung ke AAPanel membutuhkan token terpisah
    // Implementasi melalui AAPanel upload endpoint
  }
  toast('Untuk upload, gunakan tombol di AAPanel langsung (keamanan)', 'info', 5000);
  fmRefresh();
}

// ---- Modals ----
function modalNewFile() {
  openModal('File Baru', `
    <div class="form-row"><label>Nama File</label>
    <input type="text" id="new-file-name" placeholder="contoh: index.php"></div>`,
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="createFile()">Buat</button>`);
}

async function createFile() {
  const name = document.getElementById('new-file-name').value.trim();
  if (!name) return;
  const r = await api('file.create', {path: S.fm.path + '/' + name});
  closeModal();
  if (r.status !== false) { toast(`${name} dibuat`, 'success'); fmRefresh(); }
  else toast('Gagal: ' + r.msg, 'error');
}

function modalNewDir() {
  openModal('Folder Baru', `
    <div class="form-row"><label>Nama Folder</label>
    <input type="text" id="new-dir-name" placeholder="contoh: uploads"></div>`,
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="createDir()">Buat</button>`);
}

async function createDir() {
  const name = document.getElementById('new-dir-name').value.trim();
  if (!name) return;
  const r = await api('file.mkdir', {path: S.fm.path + '/' + name});
  closeModal();
  if (r.status !== false) { toast(`Folder ${name} dibuat`, 'success'); fmRefresh(); }
  else toast('Gagal: ' + r.msg, 'error');
}

function modalRename(path, name) {
  closeCtx();
  openModal('Rename', `
    <div class="form-row"><label>Nama Baru</label>
    <input type="text" id="rename-name" value="${esc(name)}"></div>`,
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="doRename('${esc(path)}')">Rename</button>`);
}

async function doRename(src) {
  const newName = document.getElementById('rename-name').value.trim();
  if (!newName) return;
  const dir = src.substring(0, src.lastIndexOf('/'));
  const r = await api('file.rename', {src, dst: dir + '/' + newName});
  closeModal();
  if (r.status !== false) { toast('Berhasil di-rename', 'success'); fmRefresh(); }
  else toast('Gagal: ' + r.msg, 'error');
}

function modalChmod(path) {
  closeCtx();
  openModal('Ubah Permissions', `
    <div class="form-row"><label>Mode (octal)</label>
    <input type="text" id="chmod-mode" value="755" maxlength="4"></div>
    <div class="form-row"><label>Owner</label>
    <input type="text" id="chmod-owner" value="www"></div>
    <label class="checkbox-row"><input type="checkbox" id="chmod-rec"> Rekursif</label>`,
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="doChmod('${esc(path)}')">Terapkan</button>`);
}

async function doChmod(path) {
  const mode = document.getElementById('chmod-mode').value;
  const owner = document.getElementById('chmod-owner').value;
  const recursive = document.getElementById('chmod-rec').checked;
  const r = await api('file.chmod', {path, mode, owner, recursive: recursive?1:0});
  closeModal();
  if (r.status !== false) toast('Permission diubah', 'success');
  else toast('Gagal: ' + r.msg, 'error');
}

function modalCompress() {
  if (!S.fm.selected.length) { toast('Pilih file terlebih dahulu', 'info'); return; }
  openModal('Compress Files', `
    <p class="muted" style="margin-bottom:12px">${S.fm.selected.length} file dipilih</p>
    <div class="form-row"><label>Nama Arsip</label>
    <input type="text" id="comp-name" value="archive"></div>
    <div class="form-row"><label>Format</label>
    <select id="comp-type"><option value="zip">ZIP</option><option value="tar.gz">TAR.GZ</option><option value="tar">TAR</option></select></div>`,
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="doCompress()">Compress</button>`);
}

async function doCompress() {
  const name = document.getElementById('comp-name').value;
  const type = document.getElementById('comp-type').value;
  const r = await api('file.compress', {files: JSON.stringify(S.fm.selected), to_path: S.fm.path, name, type});
  closeModal();
  if (r.status !== false) { toast('Berhasil dicompress', 'success'); fmRefresh(); }
  else toast('Gagal: ' + r.msg, 'error');
}

function closeCtx() { document.getElementById('ctx-menu').style.display='none'; }

// =============================================
// WEBSITES
// =============================================
async function loadSites() {
  const tbody = document.getElementById('sites-tbody');
  tbody.innerHTML = '<tr><td colspan="7"><div class="loading"><div class="spinner"></div>Memuat...</div></td></tr>';
  const search = document.getElementById('sites-search').value;
  const r = await api('sites.list', {p: S.sites.page, search}, 'GET');

  if (!r.status && r.msg) { tbody.innerHTML = `<tr><td colspan="7"><div class="empty">${esc(r.msg)}</div></td></tr>`; return; }

  const list = r.data || r.DATA || [];
  S.sites.list = list;
  document.getElementById('sites-count').textContent = `${list.length} website`;

  if (!list.length) { tbody.innerHTML = '<tr><td colspan="7"><div class="empty">Belum ada website</div></td></tr>'; return; }

  tbody.innerHTML = list.map(s=>`
    <tr>
      <td><strong>${esc(s.name||s.siteName||'-')}</strong><br><span class="muted">${esc(s.path||'-')}</span></td>
      <td class="muted" style="max-width:160px;overflow:hidden;text-overflow:ellipsis">${esc(s.path||'-')}</td>
      <td><span class="badge badge-blue">PHP ${esc(s.php_version||s.phpVersion||'-')}</span></td>
      <td><span class="badge ${s.status==1||s.status==='1'?'badge-green':'badge-red'}">${s.status==1?'Aktif':'Stop'}</span></td>
      <td><span class="badge ${s.ssl==1?'badge-green':'badge-gray'}">${s.ssl==1?'SSL':'HTTP'}</span></td>
      <td class="muted">${fmtDate(s.addtime||s.created_at)}</td>
      <td>
        <button class="btn btn-default btn-sm" onclick="siteDetails(${s.id},'${esc(s.name||s.siteName)}')">Detail</button>
        <button class="btn btn-default btn-sm" onclick="siteFiles('${esc(s.path||'')}')" title="Buka File Manager">📁</button>
        ${s.status==1
          ? `<button class="btn btn-danger btn-sm" onclick="sitePause(${s.id},'${esc(s.name||s.siteName)}')">Stop</button>`
          : `<button class="btn btn-primary btn-sm" onclick="siteResume(${s.id},'${esc(s.name||s.siteName)}')">Start</button>`}
      </td>
    </tr>`).join('');
}

function siteFiles(path) {
  if (!path) return;
  navigateTo(path);
}

async function sitePause(id, name) {
  if (!confirm(`Stop website ${name}?`)) return;
  const r = await api('sites.stop', {id, name});
  if (r.status !== false) { toast(`${name} dihentikan`, 'success'); loadSites(); }
  else toast('Gagal: ' + r.msg, 'error');
}

async function siteResume(id, name) {
  const r = await api('sites.start', {id, name});
  if (r.status !== false) { toast(`${name} diaktifkan`, 'success'); loadSites(); }
  else toast('Gagal: ' + r.msg, 'error');
}

function siteDetails(id, name) {
  openModal(`Website: ${name}`, `
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <button class="btn btn-default" onclick="siteDomains(${id},'${esc(name)}')">🌐 Domain</button>
      <button class="btn btn-default" onclick="siteSSL(${id},'${esc(name)}')">🔒 SSL</button>
      <button class="btn btn-default" onclick="sitePhpVer('${esc(name)}')">PHP Version</button>
      <button class="btn btn-default" onclick="siteBackups(${id},'${esc(name)}')">💾 Backup</button>
    </div>`, '');
}

async function siteDomains(id, name) {
  const r = await api('sites.domain.list', {id}, 'GET');
  const domains = r.data || [];
  openModal(`Domain: ${name}`, `
    <table style="width:100%;margin-bottom:12px">
      <thead><tr><th>Domain</th><th>Port</th><th>Aksi</th></tr></thead>
      <tbody>${domains.map(d=>`<tr>
        <td>${esc(d.name||d.domain)}</td>
        <td>${esc(d.port)}</td>
        <td><button class="btn btn-danger btn-sm" onclick="deleteDomain(${id},'${esc(name)}','${esc(d.name||d.domain)}',${d.port})">Hapus</button></td>
      </tr>`).join('')}</tbody>
    </table>
    <div class="form-row"><label>Tambah Domain</label>
    <input type="text" id="new-domain" placeholder="contoh.com"></div>`,
    `<button class="btn btn-default" onclick="closeModal()">Tutup</button>
     <button class="btn btn-primary" onclick="addDomain(${id},'${esc(name)}')">Tambah Domain</button>`);
}

async function addDomain(id, name) {
  const domain = document.getElementById('new-domain').value.trim();
  if (!domain) return;
  const r = await api('sites.domain.add', {id, name, domain});
  if (r.status !== false) { toast('Domain ditambahkan', 'success'); siteDomains(id, name); }
  else toast('Gagal: ' + r.msg, 'error');
}

async function deleteDomain(id, name, domain, port) {
  if (!confirm(`Hapus domain ${domain}?`)) return;
  const r = await api('sites.domain.delete', {id, name, domain, port});
  if (r.status !== false) { toast('Domain dihapus', 'success'); siteDomains(id, name); }
  else toast('Gagal: ' + r.msg, 'error');
}

async function siteSSL(id, name) {
  const r = await api('sites.ssl.info', {id, name}, 'GET');
  const ssl = r.data || {};
  openModal(`SSL: ${name}`, `
    <p style="margin-bottom:12px;color:var(--text2);font-size:12px">
      Status: <strong style="color:${ssl.status?'#3fb950':'var(--red-h)'}">${ssl.status?'Aktif':'Tidak Aktif'}</strong>
      ${ssl.enddate?' — Kadaluarsa: <strong>'+esc(ssl.enddate)+'</strong>':''}
    </p>
    <details style="margin-bottom:14px">
      <summary style="cursor:pointer;color:var(--blue-light);font-size:12px">SSL Manual (PEM)</summary>
      <div class="form-row" style="margin-top:8px"><label>Private Key (.key)</label>
      <textarea id="ssl-key" rows="4" placeholder="-----BEGIN PRIVATE KEY-----"></textarea></div>
      <div class="form-row"><label>Certificate (.crt)</label>
      <textarea id="ssl-crt" rows="4" placeholder="-----BEGIN CERTIFICATE-----"></textarea></div>
    </details>
    <details>
      <summary style="cursor:pointer;color:var(--blue-light);font-size:12px">Let's Encrypt (Free SSL)</summary>
      <div class="form-row" style="margin-top:8px"><label>Domain (satu per baris)</label>
      <textarea id="le-domains" rows="3" placeholder="${esc(name)}">${esc(name)}</textarea></div>
    </details>`,
    `<button class="btn btn-default" onclick="closeModal()">Tutup</button>
     ${ssl.status?`<button class="btn btn-danger" onclick="closeSSL(${id},'${esc(name)}')">Nonaktifkan SSL</button>`:''}
     <button class="btn btn-blue" onclick="applyLE(${id},'${esc(name)}')">Pasang Let's Encrypt</button>
     <button class="btn btn-primary" onclick="setSSL(${id},'${esc(name)}')">Pasang Manual</button>`);
}

async function setSSL(id, name) {
  const key = document.getElementById('ssl-key').value;
  const crt = document.getElementById('ssl-crt').value;
  if (!key||!crt) { toast('Isi key dan certificate', 'error'); return; }
  const r = await api('sites.ssl.set', {id, name, key, crt});
  closeModal();
  if (r.status !== false) toast('SSL dipasang', 'success');
  else toast('Gagal: ' + r.msg, 'error');
}

async function applyLE(id, name) {
  const domains = document.getElementById('le-domains').value;
  toast("Mengajukan SSL Let's Encrypt...", 'info');
  const r = await api('sites.ssl.apply', {id, name, domains});
  closeModal();
  if (r.status !== false) toast("SSL Let's Encrypt berhasil", 'success');
  else toast('Gagal: ' + r.msg, 'error');
}

async function closeSSL(id, name) {
  const r = await api('sites.ssl.close', {id, name});
  closeModal();
  if (r.status !== false) { toast('SSL dinonaktifkan', 'success'); loadSites(); }
  else toast('Gagal: ' + r.msg, 'error');
}

async function sitePhpVer(name) {
  const vr = await api('sites.php_list', {}, 'GET');
  const cr = await api('sites.php_version', {name}, 'GET');
  const vers = vr.data || [];
  const cur = cr.data || '';
  openModal(`PHP Version: ${name}`, `
    <div class="form-row"><label>Versi PHP</label>
    <select id="php-ver">${vers.map(v=>`<option value="${esc(v.version||v)}" ${(v.version||v)===cur?'selected':''}>${esc(v.version||v)}</option>`).join('')}</select></div>`,
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="doSetPhp('${esc(name)}')">Terapkan</button>`);
}

async function doSetPhp(name) {
  const version = document.getElementById('php-ver').value;
  const r = await api('sites.php_version', {name, version});
  closeModal();
  if (r.status !== false) { toast('PHP version diubah', 'success'); loadSites(); }
  else toast('Gagal: ' + r.msg, 'error');
}

async function siteBackups(id, name) {
  const r = await api('sites.backup.list', {id}, 'GET');
  const bkps = r.data || [];
  openModal(`Backup: ${name}`, `
    <table style="width:100%;margin-bottom:12px">
      <thead><tr><th>File</th><th>Ukuran</th><th>Tanggal</th><th>Aksi</th></tr></thead>
      <tbody>${bkps.length ? bkps.map(b=>`<tr>
        <td class="mono">${esc(b.filename||b.name)}</td>
        <td>${fmtSize(b.size)}</td>
        <td class="muted">${fmtDate(b.addtime)}</td>
        <td><button class="btn btn-danger btn-sm" onclick="deleteSiteBackup(${b.id})">Hapus</button></td>
      </tr>`).join('') : '<tr><td colspan="4" class="muted" style="text-align:center;padding:12px">Belum ada backup</td></tr>'}</tbody>
    </table>`,
    `<button class="btn btn-default" onclick="closeModal()">Tutup</button>
     <button class="btn btn-primary" onclick="createSiteBackup(${id},'${esc(name)}')">+ Backup Sekarang</button>`);
}

async function createSiteBackup(id, name) {
  toast('Membuat backup...', 'info');
  const r = await api('sites.backup.create', {id});
  if (r.status !== false) { toast('Backup berhasil', 'success'); siteBackups(id, name); }
  else toast('Gagal: ' + r.msg, 'error');
}

async function deleteSiteBackup(id) {
  if (!confirm('Hapus backup ini?')) return;
  const r = await api('sites.backup.delete', {id});
  if (r.status !== false) toast('Backup dihapus', 'success');
  else toast('Gagal: ' + r.msg, 'error');
}

function modalNewSite() {
  openModal('Tambah Website Baru', `
    <div class="form-grid">
      <div class="form-row"><label>Nama Domain *</label>
      <input type="text" id="ns-name" placeholder="contoh.com"></div>
      <div class="form-row"><label>Port</label>
      <input type="text" id="ns-port" value="80"></div>
      <div class="form-row"><label>Path Root</label>
      <input type="text" id="ns-path" placeholder="/www/wwwroot/contoh.com"></div>
      <div class="form-row"><label>PHP Version</label>
      <select id="ns-php"><option value="80">PHP 8.0</option><option value="74">PHP 7.4</option><option value="81">PHP 8.1</option><option value="82">PHP 8.2</option></select></div>
    </div>
    <div class="form-row"><label>Keterangan</label>
    <input type="text" id="ns-remark" placeholder="Opsional"></div>`,
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="createSite()">Buat Website</button>`);
  document.getElementById('ns-name').addEventListener('input', function() {
    const pf = document.getElementById('ns-path');
    if (!pf.value || pf.value.startsWith('/www/wwwroot/')) pf.value = '/www/wwwroot/' + this.value;
  });
}

async function createSite() {
  const name = document.getElementById('ns-name').value.trim();
  if (!name) { toast('Domain wajib diisi', 'error'); return; }
  const r = await api('sites.create', {
    webname: name,
    path: document.getElementById('ns-path').value || '/www/wwwroot/'+name,
    port: document.getElementById('ns-port').value || '80',
    php_version: document.getElementById('ns-php').value,
    remark: document.getElementById('ns-remark').value,
    domains: name,
  });
  closeModal();
  if (r.status !== false) { toast(`Website ${name} dibuat`, 'success'); loadSites(); }
  else toast('Gagal: ' + r.msg, 'error');
}

// =============================================
// DATABASE
// =============================================
async function loadDbs() {
  const tbody = document.getElementById('db-tbody');
  tbody.innerHTML = '<tr><td colspan="7"><div class="loading"><div class="spinner"></div>Memuat...</div></td></tr>';
  const search = document.getElementById('db-search').value;
  const r = await api('db.list', {p: S.dbs.page, search}, 'GET');
  if (!r.status && r.msg) { tbody.innerHTML = `<tr><td colspan="7"><div class="empty">${esc(r.msg)}</div></td></tr>`; return; }
  const list = r.data || [];
  S.dbs.list = list;
  document.getElementById('db-count').textContent = `${list.length} database`;
  if (!list.length) { tbody.innerHTML = '<tr><td colspan="7"><div class="empty">Belum ada database</div></td></tr>'; return; }
  tbody.innerHTML = list.map(d=>`<tr>
    <td><strong>${esc(d.name)}</strong></td>
    <td class="mono">${esc(d.username)}</td>
    <td><span class="badge badge-blue">${esc(d.address||'%')}</span></td>
    <td class="muted">${esc(d.codeing||d.charset||'utf8mb4')}</td>
    <td class="muted">${esc(d.ps||d.remark||'-')}</td>
    <td class="muted">${fmtDate(d.addtime)}</td>
    <td>
      <button class="btn btn-default btn-sm" onclick="dbResetPass(${d.id},'${esc(d.name)}','${esc(d.username)}')">Reset PW</button>
      <button class="btn btn-default btn-sm" onclick="dbBackup(${d.id},'${esc(d.name)}')">💾 Backup</button>
      <button class="btn btn-danger btn-sm" onclick="dbDelete(${d.id},'${esc(d.name)}')">Hapus</button>
    </td>
  </tr>`).join('');
}

function modalNewDb() {
  openModal('Tambah Database', `
    <div class="form-row"><label>Nama Database *</label>
    <input type="text" id="nd-name" placeholder="mydb"></div>
    <div class="form-row"><label>Username *</label>
    <input type="text" id="nd-user" placeholder="mydb_user"></div>
    <div class="form-row"><label>Password *</label>
    <input type="password" id="nd-pass" placeholder="password kuat">
    <button onclick="genPass()" style="margin-top:4px;background:none;border:none;color:var(--blue-light);cursor:pointer;font-size:11px">Generate password</button></div>
    <div class="form-row"><label>Izin Akses</label>
    <input type="text" id="nd-addr" value="%" placeholder="% atau 127.0.0.1"></div>
    <div class="form-row"><label>Keterangan</label>
    <input type="text" id="nd-remark"></div>`,
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="createDb()">Buat</button>`);
  document.getElementById('nd-name').addEventListener('input', function() {
    const uf = document.getElementById('nd-user');
    if (!uf.value) uf.value = this.value + '_user';
  });
}

function genPass() {
  const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
  let pass = '';
  for (let i=0;i<16;i++) pass += chars[Math.floor(Math.random()*chars.length)];
  document.getElementById('nd-pass').value = pass;
  document.getElementById('nd-pass').type = 'text';
}

async function createDb() {
  const name = document.getElementById('nd-name').value.trim();
  const username = document.getElementById('nd-user').value.trim();
  const password = document.getElementById('nd-pass').value;
  if (!name||!username||!password) { toast('Isi semua field wajib', 'error'); return; }
  const r = await api('db.create', {name, username, password,
    address: document.getElementById('nd-addr').value||'%',
    remark: document.getElementById('nd-remark').value});
  closeModal();
  if (r.status !== false) { toast(`Database ${name} dibuat`, 'success'); loadDbs(); }
  else toast('Gagal: ' + r.msg, 'error');
}

async function dbDelete(id, name) {
  if (!confirm(`Hapus database "${name}"?\nSemua data akan hilang permanen!`)) return;
  const r = await api('db.delete', {id, name});
  if (r.status !== false) { toast(`${name} dihapus`, 'success'); loadDbs(); }
  else toast('Gagal: ' + r.msg, 'error');
}

function dbResetPass(id, name, username) {
  openModal(`Reset Password: ${name}`, `
    <div class="form-row"><label>Password Baru</label>
    <input type="password" id="rp-pass" placeholder="Password baru">
    <button onclick="genPassRp()" style="margin-top:4px;background:none;border:none;color:var(--blue-light);cursor:pointer;font-size:11px">Generate</button></div>`,
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="doResetPass(${id},'${esc(name)}','${esc(username)}')">Reset</button>`);
}

function genPassRp() {
  const chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
  let p='';for(let i=0;i<16;i++)p+=chars[Math.floor(Math.random()*chars.length)];
  document.getElementById('rp-pass').value=p;document.getElementById('rp-pass').type='text';
}

async function doResetPass(id, name, username) {
  const password = document.getElementById('rp-pass').value;
  if (!password) { toast('Isi password baru', 'error'); return; }
  const r = await api('db.reset_password', {id, name, username, password});
  closeModal();
  if (r.status !== false) toast('Password berhasil direset', 'success');
  else toast('Gagal: ' + r.msg, 'error');
}

async function dbBackup(id, name) {
  toast(`Membuat backup ${name}...`, 'info');
  const r = await api('db.backup', {id});
  if (r.status !== false) toast('Backup database berhasil', 'success');
  else toast('Gagal: ' + r.msg, 'error');
}

async function openPhpMyAdmin() {
  const r = await api('db.phpmyadmin', {}, 'GET');
  if (r.status && r.url) {
    S.pmaUrl = r.url;
    showPage2('phpmyadmin');
    document.querySelectorAll('.nav-btn').forEach(b=>b.classList.remove('active'));
    document.querySelector('[data-page="databases"]')?.classList.add('active');
    document.getElementById('pma-frame').src = r.url;
  } else {
    toast('Gagal buka phpMyAdmin: ' + r.msg, 'error');
  }
}

function openPmaNewTab() {
  if (S.pmaUrl) window.open(S.pmaUrl, '_blank');
}

// =============================================
// USER MANAGEMENT
// =============================================
async function loadUsers() {
  const tbody = document.getElementById('users-tbody');
  tbody.innerHTML = '<tr><td colspan="10"><div class="loading"><div class="spinner"></div>Memuat...</div></td></tr>';
  const r = await api('users.list', {}, 'GET');
  if (!r.status) { tbody.innerHTML = `<tr><td colspan="10"><div class="empty">${esc(r.msg)}</div></td></tr>`; return; }
  const list = r.data || [];
  tbody.innerHTML = list.map(u=>{
    const paths = u.allowed_paths ? JSON.parse(u.allowed_paths) : [];
    return `<tr>
      <td><strong>${esc(u.username)}</strong><br><span class="muted">${esc(u.email||'-')}</span></td>
      <td><span class="badge ${u.role==='superadmin'?'badge-red':u.role==='admin'?'badge-yellow':'badge-gray'}">${esc(u.role)}</span></td>
      <td>${chkBadge(u.can_upload)}</td>
      <td>${chkBadge(u.can_delete)}</td>
      <td>${chkBadge(u.can_manage_sites)}</td>
      <td>${chkBadge(u.can_manage_dbs)}</td>
      <td class="muted">${paths.length?paths.join('<br>'):'Semua'}</td>
      <td class="muted">${u.last_login?`${fmtDate(Math.floor(new Date(u.last_login).getTime()/1000))}<br><span style="color:var(--text3)">${esc(u.login_ip||'-')}</span>`:'-'}</td>
      <td><span class="badge ${u.is_active==1?'badge-green':'badge-red'}">${u.is_active==1?'Aktif':'Nonaktif'}</span></td>
      <td>
        <button class="btn btn-default btn-sm" onclick="editUser(${u.id})">Edit</button>
        ${u.role!=='superadmin'?`<button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id},'${esc(u.username)}')">Hapus</button>`:''}
      </td>
    </tr>`;
  }).join('');
}

function chkBadge(v) {
  return v==1 ? '<span class="badge badge-green">✓</span>' : '<span class="badge badge-gray">✗</span>';
}

function modalNewUser() {
  openModal('Tambah User Baru', userForm(), `
    <button class="btn btn-default" onclick="closeModal()">Batal</button>
    <button class="btn btn-primary" onclick="createUser()">Buat User</button>`);
}

function userForm(u={}) {
  return `
    <div class="form-grid">
      <div class="form-row"><label>Username *</label>
      <input type="text" id="uf-user" value="${esc(u.username||'')}"></div>
      <div class="form-row"><label>Password ${u.id?'(kosongkan=tidak ganti)':' *'}</label>
      <input type="password" id="uf-pass"></div>
      <div class="form-row"><label>Email</label>
      <input type="email" id="uf-email" value="${esc(u.email||'')}"></div>
      <div class="form-row"><label>Role</label>
      <select id="uf-role">
        <option value="viewer" ${u.role==='viewer'?'selected':''}>Viewer</option>
        <option value="admin" ${u.role==='admin'?'selected':''}>Admin</option>
        <option value="superadmin" ${u.role==='superadmin'?'selected':''}>Superadmin</option>
      </select></div>
    </div>
    <p style="font-size:11px;color:var(--text3);margin-bottom:8px;font-weight:500">Izin</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:12px">
      <label class="checkbox-row"><input type="checkbox" id="uf-upload" ${u.can_upload!=0?'checked':''}> Upload File</label>
      <label class="checkbox-row"><input type="checkbox" id="uf-delete" ${u.can_delete==1?'checked':''}> Hapus File</label>
      <label class="checkbox-row"><input type="checkbox" id="uf-edit" ${u.can_edit_files!=0?'checked':''}> Edit File</label>
      <label class="checkbox-row"><input type="checkbox" id="uf-sites" ${u.can_manage_sites==1?'checked':''}> Kelola Website</label>
      <label class="checkbox-row"><input type="checkbox" id="uf-dbs" ${u.can_manage_dbs==1?'checked':''}> Kelola Database</label>
      ${u.id?`<label class="checkbox-row"><input type="checkbox" id="uf-active" ${u.is_active!=0?'checked':''}> Aktif</label>`:''}
    </div>
    <div class="form-row"><label>Batasi Akses Path (satu per baris, kosong=semua)</label>
    <textarea id="uf-paths" rows="3" placeholder="/www/wwwroot/domain.com">${esc((u.allowed_paths?JSON.parse(u.allowed_paths):[]||[]).join('\n'))}</textarea></div>`;
}

async function createUser() {
  const username = document.getElementById('uf-user').value.trim();
  const password = document.getElementById('uf-pass').value;
  if (!username||!password) { toast('Username dan password wajib', 'error'); return; }
  const r = await api('users.create', {
    username, password,
    email: document.getElementById('uf-email').value,
    role: document.getElementById('uf-role').value,
    can_upload: document.getElementById('uf-upload').checked?1:0,
    can_delete: document.getElementById('uf-delete').checked?1:0,
    can_edit_files: document.getElementById('uf-edit').checked?1:0,
    can_manage_sites: document.getElementById('uf-sites').checked?1:0,
    can_manage_dbs: document.getElementById('uf-dbs').checked?1:0,
    allowed_paths: document.getElementById('uf-paths').value,
  });
  closeModal();
  if (r.status) { toast('User berhasil dibuat', 'success'); loadUsers(); }
  else toast('Gagal: ' + r.msg, 'error');
}

async function editUser(id) {
  const all = await api('users.list', {}, 'GET');
  const u = (all.data||[]).find(x=>x.id==id);
  if (!u) return;
  openModal(`Edit User: ${u.username}`, userForm(u),
    `<button class="btn btn-default" onclick="closeModal()">Batal</button>
     <button class="btn btn-primary" onclick="updateUser(${id})">Simpan</button>`);
}

async function updateUser(id) {
  const r = await api('users.update', {
    id, email: document.getElementById('uf-email').value,
    role: document.getElementById('uf-role').value,
    password: document.getElementById('uf-pass').value,
    can_upload: document.getElementById('uf-upload').checked?1:0,
    can_delete: document.getElementById('uf-delete').checked?1:0,
    can_edit_files: document.getElementById('uf-edit').checked?1:0,
    can_manage_sites: document.getElementById('uf-sites').checked?1:0,
    can_manage_dbs: document.getElementById('uf-dbs').checked?1:0,
    is_active: document.getElementById('uf-active')?.checked?1:0,
    allowed_paths: document.getElementById('uf-paths').value,
  });
  closeModal();
  if (r.status) { toast('User diupdate', 'success'); loadUsers(); }
  else toast('Gagal: ' + r.msg, 'error');
}

async function deleteUser(id, username) {
  if (!confirm(`Hapus user "${username}"?`)) return;
  const r = await api('users.delete', {id});
  if (r.status) { toast('User dihapus', 'success'); loadUsers(); }
  else toast('Gagal: ' + r.msg, 'error');
}

// =============================================
// AUDIT LOG
// =============================================
async function loadLogs() {
  const tbody = document.getElementById('logs-tbody');
  tbody.innerHTML = '<tr><td colspan="5"><div class="loading"><div class="spinner"></div>Memuat...</div></td></tr>';
  const r = await api('audit.list', {}, 'GET');
  const list = r.data || [];
  tbody.innerHTML = list.map(l=>`<tr>
    <td class="muted">${fmtDate(Math.floor(new Date(l.created_at).getTime()/1000))}</td>
    <td><strong>${esc(l.username)}</strong></td>
    <td><span class="badge ${getActionBadge(l.action)}">${esc(l.action)}</span></td>
    <td class="mono" style="max-width:240px;overflow:hidden;text-overflow:ellipsis">${esc(l.target||'-')}</td>
    <td class="muted">${esc(l.ip||'-')}</td>
  </tr>`).join('') || '<tr><td colspan="5" class="muted" style="text-align:center;padding:16px">Log kosong</td></tr>';
}

function getActionBadge(action) {
  if (action.includes('delete')||action.includes('Delete')) return 'badge-red';
  if (action.includes('create')||action.includes('Create')) return 'badge-green';
  if (action==='login') return 'badge-blue';
  return 'badge-gray';
}

// =============================================
// ENTER KEY SHORTCUT
// =============================================
document.getElementById('login-pass').addEventListener('keydown', e=>{ if(e.key==='Enter') doLogin(); });
document.getElementById('login-user').addEventListener('keydown', e=>{ if(e.key==='Enter') document.getElementById('login-pass').focus(); });
</script>
</body>
</html>
