<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="designer">
	<div class="left-pane">
		<div class="item-tree">
			<design.item element="designItem" value="{this.value}" selecteditem="{this.selectedItem}" (selected)="this.didSelectItem(event)"
			 (change)="this.designChanged(event)" tags="{this.tags}"></design.item>
		</div>
	</div>
	<div class="content-pane">
		<div if="this.selectedElement != undefined">
			<form.text label="<?= translate('name') ?>" value="{(this.selectedItem && this.selectedItem.value != null ) then this.selectedItem.value.name}" (change)="this.changedElementName(event)"></form.text>
			<dropdown label="<?= translate('tag') ?>" options="{this.allowedResourceOptions ? this.allowedResourceOptions : this.tagOptions}" value="{this.selectedElement.value.tag}"
			 (change)="this.changedItemTag(event)"></dropdown>
			<form.resource (change)="this.parameterChanged(event)" resourcetype="server:{this.selectedElement.value.tag}" value="{this.currentValue}"></form.resource>
		</div>
	</div>
	<div class="right-pane" if="this.active == true && this.tag != null">
        <iframe element="iframe" style="width:100%;height:100%;" src="{ (editor$.currentServer && editor$.currentServer.http_host ? editor$.currentServer.http_host : 'https://www.metayota.com/') +'call/index?tag='+this.tag.name}"></iframe>
	</div>
</div>