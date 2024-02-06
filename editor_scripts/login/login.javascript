class FormLogin extends Tag {
    init() {
        if (document.location.protocol == 'http:') {
            document.location.protocol = 'https';
        }
    }
    logout() {
		resource.action('logout', {}).then( x => {     
            Tag.publish('loggedInUser$',{})
         })
    }
    login(loginData) {
        resource.call(loginData).then( result => {
            if (result.error != undefined) {
                this.setAttribute('errorMessage',result.error)
                this.loginform.reset();
            } else {
                Tag.publish('loggedInUser$',Object.assign(new JJUser(), result))
                router$.goto('/login')
            }
        })
    }
}