class VSCodeEditor extends Tag {

    setup() {
        resource.get({ server_id: editor$.currentServer?.id }).then(tags => {
            this.tags = tags
        });
        if (Resource.wp) {
            this.definedFunctions = []
        } else {
            resource.action('defined_fn', null, true).then(definedFunctions => {

                this.definedFunctions = definedFunctions
            })
        }
    }

    set tab(t) {
        if (t != this._tab) {
            this._tab = t
            this.getErrors()
        }
    }

    get tab() {
        return this._tab
    }

    set resource(r) {
        if (r != this._resource) {
            this._resource = r
            this.getErrors()
        }
    }

    get resource() {
        return this._resource
    }

    findLineAndPosition(searchString, fullString) {
        var position = fullString.indexOf(searchString);
        var lineNumber = fullString.substr(0, position).split('\n').length;
        var linePosition = position - fullString.lastIndexOf('\n', position - 1);
        return { lineNumber: lineNumber, linePosition: linePosition };
    }
    updateLineNumberHash(modelMarkers) {
        if (window.location.hash) {
            let params = window.location.hash.substring(1).split(',')
            let line = 0;
            let tab = ''
            let codepoint = undefined
            let message = "Error in your source code"
            for (let param of params) {
                let paramDetail = param.split('=')
                let paramName = paramDetail[0]
                let paramValue = paramDetail[1]

                if (paramName == 'line') {
                    line = paramValue * 1;
                }
                if (paramName == 'tab') {
                    tab = paramValue;
                }
                if (paramName == 'codepoint') {
                    codepoint = decodeURIComponent(paramValue)
                }
                if (paramName == 'message') {
                    message = decodeURIComponent(paramValue)
                }

            }
            if (codepoint != undefined) {
                let pos = this.findLineAndPosition(codepoint, monacoEditor$.getValue())
                modelMarkers.push({ severity: monaco.MarkerSeverity.Error, startLineNumber: pos.lineNumber * 1, startColumn: pos.linePosition * 1, endLineNumber: pos.lineNumber * 1, endColumn: pos.linePosition + codepoint.length, message: message })
                monaco.editor.setModelMarkers(monaco.editor.getModels()[0], '', modelMarkers)
                monacoEditor$.revealLine(pos.lineNumber)
                monacoEditor$.setPosition({ column: pos.linePosition, lineNumber: pos.lineNumber })

            }

            if (tab != '' || codepoint != undefined) {
                history.pushState("", document.title, window.location.pathname
                    + window.location.search);
            }

        }
    }

    getLineInfo(textContent, lineNumber) {

        // Split the text content into an array of lines
        var lines = textContent.split('\n');

        // Get the line specified by the lineNumber argument
        var line = lines[lineNumber - 1];

        // Find the position of the first non-whitespace character on the line
        var firstNonWhitespaceIndex = line.search(/\S/);

        // If there are no non-whitespace characters on the line, return null
        if (firstNonWhitespaceIndex === -1) {
            return {
                position: 0,
                length: 5
            };
        }

        // Get the length of the content on the line, ignoring leading and trailing whitespace
        var contentWithoutWhitespace = line.trim();
        var contentLength = contentWithoutWhitespace.length;

        // Return an object containing the position and length of the content on the line
        return {
            position: firstNonWhitespaceIndex,
            length: contentLength
        };
    }

    getErrors() {
        if (!this.resource || !this.tab) {
            return
        }


        resource.action('get_errors', { resource: this.resource, tab: this.tab }).then(result => {
            let modelMarkers = []
            for (let err of result) {

                let lineInfo = this.getLineInfo(monacoEditor$.getValue(), err.line);
                modelMarkers.push({ severity: monaco.MarkerSeverity.Error, startLineNumber: err.line * 1, startColumn: lineInfo.position + 1, endLineNumber: err.line * 1, endColumn: lineInfo.position + lineInfo.length, message: err.message })

            }

            monaco.editor.setModelMarkers(monaco.editor.getModels()[0], '', modelMarkers)
            this.updateLineNumberHash(modelMarkers)
        })

    }

    set language(l) {
        this._language = l
        if (this.editor)
            monaco.editor.setModelLanguage(this.editor.getModel(), l)
    }

    get language() {
        return this._language
    }

    set value(v) {
        if (!v) {
            v = ''
        }
        this._value = v
        if (this.editor)
            this.editor.setValue(v)
    }

    get value() {
        if (this.editor) {
            return this.editor.getValue()
        }
        return this._value
    }

    initToken() {
        monaco.editor.setModelMarkers(monaco.editor.getModels()[0], '', [{ severity: monaco.Severity.Error, startLineNumber: 1, startColumn: 1, endLineNumber: 1, endColumn: 21, message: 'This is just a test!!!' }])

    }

    render(browserElement) {

        this.loadEditor(browserElement.node)
    }

    hasOpenedBrackets(str) {
        const indexEqualsDoubleQuote = str.lastIndexOf('="');
        const indexEqualsDoubleQuoteCurlyBrace = str.lastIndexOf('="{');

        return indexEqualsDoubleQuoteCurlyBrace >= indexEqualsDoubleQuote;
    }

    getLastOpenedParameter(valueInRange) {
        const paramRegex = /\s*([\w-]+|\([\w-]+\))\s*=\s*"[^"]*$/;
        const match = valueInRange.match(paramRegex);
        return match ? match[1] : null;
    }


    loadEditor(element) {
        //    require.config({ paths: { 'vs': 'libs/monaco-editor/min/vs' } });
        //    require(['vs/editor/editor.main'], function () {

        // let element = document.getElementById('vscodeeditor')//.attachShadow({mode: 'closed'})

        this.editor = monaco.editor.create(element, {
            value: this.value,
            language: this.language,
            wordBasedSuggestions: false,
            theme: 'vs-dark',
            automaticLayout: true,
            minimap: {
                enabled: false,
                renderCharacters: false
            }
        });

        this.publish('monacoEditor$', this.editor)
        this.editor.onDidChangeModelContent(function (e) {
            this.fire('change', this.value)

        }.bind(this))

        // Add a custom action
        
        //if (this.editor.getModel().getLanguageId() === 'php') {
            this.editor.addAction({
                id: 'formatPHP',
                label: translate('format_php_code'),
                keybindings: [monaco.KeyMod.CtrlCmd | monaco.KeyCode.KEY_F], // Optional: Set a keybinding
                contextMenuGroupId: 'navigation', // Add to context menu
                contextMenuOrder: 1.5, // Order in the context menu
                run: function (ed) {
                    const val = ed.getValue();

                    GlobalResource.action('rc.formatter', 'format_php', { php: val }).then(result => {
                        if (result.php) {
                            ed.setValue(result.php);
                        }
                    });

                    return null;
                }
            });
       // }

        let fn = function getXmlCompletionProvider(monaco) {
            return {
                triggerCharacters: ['<', ' ', '"', '='],
                provideCompletionItems: function (model, position) {
                    let valueInRange = model.getValueInRange({ startLineNumber: 1, startColumn: 1, endLineNumber: position.lineNumber, endColumn: position.column });
                    let language = this.language;

                    if (language == 'html' || language == 'html.php') {
                        let lastTag = this.getLastOpenedTag(valueInRange);
                        if (lastTag && lastTag.tagName) {
                            let tag = this.getTagByName(lastTag.tagName);
                            if (tag && tag.parameters) {
                                let completionItems = [];
                                let lastOpenedParameter = this.getLastOpenedParameter(valueInRange);
                                if (lastOpenedParameter) {
                                    let parameterDefinition = tag.parameters.find(param => param.name === lastOpenedParameter)
                                    let paramType = parameterDefinition ? parameterDefinition.type : 'unknown';
                                    if (paramType === 'boolean') {
                                        completionItems.push(
                                            { label: 'true', insertText: '{true}', documentation: 'Sets the value to true.' },
                                            { label: 'false', insertText: '{false}', documentation: 'Sets the value to false.' }
                                        );
                                    } else {
                                        // Other parameter types can be handled here.
                                    }
                                    let needsBrackets = true
                                    if (lastOpenedParameter.startsWith('(') || lastOpenedParameter == 'if' || lastOpenedParameter == 'for') {
                                        needsBrackets = false
                                    }
                                    if (this.hasOpenedBrackets(valueInRange)) {
                                        needsBrackets = false
                                    }
                                    let openBracket = needsBrackets ? '{' : '';
                                    let closeBracket = needsBrackets ? '}' : '';
                                    if (lastOpenedParameter.startsWith('(')) {
                                        completionItems.push(
                                            { label: 'event', insertText: 'event', documentation: translate('event_variable_documentation') }
                                        );
                                    }
                                    if (editor$.implementation?.javascript != undefined) {
                                        let jsCode = editor$.implementation?.javascript
                                        const functionNames = jsCode.match(/(\w+)\s*\(\s*(.*?)\s*\)\s*{[\s\S]*?}/g).map(func => func.match(/(\w+)\s*\(/)[1]);
                                        functionNames.forEach(functionName => {
                                            completionItems.push(
                                                { label: openBracket + 'this.' + functionName + '()' + closeBracket, insertText: openBracket + "this." + functionName + "()" + closeBracket, documentation: translate('custom_function_documentation', functionName) }
                                            );
                                        });

                                    }
                                } else {
                                    for (let param of tag.parameters) {
                                        completionItems.push({
                                            label: param.name,
                                            kind: monaco.languages.CompletionItemKind.Keyword,
                                            insertText: param.name + '="$0"',
                                            insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                                            documentation: param.documentation_translated,
                                            detail: param.title_translated
                                        });
                                    }
                                }

                                return { suggestions: completionItems };
                            }
                            if (!tag) {
                                let tags = this.getTagsStartByName(lastTag.tagName);

                                let completionItems = [];
                                for (let tag of tags) {
                                    completionItems.push({
                                        label: tag.name,
                                        kind: monaco.languages.CompletionItemKind.Keyword,
                                        insertText: `${tag.name}$0></${tag.name}>`,
                                        insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                                        documentation: tag.documentation
                                    });
                                }

                                return { suggestions: completionItems };
                            }
                        }
                    }
                }.bind(this)
            };
        }.bind(this);

        // Here you'll need to implement 'getLastOpenedParameter' function that 
        // extracts the last parameter that user is typing from 'valueInRange'.



        monaco.languages.registerCompletionItemProvider('html', fn(monaco));


        /*  var originalProvider = monaco.languages.getCompletionItemProvider('html');
  
          // Now, override the provider with a new one that filters out the unwanted items
          monaco.languages.registerCompletionItemProvider('html', {
              provideCompletionItems: function(model, position, context, token) {
                  // First, get the default completions from the original provider
                  return originalProvider.provideCompletionItems(model, position, context, token).then(function(completions) {
                      // Next, filter out the unwanted completion items
                      completions.suggestions = completions.suggestions.filter(function(item) {
                          return item.label !== '<!DOCTYPE html>';
                      });
  
                      // Finally, return the filtered list of completion items
                      return completions;
                  });
              }
          });*/

        let phpComplete = function getXmlCompletionProvider(monaco) {
            return {
                provideCompletionItems: function (model, position) {
                    let thisLine = model.getValueInRange({ startLineNumber: position.lineNumber, startColumn: 0, endLineNumber: position.lineNumber, endColumn: position.column })
                    //var myRegexp = /([_A-Za-z0-9]*)[\$,\' "(a-zA-Z_]*$/;
                    var myRegexp = /([_A-Za-z0-9.\-]*)[\$,\' "(a-zA-Z_]*$/;
                    var match = myRegexp.exec(thisLine);
                    let results = []
                    if (match && match.length > 0) {
                        let functionName = (match[1]); // abc
                        results = this.definedFunctions.filter(fn => fn.name.startsWith(functionName));

                    }
                    let completionItems = []
                    for (let result of results) {
                        completionItems.push({
                            label: result.name,
                            kind: monaco.languages.CompletionItemKind.Function,
                            insertText: result.name + '()',
                            documentation: result.description,
                            detail: result.definition
                        })
                    }
                    return { suggestions: completionItems }

                    console.log(thisLine);
                }.bind(this)

            }
        }.bind(this)




        monaco.languages.registerCompletionItemProvider('php', phpComplete(monaco));
        /*            format: {
                        autoClosingTags: true
                    }*/
        monaco.languages.html.htmlDefaults.setOptions({
            suggest: {
                angular1: false,
                html5: false,
                ionic: false,
            }
        });

    }

    getTagByName(name) {
        let tagIdx = this.tags.findIndex(t => { return t.name == name })
        return this.tags[tagIdx]
    }

    getTagsStartByName(name) {
        return this.tags.filter(t => { return t.name.startsWith(name) })
    }

    getLastOpenedTag(text) {
        // get all tags inside of the content
        var tags = text.match(/<\/*(?=\S*)([a-zA-Z-\.]+)/g);
        if (!tags) {
            return undefined;
        }
        // we need to know which tags are closed
        var closingTags = [];
        for (var i = tags.length - 1; i >= 0; i--) {
            if (tags[i].indexOf('</') === 0) {
                closingTags.push(tags[i].substring('</'.length));
            }
            else {
                // get the last position of the tag
                var tagPosition = text.lastIndexOf(tags[i]);
                var tag = tags[i].substring('<'.length);
                var closingBracketIdx = text.indexOf('/>', tagPosition);
                // if the tag wasn't closed
                if (closingBracketIdx === -1) {
                    // if there are no closing tags or the current tag wasn't closed
                    if (!closingTags.length || closingTags[closingTags.length - 1] !== tag) {
                        // we found our tag, but let's get the information if we are looking for
                        // a child element or an attribute
                        text = text.substring(tagPosition);
                        return {
                            tagName: tag,
                            isAttributeSearch: text.indexOf('<') > text.indexOf('>')
                        };
                    }
                    // remove the last closed tag
                    closingTags.splice(closingTags.length - 1, 1);
                }
                // remove the last checked tag and continue processing the rest of the content
                text = text.substring(0, tagPosition);
            }
        }
    }

}