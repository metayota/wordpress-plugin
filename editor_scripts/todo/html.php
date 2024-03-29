<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="todo">
    
	<div class="todo-card">
        <div class="filter-todo" if="this.viewaction != 'add'">

                    <form.radio label="<?= translate('priority') ?>" (change)="this.filterPriority(event)" options="{this.priorityOptions}"></form.radio>
                    <form.radio value="open" label="<?= translate('to_do_status') ?>" (change)="this.filterStatus(event)" options="{this.statusOptions}"></form.radio>
                    <dropdown label="<?= translate('to_do_type') ?>" (change)="this.filterType(event)" options="{this.typeOptions}" translate_options="{true}"></dropdown>
                    <dropdown label="<?= translate('version') ?>" (change)="this.filterVersion(event)" options="{this.versionOptions}" translate_options="{true}"></dropdown>
        </div>
        
        

		<div if="(this.todos && this.todos.length > 0) && this.viewaction != 'add'" class="todo-content">
			<table class="table colored todo-list">
                <thead>
                    <tr>
                        <th style="width:21px"></th>
                        <th><?= translate('title') ?></th>
                        <th><?= translate('to_do_type') ?></th>
                        <th><?= translate('version') ?></th>
                        <th><?= translate('time') ?></th>
                        <th><?= translate('priority') ?></th>
                        <th><?= translate('actions') ?></th>
                    </tr>
                </thead>
				<tbody class="todo-item" for="todo of this.todos">
					<tr>
                        <td>
                            <span if="todo.status=='done'">✓</span>
                        </td>
                        <td>
                            
                            <div><b style="font-size:120%;">{todo.title} </b></div>
                            <b if="this.listall">{todo.resource}</b>
                            <!--p if="!this.listall && todo.description && todo.description != ''">{todo.description}</p-->
                        </td>
                        <td>
                            <text name="to_do_type_{todo.type}"></text>
                        </td>
                        <td>
                            {todo.version}
                        </td>
                        <td>
                            {translate('to_do_minutes',todo.time)}
                        </td>
                        <td style="white-space:nowrap;">
                            {translate('priority_'+todo.priority)}
                        </td>
                        <td style="white-space:nowrap;">
                            <i if="todo.status != 'done'" (click)="this.checkTodo( todo )" class="action"><img style="filter:none" class="icon" src="/resource/rc.icon/check.svg"/></i>
                            <i (click)="this.todoEditor( todo )" class="action"><img class="icon" src="/resource/rc.icon/edit.svg"/></i>
                            <i (click)="this.removeElement( todo )" class="action"><img class="icon" src="/resource/rc.icon/delete.svg"/></i>
                        </td>
					</tr>
				</tbody>
			</table>
            <a name="edit-todo"></a>
		</div>
        <p if="this.viewaction != 'add'" style="margin-left:-18px">{translate('to_do_work_left',this.time)}</p>
        <div class="todo-content-add">
            
            <p if="(!this.todos || this.todos.length == 0) && this.viewaction != 'add'">
                <?= translate('no_todos') ?>
            </p>

            <p if="this.viewaction != 'add' && this.addform">
                <form.button (click)="this.setAttribute('viewaction','add')" label="<?= translate('add_to_do') ?>"></form.button>
            </p>

            <div if="this.viewaction == 'add' && this.addform" >
                <form.resource showdocumentation="window.helpMode$ && helpMode$" element="addtodo" (submit)="this.addToDo(event)" label="<?= translate('add_to_do') ?>" resourcetype="todo.item" submitLabel="<?= translate('add_to_do') ?>"></form.resource>
            </div>
		</div>
	</div>
</div>