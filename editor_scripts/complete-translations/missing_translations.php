<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../deepl/deepl.php');   include_once('../chat-gpt/chat-gpt.php');   include_once('../translation-service/translation-service.php');  

$useDB = $db;
if (getConfig('main_server')) {
    $useDB = $serverDB;
}
$main_language = getConfig('main_language','de');
$main_language = 'de';
$sql = "SELECT
lang.language_key,
lang.id,  -- assuming the column name is language_id
trans1.translation_key,
trans1_language.translation as translation_in_language1,
trans2.translation as translation_in_trans2,
trans2.language as language_in_trans2
FROM
(SELECT DISTINCT language_key, id FROM language WHERE language.language_key != 'hi') lang  -- select language_id here
CROSS JOIN
(SELECT DISTINCT translation_key FROM translation WHERE dont_translate=0) trans1
LEFT JOIN
translation trans1_language
ON trans1.translation_key = trans1_language.translation_key
AND trans1_language.language = '$main_language'
LEFT JOIN
translation trans2
ON trans1.translation_key = trans2.translation_key
AND lang.language_key = trans2.language
WHERE
(trans2.translation IS NULL
OR trans2.translation = '') 
ORDER BY
lang.id
LIMIT 200;";

/// ADD HINDI AGAIN

$results = $useDB->getAllByQuery($sql);


$jsonArray = [];

foreach($results as $result) {
    $translation_key = $result['translation_key'];
    $language_key = $result['language_key'];
    $mainDBResult = $db->getByQuery("SELECT translation FROM translation WHERE translation_key = :translation_key AND language = :language_key",['translation_key'=>$translation_key,'language_key'=>$language_key]);
    $otherLanguagesResult = $useDB->getByQuery("SELECT translation, language FROM translation WHERE translation_key = :translation_key AND language <> :language_key AND translation IS NOT NULL AND translation <> '' ORDER BY (language='$main_language') DESC", ['translation_key' => $translation_key, 'language_key' => $language_key]);
    $cases = ['ignore','delete','dont_translate'];
    $arrayItem = [
        'translation_key' => $translation_key,
        'language_key' => $language_key,
        'language_translated' => translate('language_'.$language_key),
        'case' => 'delete'
    ];
    if(!empty($mainDBResult) && !empty($mainDBResult['translation'])) {
        $arrayItem['case'] = 'import_main_db';
        $arrayItem['translation'] = $mainDBResult['translation'];
        $cases[] = 'import_main_db';
    }
    if(!empty($otherLanguagesResult)) {
        $arrayItem['case'] = 'auto_translate';
        $arrayItem['other_language'] = $otherLanguagesResult['language'];
        $arrayItem['other_language_translated'] = translate('language_'.$otherLanguagesResult['language']);
        $arrayItem['other_translation'] = $otherLanguagesResult['translation'];
        $cases[] = 'auto_translate';
        
        // Check if the translation found in another language has a corresponding translation in the current language
        $suggestedTranslationResult = $useDB->getByQuery(
        "SELECT t1.translation_key, t1.translation
        FROM translation t1
        INNER JOIN translation t2
        ON t1.translation_key = t2.translation_key
        AND t1.language = :language_key
        AND t2.language = :other_language
        AND t2.translation = :other_language_translation
        WHERE
        t1.translation IS NOT NULL
        AND t1.translation <> ''
        ORDER BY t2.id",
        [
            'language_key' => $language_key,
            'other_language' => $otherLanguagesResult['language'],
            'other_language_translation' => $otherLanguagesResult['translation']
        ]
        );
        
        
        if(!empty($suggestedTranslationResult)) {
            $arrayItem['case'] = 'suggest_translation';
            $arrayItem['translation_key'] = $translation_key;
            $arrayItem['language_key'] = $language_key;
            $arrayItem['language_translated'] = translate('language_'.$language_key);
            $arrayItem['suggested_translation'] = $suggestedTranslationResult['translation'];
            $arrayItem['suggested_translation_key'] = $suggestedTranslationResult['translation_key'];
            $cases[] = 'suggest_translation';
        }
    }
    
    if (str_contains($translation_key,' ')) {
        $arrayItem['case'] = 'delete';
    }
    $arrayItem['cases'] = $cases;
    $jsonArray[] = $arrayItem;
}


$jsonOutput = json_encode($jsonArray, JSON_PRETTY_PRINT);
echo $jsonOutput;
?>