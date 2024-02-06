class CreateFreeWebspace extends Tag {
    createFreeServer(software) {
        this.setAttribute('creating', true)
        let formData = {"product":1, "name":"Your Website", time:1,"location":"de","software":software};
        GlobalResource.call('rc.product.buy',formData).then( result => {
            GlobalResource.action('metayota','export_app');
            alert('The server has been created')
            toast$.show('The Free Edition Webspace has been created sucessfully!', true, '/editor/view/server.admin', 'Server Admin')
            this.setAttribute('creating', false)
        });
    }
}