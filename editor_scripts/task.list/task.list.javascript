class TaskList extends Tag {
    setup() {
        //this.tasks = [
    }
    set label(l) {
        this._label = l
        this.update('this.label')
    }
    get label() {
        return this._label
    }
    set tasks(t) {
    //    if (t) {
            this._tasks = t
            this.update('this.tasks')
      //  }
    }
    get tasks() {
        return this._tasks
    }
}