<?php namespace Metayota; include_once('../metayota/library.php');  ?><div if="this.tag != undefined" class="overview">
    
    <h1>{this.tag.title_translated}</h1>
    <p>
        <innerhtml value="{this.tag.documentation_translated}"></innerhtml>
    </p>
    <dt><?= translate('name') ?></dt>
    <dd><a href="/editor/resource/{this.tag.name}/vscode">{this.tag.name}</a></dd>
    <dt><?= translate('vendor') ?></dt>
    <dd>{this.tag.vendor}</dd>
    <dt><?= translate('version') ?></dt>
    <dd>{this.tag.version}</dd>
    <dt><?= translate('type') ?></dt>
    <dd>{translate('type_'+this.tag.type)}</dd>
    <dt if="this.tag.project"><?= translate('project') ?></dt>
    <dd if="this.tag.project">{this.tag.project}</dd>
    <dt if="this.tag.license"><?= translate('license') ?></dt>
    <dd if="this.tag.license"><a href="https://www.metayota.com/page/rc.license.view/Metayota-OSS" target="_BLANK">{this.tag.license}</a></dd><!-- todo bug with link= -->

    <div if="this.implementationTabs != null and this.implementationTabs.length > 0">
        <h4><?= translate('implementation') ?></h4>
        <ul class="list" for="implementation of this.implementationTabs">
            <li><a link="/editor/resource/{this.tag.name}/vscode#tab={implementation}"><b>{this.replaceNames(implementation)}</b></a></li>
        </ul>
    </div>

    <testcases tag="{this.tag}"></testcases>
    
    <div if="this.todos && this.todos.length > 0">
        <h4><?= translate('todo_list') ?></h4>
        <p>
            <ul class="list" for="todo of this.todos">
                <li><b>{todo.title} </b></li>
            </ul>
        </p>   
    </div> 
    
    <div>
        <div if="this.parameters && this.parameters.length > 0">
            <h4><?= translate('parameters') ?></h4>
            <ul class="list" for="parameter of this.parameters">
                <li><b>{parameter.title_translated} </b>({parameter.name}, {parameter.type})
                <div if="parameter.documentation != null">{parameter.documentation_translated}</div></li>
            </ul>
        </div>
    </div>
    

    <div if="this.dependencies and this.dependencies.length > 0">   
        <rc.dependencies.view dependencies="{this.dependencies}"></rc.dependencies.view>
    </div>

</div>