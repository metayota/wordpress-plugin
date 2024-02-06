class EditorDependencies extends Tag {
    setup() {
        this._dependencies = []
    }
    set tags(tags) {
        this._tags = tags
        if (tags) this.options = tags.map((v, i) => { return { name: v.title + ' ('+v.name+')', info: '(' + v.type + ')', value: v.name } });
        this.update('this.options')
    }

    get tags() {
        return this._tags
    }

    set dependencies(d) {
        if (d === null || d === undefined || d == '') {
            d = []
        }
        try {
            let dependencies = d.map(v => { return Object.assign(v,{'name':v.name }) })
			this.sortDependencies(dependencies)
            this._dependencies = dependencies
        } catch(error) {
            console.error('editor:dependencies',error)
        }
        this.update('this.dependencies')
    }

    get dependencies() {
        return this._dependencies
    }

	sortDependencies(d) {
		d.sort(function(a, b){ 
			if(a.name < b.name) return -1;
			if(a.name > b.name) return 1;
			return 0;
		});
	}

    deleteDependency(idx) {
        this.dependencies.splice(idx,1)
        this.update('this.dependencies')
        this.resourceChanged()
    } 

    resourceChanged() {
        this.fire('change',JSON.stringify(this.dependencies))
    }

    addDependency() {
        if (!this.dependencies) {
            this._dependencies = []
        }
        this.dependencies.push({version:'1.0.0'})
        this.updateAppended('this.dependencies')
        this.resourceChanged();
    }


    getDependencyType(resName) {
        let idx = this.tags.findIndex((t) => { return t.name == resName })
        if (idx > -1 && this.tags[idx]) {
            return this.tags[idx].type
        }
        return ''
    }

    updateResource(dependency,newResource) {
        dependency.type = this.getDependencyType(newResource)
		dependency.name = newResource
        let idx = this.dependencies.findIndex((t) => { return t.name == newResource })
        this.resourceChanged();
        this.updateItem('this.dependencies',idx)
    }

    updateVersion(dependency,newVersion) {
        dependency.version = newVersion
        this.resourceChanged();
    }
}