<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="j-settings">
    <div if="!!this.resource">
        <h2>{this.resource then this.resource.title_translated}</h2>
        <innerhtml value="{this.resource then this.resource.documentation_translated}"></innerhtml>

        <div if="this.resource.type == 'webservice'">
            <form.resource showdocumentation="{window.helpMode$ && helpMode$}" target="webservice" method="POST" action="{editor$.currentServer ? editor$.currentServer.http_host : ''}resource-manager/call.php?function={this.resource.name}" submitlabel="<?php echo translate('submit') ?>" (submit)="this.fire('webservice',event);element.form.submit()" resourcetype="server:{this.resource then this.resource.name}"></form.resource>
        </div>
        <div if="this.resource.type != 'webservice'">
            <form.resource showdocumentation="{window.helpMode$ && helpMode$}" submitlabel="<?php echo translate('view') ?>" (submit)="this.fire('view',event)" resourcetype="server:{this.resource then this.resource.name}"></form.resource>
        </div>
    </div>
</div>