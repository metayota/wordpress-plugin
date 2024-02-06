class JJFormArray extends RCBaseFormElement {
    setup() {
        this._value = []
    }
    set value(v) {
        if (typeof v == 'string' && v != '') {
            v = JSON.parse(v)
        }
        if (v) {
            this._value = v
        } else {
            this._value = []
        }
        this.update('this.value')
    }
    get value() {
        return this._value
    }
    set resourcetype(r) {
        this._resourcetype = r
        Tag.registerAndLoad(r)
        Resource.loaded(r).then( resource => {
            this.resource = resource
            this.update('this.resource')
            this.update('this.value')
        })
        this.update('this.resourcetype')
    }
    editItem(i) {
        let item = this.value[i]
         Resource.cls('dialog').createDialogWithData(this.resourcetype, item).then( item => {
            this.value[i] = item
            this.update('this.value')
            this.changed()
        })
    }
    deleteItem(i) {
        this.value.splice(i,1)
        this.resourceUpdated(this.value)
    }
    get resourcetype() {
        return this._resourcetype
    }
    resourceUpdated(value,index) {
        this.value[index] = value;
        this.update('this.value')
        this.changed()
    }
    addItemDialog() {
        Resource.cls('dialog').createDialogWithData(this.resourcetype, {}).then( item => {
            this.addItem(item)
        })
    }
    changed() {
        this.fire('change',this.value)
    }
    addItem(value) {
        this._value.push(value);
        this.update('this.value')
        this.changed()
    }
    allowDrop(ev,node) {
        ev.preventDefault();
        node.classList.add('active')
    }
    leaveDroparea(event,node) {
        node.classList.remove('active')
    }

    drag(ev,index) {
        ev.dataTransfer.setData("index", index);
    }

    drop(ev, newIndex) {
        ev.preventDefault();
        var index = ev.dataTransfer.getData("index");

        this.value.splice(newIndex, 0, this.value.splice(index, 1)[0]);
        this.update('this.value')
        //ev.target.appendChild(document.getElementById(data));
    }
}