class DesignItem extends Tag {
    setup() {
        this.items = []
        this.value = { tag:'div', items: this.items }
        this.selected = false
    }
    
    addItem() {
        if (this.items == undefined) {
            this.items = []
        }
        let randomName = 'item' + Math.round(Math.random()*1000)
        this.items.push({tag:'div',items:[], name:randomName})
        this.update('this.value')
        this.update('this.items')
        this.value.items = this.items
        this.resourceChanged()
    }
    
    deleteItem() {
        this.fire('deleted',null);
    }
    deleteSubitem(idx) {
        this.items.splice(idx,1);
        this.update('this.items')
        this.update('this.value')
        this.resourceChanged()
    }
    
    set tags(t) {
		if (!t) {
			return;
		}
        this.tagOptions = t.map((v, i) => { return { name: v.name, value: v.name } })
        this._tags = t
        this.update('this.tagOptions')
    }
    get tags() {
        return this._tags
    }
    
    set selecteditem(v) {
        this._selecteditem = v;
        this.selected = (this.value && this == v)
        this.update('this.selecteditem');
        this.update('this.selected');
    }
    get selecteditem() {
        return this._selecteditem;
    }
    
    set value(v) {
        this._value = v
        if (v && v.items) {
            this.items = v.items
        } else {
            this.items = []
        }
        this.selected = (this.value && this.selecteditem && this.value.name == this.selecteditem)
        this.update('this.value')
        this.update('this.items')
    }
    setItemName(itemName) {
        this.value.name = itemName
        this.update('this.value')
        this.resourceChanged()
    }
    setTagName(tagName) {
        this.value.tag = tagName
        this.update('this.value')
        this.resourceChanged()
    }
    getTagName() {
        return this.value.tag
    }
    setParameter(parameterName,parameterValue) {
        if (this.value.parameters === undefined) {
            this.value.parameters = {}
        }
        this.value.parameters[parameterName] = parameterValue
        this.update('this.value')
        this.resourceChanged()
    }
    setParameters(parameterValue) {
        this.value.parameters = parameterValue
        this.update('this.value')
        this.resourceChanged()
    }
    get value() {
        return this._value
    }
    resourceChanged() {
        this.fire('change',this.value)
    }
    designItemChanged(item, index) {
        this.items[index] = item;
        this.fire('change', this.value)
    }
    tagNameChanged(value) {
        this.value.tag = value;
        this.fire('change', this.value)
    }
    didSelectItem(itemName, element, item) {
        this.fire('selected', {itemName:itemName,element:element,item:item});
    }

    leaveDroparea(event,node) {
        node.classList.remove('active')
    }
    drag(ev,index) {
     //   ev.dataTransfer.setData("index", index);
        DesignItem.fromTag = this
        this.dragging = true
        this.update('this.dragging')
        ///ev.dataTransfer.setData("tag", this);
    }
    allowDrop(ev,node) {
        ev.preventDefault();
        node.classList.add('active')
    }
    drop(ev, newIndex) {
        ev.preventDefault();
        //var index = ev.dataTransfer.getData("index");
        var itm = DesignItem.fromTag
        
        let idx = itm.parent.items.findIndex( i => i == itm.value );
        itm.parent.items.splice(idx,1)
        
        if (itm.parent.items == this.items) {
            if (idx > newIndex) {
                //newIndex++
            } else {
                newIndex--
               // newIndex++
            }
        }
        //this.items.push(itm)
        this.items.splice(newIndex, 0, itm.value)
        //this.items.splice(newIndex, 0, this.items.splice(index, 1)[0]);
        itm.parent.update('this.items')
        this.update('this.items')
        this.fire('change', this.value)
    //    itm.didSelectItem(itm,itm,itm)
        //ev.target.appendChild(document.getElementById(data));
    }
}