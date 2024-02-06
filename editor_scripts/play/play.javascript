class JJPlay extends Tag {
    setup() {
        this.active = true
        this.lastEvents = []
    }
    init() {
        if (window.play_mode) {
            this.playMode = true
        }
        window.play_mode = true
        window.addEventListener('message', function(e) {
            if (e.type != 'toast') {
                this.lastEvents.push( e.data )
                this.update('this.lastEvents')
            }
        }.bind(this))
    }
    destroy() {
        window.play_mode = false
    }

    eventFired(event) {
        this.lastEvents.push( event )
        this.update('this.lastEvents')
    }

    set parameters(p) {
        this._parameters = p
        this.update('this.parameters')
    }

    get parameters() {
        return this._parameters
    }

    set resourcename(r) {
        if (r) {
            this.tname = r
            Tag.registerAndLoad(r)
        }
        this.update('this.tname')
    }

    set resource(r) {
        this._resource = r
        if (r) {
            this.rname = r.name
            Tag.registerAndLoad(r.name)
        }

        this.update('this.resource')
        this.update('this.rname')
    }

    get resource() {
        return this._resource
    }

    submit(p) {
        return this.submitWithParameters(p)
    }
    submitWithParameters(p) {
        if (this.resource) {
            if (this.resource.type == 'webservice') {

                return Resource.callText('server:'.this.resource.name, null, p).then(result => {
                    this.webserviceResult = result
                    this.update('this.webserviceResult')                    
                })
            } else if (this.resource.type == 'tag') {
                this.params = p
                this.update('this.params')
                this.webserviceResult = ''
                this.update('this.webserviceResult')
                return Promise.resolve(true)
            } else if (this.resource.type == 'object') {
                this.webserviceResult = JSON.stringify(p)
                this.update('this.webserviceResult')                    
                return Promise.resolve(true)
            }
        }
    }


    saveAsTestCase() {
        let title = prompt('Title')
        let parameters = JSON.stringify(this.parameters)
        resource.call({ action: 'save_as_testcase', title: title, resource_id: this.tag.id, parameters: parameters })
    }

    paramsChanged(value) {
        this.params = value
        this.update('this.params')
        this.fire('change', value)
    }

    firedEvent(event) {
        this.lastEvent = event
        this.update('this.lastEvent')
    }

    refresh() {
        Tag.tags[this.tname] = undefined
        Resource.resources[name] = undefined
        this.update('this.parameters')
        this.reloadPreview()
    }

    reloadPreview() {
        this.active = false
        this.update('this.active')
        this.active = true
        this.update('this.active')
    }
}