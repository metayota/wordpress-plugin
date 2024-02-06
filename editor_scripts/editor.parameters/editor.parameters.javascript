class EditorParameters extends Tag {

    setup() {
        this.paramToSave = {}
        this.parameterTypes = Resource.getResource('parametertype').data
        resource.action('get_parameter_types').then(result=> {
            this.setAttribute('parameterTypes',result)
            this.typeOptions = this.parameterTypes.map(v => { return {name:v.title,value:v.name} })
        })
        this.typeOptions = this.parameterTypes.map(v => { return {name:v.title,value:v.name} })
    }

    init() {
        this.updateTypesettigsEditor()
        this.paramTypeChanged()
    }

    paramByName(name) {
        let idx = this.parameterTypes.findIndex(v=>{return v.name == name})
        return this.parameterTypes[idx]
    }

    showAddParameter() {
        this.setAttribute('showEdit',true)
    }

    parametersChanged() {
        this.update('this.parameters');
        this.resourceChanged();
    }

    getTypeSettings(name) {
        let obj = this.paramByName(name)
        if (!obj) {
            return null
        }
        return obj.typesettings
    }

    getTypesettingsDefaults(name) {
        let obj = this.paramByName(name)
        if (!obj) {
            return null
        }
        if (typeof obj.editorDefault == 'string') {
            return JSON.parse(obj.editorDefault)
        }
    }


     getTypeSettingsEditor(name) {
        let obj = this.paramByName(name)
        if (!obj) {
            return null
        }
        return obj.typesettings_editor
    }

    titleUpdated(title) {
      //  this.nameSuggestion = title
      //  this.update('this.nameSuggestion')
    }

    editValidator(idx) {
        let validator = this.paramToSave.validators[idx]
        
        Resource.cls('dialog').displayTagInDialog('add.validator', validator).then( validator => {
            let name = validator.tag.tagtype 
            let options = validator.tag.options 
            validator.name = name 
            validator.options = options
            this.update('this.paramToSave.validators')
        });
    }

    addValidator() {
        this.addvalidator.visible = true/*
        Resource.cls('modal.dialog').createDialogWithTag('add.validator', {label:'Validator', submitlabel:"Add"}).then( validator => {
            console.log('Validator added', validator)
         
        });*/
    }

    validatorAdded(validator) {
        if (!this.paramToSave.validators) {
            this.paramToSave.validators = []
        }
        this.paramToSave.validators.push(validator)
        this.update('this.paramToSave.validators')
    }

    removeValidator(idx) {
        this.paramToSave.validators.splice(idx,1);
        this.update('this.paramToSave.validators')
    }

    paramTypeChanged(value) {
        this.paramToSave.type = value
        this.typesettingVal = null;
        this.typesettings = this.getTypeSettings(this.paramToSave.type); //this.paramTypeElement.value
        this.paramToSave.options = this.getTypesettingsDefaults(this.paramToSave.type)

        this.update('this.paramToSave.options')
        this.updateTypesettigsEditor()
        this.update('this.typesettings')
    }

    createDbTable() {
        Resource.call('db.table.create',{resourcetype:this.tag.name,tablename:this.tag.name}).then(result=> {
            this.updateDatabaseTable()
            toast$.show('database_table_creation_success_message',false,'success')
        })
    }

    updateDbTable() {
        Resource.call('update-database-table',{resourcetype:this.tag.name,tablename:this.tag.name}).then(result=> {
            this.updateDatabaseTable()
            toast$.show('database_table_update_success_message',false,'success')
        })
    }

    updateTypesettigsEditor() {
        if (this.paramTypeElement) {
            this.typesettings_editor = this.getTypeSettingsEditor(this.paramToSave.type) // this.paramTypeElement.value
        } else {
            this.typesettings_editor = null
        }
        this.update('this.typesettings_editor')
    }

    typeSettingChanged(v) {
        this.paramToSave.options = v
    }

    updateDatabaseTable() {
        if (this.tag != undefined) {
            if (this.tag.type == 'dbtable') {
                resource.action('table_info',{table:this.tag.name}).then(result=> {
                    if (result.table_does_not_exist == undefined) {
                        this.tableinfo = result;
                        this.update('this.tableinfo')
                    }
                })
            }
        }
    }

    set tag(tag) {
        this._tag = tag
        if (tag && tag.parameters != '') {
            try {
                this.parameters = JSON.parse(tag.parameters)
                if (!this.parameters) {
                    this.parameters = []
                }
                this.updateDatabaseTable()
            } catch (e) {
                this.parameters = []
                console.warn('Parameters are not valid JSON!', e)
            }
        } else {
            this.parameters = []
        }
        this.update('this.parameters')
    }

    get tag() {
        return this._tag
    }

    showAddParameterModal(parameter=null) {
        if (parameter != null) {
            this.paramToSave = parameter
            this.setAttribute('mode','edit')
        } else {
            this.paramToSave = {}
            this.setAttribute('mode','create')
        }
        this.update('this.paramToSave')
        this.update('this.paramTypeElement')
        this.typesettings = this.getTypeSettings(this.paramToSave.type);
        this.update('this.typesettings');
        this.setAttribute('showEdit',true)
    }

    resourceChanged() {
        this.fire('change', JSON.stringify(this.parameters))
    }

    hideAddParameterModal() {
      //  this.addParameterModal.classList.remove('active')
      this.setAttribute('showEdit',false)
    }

    deleteParameter(parameterName) {
        let parameterIdx = this.parameters.findIndex((v) => { return v.name == parameterName })
        this.parameters.splice(parameterIdx, 1)
        this.update('this.parameters');
        this.resourceChanged()
    }

    saveParameter() {
        let formValues = this.getFormValues()
        if (this.tag.type == 'dbtable' && formValues.name != this.paramToSave.name) {
            resource.action('rename_db_column', {table_name:this.tag.name, new_name:formValues.name, old_name:this.paramToSave.name})
        }
        Object.assign(this.paramToSave,this.getFormValues())
        this.paramTitleElement.saveTranslations();
        
        this.paramDocumentationElement.saveTranslations()
        this.paramToSave.title_translated = this.paramTitleElement._translated_value 
        this.paramToSave.documentation_translated = this.paramDocumentationElement._translated_value 
        this.hideAddParameterModal()
        this.update('this.parameters')
        this.resourceChanged()
    }

    getFormValues() {
        let vals = {
            'name': this.paramNameElement.value,
            'title': this.paramTitleElement.value,
            'type': this.paramTypeElement.value,
            'documentation': this.paramDocumentationElement.value
        };
        if (this.typesettingsElement != undefined ) {
            vals.options = this.typesettingsElement.value
        }
        if (this.eventElement.value === true) {
            vals.event = true
        }//TODO
        if (this.readonlyElement.value === true) {
            vals.readonly = true
        } else {
            vals.readonly = false
        }
        if (this.requiredElement.value === true) {
            vals.required = true
        } else {
            vals.required = false
        }
        if (this.validators) {
            vals.validators = this.validators
        }
        return vals;
    }

    addParameter() {
        this.paramTitleElement.saveTranslations();
        this.paramDocumentationElement.saveTranslations()
        let newParam = this.getFormValues()
        newParam.title_translated = translate(newParam.title)
        newParam.documentation_translated = translate(newParam.documentation)
        this.parameters.push(newParam);
        this.update('this.parameters');
        this.hideAddParameterModal();
        this.paramToSave = {}
        this.update('this.paramToSave')
        this.update('this.typesettings')
        this.resourceChanged();
        
    }
}