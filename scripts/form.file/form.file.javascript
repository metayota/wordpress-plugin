class FormFile extends RCBaseFormElement {

	setup() {
		this.uploadLabel = translate('choose_file')
	}

	set uploading(u) {
		this._uploading = u
		this.update('this.uploading')
	}

	get uploading() {
		return this._uploading
	}

    removeFile() {
        this.setAttribute('value','')
        this.fire('change','')
    }

    chooseFile() {
		Tag.ready('form.file.helper').then(ready => {
			let renderedTag = TagModel.renderTag('form.file.helper',{folder:this.folder})
			document.body.appendChild(renderedTag.node)
			renderedTag.tag.chooseFile()
			renderedTag.tag.fileTag = this

			renderedTag.tag.fileChosen = function(result) {
				this.setAttribute('value', result)
				this.fire('change',this.value)
			}.bind(this)

            renderedTag.tag.errorHappened = function(result) {
                this.setAttribute('errorMessage',result.message)
				this.setAttribute('value', '')
                this.setAttribute('uploading', false)
				this.fire('change',this.value)
			}.bind(this)
		})
    }
}