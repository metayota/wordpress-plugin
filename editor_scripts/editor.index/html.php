<?php namespace Metayota; include_once('../metayota/library.php');   
    if (isset($_GET['language'])) {
        $language = $_GET['language'];

        // Set a cookie named 'language' with the value of the 'language' query parameter
        setcookie('language', $language, time() + 86400, '/'); // The cookie will expire in 1 day
    }
?><!doctype html>
<html lang="en">
<head>
    <base href="/" target="_blank">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="data:image/x-icon;,">
    <title>Metayota</title>
    <?php if(getConfig('wordpress')): ?>
        <link rel="stylesheet" type="text/css" href="/wp-content/plugins/metayota/editor_scripts/editor.index/editor.index.css">
        <script src='/wp-content/plugins/metayota/editor_scripts/framework/framework.javascript' id='metayota-js-js'></script>
        <script type="text/javascript">
            Resource.scriptsPath = '/wp-content/plugins/metayota/editor_scripts/'
            Resource.wp = true;
        </script>
        <style type="text/css">
            body .j-editor .editor-controls .menu-dropdown .dropdown-form-element {
                background-image: url(/wp-content/plugins/metayota/editor_scripts/rc.icon/menu_white.svg);
            }
            html body {
                background-image: url(/wp-content/plugins/metayota/exr_bg_f.webp);
            }
        </style>
    <?php else: ?>
        <link rel="stylesheet" type="text/css" href="/resource/editor.index.css">
        <link rel="stylesheet" type="text/css" href="/call/rc.choose.background/get_dynamic_css">
        <script type="text/javascript" src="/resource/framework.javascript"></script>
    <?php endif; ?>
    <script type="text/javascript">
        function getBrowserLanguage() {
            return navigator.languages.find(
                lang => ['de', 'en', 'fr', 'zh','es','ja','ru','hi','tr'].some(supportedLang => lang.startsWith(supportedLang))
            );
        }

        function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        }

        var language;
        if (window.location.hostname.includes('.de')) {
        language = 'de';
        } else {
        language = getCookie('language') || getBrowserLanguage() || 'en';

        // Convert language variations to standard language codes
        if (language.startsWith('de')) {
            language = 'de';
        } else if (language.startsWith('en')) {
            language = 'en';
        } else if (language.startsWith('fr')) {
            language = 'fr';
        } else if (language.startsWith('zh')) {
            language = 'zh';
        }
        }

//        var language = window.location.hostname.includes('.de') ? 'de' : 'en';

   <?php if(getConfig('wordpress')): ?>
<?php
function get_current_language() {
    // List of supported languages in your WordPress site
    $supported_languages = ['de', 'en', 'fr', 'zh','es','ja','ru','hi'];

    // Get the current WordPress locale/language, you may retrieve this from user's profile/settings or other ways based on your setup
    $locale = get_locale();

    // Extract the first two characters to get the language code
    $current_language = substr($locale, 0, 2);

    // Check if the current language is supported, if not, default to English ('en')
    if (!in_array($current_language, $supported_languages)) {
        $current_language = 'en';
    }

    return $current_language;
}
$wp_language = get_current_language();
?>

        Resource.appendToAllRequests({language: '<?= $wp_language ?>'});
        window.cacheEnabled$ = true


        window.loadedPromise = fetch('/wp-content/plugins/metayota/resource_pack_<?= $wp_language ?>.json', { credentials: "include" })
        .then(result => {
            return result.text()
        })
        .then(result => {
            return JSON.parse(result)
        })
        .then(result=> {
            Resource.addResourcePack(result)
        })
        <?php else: ?>
        Resource.appendToAllRequests({language: language});
        window.loadedPromise = Resource.loadPack(['editor','form.button','database.table.editor','designer','dialog','dropdown','editor.call','editor.css','editor.defaults','editor.dependencies','form.livesearch','overview','play','ratings','rc.accesscontrol','rc.account','rc.change','rc.db.table','rc.editor.tasks','rc.project','rc.spider','router','search','settings','tabs','toast','todo','translate','vs.codeeditor','db.row.editor','db.table','form.resource','form.text','object.viewparameter','editor.manage.project','editor.parameters','resource.configuration','when','resource','design.item','form.element','base.form.element','editor.addresource','user','login','object.view','innerhtml','stars.rating','task','title','task.list','rc.code','form.radio','where','parametertype','rc.dependencies.view','rc.parameters.view','rc.table','db.list','rc.diff','form.checkbox','form.textarea','text','vscode.inc','design.resparameter','form.array','form.tagtype','form.text.translated','rc.arrange','rc.resource.config','rc.validator.regex','form.number','form.addresource','loader','editor.welcome','resource_type', 'editor.server.login','create-free-webspace','user_obj','paginator','form.options.dbrow']);

        <?php endif; ?>
    </script>
    
    
    <link rel="apple-touch-icon-precomposed" href="/images/icons/apple-touch-icon.png">
    <link rel="icon" href="/images/icons/favicon.ico">
</head>

<body>
    <div id="main-spinner" class="spinner"></div>
    <script type="text/javascript">
        window.loadedPromise.then(result=> {
            let node = Tag.render('editor').node
		    document.body.appendChild(node);
        })
		
    </script>
</body>

</html>