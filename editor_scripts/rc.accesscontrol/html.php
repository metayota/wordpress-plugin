<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="editor-plugin-accesscontrol">
    <h1><?= translate('access_control') ?></h1>
    <div for="accessRight of this.accessRights">
        <div style="{accessRight.name == this.getHighlight() then 'background:rgba(255,0,0,0.3);'}">
            <h3>{accessRight.title_translated}</h3>
            <p>{accessRight.description_translated}</p>
            <div for="usergroup of this.usergroups">
                <div class="usergroup">
                    <form.checkbox (change)="this.accessChanged( accessRight.access_right, usergroup.id, event )" value="{this.hasAccess( accessRight.access_right, usergroup.id )}" label="{usergroup.title_translated}"></form.checkbox>
                </div>
            </div>
        </div>
    </div>
</div>
