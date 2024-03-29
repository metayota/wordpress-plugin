<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  ?><div class="form-file-helper">
	<form target="{this.iframeName}" element="form" method="post" action="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/scripts/form.file.helper/form.file.helper.php' : '/call/form.file.helper' ?>" enctype="multipart/form-data">
		<input name="file" element="file" type="file" (change)="this.fileSelected()"/>
        <input type="hidden" name="folder" value="{this.folder}"/>
		<input type="hidden" name="iframe" value="{this.iframeName}"/>
		<input type="submit"/>
	</form>
	<iframe (message)="this.uploadComplete(event)" name="{this.iframeName}"></iframe>
	<p if="!!this.errorMessage" class="error-message">{this.errorMessage}</p>
</div>