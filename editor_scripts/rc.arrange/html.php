<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="j-arrange">
    <div class="droparea" (dragleave)="this.leaveDroparea(event,node)" (dragover)="this.allowDrop(event,node)" (drop)="this.drop(event, this.idx)" draggable="true" (dragstart)="this.drag(event, this.idx)" >
        <img class="icon" src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/move.svg"/>
    </div>
</div>