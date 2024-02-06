class Search extends Tag {
    performSearch() {
        this.call('search',{'search':this.formSearchText.value}).then( (result) => {
            this.searchResults = result
            this.update('this.searchResults')
        } ).catch(error => {
			console.error(error) 
		});
        
    }
    goTo(resource,tab,line) {
        editor$.goto(resource,tab,line)
    }

    toggle() {
        if (this.visible) {
            this.close()
        } else {
            this.show()
            this.focus();
        }
    }

    focus() {

        this.formSearchText.input.focus();
     
    }

    show() {
        this.setAttribute('visible',true)
        document.getElementById('rc-editor').style.left = '364px'
        this.focus();
    }

    close() {
        this.setAttribute('visible',false)
        document.getElementById('rc-editor').style.left = '0px'
    }
}