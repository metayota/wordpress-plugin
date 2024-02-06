class Overview extends Tag {

    set resource(r) {
        GlobalResource.action('editor','load-resource',{name:r}).then(r=> {
            this.tag = r
            this.update('this.tag')
        })
    }

    set tag(tag) {
        this._tag = tag
        if (tag) {
            if (typeof tag.parameters == 'object') {
                this.parameters = tag.parameters
            }  else if (tag.parameters && tag.parameters != '') {
                this.parameters = JSON.parse(tag.parameters)
            } else {
                this.parameters = null
            }
            if (tag.todos && tag.todos != '') {
                this.todos = JSON.parse(tag.todos)
            } else {
                this.todos = []
            }
        }
        this.update('this.parameters');
        this.update('this.todos');

        if (tag) {
            resource.action('get_dependencies', tag.name).then(result=> {
                this.setAttribute('dependencies', result)
            })
           /* Resource.loaded('server:' + tag.name).then(resource => {
                this.resource = resource
                this.update('this.resource')
            })*/
        }

        this.updateElementType()
        this.updateImplementationTabs()
    }
    get tag() {
        return this._tag
    }
    set tags(tags) {
        if (!tags) {
            return
        }
        this._tags = tags
        this.options = tags.map((v, i) => { return { name: v.name, value: i } });
        this.update('this.options')
        this.updateElementType()
    }
    get tags() {
        return this._tags
    }
    replaceNames(technologyName) {
        if (technologyName == 'php') {
            return 'PHP'; 
        }
        if (technologyName == 'javascript') {
            return 'JavaScript';
        }
        if (technologyName == 'css') {
            return 'CSS';
        }
        if (technologyName == 'html') {
            return 'HTML';
        }
        if (technologyName == 'design') {
            return 'Design';
        }
        return technologyName;
    }
    updateElementType() {
        if (this.tags && this.tag && this.tags[this.tag.extends_id]) {
            this.elementType = this.tags[this.tag.extends_id].name
        } else {
            this.elementType = '(source)'
        }
        this.update('this.elementType')
    }
    updateImplementationTabs() {
        if (this.tag) {
            if (typeof this.tag.implementation == 'string') {
                let implementation = JSON.parse(this.tag.implementation)
                if (implementation) {
                    this.implementationTabs = Object.keys(implementation)
                } else {
                    this.implementationTabs = null
                }
            }
            this.update('this.implementationTabs')
        }
    }
}