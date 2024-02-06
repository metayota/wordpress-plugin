<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../deepl/deepl.php');   include_once('../chat-gpt/chat-gpt.php');   
    $mainLanguage = getConfig('main_language');
    // Assuming $data is the data received from your JavaScript
    $existingTranslations = $data['existing_translations'];
    $neededLanguages = $data['needed_languages'];
    $translationKey = $data['translation_key'];
    
    // Construct a string that includes the existing translations
    $translated = [];
    foreach ($neededLanguages as $neededLanguage) {
        
        if (!empty(getConfig('deepl_api_key'))) {
            foreach ($existingTranslations as $existingTranslation) {
                if (!empty($existingTranslation['translation'])) {
                    $translated[$neededLanguage] = translateDeepL( $existingTranslation['translation'], $existingTranslation['language'], $neededLanguage);
                    continue;
                }
            }
        } else {
            $chatGPT = new ChatGPT();
            $chatMessages = [];
            $existingTranslationsStr = "I have a word, or sentence, or text in ".count($existingTranslations)." languages. I want to translate it to ".translateLanguage($mainLanguage,'language_'.$neededLanguage).". Please just give me the translated text, no other comments. This is the translation key '$translationKey', it could give hints about the context. The following words are to translate:";
            
            $chatMessages[] = getMessageObject($existingTranslationsStr, 'assistant');
            foreach ($existingTranslations as $existingTranslation) {
                $gpttext =  translateLanguage($mainLanguage,'language_'.$existingTranslation['language']).": \"{$existingTranslation['translation']}\"";
                $chatMessages[] = getMessageObject($gpttext, 'assistant');
            }
            $chatMessages[] = getMessageObject("Translate it to ".translateLanguage($mainLanguage,'language_'.$neededLanguage).": ", 'assistant');
            

            // Get the translation from GPT-3
            $translatedText = $chatGPT->chat($chatMessages);
            $translatedText = trim($translatedText, "'\""); 
            $translated[$neededLanguage] = $translatedText;
        }
    }

    echo json_encode($translated);
?>