class FormLiveSearch extends Tag {
	setup() {
		this.valueattribute = 'name'
        let recentlyViewed = sessionStorage.getItem('recentlyviewed')
        if (recentlyViewed) {
            this.recentlyViewed = JSON.parse(recentlyViewed)
        } else {
		    this.recentlyViewed = []
        }
		this.options = []
		this.filteredOptions = []
	}
	init() {
		this.default = true
		this.searchOptionText = ''
		if (!this.chosenItems)
			this.chosenItems = []
		if (!this.defaulttext)
			this.defaulttext = '...'
		this.updateFilteredOptions()
	}
    clickedElement() {
        if (document.activeElement != this.searchElement && document.activeElement != this.dropdownform) {
                this.searchElement.focus();
        }  else {
            if (!this.itemListVisible) {
                this.showOptions()
            } else {
                this.hideOptions()
            }
        }
    }
	setValues(values) {
		if (values.options !== undefined) {
			this._options = values.options
		}
		if (values.value != undefined) {
			this._value = values.value;
		}
	}
	clearRecentlyViewed() {
		this.recentlyViewed = []
	}
	set class(value) {
		this._class = value
		this.update('this.class')
	}
	get class() {
		return this._class;
	}
	set valueattribute(v) {
		this._valueattribute = v
	}
	get valueattribute() {
		return this._valueattribute
	}
	set defaultfilter(v) {
		this._defaultfilter = v
	}
	get defaultfilter() {
		return this._defaultfilter
	}
    resetSearch() {
        this.searchElement.innerHTML = ''
    }
    deleteRecentlyViewed(name) {
         this.recentlyViewed = this.recentlyViewed.filter( v => v.name !== name )
    }
	addRecentlyViewed(option) {
		let idx = this.recentlyViewed.findIndex( v => v.name == option.name )
        this.recentlyViewed = this.recentlyViewed.filter( v => v.name != option.name )
		if (!option.isPlaceholder) {
			this.recentlyViewed.unshift(option)
			if (this.recentlyViewed.length > 5) {
				this.recentlyViewed.pop()
			}
            sessionStorage.setItem('recentlyviewed',JSON.stringify(this.recentlyViewed))
		}
		this.updateFilteredOptions()
	}
	selectSearchElement() {
		var doc = document;
		var element = this.searchElement
		if (doc.body.createTextRange) {
			var range = document.body.createTextRange();
			range.moveToElementText(element);
			range.select();
		} else if (window.getSelection) {
			var selection = window.getSelection();
			var range = document.createRange();
			range.selectNodeContents(element);
			selection.removeAllRanges();
			selection.addRange(range);
		}
	}
	searchFocus() {
        this.searchElement.focus()
		this.showOptions()
		this.selectSearchElement()
	}
	placeCaretAtEnd(el) {
		el.focus();
		if (typeof window.getSelection != "undefined"
			&& typeof document.createRange != "undefined") {
			var range = document.createRange();
			range.selectNodeContents(el);
			range.collapse(false);
			var sel = window.getSelection();
			sel.removeAllRanges();
			sel.addRange(range);
		} else if (typeof document.body.createTextRange != "undefined") {
			var textRange = document.body.createTextRange();
			textRange.moveToElementText(el);
			textRange.collapse(false);
			textRange.select();
		}
	}

	inputChanged(value) {
		this.searchResults = this.getOptionsFromTextSearch(value)
		this.searchInput = value
		this.update('this.searchInput')
	}
	set searchResults(r) {
		this._searchResults = r
		this.updateFilteredOptions()
	}
	get searchResults() {
		return this._searchResults
	}
	updateFilteredOptions() {
		if (!this.searchResults || this.searchResults.length == 0) {
			this.filteredOptions = this.getDefaultOptions()
		} else {
			this.filteredOptions = this.searchResults
		}
		this.update('this.filteredOptions')
	}
	set value(value) {
		if (value === null || value === undefined) {
			this._value = null;
			this.chosenItems = []
			this.update('tag.getChosenItems')
			this.update('tag.chosenItems.length')
			return;
		}

		this._value = value;
		if (!this.options && this.tempValue === undefined) {
			this.tempValue = value;
			return;
		} if (!this.options) return;
		this.tempValue = undefined;
		if (this.multiple || Array.isArray(value)) {
			this.chosenItems = [];
			for (let valueItem of value) {
				let foundItem = this.options.findIndex(option => option[this.valueattribute] == valueItem);
				this.chosenItems.push(this.options[foundItem]);
			}
			this._value = this.chosenItems.map((option) => option[this.valueattribute]);
		} else {
			let foundItem = this.options.findIndex(option => option[this.valueattribute] == value);
			if (foundItem == -1) {
				return;
			}
			this.chosenItems = [this.options[foundItem]]
			let foundItems = this.chosenItems.map((option) => option && option[this.valueattribute]);
			if (foundItems)
				this._value = foundItems[0];
		}
		this.update('tag.getChosenItems')
		this.update('tag.chosenItems.length')
	}
	get value() {
		return this._value;
	}
	set options(options) {
		this._options = options;
		if (this.tempValue !== undefined) {
			this.value = this.tempValue;
		}
		this.update('this.options')
		this.updateFilteredOptions()
	}
	get options() {
		if (this.default && this._options) {
			return [{ name:this.defaulttext, title: this.defaulttext, value: null, isPlaceholder: true }, ...this._options]
		}
		return this._options;
	}
	focusSearchEl() {
		this.searchElement.focus();

	}
	doKeyHandle(event) {
		if (event.code == 'Backspace') {
			this.focusSearchEl()
			if (document.activeElement !== this.searchElement) {

				//.selectionStart = this.searchElement.innerHTML.length-1;
			}
		}
	}
	doKeypress(event) {
		if (this.readonly) {
			return;
		}
		if (event.metaKey) {
		//	return;
		}

		if (event.code == "Enter") {
			if (!this.itemListVisible) {
				this.showOptions();
			} else if (!this.chooseFocusedOption()) {
				this.toggleOptions();
			}
			event.preventDefault();
			event.stopPropagation();
		} else if (event.code == "Enter") {
			event.preventDefault();
			event.stopPropagation();

			this.chooseFocusedOption()
			this.hideOptions();
			this.searchElement.blur();

			this.update('this.getChosenItems')
        } /*else if (event.code == "Space") {
			if (this.searchTimer == null) {
			//	this.toggleOptions();
			}
			this.avoidScrollEvent(event);
		}*/ else if (event.code == "Escape") {
			this.hideOptions();
		} else if (event.code == "Tab") {
			this.chooseFocusedOption();
			this.hideOptions();
		} else if (event.code != 'ArrowDown' && event.code != 'ArrowUp') {
			this.focusSearchEl()
		}
	}
	hideOptionsSoon() {
		window.setTimeout(function () {
			this.hideOptions()
		}.bind(this), 300);
	}
	getChildIdx(option) {
		return this.filteredOptions.findIndex((o) => o == option)
	}
	getOptionElement(option) {
		let childIdx = this.getChildIdx(option);
		var children = this.itemlist.children
		return children[childIdx];
	}
	render(browserElement) {
		if (this.multiple) {
			for (let selectedOption of this.chosenItems) {
				this.getOptionElement(selectedOption).classList.add('selected');
			}
		}
		this.searchResults = this.getOptionsFromTextSearch(this.value)
		this.updateFilteredOptions()
		//return browserElement;
	}
	chooseOption(option) {
		
		this.focusOption(option, false)
		if (this.multiple) {
			let alreadySelectedItem = this.chosenItems.some((selOption) => selOption[this.valueattribute] === option[this.valueattribute]);
			if (alreadySelectedItem) {
				this.getOptionElement(option).classList.remove('selected');
				this.chosenItems = this.chosenItems.filter((selOption) => selOption[this.valueattribute] !== option[this.valueattribute]);
			} else {
				this.chosenItems.push(option);
				var childEl = this.getOptionElement(option).classList.add('selected');
			}
			this._value = this.chosenItems.map((option) => option[this.valueattribute]);
		} else {
			this._value = option[this.valueattribute];
			this.chosenItems = [option];
			this.hideOptions();
			this.focusOption(null)
		}
		this.unsetSearch()
		this.touched();
		this.update('tag.getChosenItems')
		this.update('tag.chosenItems.length')
		this.fire('change', this.value)
		this.addRecentlyViewed(option)
	}
	elementKeydown(event) {


		if (event.key.length === 1 &&
			(event.key == ' ' || this.searchOptionText.length > 0)) {
			//this.performSearch(event);
		}

	}
	removeFocusedElement() {
		if (this.focusedElement) {
			this.focusedElement.classList.remove('focus')
			//this.focusedElement.innerHTML = this.focusedOption.name
			this.focusedElement = null;
		}
	}
	focusOption(option, scrollToTop = false) {
        
		this.removeFocusedElement();
		if (this.itemlist != null && option != null) {
			var childEl = this.getOptionElement(option);
			if (!childEl) {
				return
			}
			if (scrollToTop) {
				this.itemlist.scrollTop = childEl.offsetTop
			} else {
				this.scrollElementVisible(childEl)
			}
			childEl.classList.add('focus');
			if (this.multiple && this.multipleEl) {
				this.multipleEl.nativeElement.focus(); //keep the focus on the itemlist, after clicking
			}
			this.focusedElement = childEl

			let idx = option.name.toLowerCase().indexOf(this.searchOptionText)
		}
		this.focusedOption = option;
	}
	touched() {
		this._touched = true;
	}
	getDefaultOptions() {
		if (!this.options) {
			return []
		}
		let matchingItems = this.options.filter( v => {
			return (v[this.defaultfilter.name] == this.defaultfilter.value);
		})
        if (!this.projectresources) {
            this.projectresources = []
        }
		return [{ name:'recent', title: translate('recently_viewed'), value: null, isPlaceholder: true },...this.recentlyViewed, { name:'project', title: translate('project_files'), value: null, isPlaceholder: true },  ...this.projectresources]
	}
	getOptionsFromTextSearch(qry) {
		if (!this.options) {
			return []
		}
		var foundItems = [];
		qry = qry.toLowerCase()
		if (qry.length > 0) {
			if (this.multiple) {
				this.showOptions();
			}
			this.options.forEach((option) => {
				if ( !option.isPlaceholder  && ((option.type != undefined && option.type.toLowerCase().startsWith(qry)) || (option.name != undefined && option.name.toLowerCase().startsWith(qry))) ) {
					foundItems.push(option);
				}
			});
			this.options.forEach((option) => {
                if (!option.isPlaceholder) {
                    if ((option.type != undefined && option.type.toLowerCase().includes(qry)) || (option.name != undefined && option.name.toLowerCase().includes(qry)) || option.title.toLowerCase().includes(qry)) {
                        if (foundItems.findIndex(v => v === option) == -1) {
                            foundItems.push(option);
                        }
                    }
                }
			});
		}
		return foundItems;
	}
	unsetSearch() {
		this.cancelTimer()
		this.searchOptionText = ''
		this.searchTimer = undefined
	}
	cancelTimer() {
		if (this.searchTimer !== undefined) {
			window.clearTimeout(this.searchTimer)
			this.searchTimer = undefined
		}
	}
	hideOptions() {
		this.itemListVisible = false;
		this.update('tag.itemListVisible')
	}
	showOptions() {
		if (!this.itemListVisible) {
			this.itemListVisible = true;
			this.update('tag.itemListVisible');
			this.removeFocusedElement();
			if (this.chosenItems[0])
				this.focusOption(this.chosenItems[0]);
			else
				this.itemlist.scrollTop = 0;
		}
	}
	
	getChosenItems() {
		if (!this.searchElement) {
			return []
		}
		let prefix = this.searchElement.innerHTML
		if (this.multiple) {
			return this.chosenItems && this.chosenItems.length > 0 ? this.chosenItems.map((option) => option.name.replace(prefix, '')).join(', ') : this.defaulttext;
		} else {
			return this.chosenItems && this.chosenItems.length > 0 ? this.chosenItems[0].name : this.defaulttext;
		}
	}
	chooseFocusedOption() {
		if (this.itemlist != null) {
			if (this.focusedOption) {
				this.chooseOption(this.focusedOption)
				return true
			}
		}
		return false;
	}

	hideSoon() {


	}

	handleBlur(event) {
		this.touched()
		this.hasfocus = false

	}
	handleFocus(event) {
		this.hasfocus = true
		this.update('tag.hasfocus')
		//this.showOptions()
	}
	avoidScrollEvent(event) {
		event.preventDefault();
		event.stopPropagation();
	}
	toggleOptions() {
		if (this.itemListVisible !== true) {
			if (document.activeElement == this.dropdownform || document.activeElement == this.searchElement) {
				this.showOptions()
			} else {
				this.dropdownform.focus()
                this.showOptions()
			}
		} else {
			this.hideOptions()
		}
	}
	scrollElementVisible(element) {
		if (this.itemlist.scrollTop > element.offsetTop) {
			this.itemlist.scrollTop = element.offsetTop;
		}
		let selectedElementVisibleTop = element.offsetTop - this.itemlist.offsetHeight + element.offsetHeight;
		if (this.itemlist.scrollTop < selectedElementVisibleTop) {
			this.itemlist.scrollTop = selectedElementVisibleTop;
		}
	}
	handleNavKeys(event) {
		if (event.code == "ArrowUp" || event.code == "ArrowDown") {
			this.unsetSearch();
			if (this.itemlist != null) {
				var children = this.itemlist.children;
				let selectedChildIdx = -1;
				for (var i = 0; i < children.length; i++) {
					var childEl = children[i];
					if (childEl.classList.contains('focus')) {
						selectedChildIdx = i;
						childEl.classList.remove('focus')
					}
				}
				for (var i = 0; i < children.length; i++) {
					var childEl = children[i];
					if (((event.code == "ArrowDown") && ((selectedChildIdx + 1) == i)) || ((event.code == "ArrowUp") && ((selectedChildIdx - 1) == i))) {
						this.focusOption(this.filteredOptions[i], false)
					}
				}
			}
			this.avoidScrollEvent(event);
		}
		this.update('tag.searchOptionText')
	}
	clickedOption(option) {
        if (option) {
            this.unsetSearch();
            this.chooseOption(option)
            this.dropdownform.focus();
            this.hasfocus = true;
            this.hideOptions();
        }
	}
	handleMousedown(event) {
		this.dropdownform.focus();
		this.hasfocus = true;
		event.stopPropagation();
		event.preventDefault();
	}
}