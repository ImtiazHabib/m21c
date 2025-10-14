<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hello </title>
</head>
<body>
    
       <p> User name : {{ $user->name }}</p>
       <p> Your password rest link is here </p>
       <p> <a href="{{ url('/password_reset_link?token=.'. $token) }}"> Rest Link </a></p>
</body>
</html>