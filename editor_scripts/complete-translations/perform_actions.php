<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../deepl/deepl.php');   include_once('../chat-gpt/chat-gpt.php');   include_once('../translation-service/translation-service.php');   
    $useDB = $db;
    if (getConfig('main_server')) {
        $useDB = $serverDB;
    }  

    $missingTranslations = $data['missing_translations'];
    $engine = 'deepl';
    $chatGPT = new ChatGPT();
    foreach($missingTranslations as $missing) {

        if ($missing['case'] == 'dont_translate') {
            $useDB->update('translation',['dont_translate'=>1],['translation_key'=>$missing['translation_key']]);
        }

        if ($missing['case'] == 'delete') {   
            $useDB->delete('translation',['translation_key'=>$missing['translation_key'], 'language'=>$missing['language_key']]);
        }

        if ($missing['case'] == 'suggest_translation') {
            $query = "INSERT INTO translation (translation_key, language, translation) 
                    VALUES (:translation_key, :language_key, :translation) 
                    ON DUPLICATE KEY UPDATE translation = :translation";

            $params = [
                'translation_key' => $missing['translation_key'],
                'language_key' => $missing['language_key'],
                'translation' => $missing['suggested_translation'],  // ersetzen Sie dies durch den tatsächlichen Wert
            ];
            
            $useDB->query($query, $params);
        }
        if ($missing['case'] == 'import_main_db') {
            $query = "INSERT INTO translation (translation_key, language, translation) 
                    VALUES (:translation_key, :language_key, :translation) 
                    ON DUPLICATE KEY UPDATE translation = :translation";

            $params = [
                'translation_key' => $missing['translation_key'],
                'language_key' => $missing['language_key'],
                'translation' => $missing['translation'],  // ersetzen Sie dies durch den tatsächlichen Wert
            ];
            
            $useDB->query($query, $params);
        }
        if ($missing['case'] == 'auto_translate') {
            if ($engine == 'chatgpt') {
                $chatMessages = [
                    getMessageObject('I need a translation for a website in the software development field. Please do not add any comments, just the translation. Please translate the following sentence, word, or text from ' . $missing['other_language_translated'] . ' to ' . $missing['language_translated'] . '.', 'assistant'),
                    getMessageObject($missing['other_language_translated'] . ': "' . $missing['other_translation'] . '" ' . $missing['language_translated'] . ':', 'assistant')
                ];
                $translatedText = $chatGPT->chat($chatMessages);
            } else {
                $translatedText = translateDeepL($missing['other_translation'], $missing['other_language'], $missing['language_key']);
            }
            if (empty($translatedText)) {
                success();
                continue;
                die();
            }

            $translatedText = trim($translatedText, "\"'");
            $translatedTranslation = $translatedText; // bitte anpassen je nach Struktur der Antwort

            $insertOrUpdateSql = "
                INSERT INTO translation (translation_key, translation, language) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE translation = ?
            ";
            $useDB->query($insertOrUpdateSql, [$missing['translation_key'], $translatedTranslation, $missing['language_key'], $translatedTranslation]);

        }
        
    }
    success();
?>