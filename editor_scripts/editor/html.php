<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../format-text/format-text.php');  ?><div>
    <router name="router" base="">
        <routes array="">
            <route url="/editor/" page="editor.welcome"></route>
            <route url="/editor/view/[page]/[subpage]"></route>
			<route url="/editor/view/[page]"></route>
            <route url="/editor/resource/[resource]/[tab]"></route>
            <route url="/page/[resource]/[tab]" resource="website.view"></route>
            <route url="/page/[resource]" resource="website.view"></route>
            <route url="/submit/[resource]" page="rc.submit.work"></route>
            <route url="/tasks/" resource="website.tasks"></route>
            <route url="/documentation" page="documentation-metayota-editor"></route>
            <route url="/tasks/[taskid]" page="website.task.detail"></route>
			<route url="/change/[resource]" page="rc.change"></route>

            <!-- please use this pattern, since it is the standard way, even if the router has more code, everything gets easier and better URLs -->
            <route url="/editor/compare-to-repository/[repository]/[resourcename]" title="Form Button Test">
                <content tag="rc.change"></content>
            </route>
            <route url="/editor/mail-manager/[server]" title="E-Mail Management">
                <content tag="mail-manager"></content>
            </route>
            <route url="/editor/translator" title="Translator">
                <content tag="translator"></content>
            </route>
        </routes>
    </router>
	<input element="is_wordpress" value="<?= getConfig('wordpress') ? 'yes' : 'no' ?>" type="hidden"/>
    <when test="{router$.attributes != undefined ? router$.attributes.resource : null}" then="this.resourceSelected( value )"></when>
    <when test="{router$.attributes != undefined ? router$.attributes.tab : null}" then="this.tabChanged( value )"></when>
	<when test="{router$.attributes != undefined ? router$.attributes.page : null}" then="this.showTag( value )"></when>
    <when test="{router$ && router$.active && router$.active.content}" then="this.showStandardRoute()"></when>

	<div id="rc-editor" class="j-editor {this.compact then 'compact'} {this.hidden then 'hidden'} tab-{this.tab}" (keydown)="this.onKeypress(event)" tabindex="0">
		<div class="editor-controls">
            
			<dropdown sort="{false}" translate_options="{true}" default="{false}" element="rcDropdown" class="rc-dropdown editor-control" (change)="this.action(element.value,element)" stdtext="{this.getRCMenuName()}" options="{this.getRcMenuActions()}"></dropdown>
			<div if="true">
                <dropdown translate_options="{true}" default="{false}" element="menuDropdown" class="menu-dropdown editor-control" (change)="this.action(element.value,element)" stdtext="..." options="{this.actions}"></dropdown>
			</div>
			<dropdown translate_options="{true}" default="{false}" element="tabDropdown" (change)="this.gotoTab(element.value)" options="{this.tabs}" value="{this.tab}" class="tab-dropdown dropdown editor-control"></dropdown>
            <button class="button icon-button editor-control { (router$.active && router$.active.content && router$.active.content.tag == 'website.view') || this.tab == 'view'  || this.tab == 'showtag'  ? '' : 'hidden' }" (click)="this.lastTab()"><span>X</span><img class="back-button" src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/back.svg"/></button>
            <button class="button icon-button see-button editor-control " (click)="this.switchAndReloadView()"><span>X</span><img src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/eye_white.svg"/></button>
			<form.livesearch stdtext="!{this.currentServer && this.currentServer.id ? '(No server selected)' : null}" defaultfilter="{{name:'project_id',value:this.selectedProject}}" element="tagDropdown" (change)="this.gotoResource(element.value)" valueattribute="name" options="{this.allResources}" projectresources="{this.projectResources ? this.projectResources : []}" value="{this.selectedTag}" class="tag-dropdown editor-control {this.loading ? 'search-loading' : '' }"></form.livesearch>
			<button class="button icon-button editor-control control-save { !this.unsaved then 'disabled' } { this.tab == 'view' ? 'hidden' : '' }" (click)="(this.unsaved then this.save())"><span>X</span><img src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/document_white.svg"/></button>
            <button class="button icon-button control-settings editor-control" (click)="this.toggleSettings()"><span>X</span><img src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/settings.svg"/></button>
			<div style="{this.settingsVisible ? '' :'display:none'}">
            	<settings server="{this.currentServer}" (webservice)="this.gotoTab('webservice')" (view)="this.showViewWithParameters(event)" resource="{this.selectedTagObj}"></settings>
			</div>
		</div>

        <div id="j-editor-content">
            <div if="router$.active and router$.active.include and !this.disableview and !this.reloading" class="tab includepage visible">
                <!--include tag="{router$.attributes.resource}" attributes="{router$.attributes}"></include-->
            </div>

            <div class="tab implementation code-editor { !(router$.active && router$.active.include) && this.tab == 'vscode' then 'visible'}">
                <tabs (change)="this.implementationTabChanged(event)" tabs="{this.implementationTabs}" active="{this.activeImplementationTab}"></tabs>
                <dropdown default="{false}" element="implTabDropdown" options="{this.implTabs}" displaytext="..." class="implementation-tabs-dropdown dropdown editor-control"></dropdown>
                <vs.codeeditor tab="{this.activeImplementationTab}" resource="{this.selectedTagObj then this.selectedTagObj.name}" (change)="this.resourceChanged()" element="msedit" value="{this.activeImplementation}" language="{this.getSuffix( this.activeImplementationTab )}"></vs.codeeditor>
            </div>

            <div if="!(router$.active && router$.active.include)">

                <div class="tab visible" if="this.tab == 'designer'">
                    <designer tags="{this.projectResourceOptions}" element="designer" (change)="this.designUpdated(event.design,event.html); this.update('this.getPreviewTag')" tag="{this.selectedTagObj}" value="{ this.getDesign() }"></designer>			
                </div>
                
                <div class="tab {this.tab == 'documentation' then 'visible'}">
                    <form.textarea (change)="this.documentationUpdated(element.value)" element="elDocumentation" label="Documentation" value="{this.selectedTagObj then this.selectedTagObj.documentation}"></form.textarea>
                </div>
                
                <div class="tab tab-content visible" if="this.tab == 'overview'">
                    <overview tags="{this.tags}" tag="{this.selectedTagObj}" element="overview"></overview>
                </div>

                <search element="searchDialog"></search>
                
                <div class="tab tab-content tab-todo visible" if="this.tab == 'todo' && this.selectedTagObj != null">
                    <todo addform="{true}" element="todo" resourcename="{this.selectedTagObj.name}"></todo>
                </div>
                
                <div class="tab tab-content visible" if="this.tab == 'config'">
                    <resource.configuration resourcename="{this.selectedTagObj then this.selectedTagObj.name}" element="config"></resource.configuration>
                </div>
                
                <div class="tab tab-content visible" if="this.tab == 'development'">
                    <rc.editor.tasks resource="{this.selectedTagObj}" element="development" (change)="this.testsChanged(event)"></rc.editor.tasks>
                </div>
                
                <div class="tab tab-content visible" if="this.tab == 'ratings' ">
                    <ratings resourcename="{this.selectedTagObj then this.selectedTagObj.name}"></ratings>
                </div>

                <div class="tab tab-access tab-content visible" if="this.tab == 'access' ">
                    <rc.accesscontrol resource="{this.selectedTagObj}"></rc.accesscontrol>
                </div>

                <div class="tab tab-change tab-content visible" if="this.tab == 'changes'">
                    <rc.change resourcename="{this.selectedTagObj then this.selectedTagObj.name}"></rc.change>
                </div>

                <div class="tab tab-defaults tab-content visible" if="this.tab == 'defaults'">
                    <editor.defaults resourcename="{this.selectedTagObj then this.selectedTagObj.name}"></editor.defaults>
                </div>
                
                <div class="tab tab-content {this.tab == 'parameters' then 'visible'}">
                    <editor.parameters (change)="this.paramsChanged(event)" tag="{this.selectedTagObj}"></editor.parameters>
                </div>
                
                <div class="tab tab-content dependencies visible" if="this.tab == 'dependencies'">
                    <editor.dependencies (change)="this.resourceUpdated(event,'dependencies')" dependencies="{this.dependencies then this.dependencies}" tags="{this.allResources}"></editor.dependencies>
                </div> 

                <div if="!this.reloading and this.tab == 'debug'" class="preview-tab tab tab-content {this.tab == 'debug' ? 'visible' : 'hidden'}" >
                    <play element="play" fullscreen="{this.tab == 'view'}" resource="{this.selectedTagObj}" moreinfo="{true}" parameters="{this.parameters}"></play>
                </div>

                <div if="!this.reloading and this.tab == 'view'"  class="preview-tab tab tab-content {this.tab == 'view' ? 'visible' : 'hidden'}" >
                    <div class="preview-db-table" if="this.selectedTagObj && this.selectedTagObj.type == 'dbtable'">
                        
                        <database.table.editor table="{this.selectedTagObj.name}" resourcetype="{this.selectedTagObj.name}" title="{this.selectedTagObj.title_translated}"></database.table.editor>
                    </div>
                    <div class="preview-db-table" if="this.selectedTagObj && this.selectedTagObj.type == 'webservice'">
                        <!--settings server="{this.currentServer}" (webservice)="this.gotoTab('webservice')" (view)="this.showViewWithParameters(event)" resource="{this.selectedTagObj}"></settings-->
                        <h1>{this.selectedTagObj then this.selectedTagObj.title}</h1>
                        <p if="this.selectedTagObj && this.selectedTagObj.description">
                            {this.selectedTagObj.description}
                        </p>
                       <?php 
                            
                            if (getConfig('wordpress')) {
                                $target = "/wp-content/plugins/metayota/scripts/{this.selectedTagObj.name}/{this.selectedTagObj.name}.php";
                            } else {
                                $target= "{this.currentServer != undefined ? this.currentServer.http_host : ''}call/{this.selectedTagObj.name}";
                            }
                        ?>
                        <form.resource target="webservice" method="POST" action="<?= $target ?>" subtitle="{this.selectedTagObj then this.selectedTagObj.documentation}" submitlabel="<?= translate('submit') ?>" (submit)="element.form.submit();this.gotoTab('webservice');" resourcetype="server:{this.selectedTagObj then this.selectedTagObj.name}"></form.resource>
                    </div>
                     <?php
                        if (getConfig('wordpress')) { 
                            $previewURL = "/wp-content/plugins/metayota/editor_scripts/index/index.php";
                        } else {
                            $previewURL = "{this.currentServer && (this.currentServer.http_host != undefined) ? this.currentServer.http_host : 'https://www.metayota.com/'}call/index";
                        }
                     ?>
                     <iframe if="this.selectedTagObj && (this.selectedTagObj.type != 'dbtable' && this.selectedTagObj.type != 'webservice')" src="<?= $previewURL ?>?tag={this.selectedResource}&params={encodeURIComponent(JSON.stringify(this.selectedResourceParameters)) + '&language=<?= $GLOBALS['language'] ?>'}"></iframe>
                </div>

                <div if="!this.reloading"  class="preview-tab tab tab-content {this.tab == 'webservice' ? 'visible' : 'hidden'}" >
                    <iframe name="webservice"></iframe>
                </div>

                <div class="tab tab-content visible" if="this.tab == 'showtag' && !(router$ && router$.active && router$.active.content)">
                    <include tag="{this.tagToShow}" attributes="{this.selectedResourceParameters}"></include>
                </div>

                
                <div class="tab tab-content visible" if="this.tab == 'standard-route'">
                    <include tag="{router$ && router$.active && router$.active.content ? router$.active.content.tag : null}" attributes="{router$.active then router$.params}"></include>
                </div>
            </div>

        </div>
	</div>
<rc.spider></rc.spider>
<editor.css></editor.css>
</div>
<toast element="toaster"></toast>