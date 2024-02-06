<?php namespace Metayota; include_once('../metayota/library.php');   namespace Metayota; include_once('../metayota/library.php');  ?><div class="editor-dependencies">
    <h1><?= translate('editor_menu_dependencies') ?></h1>
	<p if="this.dependencies.length == 0"><?= translate('no_dependencies') ?></p>
	<table class="table" if="this.dependencies.length > 0">
		<thead>
			<tr>
				<th><?= translate('resource') ?></th>
				<th><?= translate('version') ?></th>
				<th><?= translate('type') ?></th>
				<th><?= translate('actions') ?></th>
			</tr>
		</thead>
		<tbody for="dependency of this.dependencies">
			<tr>
				<td>
					<dropdown (change)="this.updateResource(dependency,element.value)" options="{this.options}" value="{dependency.name}"></dropdown>
				</td>
				<td>
					<form.text (change)="this.updateVersion(dependency,element.value)" value="{dependency.version}"></form.text>
				</td>
				<td>
					{dependency.type ?  translate('type_'+dependency.type.replaceAll('-','_')) : ''}
				</td>
				<td>
                    <a (click)="this.deleteDependency( dependency_index )" ><img class="icon" src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/delete.svg"/></a> 
				</td>
			</tr>
		</tbody>
	</table>

	<form.button (click)="this.addDependency()" label="<?= translate('add_dependency') ?>"></form.button>
</div>