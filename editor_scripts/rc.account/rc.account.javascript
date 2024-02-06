class RCAccount {

    update() {
        if (!Resource.wp) {
            return resource.action('load').then( account => {
                Object.assign(this,account)
                notify('account$')
                return account
            });
        }
    }

	static getInstance() {
		if (RCAccount.instance) {
			return RCAccount.instance
		} else {
			return new RCAccount();
		}
	}

	static initialize() {
        if (!Resource.wp) {
            return resource.action('load').then( accountData => {
                let account = RCAccount.getInstance()
                Object.assign(account,accountData)
                Tag.publish('account$',account)
                return true;
            });
        }
	}

	get valueFormatted() {
		if (this.value) {
			return this.value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1â€‰")
		}
	}
}