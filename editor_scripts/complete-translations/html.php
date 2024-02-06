<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../deepl/deepl.php');   include_once('../chat-gpt/chat-gpt.php');   include_once('../translation-service/translation-service.php');  ?><h1><?= translate('complete_translations') ?></h1>
<div if="this.missing_translations != undefined && this.missing_translations.length > 0">
    <div for="missing_translation of this.missing_translations">
        <div style="margin-top:32px;margin-bottom:32px">
            <b>{missing_translation.translation_key} ({missing_translation.language_translated})</b><br/>
            
            <form.radio (change)="this.setCase(missing_translation, event)" value="{missing_translation.case}" options="{missing_translation.cases}" ></form.radio>
            
            <div style="margin-left:32px" if="missing_translation.case == 'auto_translate'">
                <b>{missing_translation.other_language_translated}: </b>
                {missing_translation.other_translation}
            </div>
            <div style="margin-left:32px" if="missing_translation.case == 'import_main_db'">
                {missing_translation.translation}
            </div>
            <div style="margin-left:32px" if="missing_translation.case == 'suggest_translation'">
                <innerhtml value="{ translate('already_translated_other_key',{suggested_translation:missing_translation.suggested_translation, other_language:missing_translation.other_language_translated,other_translation:missing_translation.other_translation,other_translation_key:missing_translation.suggested_translation_key}) }"></innerhtml>
                
            </div>
        </div>
    </div> 
    <p if="this.waiting">
        <loader></loader>
        <?= translate('please_wait_translations') ?>
    </p>
    <form.button enabled="{!this.waiting}" (click)="this.performActions()" label="<?= translate('submit') ?>"></form.button>
</div>
<div if="this.missing_translations != undefined && this.missing_translations.length == 0">
    <p><?= translate('no_missing_translations') ?></p>
</div>