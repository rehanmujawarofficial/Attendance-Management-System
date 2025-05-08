<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Well Dom</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        h1 {
            font-size: 64px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 40px;
            color: #333;
        }

        .login-btn {
            padding: 15px 30px;
            font-size: 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-transform: uppercase;
        }

        .login-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h1>Well come</h1>
    <form action="/php/login,php" method="post">
        <button type="submit" class="login-btn">Login</button>
    </form>

</body>
</html>
