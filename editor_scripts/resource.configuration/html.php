<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../rc.resource.get.for.edit/rc.resource.get.for.edit.php');   include_once('../translation-service/translation-service.php');  ?><div class="editor-resource-configuration">
    <h1><?= translate('configuration') ?></h1>
  <form.resource showdocumentation="{window.helpMode$ && helpMode$}" (submit)="this.resourceUpdated(event)" submitlabel="<?= translate('save') ?>" resourcetype="resource" value="{this.resource}"></form.resource>  
</div>