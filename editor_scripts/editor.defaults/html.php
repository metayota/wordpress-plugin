<?php namespace Metayota; include_once('../metayota/library.php');  ?><h1><?= translate('editor_menu_defaults') ?></h1>

<div if="!!this.resourcename">
    <form.resource showdocumentation="{window.helpMode$ && helpMode$}" (submit)="this.saveDefaults(event)" submitlabel="<?= translate('save') ?>" resourcetype="server:{this.resourcename}" value="{this.defaults}"></form.resource>
</div>