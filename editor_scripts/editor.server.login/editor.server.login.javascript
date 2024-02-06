class ChooseServer extends Tag {
    init() {
        this.reloadList();
    }
    reloadList() {
        resource.action('list').then( servers => {
            this.setAttribute('servers', servers)
        })
    }
    createFreeServer() {
        this.setAttribute('creating', true)
        let formData = {"product":1, "name":"website", time:1,"location":"ams"};
        GlobalResource.call('rc.product.buy',formData).then( result => {
            GlobalResource.action('metayota','export_app');
            alert('The server has been created')
            window.location = ('/editor/view/server.admin')
        });
    }

    chooseServer(id) {
        GlobalResource.action('editor','choose_server',{server:id}).then( result => {
            editor$.updateCurrentServer().then(result=> {
                this.reloadList()
                sessionStorage.setItem('recentlyviewed',"[]")
            })
        })
    }
}