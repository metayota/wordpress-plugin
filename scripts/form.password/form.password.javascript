class Password extends RCBaseFormElement {
    resourceChanged(value) {
        if (this.readonly === true) return
	    this.value = value
	    this.fire('change', value)
    }
}