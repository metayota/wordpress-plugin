<?php namespace Metayota; include_once('../metayota/library.php');  ?><div class="dropdown form-element-container {this.class} livesearch" >
	<label if="this.label !== undefined">{this.label}</label>
	<div 	element="dropdownform" 
			(blur)="tag.handleBlur(event);this.hideOptions()" 
			(focus)="tag.handleFocus(event);" 
			class="form-element dropdown-form-element {tag.itemListVisible ? 'focus' : ''} {tag.chosenItems.length == 0 ? 'default-option' : ''}"
	 		tabindex="0"
			(keydown)="this.doKeypress(event);this.handleNavKeys(event);"
			(keypress)="this.doKeyHandle(event)"
			(click)="this.clickedElement(event)">
		<span class="dropdown-down">▾</span> 
		<div 	element="searchElement" 
				class="search" 
				spellcheck="false" 
                placeholder="Search..."
				contenteditable="true" 
                tabindex="-1"
				(blur)="this.hideOptionsSoon()" 
				(focus)="this.searchFocus()" 
				(keyup)="this.doKeyHandle(event)"
				(keypress)="this.doKeypress(event)"
				(keydown)="this.handleNavKeys(event);"
				(input)="this.inputChanged(node.innerHTML)" 
				class="filter" 
				type="text" 
				autocomplete="off">&nbsp;</div><span style="{!!this.searchInput then 'visibility:hidden'}" class="doppelpunkt">:</span> 
                <span for="item of this.chosenItems"><b> {item.title}</b></span>
                
                </div>
	<div class="itemlist-container">
		<div 	element="itemlist"
				(mousedown)="tag.handleMousedown(event)"
				element="itemlist"
				class="{tag.itemListVisible ? 'dropdown-options dropdown-options-visible' : 'dropdown-options '}"
				for="option of this.filteredOptions">
			<div class="dropdown-option {option.isPlaceholder then 'dropdown-placeholder'}" (click)="this.clickedOption(option)">
				<img class="type-icon" if="!option.isPlaceholder && !!option.type" src="<?= getConfig('wordpress') ? '/wp-content/plugins/metayota/editor_scripts' : '/scripts' ?>/rc.icon/{option.type}.svg"/> <b>{option.title}</b> <span class="option-description-description"> {option.name} {option.documentation ? option.documentation : ''}</span><br/>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>