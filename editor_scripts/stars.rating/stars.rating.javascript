class RCStarsRating extends Tag {
    set stars(s) {
        this._stars = s
        this.update('this.stars')
        this.starsHelper = []
        this.starsHelper2 = []
        for (let i = 0 ; i < s ; i++) {
            this.starsHelper.push('star')
        }
        for (let i = s ; i < 5 ; i++) {
            this.starsHelper2.push('star')
        }
    }
    get stars() {
        return this._stars
    }
}