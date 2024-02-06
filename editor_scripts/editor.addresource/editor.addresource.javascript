class JJEditorAddResource extends Tag {
    saveResource(event) {
        resource.call(event).then( result => {
            editor$.updateResources().then( result => {
                editor$.gotoResource(event.name)
                editor$.gotoTab('overview')
            })
        })
    }
}