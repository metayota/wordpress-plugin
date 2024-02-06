class RCChange extends Tag {

    init() {
        if (this.repository != undefined) {
            resource.action('compare_to_repository',{'repository':this.repository,'resourcename':this.resourcename}).then(result=> {
                this.displayDiff(result)
            })
        }
    }

    setup() {
        this.recentResource = {}
        this.newResource = {}
    }

    set repository(r) {
        this._repository = r
    }

    get repository() {
        return this._repository
    }

    set versiona(v) {
        if (v != this._versiona) {
            this._versiona = v
            this.clearRecent()
            this.updateCompare()
        }
    }

    get versiona() {
        return this._versiona
    }

    set versionb(v) {
        if (this._versionb != v) {
            this._versionb = v
            this.clearNew()
            this.updateCompare()
        }
    }

    get versionb() {
        return this._versionb
    }

    changeTab(tab) {
        this.setAttribute('currentTab', tab)
    }

    set resourcename(r) {
        if (r != this._resourcename) {
            this._resourcename = r
            this.update('this.resourcename')
            this.updateCompare()
        }
    }

    get resourcename() {
        return this._resourcename
    }

    clearRecent() {
        this.setAttribute('recentParams', null)
        this.setAttribute('recentDependencies', null)
        this.setAttribute('recentImplementation', null)
        this.setAttribute('recentResource', null)
    }

    clearNew() {
        this.setAttribute('newParams', null)
        this.setAttribute('newDependencies', null)
        this.setAttribute('newImplementation', null)
        this.setAttribute('newResource', null)
    }

    displayDiff(result) {
        if (!result.recentResource || !result.newResource) {
            console.log('returned result', result)
            return
        }

        if (result.recentResource.parameters != result.newResource.parameters) {
            this.setAttribute('recentParams', JSON.parse(result.recentResource.parameters))
            this.setAttribute('newParams', JSON.parse(result.newResource.parameters))
        }
        if (result.recentResource.dependencies != result.newResource.dependencies) {
            this.setAttribute('recentDependencies', JSON.parse(result.recentResource.dependencies))
            this.setAttribute('newDependencies', JSON.parse(result.newResource.dependencies))
        }
        if (result.recentResource.implementation != result.newResource.implementation) {
            let recentImpl = JSON.parse(result.recentResource.implementation)
            let newImpl = JSON.parse(result.newResource.implementation)
            let recentKeys = Object.keys(recentImpl)
            let newKeys = Object.keys(newImpl)
            let allKeys = recentKeys.concat(newKeys)
            function onlyUnique(value, index, self) {
                return self.indexOf(value) === index;
            }
            var unique = allKeys.filter(onlyUnique);
            this.setAttribute('implementationKeys', unique)
            this.setAttribute('currentTab', unique[0])
            this.setAttribute('recentImplementation', recentImpl)
            this.setAttribute('newImplementation', newImpl)
        }

        this.setAttribute('recentResource', result.recentResource)
        this.setAttribute('newResource', result.newResource)
    }

    updateCompare() {
        if (editor$.currentTask == undefined) {
            if (!this.resourcename || !this.versiona || !this.versionb) {
                return;
            }
        }
        resource.call({ 'resourcename': this.resourcename, 'version_a': this.versiona, 'version_b': this.versionb }).then(result => {
            this.displayDiff(result)
        })
    }

    toTask() {
        router$.goto('/tasks/' + editor$.currentTask.id)
    }

    seeChanges() {
        router$.goto('/editor/view/rc.changes')
    }
}