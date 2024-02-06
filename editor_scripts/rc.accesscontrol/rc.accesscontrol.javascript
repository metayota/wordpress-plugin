class EditorPluginAccessControl extends Tag {

    setup() {
        GlobalResource.call('db.fetchall',{table:'usergroup',translate_columns:['title'],'serverdb':true}).then( usergroups => {
            this.usergroups = usergroups
            this.updateWhenReady()
        })
    }

    getHighlight() {
        if (window.location.hash) {
            let params = window.location.hash.substring(1).split(',')
            let line = 0;
            let tab = ''
            for(let param of params) {
                let paramDetail = param.split('=')
                let paramName = paramDetail[0]
                let paramValue = paramDetail[1]
    
                if (paramName == 'highlight') {
                    window.history.replaceState({}, "", '/editor/resource/'+this.resource.name+'/access');
                    return paramValue;
                }
            }
            return undefined
        }
    }

    updateWhenReady() {
        if (this.usergroups && this.accessRights && this.resourceAccessRights) {
            this.update('this.usergroups')
            this.update('this.accessRights')
        }
    }

    accessChanged(accessRight, usergroupId, event) {
        resource.action('change-access',{'access_right':accessRight, 'usergroup_id': usergroupId, 'access': event, 'resource' : this.resource.name})
    }

    set resource(r) {
        this._resource = r
        this.update('this.resource')
        if (r) {
            resource.action('list-access-rights', {resource:r.name}).then( accessRights => {
                this.resourceAccessRights = accessRights
                this.updateWhenReady()
            })
            // , where:{"type":this.resource.type}
            let accessDownloaded = GlobalResource.call('db.fetchall',{table:'access_rights',translate_columns:['title','description']}).then(accessRights => {
                this.accessRights = accessRights
                this.updateWhenReady()
            })
        }
    }
    get resource() {
        return this._resource
    }

    hasAccess(accessRight, usergroup) {
        if (usergroup == "3" && !editor$.isWP) {
            return true;
        }
        if (!this.resourceAccessRights) {
            return false 
        } 
        let idx = this.resourceAccessRights.findIndex( v => { 
            return (v.access_right == accessRight && v.usergroup_id == usergroup) 
        })
        return idx != -1
    }
}