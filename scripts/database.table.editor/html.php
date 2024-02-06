<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  ?><div class="database-editor">
    <h1>{this.title}</h1>

    <form.text placeholder="<?= translate('search') ?>" (change)="this.searchChanged(event)"></form.text>
    <div style="overflow-x: scroll;max-width: 100%;">
        <p if="this.objs and this.objs.length == 0">
            <?= translate('no_items_in_this_view') ?>
        </p>
        <table if="this.objs and this.objs.length > 0" class="table wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr if="this.resource != undefined" for="parameter of this.resource.parameters">
                    <th first>
                        <?= translate('db_editor_actions') ?>
                    </th>
                    <th (click)="this.sortBy(parameter.name)">{translate(parameter.title)}
                        {this.getSortIndicator(parameter.name)}</th>
                </tr>
            </thead>
            <tbody for="obj of this.objs">
                <tr if="this.resource != undefined" for="parameter of this.resource.parameters">
                    <td first>
                        <i (click)="this.editRow( obj.id )" class="action"><img class="icon"
                                src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/edit.svg" /></i>
                        <i (click)="this.deleteRow( obj.id )" class="action"><img class="icon"
                                src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/delete.svg" /></i>
                    </td>
                    <td>
                        <object.viewparameter showlabel="{false}" parameter="{parameter}" value="{obj[parameter.name]}" value_translated="{obj[parameter.name+'_translated']}">
                        </object.viewparameter>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="tablenav bottom">
        <paginator number_of_pages="{this.number_of_pages}" (change)="this.pageChanged(event)"></paginator>
        <form.button (click)="this.showAddDialog()" label="<?= translate('add') ?>"><form.button>
    </form.button>
</div>