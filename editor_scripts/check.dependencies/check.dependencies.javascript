class CheckDependencies extends Tag {
    init() {
        this.resource = router$.params.subpage;
        this.dependencies = []
        this.checking = {}
        resource.call({resource:this.resource}).then(result=> {
            this.setAttribute('dependencies', result);
            for (let i = 0 ; i < result.length ; i++) {
                this.checking[result[i]] = true;
            }
        })
    }

    submit() {
        let keys = Object.keys(this.checking)
        let resources = []
        for( let keyNr in keys) {
            let key = keys[keyNr]
            if (this.checking[key]) {
                resources.push(key)
            }
        }
        resource.action('add_dependencies',{'resource':this.resource,'dependencies':resources}).then(result=> {
            router$.goto('/editor/resource/'+this.resource+'/vscode')
        })
        
    }
}