<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="jj-play">
    <h1><?= translate('editor_menu_debug') ?></h1>
    <div if="!this.fullscreen">
	    <form.resource showdocumentation="{true}" (submit)="this.paramsChanged(event)"  resourcetype="server:{this.rname}" label="<?= translate('parameters') ?>" submitlabel="<?= translate('update_preview') ?>" value="{this.params}"></form.resource>
    </div>

    <div if="this.resource != null" class="jj-play-preview {this.fullscreen !== false ? 'jj-play-preview-fullscreen' : ''}">
		<div style="height:100%" if="this.resource && this.resource.type == 'tag' && this.rname != 'editor' && this.rname != undefined && this.active">
            <iframe style="width:100%;height:100%;" src="{editor$.currentServer.http_host != undefined ? editor$.currentServer.http_host : 'https://www.metayota.com/'}call/index?&tag={this.rname}&params={JSON.stringify(this.params)}&language=<?= $GLOBALS['language'] ?>"></iframe>
		</div>
        <div class="webservice-result" if="this.webserviceResult != null">{this.webserviceResult}</div>
    </div>

    <div if="!this.fullscreen && this.moreinfo === true">
        <h2><?= translate('last_events') ?></h2>

        <div class="last-events">
            <rc.table columns="{[{name:'eventname', width:20, title:'<?= translate('event_name' ) ?>'},{name:'event',width:80,title:'<?= translate('event_data' ) ?>'}]}" data="{this.lastEvents}"></rc.table>
        </div>

        <h2><?= translate('parameter_definitions') ?></h2>
        <pre>{JSON.stringify(this.parameters, null, 4)}</pre>
        <h2><?= translate('current_parameters') ?></h2>
        <pre>{JSON.stringify(this.params, null, 4)}</pre>
    </div>
    
</div>