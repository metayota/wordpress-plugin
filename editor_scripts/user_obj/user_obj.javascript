class JJUser {
    get fullname() {
        return this.firstname + ' ' + this.lastname
    }
    logout() {
        Resource.action('login','logout').then(
            x => { 
               Tag.publish('loggedInUser$', {})
        })
    }
    static getLoggedInUser() {
        return JJUser.loggedInUser
    }
    static initialize() {
        JJUser.updateLoggedInUser();
    }
    static updateLoggedInUser() {
		window.loggedInUser$ = {}
        let loggedInUser = this.getLoggedInUser()
        if (!loggedInUser) {
			window.loggedInUser$ = {}
            let loggedInUserSession = sessionStorage.loggedInUser
            if (loggedInUserSession) {
                let loggedInUserParsed = JSON.parse(loggedInUserSession)
                let loggedInUser = new JJUser()
                JJUser.loggedInUser = Object.assign( loggedInUser, loggedInUserParsed )
                Tag.publish('loggedInUser$',JJUser.loggedInUser)
                if (window.editor$) {
                    window.editor$.update('this.getRCMenuName')
                }
            }
        } 

        Tag.call('user_obj').then( user => {
            let loggedInUser = new JJUser();
            Object.assign(loggedInUser,user)
            sessionStorage.loggedInUser = JSON.stringify( Object.assign({},loggedInUser))
            Tag.publish('loggedInUser$',loggedInUser)
            if (window.editor$) {
                window.editor$.update('this.getRCMenuName')
            }
        });
    }
}