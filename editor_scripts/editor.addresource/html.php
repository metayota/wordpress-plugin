<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="editor-addresource">
    <h1><?= translate('add_resource_title') ?></h1>
    <form.resource  showdocumentation="{window.helpMode$ && helpMode$}" value="{{project_id:editor$.selectedProject,'type':'tag'}}" resourcetype="form.addresource" (submit)="this.saveResource(event)" submitlabel="<?= translate('add_resource_button') ?>"></form.resource>
</div>