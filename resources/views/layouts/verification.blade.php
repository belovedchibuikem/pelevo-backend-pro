<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .success .icon {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .error .icon {
            background-color: #fee2e2;
            color: #dc2626;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
        }

        p {
            color: #4b5563;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #1d4ed8;
        }

        .error .button {
            background-color: #dc2626;
        }

        .error .button:hover {
            background-color: #b91c1c;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html> 