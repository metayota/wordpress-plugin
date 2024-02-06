class CallPreview extends Tag {
	set resource(r) {
		this._resource = r
	}
	get resource() {
		return this._resource
	}
	set parameters(p) {
		this._parameters = p
	}
	get parameters() {
		return this._parameters
	}
	call() {
		GlobalResource.call(this.resource,this.parameters).then(result => {
			this.result = JSON.stringify(result, null, 4)
			this.update('this.result')
		})
	}
}