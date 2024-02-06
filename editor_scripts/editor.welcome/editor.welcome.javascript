class RCEditorWelcome extends Tag {

    init() {
       // this.updateServerCount()
        this.enabled = true
    }

    set enabled(e) {
        this._enabled = e
        this.update('this.enabled')
    }

    get enabled() {
        return this._enabled
    }

    buyFreeServer() {        
        if (confirm('Should we setup the new server? This process will take about 60 seconds.')) {
            this.enabled = false
            let data = {product:8, name: 'Essay edition'}
            GlobalResource.call('rc.product.buy',data).then( result => {
                GlobalResource.action('metayota','export_app');
                if (result.success) {
                    alert(result.message)
                    this.fire('success',result.message)
                    this.enabled = true
                } 
                if (result.error) {
                    alert(result.error)
                    this.enabled = true
                }
                this.updateServerCount();
            }).catch( error => {
                alert('Thank you for buying one of our products! Your server will be installed and should be ready in about 60 seconds!')
                window.setTimeout( function() {
                    this.fire('success', null)
                }.bind(this), 12000)
                router$.goto('/editor/view/server')
            })
        }
    }
}