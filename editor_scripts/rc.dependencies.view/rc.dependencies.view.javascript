class RCDependenciesView extends Tag {
    set dependencies(d) {
        this._dependencies = d
        this.update('this.dependencies')
        this.updateDependencies()
    }
    get dependencies() {
        return this._dependencies
    }
    updateDependencies() {
        resource.action('get_dependencies_info', {'dependencies':this.dependencies}).then(dependencies => {
            this._dependencies = dependencies
            this.update('this.dependencies')
        })
    }
}