class RCSpider extends Tag {
    init() {
        
        this.indexPage(document.location.pathname)
        this.setAttribute('visible', location.pathname == '/page/rc.spider')
    }

    visible() {
        return localStorage.getItem('pages') == undefined;
    }

    spider() {
        localStorage.setItem('spider', 'true')
        GlobalResource.action('editor', 'clear_cache').then( result => {
            this.processNext()    
        })
    }

    indexPage(page) {
        if (localStorage.getItem('spider') == "true") {
         
           // if (location.pathname == page) {

            
            window.setTimeout(function() {
                Tag.publish('loggedInUser$', {})
            }.bind(this), 3000)
            window.setTimeout(function() {
                Tag.publish('loggedInUser$', {})
                resource.call( {url:location.pathname,html:document.body.parentElement.innerHTML} ).then( result => {
                    this.processNext()

                })
            }.bind(this), 5000)
           /* } else {
                if (page != undefined) {
                    window.setTimeout(function() {
                    location.pathname = page
                    },1000);
                }
            }*/
        }
    }

    processNext() {
        if (localStorage.getItem('spider') == 'true') {
            resource.action('get_pages').then(page => {
                location.pathname = page.path
            })
        }
    }
}