<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  ?><div>
    <h1><?= translate('tasks') ?></h1>
    <task.list tasks="{this.tasks}"></task.list>
    <form.resource showdocumentation="{ window.helpMode$ && helpMode$ }" element="form" (submit)="this.taskCreated(event)" resourcetype="task.create" submitlabel="<?= translate('create_task') ?>">
    </form.resource>
</div>