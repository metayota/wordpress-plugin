<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="task-list">
    <h2>{this.label}</h2>
    <table if="this.tasks != undefined and this.tasks.length > 0" class="table">
        <colgroup>
            <col width="*"></col>
            <col width="100"></col>
            <col width="150"></col>
        </colgroup>
        <tr>
            <th><?= translate('task') ?></th>
            <th><?= translate('task_progress') ?></th>
            <th><?= translate('skill') ?></th>
            <th><?= translate('task_reward') ?></th>
        </tr>
        <tbody for="task of this.tasks">
            <tr>
                <td>
                    <a link="/tasks/{task.id}">{task.title}</a>
                </td>
                <td>
                    <div if="task.progress < 100"><progress value="{task.progress}" max="100"></progress></div>
                    {translate(task.status)}
                </td>
                <td>
                    {task.skill_name}
                </td>
                <td>
                    <b class="devcoins">{(task.max_price*0.85).toFixed(2)} <?= translate('local_currency') ?></b>
                </td>
            </tr>
        </tbody>
    </table>
    <div if="this.tasks != undefined and this.tasks.length == 0">
        <p><?= translate('no_tasks') ?></p>
    </div>
    <div if="this.tasks == undefined">
        <loader></loader>
    </div>
</div>