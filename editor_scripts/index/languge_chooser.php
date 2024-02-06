<?php  include_once('../metayota/library.php');  ?><html><head></head><body>
<script type="text/javascript">
// Get the browser language preferences
var userLangs = navigator.languages || [navigator.language || navigator.userLanguage];

// Define the supported languages and their corresponding language codes
var supportedLanguages = {
  "de": "/de",
  "fr": "/fr",
  "zh": "/zh",
  "en": "/en"
};

// Define the default language
var defaultLanguage = "/en"; // Change this to your desired default language

// Redirect based on the first matching language preference or default language
var languageMatched = false;
for (var i = 0; i < userLangs.length; i++) {
  var lang = userLangs[i].toLowerCase().substr(0, 2);
  if (supportedLanguages.hasOwnProperty(lang)) {
    window.location.href = supportedLanguages[lang] + window.location.pathname + window.location.search;
    languageMatched = true;
    break;
  }
}

// If no language preference matches, redirect to the default language
if (!languageMatched) {
  window.location.href = defaultLanguage + window.location.pathname + window.location.search;
}
</script></body></html>