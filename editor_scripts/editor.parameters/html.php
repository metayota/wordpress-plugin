<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="editor-parameters">
    <h1><?= translate('editor_menu_parameters') ?></h1>
    <div if="!this.showEdit">
        <table if="this.parameters && this.parameters.length > 0" class="table">
            <thead>
                <tr>
                    <th><?= translate('title_and_documentation') ?></th>
                    <th><?= translate('name') ?></th>
                    <th><?= translate('type') ?></th>
                    <th style="width:114px;"><?= translate('actions') ?></th>
                </tr>
            </thead>
            <tbody for="parameter of this.parameters">
                <tr>
                    <td><b>{parameter.title_translated}</b><br/>{parameter.documentation_translated}</td>
                    <td>{parameter.event === true ? '(' + parameter.name + ')' : parameter.name}</td>
                    <td>{translate('parameter_type_'+parameter.type)}</td>
                    <td>
                        <a (click)="this.deleteParameter(parameter.name)"><img class="icon" src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/delete.svg"/></a> 
                        <a (click)="this.showAddParameterModal( parameter )"><img class="icon" src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/edit.svg"/></a>
                        <rc.arrange (change)="this.parametersChanged()" collection="{this.parameters}" idx="{parameter_index}"></rc.arrange>
                    </td>
                </tr>
            </tbody>
        </table>

        <p if="!this.parameters || this.parameters.length == 0">
            <?= translate('no_parameters') ?>
        </p>
    
    
        <form.button label="<?= translate('add_parameter') ?>" (click)="this.showAddParameterModal()"></form.button>
    </div>

    <div if="this.showEdit">
        <h2>{this.mode == 'edit' ? '<?= translate('edit_parameter') ?>' : '<?= translate('add_parameter') ?>'}</h2>
         <form>
        <form.text element="paramNameElement" label="<?= translate('name') ?>" name="name" value="{this.paramToSave.name}" placeholder="{this.nameSuggestion}"></form.text>
        <form.text.translated unique="{true}" element="paramTitleElement" translation_category="resource_{this.tag.name}" name="title" label="<?= translate('title') ?>" value="{this.paramToSave.title}" (change)="this.titleUpdated(element.value)"></form.text.translated>
        <dropdown translate_options="{true}" options="{this.typeOptions}" (change)="this.paramTypeChanged(event)" element="paramTypeElement" label="<?= translate('type') ?>" value="{this.paramToSave.type}"></dropdown>

        <div if="this.typesettings != undefined && this.typesettings != ''">
            <form.resource element="typesettingsElement" (change)="this.typeSettingChanged(event)" resourcetype="{this.typesettings}" value="{this.paramToSave.options}"></form.resource>
        </div>
        <form.checkbox element="requiredElement" label="<?= translate('required') ?>" value="{this.paramToSave.required}"></form.checkbox>
        <form.checkbox element="readonlyElement" label="<?= translate('read_only') ?>" value="{this.paramToSave.readonly}"></form.checkbox>
        <form.checkbox element="eventElement" label="<?= translate('is_event') ?>" value="{this.paramToSave.event}"></form.checkbox>
        <form.text.translated unique="{true}" translation_category="resource" name="documentation" element="paramDocumentationElement" label="<?= translate('documentation') ?>" value="{this.paramToSave.documentation}"></form.text.translated>
        <ul class="jj-editor-parameters-validators" if="!!this.paramToSave.validators" for="validator of this.paramToSave.validators">
            <li><text name="{validator.name}"></text>
            <i (click)="this.editValidator( validator_index )" class="action"><img class="icon" src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/edit.svg"/></i>
            <i (click)="this.removeValidator( validator_index )" class="action"><img class="icon" src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/delete.svg"/></i>
            <!--a (click)="this.removeValidator( validator_index )">Delete </a>
            <a (click)="this.editValidator( validator_index )">Edit </a></li-->
            </li>
        </ul>
        <form.button (click)="this.addValidator()" label="<?= translate('add_validator') ?>"></form.button><br/>
        <form.button (click)="this.mode == 'edit' ? this.saveParameter() : this.addParameter()" label="{this.mode == 'edit' ? '<?= translate('save') ?>' : '<?= translate('save') ?>'}"></form.button>
        <form.button (click)="this.hideAddParameterModal()" label="<?= translate('cancel') ?>"></form.button>
         </form>
    </div>
    <div if="this.tag && this.tag.type == 'dbtable' && !this.tableinfo">
        <form.button label="<?= translate('create_database_table') ?>" (click)="this.createDbTable()"></form.button>
    </div>
    <div if="this.tag && this.tag.type == 'dbtable' && this.tableinfo && (this.tableinfo.length - 1) != this.parameters.length">
        <form.button label="<?= translate('update_database_table') ?>" (click)="this.updateDbTable()"></form.button>
    </div>

    <dialog display_tag="add.validator" (submit)="this.validatorAdded(event)" element="addvalidator" visible="{false}"></dialog>
    
</div>