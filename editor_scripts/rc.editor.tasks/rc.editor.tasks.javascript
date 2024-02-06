class JJDevelopment extends Tag {

    set resource(r) {
        this._resource = r
        this.update('this.resource')
        this.updateTasks()
    }

    get resource() {
        return this._resource
    }

    updateTasks() {
        if (this.resource) {
            resource.action('tasks',{resource:this.resource.name}).then( tasks => {
                this.tasks = tasks
                this.update('this.tasks')
            })
        }
    }

    taskCreated(task) {
        /* 2 -> verification */
        let taskData = Object.assign({},task,{resource:this.resource.name})
        resource.action('create_task', taskData).then(result => {
            if (result.error) {
                alert(result.error)
            } else {
                this.updateTasks()
                this.form.reset()
				account$.update()
            }
        });
    }
}