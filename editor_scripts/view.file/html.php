<?php namespace Metayota; include_once('../metayota/library.php');  ?><span class="view-file">
    <img if="this.value && (this.value.endsWith('jpg') || this.value.endsWith('png') || this.value.endsWith('webp') || this.value.endsWith('jpeg'))" src="<?= getConfig('wordpress') ? '{this.value}' : '/uploads/{this.folder}/{this.value}' ?>"/><br/>
    <a target="_blank" href="<?= getConfig('wordpress') ? '{this.value}' : '/uploads/{this.folder}/{this.value}' ?>">{this.value}</a>
</span>