class Code extends Tag {
    init() {
        var codetag = this;
        this.loaded = Tag.load('assets/js/prismjs.js').then( (value) => {
            codetag.loadComplete = true;
            return true;
        } );
    }
    render(browserElement) {
        this.browserElement = browserElement;
        if (this._content && this.language) {
            let content = this._content.replace(/&lt;/g, "<").replace(/&gt;/g, '>');
            let language = this.language;
            var example = this.example;
            content = this.fixIndentation(content).replace(/\t/g, '   ');;
            example.innerHTML = this.htmlEscape(content);
            if (this.loadComplete === true) {
                var html = Prism.highlight(content, Prism.languages[language]);
                example.innerHTML = html;
            } else {
                this.loaded.then(function () {
                    
                    var html = Prism.highlight(content, Prism.languages[language]);
                    example.innerHTML = html;
                });
            }
        }
        return browserElement;
    }
    refresh() {
        if (this.browserElement)
            this.render(this.browserElement);
    }
    set content(content) {
        this._content = content;
        this.refresh();
    }
    get content() {
        return this._content;
    }
    htmlEscape(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            
    }
    fixIndentation(text) {
        let lines = text.split('\n');
        let minSpaces = null;
        for (let line of lines) {
            if (line.trim()) {
                let spaces = line.search(/\S/)
                if (minSpaces == null || spaces < minSpaces) {
                    minSpaces = spaces;
                }
            }
        }
        let newLines = [];
        for (let line of lines) {
            if (line.trim()) {
                newLines.push(line.substr(minSpaces));
            }
        }
        if (newLines.length > 0) {
            newLines[0] = newLines[0].trim();
        }
        return newLines.join('\n');
    }
}