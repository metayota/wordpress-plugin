class JJTask  {

    doaction(action,info={}) {
        let data = {}
        Object.assign(data,{action:action, task_id: this.id},info)
        return resource.call(data).then(task => {
            if (task.message != undefined) {
                toast$.display(task)
            } else if (!task.error) {
                Object.assign(this,task)
            } else {
                alert(task.error)
            }
            return this
        })
    }
    
    get currentPrice() {
        return this.maxprice;
    }

    getActions() {
        return JSON.parse(this.actions)
    }
  
}