<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../deepl/deepl.php');   include_once('../chat-gpt/chat-gpt.php');  ?><div class="form-text-translatable {this.class}"> 
	<label if="this.label !== undefined" class="form-label">{translate(this.label)} (<a (click)="this.toggleTranslationInfo()"><?= translate('field_translated') ?></a>, <a (click)="this.checkAutoTranslate()"><?= translate('autotranslate_label') ?></a>)</label>
    <div if="!this.unique">
        <dropdown (change)="this.setExistingTranslation(event)" options="{this.existing_options}"></dropdown>
    </div>
	<input 
        if="!this.getUseTextarea() && (this.unique || this.create_new_translation)"
		name="{this.name}"
        class="input-{this.type ? this.type : 'text'} form-element"
		spellcheck="false" 
		autocomplete="{this.autocomplete == true ?  'on' : 'off'}"
		placeholder="{this.getOtherTranslationNotEmpty()}" 
		element="input" 
		name="{this.name}" 
		type="{this.type != 'undefined' ? this.type : 'text'}" 
		value="{this.translated_value}" 
		(blur)="this.blur()"
        (input)="this.resourceChanged(node.value)" 
        (focus)="this.loadRecommended(node.value)"
        (keypress)="this.keypress(event)"
		readonly="{this.readonly}"/>

    <textarea if="this.getUseTextarea()" name="{this.name}"
        class="input-{this.type ? this.type : 'text'} form-element"
		spellcheck="false" 
		autocomplete="{this.autocomplete == true ?  'on' : 'off'}"
		placeholder="{this.getOtherTranslationNotEmpty()}" 
		element="textarea" 
		name="{this.name}" 
		type="{this.type != 'undefined' ? this.type : 'text'}" 
		value="{this.translated_value}" 
		(blur)="this.blur()"
		(input)="this.resourceChanged(node.value)" 
        (keypress)="this.keypress(event)"
		readonly="{this.readonly}"></textarea>
    <p class="error-message" if="!!this.errorMessage">{this.errorMessage}</p>
    <div element="translation_info" style="display:none;" class="translation-info">
        <label class="form-label"><?= translate('translation_key') ?></label>
        <input readonly="{!this.unique && !this.create_new_translation}" type="text" (input)="this.translationKeyChanged(node.value)" value="{this.value}"/>
        
        <div for="other_language of this.other_languages">
            <label class="form-label">{other_language.language_translated}</label>
            <input type="text" (input)="this.otherLanguageChanged(other_language, node.value)" value="{other_language.translation}"/>
        </div>
        <a target="_blank" href="/<?= getConfig('main_server') ? 'editor/translator' : $GLOBALS['language'].'/translator' ?>#key:{this.value}"><?= translate('show_in_translator') ?></a>
    </div>
</div>