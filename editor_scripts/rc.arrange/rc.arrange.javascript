class JArrange extends Tag {
    set idx(i) {
        this._idx = i
    }
    get idx() {
        return this._idx
    }
    set collection(c) {
        this._collection = c
    }
    get collection() {
        return this._collection
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
        this.collection.splice(newIndex, 0, this.collection.splice(index, 1)[0]);
        this.fire('change',this.collection)
    }
}