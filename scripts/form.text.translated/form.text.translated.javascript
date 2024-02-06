/***
 * 1) A new translation & Current language is not main language & autotranslate is not available THEN MAIN LANGUAGE IS REQUIRED...
 */
class FormTextTranslated extends RCBaseFormElement {
    init() {
        this.useTextarea = false
        this.disableBlur = true
        this.main_language = resource.getData().main_language
        this.current_language = resource.getData().current_language
        this.auto_translate = resource.getData().auto_translate
        if (!this.main_language) {
            this.main_language = 'en'
            console.warn('Metayota: main_language is not set in config')
        }
    }

    set translation_category(tc) {
        this._translation_category = tc
    }
    get translation_category() {
        return this._translation_category
    }

    set translated_value(tv) {
        this._translated_value = tv
        this.update('this.translated_value')
    }

    get translated_value() {
        return this._translated_value
    }

    setTranslation(translation) {
        this.setAttribute('recommended', [])
        this.value = translation.translation_key
        this.translated_value = translation.translation
        this.translationKeyChanged(translation.translation_key)
        this.updateUseTextarea();
    }

    toggleTranslationInfo() {
        if (this.translation_info.style.display == 'block') {
            this.translation_info.style.display = 'none'
        } else {
            this.translation_info.style.display = 'block'
        }
    }

    otherLanguageChanged(other_language, newValue) {
        other_language.translation = newValue
        if (other_language.language == this.main_language && !this.unique) {
            this.updateValueNonUnique()
        }
        this.generateRandomValue()
        this.fire('change', this._value) //doesn't really needed to be called all the time, just when it's really changed
    }

    translationKeyChanged(new_key) {
        this._value = new_key
        this._original_value = new_key
        this.fire('change', this._value)
        if (!this.create_new_translation) {
            this.loadTranslation(new_key)
        }
    }

    set label(l) {
        this._label = l
        this.update('this.label')
    }

    get label() {
        return this._label
    }

    set readonly(re) {
        this._readonly = re
    }

    get readonly() {
        return this._readonly
    }

    hideRecommended() {
        this.setAttribute('recommended', [])
    }

    loadRecommended(value) {
        if (!this.unique) {
            resource.action('get_recommended', { query: value, area: this.translation_category }).then(result => {
                this.setAttribute('recommended', result)
                let existingOptions =  result.map(item => {
                    return {
                        value: item.translation_key,
                        name: item.translation
                    };
                });
                existingOptions.unshift({
                    value: 'new_translation',
                    name: translate('new_translation') // Hier verwenden wir eine translate-Funktion
                });
                this.setAttribute('existing_options', existingOptions) 
            })
        }
    }

    convertToTranslationKey(text) {
        let nameValue = text
        if (nameValue == undefined) {
            nameValue = Array.from({ length: 8 }, () => Math.random().toString(36).charAt(2)).join('');
        } else {
            nameValue = nameValue.replaceAll('-','_').replaceAll(' ','_').replaceAll('.','_').toLowerCase()
        }
        return nameValue
    }

    generateRandomValue() {
        if (this.unique && (this._value == undefined || this._value == '')) {
            let nameValue = this.convertToTranslationKey(this.input.form?.name?.value)
            this._value = (this.translation_category ? this.convertToTranslationKey(this.translation_category) + '_' : '')  + this.convertToTranslationKey(this.name) + '_'  + nameValue
            this.fire('change', this._value)
        }
    }

    textToTranslationKey(text) {
        return text.trim().replace(/(\d+)([a-zA-Z]+)/g, '$1_$2').replace(/([a-zA-Z]+)(\d+)/g, '$1_$2').replaceAll(' ', '_').replaceAll('-', '_').replaceAll('(', '_').replaceAll(')', '_').replaceAll('__', '_').replace(/[^a-z0-9]+/gi, "_").replace(/^_+|_+$/g, '').toLowerCase()
    }

    updateValueNonUnique() {
        if (this.not_found) {
            let mainTranslation = this.getMainTranslation()
            if (mainTranslation != undefined && mainTranslation != '') {
                this._value = this.textToTranslationKey(mainTranslation)
                this._value = this.translation_category ? this.textToTranslationKey(this.translation_category) + '_' + this._value : this._value
                if (this._value.length > 60) {
                    let category = this.translation_category ? this.textToTranslationKey(this.translation_category) + '_' : ''
                    let translation_key = category + this.name  + '_' + Array.from({ length: 8 }, () => Math.random().toString(36).charAt(2)).join('');
                    this._value = translation_key
                }
            } else if (this._original_value != undefined) {
                this._value = this._original_value
            }
            this.update('this.value')
            this.fire('change', this._value)
        }
    }

    setExistingTranslation(value) {
        if (value == 'new_translation') {
            this.setAttribute('create_new_translation', true);
            if (!this.auto_translate && this.current_language != this.main_language) {
                let category = this.translation_category ? this.textToTranslationKey(this.translation_category) + '_' : ''
                let translation_key = category  + Array.from({ length: 8 }, () => Math.random().toString(36).charAt(2)).join('');
                this.value = translation_key
            } else {
                this.value = this.translation_category
            }
            this.loadOtherLanguages('', false)
        } else {
            this.setAttribute('create_new_translation', false);
            this.value = value
            this.loadOtherLanguages(this._value,true)
        }
    }

    resourceChanged(value) {
        if (this.readonly === true) return

        this._translated_value = value
        if (value == '') {
            if (!this.unique) {
                this._value = ''
                this._original_value = ''
            }
        } else {
            if (!this.unique) {
                this.updateValueNonUnique()
            }
        }
        this.update('this.value')
        this.generateRandomValue()
        this.fire('change', this._value)
        this.updateUseTextarea()
    }

    keypress(e) {
        if (!e) e = window.event;
        var keyCode = e.keyCode || e.which;
        if (keyCode == '13') {
            //this.fire('enter', 'pressed enter')
            if (!this.getUseTextarea()) {
                if (this.translated_value == undefined) {
                    this.translated_value ="\n"
                } else {
                    this.translated_value+="\n"
                }
            }
            this.updateUseTextarea()
            return false;
        }
    }

    blur() {
        window.setTimeout(() => {
            this.hideRecommended()
        }, 800)
        
        if (!this.disableBlur && !this.unique) {
            resource.action('get_translation', { translation_key: this.value }).then(result => {
                if (result) {
                    if (!result.not_found) {
                        this._translated_value = result.translation
                    }
                    this.updateUseTextarea();
                    this.setAttribute('editable',false)
                } else {
                    this.setAttribute('editable',true)
                }
            });
        }
    }

    saveTranslations() {
        if (this.value != undefined && this.value != '') {
            Translation.translations[this.value] = this._translated_value

            let valuesToSave = { 'translation_key': this.value, 'translation': this._translated_value }
            if (!this.unique) {
                valuesToSave.translation_category = this.translation_category;
            }
            resource.action('save_translation', valuesToSave);
            
            this.other_languages.forEach(other_language => {
                other_language.translation_key = this.value
                resource.action('save_translation',other_language)
            });
            
        }
    }

    isValid() {
        this.saveTranslations()
        return true
    }

    loadOtherLanguages(key,including_main_language) {
        resource.action('get_translation', { translation_key: key, including_main_language:including_main_language }).then(result => {
            
            if (result != undefined && result.translation !== undefined) {
                this.setAttribute('other_languages',result.other_languages)
                this.update('this.getOtherTranslationNotEmpty')
            }
        })
    }

    getAllTranslations() {
        let allTranslations = []
        allTranslations.push(...this.other_languages)
        let translation = { 'translation': this._translated_value, 'language':this.language }
        allTranslations.push(translation) // current language
        return allTranslations
    }

    getMainTranslation() {
        let mainTranslation = this.getAllTranslations().find( t => t.language == this.main_language)
        if (mainTranslation != undefined) {
            return mainTranslation.translation
        }
        return undefined
    }

    checkAutoTranslate() {
        let allTranslations = this.getAllTranslations()
        let emptyTranslations = allTranslations.filter(item => item.translation === '');
        let filledTranslations = allTranslations.filter(item => item.translation !== '');
        let emptyLanguages = emptyTranslations.map(item => item.language);
        resource.action('autotranslate', {'translation_key': this.value, 'existing_translations':filledTranslations, 'needed_languages':emptyLanguages}).then(result=>{
            for (let key in result) {
                let value = result[key];
                if (key == this.language) {
                    
                    this.input.value = value
                
                    this.translated_value = value
                } else {
                    let langObj = this.other_languages.find(item => item.language == key);
                    if(langObj) {
                        langObj.translation = value;
                    }
                }
            }
            this.update('this.other_languages')
            this.update('this.getOtherTranslationNotEmpty')
            this.updateValueNonUnique()
        })
    }

    getOtherTranslationNotEmpty() {
        // If the property doesn't exist or isn't an array, return null
        if (!this.other_languages || !Array.isArray(this.other_languages)) {
            return null;
        }

        // Find the first language object where the translation is not an empty string
        const firstValidLanguage = this.other_languages.find(lang => lang.translation && lang.translation.trim() !== '');

        // If found, return the translation, otherwise return null
        return firstValidLanguage ? firstValidLanguage.translation : null;
    };

 
    loadTranslation(key) {
        resource.action('get_translation', { translation_key: key }).then(result => {
            if (result != undefined) {
                
                //if (!result.not_found) {
                this.not_found = result.not_found
                this.language = result.language
                    if (result.translation != undefined) {
                        if (this.input != undefined) {
                            this.input.value = result.translation
                        }
                        this.translated_value = result.translation
                        this.updateUseTextarea();
                    }
                    this.setAttribute('other_languages',result.other_languages)
                    this.update('this.getOtherTranslationNotEmpty')
                //}
            }
        })
    }

    getUseTextarea() {
        if (!this.unique) {
            return false
        }
        if (this._translated_value != undefined) {
            if (this._translated_value.length > 200) {
                return true;
            }
            if (this._translated_value.includes("\n")) {
                return true
            }
        }
        return false
    }

    updateUseTextarea() {
        this.disableBlur = true
        
        let giveFocus = document.activeElement != undefined && (this.input == document.activeElement || this.textarea == document.activeElement);
        this.update('this.getUseTextarea')
        if (giveFocus) {
            if (this.getUseTextarea()) {
                this.textarea.focus()
            } else {
                this.input.focus()
            }
        }
        this.update('this.translated_value');
        this.disableBlur = false
    }

    set value(v) {
        if (v === undefined || v === null) {
            v = ''
        }
        this._value = v
        this._original_value = v
        this.update('this.value')
        this.loadTranslation(v)
        this.loadRecommended()
    }
    get value() {
        return this._value
    }

    set type(t) {
        this._type = t
        this.update('this.type')
    }

    get type() {
        return this._type
    }
    set placeholder(p) {
        this._placeholder = p
        this.update('this.placeholder')
    }

    get placeholder() {
        return this._placeholder
    }
}