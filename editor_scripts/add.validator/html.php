<?php namespace Metayota; include_once('../metayota/library.php');  ?><div>    
    <h1><?= translate('add_validator') ?></h1>
    <form.tagtype element="tagtypename" value="{this.name}" typefilter="validator" (change)="this.tagTypeChanged(event)"></form.tagtype>
    <form.resource  submitlabel="<?= translate('add_validator') ?>" (submit)="this.submit(event)" resourcetype="{this.name}" value="{this.options}" (change)="this._options = event" element="validatorOptions"></form.resource>
</div>