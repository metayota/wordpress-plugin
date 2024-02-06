<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../deepl/deepl.php');   include_once('../chat-gpt/chat-gpt.php');   include_once('../translation-service/translation-service.php');  

$sql = "
SELECT t1.translation_key, t1.translation AS english_translation
FROM translation t1
LEFT JOIN translation t2 ON t1.translation_key = t2.translation_key AND t2.language = 'de'
WHERE t1.language = 'en' AND (t2.translation IS NULL OR t2.translation = '') AND t1.translation IS NOT NULL LIMIT 50
";

$missingTranslations = $db->getAllByQuery($sql);

function getMessageObject($message,$role) {
    return array(
    'role' => $role,
    'content' => $message
    );
}

$chatGPT = new ChatGPT();
foreach ($missingTranslations as $translation) {
    if (!empty($translation['english_translation'])) {
        $chatMessages = [
            getMessageObject('Ich benötige für eine Website im Bereich Software-Entwicklung eine Übersetzung. Bitte keine Kommentare hinzufügen, sondern nur die Übersetzung. Bitte übersetzen Sie folgenden Satz, folgendes Wort bzw. folgenden Text von Englisch nach Deutsch.', 'assistant'),
            getMessageObject('Englisch: "'.$translation['english_translation'].'" Deutsch:', 'assistant')
        ];
        
        $translatedText = $chatGPT->chat($chatMessages);
        echo "<br/><br/>".$translation['translation_key']."<br/>";
        echo $translation['english_translation'];
        
        
        $translatedText = trim($translatedText, "\"'");
        $germanTranslation = $translatedText; // bitte anpassen je nach Struktur der Antwort
        $insertOrUpdateSql = "
        INSERT INTO translation (translation_key, translation, language)
        VALUES (?, ?, 'de')
        ON DUPLICATE KEY UPDATE translation = ?
        ";
        $db->query($insertOrUpdateSql, [$translation['translation_key'], $germanTranslation, $germanTranslation]);
    }
}
?>