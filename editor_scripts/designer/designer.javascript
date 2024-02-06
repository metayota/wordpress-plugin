class Designer extends Tag {
    init() {
        this.designstr = ''
        this.active = true
    }
    set value(v) {
        this._value = v;
        this.update('this.value')
    }
    get value() {
        return this._value;
    }
    set tags(tags) { 
        if (!tags) return
        this._tags = tags
        this.tagOptions = tags.filter(tag => (tag.type == 'tag') || (tag.type == 'html') ).map( v => {
            v['name'] = v['title'];
            return v;
        })
        this.update('this.tags')
        this.update('this.tagOptions')
    }
    get tags() {
        return this._tags;
    }
    set tag(t) {
        this._tag = t
        this.update('this.tag')
    }
    get tag() {
        return this._tag
    }
    designChanged(value) {
        this.value = value
        let html = this.tagToHtml(value)
        let designParsed = JSON.stringify(value)
        let updatedValues = {design:designParsed,html:html}
        this.fire('change', updatedValues)
    }
    refreshPreview() {
	let iFrameURL = this.iframe.src
	this.iframe.src = ''
	this.iframe.src = iFrameURL
    }
    searchParentType(parentItem, searchFor) {
        let tagName = parentItem.tag
        if (parentItem.items != undefined && parentItem.items.length > 0) {
            for (let subItem of parentItem.items) {
                if (subItem === searchFor) {
                    return tagName
                } else {
                    let subitemResult = this.searchParentType(subItem, searchFor)
                    if (subitemResult !== null) {
                        return subitemResult
                    } 
                }
            }
        }
        return null
    }

    getCurrentParentType(searchItem) {
        return this.searchParentType(this.designItem.value,searchItem)
    }

    tagToHtml(tag,level=0) {
        if (!tag) return ''
        let space = '    '.repeat(level)
        let html = space + '<' + tag.tag;
        let content = null
        if (tag.parameters) {
            let paramArr = []
            for (let tagKey in tag.parameters) {
                let tagVal = tag.parameters[tagKey]
                if (typeof tagVal == 'boolean') {
                    tagVal = '{'+tagVal+'}'
                } else if (typeof tagVal == 'string') {
                  //  tagVal = tagVal.replace(/"/g, '&quot;')
                } else if (typeof tagVal == 'object') {
                    tagVal = '{'+JSON.stringify(tagVal).replace(/"/g, "'")+'}';//.replace("'{","").replace("'}",'') // not good replace
                }
                if (tagKey != 'content') {
                    paramArr.push(tagKey + '="' + tagVal + '"')
                } else {
                    content = tagVal
                }
            }

            html += ' ' + paramArr.join(' ')
        }
        html += '>'
        if (content) {
            html += content
        }
        if (tag.items) {
            for (let item of tag.items) {
                html += space + this.tagToHtml(item,level+1)
            }
        }
        html += space + '</' + tag.tag + '>'
        return html
    }

    changedItemTag(value) {
        if (this.selectedElement) {
            this.selectedElement.setTagName(value)
            this.updateParameters()
        }
        this.update('this.selectedElement')
    }
    changedElementName(value) {
        if (this.selectedElement) {
            this.selectedElement.setItemName(value)
        }
    }
    parameterChanged(value) {
        if (this.selectedElement) {
            this.selectedElement.setParameters(value)
        }
    }
    updateParameters() {
        let selectedTag = this.selectedElement.value.tag
        this.selectedTag = selectedTag
        if (!selectedTag || Object.keys(selectedTag).length == 0) {
            this.parameters = []
            this.update('this.parameters')
        } else {
            let parentType = this.getCurrentParentType( this.selectedItem.value )
            this.allowedResourceOptions = null
            this.update('this.allowedResourceOptions')
            Resource.loaded('server:'+parentType).then( resource => {
                this.allowedSubelements = resource.allowed_subelements
                if (this.allowedSubelements && this.allowedSubelements.length > 0) {
                    this.allowedResourceOptions = this.allowedSubelements.map( v => {  return {name:v.resourcetype, value:v.resourcetype} })
                } else {
                    this.allowedResourceOptions = null
                }
                this.update('this.allowedResourceOptions')
            })
            
            let tagIndex = this.tags.findIndex((t) => { return t.value == selectedTag })
            if (this.tags[tagIndex]) {
                
                
                //this.callJson('editor',{action:'load-resource',name:selectedTag})
                Resource.loaded('server:'+selectedTag).then( resource => {
                    this.parameters = resource.parameters ? resource.parameters : []
                    this.currentValue = this.selectedElement.value.parameters
                    this.update('this.parameters')
                    this.update('this.currentValue')
                });
            }
        }

        this.update('this.selectedTag')
        this.update('this.selectedElement')
        
    }
    didSelectItem(event) {
        this.selectedItem = event.item
        this.selectedElement = event.element
        this.updateParameters()
        this.update('this.selectedItem')
        this.update('this.selectedElement')
    }
}