class JRatings extends Tag {
    setup() {
        this.add = true
    }
    set resourcename(r) {
        if (r != this._resourcename) {
            this._resourcename = r
            this.update('this.resourcename') // @TODO: Change color
            this.updateRatings()
        }
    }
    get resourcename() {
        return this._resourcename
    }
    addRating(rating) {
        rating.resource_name = this.resourcename
        rating.save().then( saved => {
            this.updateRatings(this.resourcename)
            this.addRatingForm.reset()
        })
    }
    updateRatings() {
        resource.action('get-ratings',{'resource_name':this.resourcename}).then( ratings => {
            this.setAttribute('ratings', ratings)
        });
    }
    deleteRating(resourcename) {
        resource.action('delete_rating',{resourcename}).then(result=> {
            this.updateRatings()
        })
    }
    formatTime(t) {
        let d = new Date(Date.parse(t));
        return d.getDate()+"."+d.getMonth()+"."+d.getFullYear() + " at " + d.getHours() + ':' + d.getMinutes();
    }
}