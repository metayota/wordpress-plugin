<?php namespace Metayota; include_once('../metayota/library.php');  ?><h1><?= translate('add_missing_dependencies_title'); ?></h1>
<p>
    <?= translate('add_missing_dependencies_text'); ?>
</p>
<ul class="dependencies-list" for="dependency of this.dependencies">
    <li><form.checkbox (change)="this.checking[dependency] = event" value="{true}" label="{dependency}"></form.checkbox></li>
</ul>
    
<form.button (click)="this.submit()" label="<?= translate('add_to_dependencies') ?>"></form.button>