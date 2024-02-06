class TodoApp extends Tag {
    setup() {
        this.todos = []
        this.statusOptions = [{
            name: 'to_do_open',
            value: 'open'
        },{
            name: 'to_do_done',
            value: 'done'
        }]
        this.typeOptions = [{
                name: 'to_do_type_bug',
                value: 'bug'
            },
            {
                name: 'to_do_type_feature',
                value: 'feature'
            },
            {
                name: 'to_do_type_improvement',
                value: 'improvement'
            },
            {
                name: 'to_do_type_idea',
                value: 'idea'
            }
        ];
        this.priorityOptions = [
            {
                name: 'priority_1',
                value: '1'
            },
            {
                name: 'priority_2',
                value: '2'
            },
            {
                name: 'priority_3',
                value: '3'
            }
        ];
    }
    init() {
        this.updateList()
    }

    set listall(l) {
        this._listall = l
        this.update('this.listall')
    }

    get listall() {
        return this._listall
    }

    set resourcename(resourcename) {
        this._resourcename = resourcename
        if (resourcename) {
            this.updateList()
        }
    }


    cmpVersions(a, b) {
        var i, diff;
        var regExStrip0 = /(\.0+)+$/;
        var segmentsA = a.replace(regExStrip0, '').split('.');
        var segmentsB = b.replace(regExStrip0, '').split('.');
        var l = Math.min(segmentsA.length, segmentsB.length);

        for (i = 0; i < l; i++) {
            diff = parseInt(segmentsA[i], 10) - parseInt(segmentsB[i], 10);
            if (diff) {
                return diff;
            }
        }
        return segmentsA.length - segmentsB.length;
    }


    filterVersion(v) {
        this.version = v
        this.updateList()
    }

    filterType(t) {
        this.todoType = t
        this.updateList()
    }

    filterPriority(p) {
        this.priority = p
        this.updateList()
    }

    filterStatus(s) {
        this.status = s
        this.updateList()
    }

    checkTodo(t) {
        t.status = 'done'
        this.update('this.todos')
        resource.action('done', {
            id: t.id
        })
        this.updateList()
    }

    updateList() {
        let version = this.version
        if (!this.resourcename && !this.listall) {
            return
        }
        let listData = this.listall ? {
            listall: true,
            version: version,
            todoType: this.todoType,
            priority: this.priority,
            status: this.status
        } : {
            resourcename: this.resourcename,
            version: version,
            todoType: this.todoType,
            priority: this.priority,
            status: this.status
        }
        resource.action('list', listData).then(result => {
            this.todos = result
            this.update('this.todos')
            let versions = {}
            let time = 0
            for (let item of result) {
                if (item.status == 'open' && item && Number.isInteger(item.time * 1)) {
                    time += item.time * 1
                }
                versions[item.version] = item.version
            }
            if (!this.versionOptions) {
                let versionKeys = Object.keys(versions)
                versionKeys.sort(this.cmpVersions);

                let versionOptions = versionKeys.map(v => {
                    return {
                        name: v,
                        value: v
                    }
                })

                this.setAttribute('versionOptions', versionOptions);
            }

            this.setAttribute('time', time)
        });
    }
    get resourcename() {
        return this._resourcename
    }
    addToDo(todo) {
        let todoData = Object.assign({}, todo, {
            resourcename: this.resourcename
        });
        resource.action('insert', todoData)
        if (!this.todos) {
            this.todos = []
        }
        this.todos.push(todo)
        this.update('tag.todos.length');
        this.addtodo.reset()
        this.setAttribute('viewaction', 'none')
    }
    todoEdited(todo) {
        resource.action('update', todo).then(result => {
            this.setAttribute('viewaction', 'none')
            this.updateList()
            window.location.hash = ''
        })
    }
    todoEditor(todo) {
        DialogForm.createDialogWithData('todo.item', todo).then( todo => {
            this.todoEdited(todo)
        })
        /*
        this.setAttribute('viewaction', 'edit')
        this.setAttribute('editTodo', todo)
        window.location.hash = 'edit-todo'
        */
    }
    removeElement(todo) {
        resource.action('delete', {
            id: todo.id
        });
        this.todos.splice(this.todos.indexOf(todo), 1)
        this.update('tag.todos');
        this.resourceChanged()
    }
    resourceChanged() {
        this.fire('change', this.todos)
    }
}