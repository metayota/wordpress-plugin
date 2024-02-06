class DatabaseTableEditor extends Tag {
    setup() {
        this.main_server = resource.getData().main_server
        this.show_description = false
    }
    init() {
        this.page = 1
    }
    set title(t) {
        this._title = t
    }
    get title() {
        return this._title
    }
    set table(t) {
        this._table = t
        this.update('this.table')
        this.reloadData()
    }
    get table() {
        return this._table
    }

    getSortIndicator(parameter) {
        if (this.sort == parameter && this.order != undefined) {
            if (this.order == 'ASC') {
                return '↓';
            } else {
                return '↑';
            }
        }
        return '';
    }
    countItems() {
        resource.call({table:this.table,resourcetype:this.resourcetype,count:true,search:this.search}).then(result => {
            this.setAttribute('count', result)
            this.setAttribute('number_of_pages', Math.ceil(result/15))
        })
    }
    pageChanged(page) {
        this.setAttribute('page', page)
        this.reloadData()
    }
    sortBy(attribute) {
        if (attribute != this.sort) {
            this.setAttribute('order','ASC')
        } else {
            if (this.order == 'DESC') {
                this.setAttribute('order','ASC')
            } else {
                this.setAttribute('order','DESC')
            }
        }
        
        this.setAttribute('sort',attribute)
        this.reloadData()
        this.update('this.getSortIndicator')
    }
    searchChanged(query) {
        this.search = query
        this.reloadData()
    }
    reloadData() {
        if (this.table != undefined) {
            resource.call({table:this.table,resourcetype:this.resourcetype,page:this.page,sort:this.sort,order:this.order,search:this.search}).then(result => {
                if (result.length == 0 && this.page > 1) {
                    this.setAttribute('page',this.page-1);
                    this.reloadData()
                    return
                }
                this.objs = result 
                this.update('this.objs')
            })
            this.countItems()
        }
    }
    set resourcetype(r) {
        if (r != undefined && this._resourcetype !== r) {
            
            this._resourcetype = r
            
            Resource.loaded(this.getServerPrefix()+r).then( resource => {
                this.setAttribute('resource',resource)
                this.update('this.resourcetype')
                this.update('this.objs')
                this.update('this.resource')
            });
        }
    }
    get resourcetype() {
        if (this._resourcetype == undefined) {
            return this._table
        }
        return this._resourcetype
    }
    deleteRow(rowid) {
        if (confirm('Are you sure you want to delete this row?')) {
            resource.action('delete', {table:this._resourcetype, row_id:rowid}).then(result => {
                this.reloadData();
            })
        }
    }
    editRow(rowid) {
        let value = this.objs.filter( obj => obj.id == rowid)[0]
        DialogForm.createDialogWithData(this.getServerPrefix()+this.resourcetype,value,{'show_description':this.show_description}).then(resourceData => {
            resource.action('update', {'table':this.table,'rowid':rowid, 'data':resourceData}).then( result => {
                this.reloadData();
            }) 
        })
    }
    getServerPrefix() {
        debugger
        if (this.main_server) {
            return 'server:'
        } else {
            return '';
        }
    }
    showAddDialog() {
        DialogForm.createDialogWithData(this.getServerPrefix()+this.resourcetype, {}, {'show_description':this.show_description}).then( resourceData => {
            resource.action('insert', {'table':this.table, 'resourceData':resourceData}).then(result => {
                this.reloadData()
            })
        })
    }
}