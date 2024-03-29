<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="editor-search {this.visible then 'visible'}">
    <form.text (enter)="this.performSearch()" element="formSearchText" label="<?= translate('search_text') ?>"></form.text>
    
    <img (click)="this.close()" class="close-btn" src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/close_white.svg" />
    <div class="search-results" for="searchResult of this.searchResults">
        <h3>{searchResult.name} ({searchResult.language ? searchResult.language : searchResult.type})</h3>  
        
        <div if="!!searchResult.lines">
            <div for="line of searchResult.lines"> 
                <div (click)="this.goTo(searchResult.name,searchResult.language,line.line)">
                    <rc.code content="{line.code}" language="{searchResult.language}"></rc.code>
                </div>
            </div>
         </div>

        <div if="!!searchResult.parameters">
            <div for="parameter of searchResult.parameters"> 
                <div (click)="router$.goto('/editor/resource/'+searchResult.name+'/parameters')">
                    <?= translate('parameter') ?>: {parameter.name}
                </div>
            </div>
        </div>
    </div>
</div>