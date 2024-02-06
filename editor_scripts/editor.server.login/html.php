<?php namespace Metayota; include_once('../metayota/library.php');  ?><div>
    <h2 if="this.servers && this.servers.length > 0"><?= translate('login_to_server_title') ?></h2>

    <div for="server of this.servers">
        <form.button icon="server" enabled="{!editor$.currentServer || editor$.currentServer.id != server.id}" (click)="this.chooseServer(server.id)" label="{server.title}"></form.button>
    </div>

    <div if="this.servers && this.servers.length == 0">
        <div if="!this.creating && window.loggedInUser$ && window.loggedInUser$.id != undefined">
            <create-free-webspace ></create-free-webspace>
        </div>
        
        <p if="this.creating">
            <loader ></loader>
            <?= translate('please_wait_server_created') ?>
        </p>
    </div>

    
</div>