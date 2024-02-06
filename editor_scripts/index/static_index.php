<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   
sendCacheHeaders();
?><!doctype html>
<html lang="en">
<head>
    <title>Your website title</title>
    <base href="/">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Description for your website!" />
    <meta name="keywords" content="website,keywords,..." />
    <meta name="robots" content="index, follow" />
    <meta name="revisit-after" content="1 week" />
    <script type="text/javascript" src="/resource/framework.javascript"></script>
    <script type="text/javascript">
        Tag.registerAndLoad('app')
    </script>
    <link rel="stylesheet" type="text/css" href="/resource/index.css">
    <link rel="apple-touch-icon-precomposed" href="/images/icons/apple-touch-icon.png">
    <link rel="icon" href="/images/icons/favicon.ico">
</head>
<body>
    <script id="main-script" type="text/javascript">
        document.body.appendChild(Tag.render('app').node);
        document.getElementById('main-script').remove();
    </script>
</body>
</html>