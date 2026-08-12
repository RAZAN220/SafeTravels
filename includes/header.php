<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Travels – Travel Safety Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root { --primary: #1a73e8; --primary-dark: #1557b0; --sidebar-width: 260px; --header-height: 60px; --font: 'Inter', sans-serif; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background: #f0f4f8; font-family: var(--font); }
        .sidebar { position:fixed; top:0; left:0; width:var(--sidebar-width); height:100vh; background:#0d1b2a; color:#fff; z-index:1050; overflow-y:auto; padding-bottom:20px; transition:transform 0.3s; }
        .sidebar-brand { padding:18px 20px; border-bottom:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; gap:12px; }
        .sidebar-brand i { font-size:28px; color:#4fc3f7; }
        .sidebar-brand span { font-weight:700; font-size:18px; }
        .sidebar-brand small { display:block; font-size:11px; color:#81d4fa; }
        .sidebar-menu { padding:12px 16px; }
        .sidebar-menu .menu-label { font-size:11px; font-weight:600; text-transform:uppercase; color:#4fc3f7; padding:12px 12px 6px; letter-spacing:0.5px; }
        .sidebar-menu a { display:flex; align-items:center; gap:14px; padding:10px 14px; margin:2px 0; border-radius:10px; color:#b0bec5; text-decoration:none; font-weight:500; font-size:14px; transition:0.2s; }
        .sidebar-menu a i { width:20px; color:#78909c; }
        .sidebar-menu a:hover { background:rgba(79,195,247,0.15); color:#fff; }
        .sidebar-menu a:hover i { color:#4fc3f7; }
        .sidebar-menu a.active { background:#1a73e8; color:#fff; }
        .sidebar-menu a.active i { color:#fff; }
        .main-content { margin-left:var(--sidebar-width); min-height:100vh; }
        .top-nav { height:var(--header-height); background:#fff; border-bottom:1px solid rgba(0,0,0,0.06); padding:0 30px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:1040; box-shadow:0 1px 4px rgba(0,0,0,0.03); }
        .top-nav .toggle-sidebar { display:none; background:none; border:none; font-size:22px; color:#0d1b2a; }
        .top-nav .user-dropdown { display:flex; align-items:center; gap:16px; }
        .top-nav .user-dropdown .avatar { width:38px; height:38px; border-radius:50%; background:#1a73e8; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:16px; }
        .page-content { padding:24px 30px 40px; }
        .stat-card { background:#fff; border-radius:16px; padding:20px 24px; border:1px solid rgba(0,0,0,0.05); height:100%; transition:0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
        .stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.07); }
        .stat-card .stat-number { font-size:28px; font-weight:700; color:#0d1b2a; }
        .stat-card .stat-label { font-size:14px; color:#6c757d; }
        .stat-card .stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; }
        .card-custom { background:#fff; border-radius:16px; border:1px solid rgba(0,0,0,0.05); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
        .card-custom .card-header { background:transparent; border-bottom:1px solid #f0f0f0; padding:18px 24px; font-weight:600; color:#0d1b2a; }
        .card-custom .card-body { padding:24px; }
        .auth-wrapper { min-height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#e3f2fd,#bbdefb); padding:20px; }
        .auth-card { background:#fff; border-radius:24px; padding:48px 40px; max-width:440px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.08); }
        .form-control, .form-select { border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; font-size:14px; }
        .form-control:focus, .form-select:focus { border-color:#1a73e8; box-shadow:0 0 0 4px rgba(26,115,232,0.12); }
        .btn { border-radius:10px; font-weight:600; padding:10px 22px; }
        .btn-primary { background:#1a73e8; border-color:#1a73e8; }
        .btn-primary:hover { background:#1557b0; border-color:#1557b0; transform:translateY(-1px); box-shadow:0 4px 12px rgba(26,115,232,0.3); }
        .btn-danger { background:#d32f2f; border-color:#d32f2f; }
        .btn-danger:hover { background:#b71c1c; border-color:#b71c1c; }
        .map-container { height:400px; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0; }
        .alert-critical { background:#ffebee; border-left:4px solid #c62828; }
        .alert-high { background:#fff3e0; border-left:4px solid #e65100; }
        .alert-medium { background:#fff8e1; border-left:4px solid #f9a825; }
        .place-icon { font-size:24px; width:45px; text-align:center; }
        .emergency-card { background:#ffebee; border:2px solid #c62828; border-radius:12px; padding:20px; }
        @media (max-width:992px) { .sidebar { transform:translateX(-100%); } .sidebar.show { transform:translateX(0); } .main-content { margin-left:0; } .top-nav .toggle-sidebar { display:block; } .page-content { padding:16px; } }
        @media (max-width:576px) { .auth-card { padding:30px 20px; } .stat-card .stat-number { font-size:22px; } }
    </style>
</head>
<body>