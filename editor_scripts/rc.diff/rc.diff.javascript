class RCDiff extends Tag {
    set language(l) {
        this._language = l
        if (this.editor)
            monaco.editor.setModelLanguage(this.editor.getModel(), l)
    }

    get language() {
        return this._language
    }

    set original(o) {
        this._original = o
        this.loadEditor()
    }

    get original() {
        return this._original
    }

    set updated(u) {
        this._updated = u
        this.loadEditor()
    }

    get updated() {
        return this._updated
    }

    render(browserElement) {
        this.loadEditor(browserElement.node);
    }

    loadEditor(element) {
        /*if (this.done) {
            return
        }*/
        if (!element) {
            element = this.differ
        }
        if (!element) {
            return
        }
        if (this.original != undefined && this.updated != undefined) {
            this.done = true
          //  let element = this.differ

            var originalModel = monaco.editor.createModel(this.original, "text/plain");
            var modifiedModel = monaco.editor.createModel(this.updated, "text/plain");
            if (!this.diffEditor) {
                this.diffEditor = monaco.editor.createDiffEditor(element);
            }
            this.diffEditor.setModel({
                original: originalModel,
                modified: modifiedModel
            });
            setTimeout( () => {
                this.diffEditor.layout();
            },100)
            
        }
    }
    
}