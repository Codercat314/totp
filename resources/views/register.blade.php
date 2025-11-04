<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        label{
            display:block;
            margin: 1em;
        }
        .error{
            background-color: pink;
            color: darkred;
        }

    </style>
</head>
<body>
    @if(isset($message))
    <p class="error">{{$message}}</p>
    @endif
    <h3>Register</h3>
    <form method="post">
        <label>Namn: <input type="text" name="name" placeholder="Enter your name"></label>
        <label>Epost: <input type="email" name="email" placeholder="Enter your Email"></label>
        <input type="submit" value="Register">
    </form>
</body>
</html>