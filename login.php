<?php
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
<<<<<<< HEAD
    header("Location: " . (isAdmin() ? 'home.php' : 'operator_dashboard.php'));
=======
    header("Location: home.php");
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $conn = getDBConnection();
<<<<<<< HEAD
        $stmt = $conn->prepare("SELECT id, username, password, full_name, role, section, product_code FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
=======
        $stmt = $conn->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
        $stmt->close();
        $conn->close();

        $valid = false;
        if ($user) {
<<<<<<< HEAD
            if (password_verify($password, $user['password'])) $valid = true;
            elseif ($user['password'] === $password) $valid = true;
        }

        if ($valid) {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['full_name']    = $user['full_name'];
            $_SESSION['role']         = $user['role'];
            $_SESSION['section']      = $user['section'];
            $_SESSION['product_code'] = $user['product_code'];
            header("Location: " . ($user['role'] === 'admin' ? 'home.php' : 'operator_dashboard.php'));
            exit();
        } else {
            $error = 'Invalid credentials. Please check your username and password.';
        }
    }
}

// Section colour map for the visual hint panel
$sections = [
    ['code'=>'OFV-556','name'=>'5.56 Bullet','color'=>'#D97706','bg'=>'#FFFBEB','user'=>'op_556','pass'=>'ofv556'],
    ['code'=>'OFV-762','name'=>'7.62 Bullet','color'=>'#7C3AED','bg'=>'#F5F3FF','user'=>'op_762','pass'=>'ofv762'],
    ['code'=>'OFV-PRM','name'=>'Primer',      'color'=>'#0891B2','bg'=>'#F0F9FF','user'=>'op_prm','pass'=>'ofvprm'],
    ['code'=>'OFV-CAL','name'=>'Calibur',     'color'=>'#059669','bg'=>'#F0FDF4','user'=>'op_cal','pass'=>'ofvcal'],
    ['code'=>'OFV-PKG','name'=>'Packing',     'color'=>'#DB2777','bg'=>'#FDF2F8','user'=>'op_pkg','pass'=>'ofvpkg'],
];
=======
            if (password_verify($password, $user['password'])) {
                $valid = true;
            } elseif ($user['password'] === $password) {
                $valid = true;
            }
        }

        if ($valid) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            header("Location: home.php");
            exit();
        } else {
            $error = 'Invalid username or password. Please try again.';
        }
    }
}
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
?>
<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< HEAD
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In &mdash; OFV Inventory Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --navy:#0F2044; --accent:#2563EB; --accent2:#1D4ED8;
            --bg:#F1F5F9; --surface:#fff; --border:#E2E8F0; --border-mid:#CBD5E1;
            --text:#0F172A; --muted:#64748B; --subtle:#94A3B8;
            --danger:#DC2626; --font:'DM Sans',-apple-system,BlinkMacSystemFont,sans-serif;
        }
        body { font-family:var(--font); background:var(--bg); min-height:100vh; display:flex; align-items:center; justify-content:center; -webkit-font-smoothing:antialiased; }

        .login-shell { display:flex; width:100%; max-width:1000px; min-height:600px; border-radius:18px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.14),0 4px 20px rgba(0,0,0,0.08); margin:24px; }

        /* ── LEFT PANEL ── */
        .panel-left { flex:1; background:var(--navy); padding:48px 44px; display:flex; flex-direction:column; justify-content:space-between; position:relative; overflow:hidden; }
        .panel-left::before { content:''; position:absolute; top:-100px; right:-100px; width:340px; height:340px; background:radial-gradient(circle,rgba(37,99,235,0.22) 0%,transparent 70%); border-radius:50%; }
        .panel-left::after  { content:''; position:absolute; bottom:-80px; left:-80px;  width:280px; height:280px; background:radial-gradient(circle,rgba(37,99,235,0.1)  0%,transparent 70%); border-radius:50%; }

        .panel-brand { position:relative; z-index:1; }
        .emblem-wrap { display:flex; align-items:center; gap:14px; margin-bottom:30px; }
        .panel-title { font-size:28px; font-weight:700; color:#fff; line-height:1.2; letter-spacing:-0.02em; margin-bottom:12px; }
        .panel-title span { color:var(--accent); display:block; }
        .panel-desc { font-size:13.5px; color:rgba(255,255,255,0.45); line-height:1.75; max-width:300px; }

        .panel-features { position:relative; z-index:1; }
        .feat { display:flex; align-items:flex-start; gap:14px; margin-bottom:18px; }
        .feat:last-child { margin-bottom:0; }
        .feat-dot { width:36px; height:36px; background:rgba(37,99,235,0.18); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#60A5FA; font-size:14px; }
        .feat-name { font-size:13px; font-weight:600; color:rgba(255,255,255,0.88); }
        .feat-sub  { font-size:12px; color:rgba(255,255,255,0.38); margin-top:2px; }

        /* Operator accounts mini-list */
        .op-accounts { position:relative; z-index:1; }
        .op-accounts-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:rgba(255,255,255,0.25); margin-bottom:10px; }
        .op-pill { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600; color:rgba(255,255,255,0.7); border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.04); margin:3px 4px 3px 0; }
        .op-dot { width:7px; height:7px; border-radius:50%; }

        .panel-gov { position:relative; z-index:1; font-size:10.5px; color:rgba(255,255,255,0.18); letter-spacing:0.02em; }

        /* ── RIGHT PANEL ── */
        .panel-right { width:430px; flex-shrink:0; background:var(--surface); padding:48px 44px; display:flex; flex-direction:column; justify-content:center; }

        .login-head { margin-bottom:30px; }
        .login-head h1 { font-size:24px; font-weight:700; color:var(--text); letter-spacing:-0.02em; }
        .login-head p  { font-size:13.5px; color:var(--muted); margin-top:6px; }

        .form-group { margin-bottom:18px; }
        .form-label { display:block; font-size:12.5px; font-weight:600; color:#334155; margin-bottom:7px; }

        .inp-wrap { position:relative; }
        .inp-icon { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:var(--subtle); font-size:13px; pointer-events:none; }
        .form-control { width:100%; padding:10px 13px 10px 38px; border:1.5px solid var(--border); border-radius:8px; font-size:14px; font-family:var(--font); color:var(--text); background:var(--surface); outline:none; transition:border-color 150ms,box-shadow 150ms; appearance:none; }
        .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
        .form-control::placeholder { color:#CBD5E1; }

        .pw-wrap { position:relative; }
        .pw-toggle { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--subtle); font-size:13px; padding:4px; transition:color 150ms; }
        .pw-toggle:hover { color:#475569; }

        .form-utils { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; }
        .chk-label { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted); cursor:pointer; user-select:none; }
        .chk-label input { width:15px; height:15px; accent-color:var(--accent); cursor:pointer; }

        .btn-signin { width:100%; padding:11px 20px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-size:14.5px; font-weight:700; font-family:var(--font); cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:background 150ms,transform 100ms,box-shadow 150ms; box-shadow:0 1px 3px rgba(37,99,235,0.3); }
        .btn-signin:hover { background:var(--accent2); transform:translateY(-1px); box-shadow:0 4px 14px rgba(37,99,235,0.35); }
        .btn-signin:active { transform:translateY(0); }

        .alert-error { display:flex; align-items:flex-start; gap:10px; padding:12px 14px; background:#FEF2F2; border:1px solid #FECACA; border-radius:8px; margin-bottom:20px; font-size:13px; color:var(--danger); }

        /* Credentials hint box */
        .creds-box { margin-top:24px; padding-top:20px; border-top:1px solid var(--border); }
        .creds-label { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--subtle); margin-bottom:10px; }
        .creds-grid  { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
        .cred-card   { background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:10px 12px; cursor:pointer; transition:border-color 150ms,background 150ms; }
        .cred-card:hover { border-color:var(--accent); background:var(--surface); }
        .cred-card .cc-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--subtle); margin-bottom:4px; }
        .cred-card .cc-user  { font-size:12.5px; font-weight:700; color:var(--text); }
        .cred-card .cc-role  { font-size:11px; color:var(--muted); margin-top:1px; }
        .cred-card.admin { border-left:3px solid var(--accent); }
        .cred-dot { display:inline-block; width:7px; height:7px; border-radius:50%; margin-right:4px; vertical-align:middle; }

        @media(max-width:820px){ .panel-left{display:none;} .panel-right{width:100%;padding:36px 28px;} .login-shell{max-width:460px;} }
        @media(max-width:480px){ .panel-right{padding:28px 20px;} .creds-grid{grid-template-columns:1fr;} }
    </style>
</head>
<body>

<div class="login-shell">
    <!-- LEFT PANEL -->
    <div class="panel-left">
        <div class="panel-brand">
            <div class="emblem-wrap">
                <img src="/ims-enterprise/ministry_of_defence.png" alt="Government of India Emblem"
                     style="width:44px;height:44px;object-fit:contain;">
                <div>
                    <div style="font-size:18px;font-weight:700;color:#fff;letter-spacing:-0.01em;">OFV &ndash; IMS</div>
                    <div style="font-size:10px;color:rgba(255,255,255,0.38);text-transform:uppercase;letter-spacing:0.06em;margin-top:2px;">Ministry of Defence</div>
                </div>
            </div>
            <div class="panel-title">Inventory<span>Management System</span></div>
            <p class="panel-desc">Ordnance Factory Varangaon &mdash; Secure, role-based inventory control with complete audit trails.</p>
        </div>

        <div class="panel-features">
            <div class="feat"><div class="feat-dot"><i class="fas fa-user-shield"></i></div><div><div class="feat-name">Role-Based Access</div><div class="feat-sub">Admin &amp; section-level operators</div></div></div>
            <div class="feat"><div class="feat-dot"><i class="fas fa-history"></i></div><div><div class="feat-name">Complete Audit Trail</div><div class="feat-sub">Every change logged automatically</div></div></div>
            <div class="feat"><div class="feat-dot"><i class="fas fa-bell"></i></div><div><div class="feat-name">Low Stock Monitoring</div><div class="feat-sub">Real-time threshold alerts</div></div></div>
        </div>

        <div class="op-accounts">
            <div class="op-accounts-title">Operator Sections</div>
            <?php foreach($sections as $s): ?>
            <span class="op-pill"><span class="op-dot" style="background:<?= $s['color'] ?>;"></span><?= $s['name'] ?></span>
            <?php endforeach; ?>
        </div>

        <div class="panel-gov">Ministry of Defence &nbsp;&bull;&nbsp; Government of India &nbsp;&bull;&nbsp; <?= date('Y') ?></div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="panel-right">
        <div class="login-head">
            <h1>Welcome back</h1>
            <p>Sign in with your assigned credentials</p>
        </div>

        <?php if ($error): ?>
        <div class="alert-error" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate autocomplete="on">
            <div class="form-group">
                <label class="form-label" for="username">Username / Service ID</label>
                <div class="inp-wrap">
                    <i class="inp-icon fas fa-user"></i>
                    <input type="text" id="username" name="username" class="form-control"
                        placeholder="Enter your username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        autocomplete="username" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="inp-wrap pw-wrap">
                    <i class="inp-icon fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Enter your password"
                        autocomplete="current-password" required>
                    <button type="button" class="pw-toggle" onclick="togglePw()" id="pwBtn" aria-label="Toggle password">
                        <i class="fas fa-eye" id="pwIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-utils">
                <label class="chk-label"><input type="checkbox" name="remember_me"> Remember me</label>
            </div>

            <button type="submit" class="btn-signin">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <!-- Quick-fill credential cards -->
        <div class="creds-box">
            <div class="creds-label">Quick Access &mdash; Click to fill</div>
            <div class="creds-grid">
                <div class="cred-card admin" onclick="fillCreds('admin','admin123')">
                    <div class="cc-label">Administrator</div>
                    <div class="cc-user"><span class="cred-dot" style="background:#2563EB;"></span>admin</div>
                    <div class="cc-role">Full inventory access</div>
                </div>
                <?php foreach($sections as $s): ?>
                <div class="cred-card" style="border-left:3px solid <?= $s['color'] ?>;" onclick="fillCreds('<?= $s['user'] ?>','<?= $s['pass'] ?>')">
                    <div class="cc-label">Operator</div>
                    <div class="cc-user"><span class="cred-dot" style="background:<?= $s['color'] ?>;"></span><?= $s['user'] ?></div>
                    <div class="cc-role"><?= $s['name'] ?> section</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePw(){
        const f=document.getElementById('password'),i=document.getElementById('pwIcon');
        f.type=f.type==='password'?'text':'password';
        i.className=f.type==='text'?'fas fa-eye-slash':'fas fa-eye';
    }
    function fillCreds(u,p){
        document.getElementById('username').value=u;
        document.getElementById('password').value=p;
        document.getElementById('username').focus();
    }
</script>
=======
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ordnance Factory Varangaon - Login</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #1a3a5c 0%, #0d2137 50%, #2e5c3a 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .login-container {
      background-color: #ffffff;
      padding: 40px 35px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 400px;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
    }

    .header img {
      width: 70px;
      height: 70px;
      margin-bottom: 12px;
    }

    .header h1 {
      font-size: 18px;
      color: #1a3a5c;
      font-weight: bold;
      margin-bottom: 4px;
    }

    .header p {
      font-size: 13px;
      color: #666;
    }

    .divider {
      border: none;
      border-top: 1px solid #ddd;
      margin: 20px 0;
    }

    /* Error message */
    .alert-error {
      background: #f8d7da;
      border: 1px solid #f5c6cb;
      border-left: 4px solid #dc3545;
      color: #721c24;
      padding: 10px 14px;
      border-radius: 5px;
      font-size: 13px;
      margin-bottom: 18px;
    }

    .form-group { margin-bottom: 18px; }

    .form-group label {
      display: block;
      font-size: 14px;
      color: #333;
      margin-bottom: 6px;
      font-weight: bold;
    }

    .form-group input[type="text"],
    .form-group input[type="password"] {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
      color: #333;
      outline: none;
      transition: border-color 0.2s;
    }

    .form-group input:focus { border-color: #1a3a5c; }

    .password-wrapper { position: relative; }
    .password-wrapper input { width: 100%; padding-right: 40px; }

    .eye-label {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #aaa;
      line-height: 0;
    }
    .eye-label:hover { color: #1a3a5c; }

    .form-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      font-size: 13px;
    }

    .form-row label {
      display: flex;
      align-items: center;
      gap: 6px;
      color: #555;
      cursor: pointer;
      font-weight: normal;
    }

    .form-row a { color: #1a3a5c; text-decoration: none; }
    .form-row a:hover { text-decoration: underline; }

    .btn-login {
      width: 100%;
      padding: 11px;
      background-color: #1a3a5c;
      color: #ffffff;
      font-size: 15px;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    .btn-login:hover { background-color: #14304d; }

    .footer {
      text-align: center;
      margin-top: 20px;
      font-size: 12px;
      color: #999;
    }
  </style>
</head>
<body>

  <div class="login-container">

    <div class="header">
      <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" alt="Govt Logo" />
      <h1>Ordnance Factory Varangaon</h1>
      <p>Ministry of Defence</p>
    </div>

    <hr class="divider" />

    <?php if ($error): ?>
    <div class="alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">

      <div class="form-group">
        <label for="username">Employee / Service ID</label>
        <input type="text" id="username" name="username"
               placeholder="Enter your service ID"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               required autofocus />
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="password-wrapper">
          <input type="password" name="password" id="passwordField"
                 placeholder="Enter your password" required />
          <span class="eye-label" onclick="togglePass()" id="eyeIcon">
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg id="eyeClosed" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
              <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
          </span>
        </div>
      </div>

      <div class="form-row">
        <label>
          <input type="checkbox" /> Remember me
        </label>
        <a href="#">Forgot password?</a>
      </div>

      <button type="submit" class="btn-login">Login</button>

    </form>

    <div class="footer">
      &copy; <?= date('Y') ?> Ordnance Factory Varangaon. All rights reserved.
    </div>

  </div>

  <script>
    function togglePass() {
      const field = document.getElementById('passwordField');
      const open  = document.getElementById('eyeOpen');
      const closed = document.getElementById('eyeClosed');
      if (field.type === 'password') {
        field.type = 'text';
        open.style.display   = 'none';
        closed.style.display = 'block';
      } else {
        field.type = 'password';
        open.style.display   = 'block';
        closed.style.display = 'none';
      }
    }
  </script>

>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
</body>
</html>
