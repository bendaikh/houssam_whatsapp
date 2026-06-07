<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cette page ne fonctionne pas</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #fff;
            color: #202124;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .container {
            max-width: 600px;
            text-align: center;
        }
        .icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 24px;
            opacity: 0.7;
        }
        h1 {
            font-size: 22px;
            font-weight: 400;
            color: #202124;
            margin-bottom: 12px;
            line-height: 1.4;
        }
        p {
            font-size: 15px;
            color: #5f6368;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        .code {
            font-size: 13px;
            color: #80868b;
            margin-top: 20px;
        }
        .reload {
            display: inline-block;
            margin-top: 28px;
            padding: 10px 24px;
            background: #1a73e8;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .reload:hover { background: #1765cc; }
    </style>
</head>
<body>
    <div class="container">
        <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#5f6368"/>
        </svg>
        <h1>Cette page ne fonctionne pas</h1>
        <p>Impossible de traiter cette demande pour le moment.</p>
        <p>Veuillez réessayer plus tard.</p>
        <p class="code">HTTP ERROR 503</p>
        <a href="javascript:location.reload()" class="reload">Actualiser</a>
    </div>
</body>
</html>
