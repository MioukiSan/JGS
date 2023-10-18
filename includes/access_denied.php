<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <?php require_once 'head.php'; ?>
    <style>
        .card {
            width: 50em;
            height: 26em;
            margin-left: 35em;
            padding: 2.5em;
        }
        @media (max-width: 768px) {
            .card {
                width: 13em; 
                height: auto;
                max-width: 100%;
                margin: 0; 
            }
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="card border shadow text-center" style="width: 25em; height: 15em;">
        <h1><ion-icon name="warning-outline" size="large"></ion-icon> Access Denied</h1>
        <p>You do not have permission to access this page.</p>
        <p>Please <a href="../index.php">log in</a> to access this content.</p>
    </div>
</body>
</html>