<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  ?><div>
    <form.resource showdocumentation="{this.showdocumentation}" element="form" (submit)="this.save(event)" label="{this.label}" submitlabel="<?= translate('save') ?>" resourcetype="{this.resourcetype}" value="{this.value}"></form.resource>
</div>