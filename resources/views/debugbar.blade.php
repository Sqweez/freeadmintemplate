<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div id="apiResponse"></div>
<script>
    fetch('http://127.0.0.1:8000/api/opt/v1/home')
    .then(res => res.json())
    .then(data => {
        document.querySelector('#apiResponse').innerHTML = JSON.stringify(data)
    })
</script>
</body>
</html>
