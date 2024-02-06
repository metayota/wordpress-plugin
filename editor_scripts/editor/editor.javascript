class JEditor extends Tag {

    setup() {
        this.tab = 'overview'
    }
    init() {
        this.isWP = resource.getData().wordpress
        this.history = {}
        this.tab_history = {}
        this.disableSnapback()
        this.publish('editor$', this)
        this.selectedProject = localStorage.selectedProject
        if (this.isWP) {
            this.selectedProject = 1
            localStorage.selectedProject = 1
        }
        this.previousTab = this.tab
        this.hidden = localStorage.editorHidden == 'true'
        this.fullscreen = true
        this.initImplementationDropdown()
        this.updateCurrentTask()
        this.updateTabs()
        this.tab = 'overview'
        if (localStorage.helpmode == 'on') {
            Tag.publish('helpMode$', true)
        }
        if (this.isWP) {
            this.actions = [
                { value: 'add_resource', name: 'editor_menu_add_resource' },
                { value: 'delete_resource', name: 'editor_menu_delete_resource' },
                { value: 'search', name: 'editor_menu_search' },
                { value: 'translator', name: 'editor_menu_translator' },
                { value: 'admin_menu', name: 'wp_admin_menus' },
                
            ];
        } else {
            this.actions = [
                { value: 'errors', name: 'editor_menu_errors' },
                { value: 'add_resource', name: 'editor_menu_add_resource' },
                { value: 'delete_resource', name: 'editor_menu_delete_resource' },
                { value: 'create_project', name: 'editor_menu_create_project' },
                { value: 'open_project', name: 'editor_menu_open_project' },
                { value: 'todos', name: 'editor_menu_todos' },
                { value: 'search', name: 'editor_menu_search' },
                { value: 'submit_work', name: 'editor_menu_submit_work' },
                { value: 'update', name: 'editor_menu_update' },
                { value: 'translator', name: 'editor_menu_translator' }
            ];
        }


        this.options = [];
        this.updateResources();

        window.onbeforeunload = function () {
            if (!this.unsaved) {
                return null
            }
            return translate('confirm_exit_message');
        }.bind(this);

        document.onkeypress = function (e) {
            this.onKeypress(e)
        }.bind(this)

        window.onresize = function (e) {
            this.doLayout(this.tag)
        }.bind(this)

        this.updateCurrentServer();
        this.showTag('editor.welcome');
        //  this.resourceSelected(this.selectedTag)

        this.chat()
    }

    showStandardRoute() {
        this.setAttribute('tab', 'standard-route')
    }


    chat() {

    }

    formatSource() {
        let val = this.msedit.editor.getValue()
        let suffix = this.getSuffix(this.activeImplementationTab)
        if (suffix == 'javascript') {
            GlobalResource.call('rc.formatter', { js: val }).then(result => {
                if (result.js) {
                    this.activeImplementation = result.js
                    this.update('this.activeImplementation')
                }
            })
        }
        if (suffix == 'css') {
            GlobalResource.action('rc.formatter', 'format_css', { css: val }).then(result => {
                if (result.css) {
                    this.activeImplementation = result.css
                    this.update('this.activeImplementation')
                }
            })
        }
        if (suffix == 'php') {
            GlobalResource.action('rc.formatter', 'format_php', { php: val }).then(result => {
                if (result.php) {
                    this.activeImplementation = result.php
                    this.update('this.activeImplementation')
                }
            })
        }
        //value="{this.activeImplementation}" language="{}">
        //
    }

    getRCMenuName() {
        if (this.currentTask) {
            return 'Task #' + this.currentTask.id
        }
        if (this.currentServer && this.currentServer.title) {
            let username = loggedInUser$.username
            if (this.currentServer.servername) {
                //          return username + '@' + this.currentServer.servername
            }
            return this.currentServer.title
        }
        return 'Metayota'
    }

    updateCurrentServer() {
        return resource.action('current_server').then(server => {
            this.currentServer = server
            this.update('this.currentServer')
            this.update('this.getRcMenuActions')
            this.update('this.getRCMenuName')
            this.updateResources()
        })
    }

    getSuffix(filename) {
        if (filename == 'html.php') {
            return 'html'
        }
        if (!filename) {
            return ''
        }
        if (filename.includes('.')) {
            return filename.split('.').pop()
        } else {
            return filename
        }
    }

    updateCurrentTask() {
        resource.action('current_task').then(task => {
            if (task && task.id) {
                this.currentTask = task
            } else {
                this.currentTask = undefined
            }
            notify('editor$.currentTask')
            this.update('this.getRcMenuActions')
            this.updateTabs()
            this.update('this.getRCMenuName')
        })
    }

    toggleSettings() {
        this.setAttribute('settingsVisible', !this.settingsVisible)
    }

    enterFullscreen(element) {
        if (element.requestFullscreen) {
            element.requestFullscreen()
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen()
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen()
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen()
        }
    }
    initImplementationDropdown() {
        this.implTabs = [
            {
                name: translate('create_file'), fn: function () {
                    Resource.cls('dialog').createDialog('rc.editor.tab.create', { label: 'create_tab_label', submitlabel: "create_tab_submit_label" }).then(project => {
                        if (project.technology || project.custom) {

                            let resourceName = project.custom != '' && project.custom != null ? project.custom : project.technology
                            this.activeImplementationTab = resourceName
                            this.activeImplementation = ''
                            if (!this.implementation) {
                                this.implementation = {}
                            }
                            this.implementation[resourceName] = '';
                            this.implementationTabs.push({ name: resourceName })

                            this.update('this.implementationTabs')
                            this.update('this.activeImplementationTab')
                            this.update('this.activeImplementation')
                            this.resourceChanged();
                        }
                    })
                }.bind(this)
            },
            {
                name: translate('delete_file'), fn: function () {
                    if (confirm(translate('confirm_delete_file'))) {
                        delete this.implementation[this.activeImplementationTab]
                        let idx = this.implementationTabs.findIndex(tab => { return tab.name == this.activeImplementationTab })
                        this.implementationTabs.splice(idx, 1)
                        this.activeImplementationTab = null
                        this.activeImplementation = ''
                        this.update('this.implementationTabs');
                        this.update('this.activeImplementationTab');
                        this.update('this.implementation');
                        this.update('this.activeImplementation')
                        this.resourceChanged();

                        if (this.implementationTabs.length > 0) {
                            let tabName = this.implementationTabs[0].name
                            this.implementationTabChanged(tabName)
                        }
                    }
                }.bind(this)
            },
            {
                name: translate('rename_file'), fn: function () {
                    let oldResourceName = this.activeImplementationTab
                    let newResourceName = prompt('Enter new name for tab', oldResourceName)
                    if (newResourceName && (newResourceName != oldResourceName)) {
                        this.implementation[newResourceName] = this.implementation[oldResourceName]
                        delete this.implementation[oldResourceName]
                        let idx = this.implementationTabs.findIndex(tab => { return tab.name == oldResourceName })
                        this.implementationTabs.splice(idx, 1)
                        this.implementationTabs.push({ name: newResourceName })

                        this.activeImplementationTab = newResourceName
                        this.update('this.implementationTabs');
                        this.update('this.activeImplementationTab');
                        this.update('this.implementation');
                        this.resourceChanged();
                    }
                }.bind(this)
            }
        ]
    }
    editorFullscreen() {
        this.enterFullscreen(document.documentElement);
    }
    showViewWithParameters(parameters) {
        debugger
        this.selectedResource = this.selectedTag
        this.setAttribute('settingsVisible', false)
        this.gotoTab('view')
        this.selectedResourceParameters = parameters
        this.update('this.selectedResourceParameters')
        this.update('this.selectedResource')

    }
    findLineAndPosition(searchString, fullString) {
        var position = fullString.indexOf(searchString);
        var lineNumber = fullString.substr(0, position).split('\n').length;
        var linePosition = position - fullString.lastIndexOf('\n', position - 1);
        return { lineNumber: lineNumber, linePosition: linePosition };
    }
    updateLineNumberHash() {
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


            }
            if (tab != '') {
                this.switchTab('vscode')
                if (tab == 'html') {
                    if (this.implementationTabs) {
                        let idx = this.implementationTabs.findIndex(tab => { return tab.name == 'html' })
                        if (idx == -1) {
                            tab = 'html.php'
                        }
                    }
                }
                this.switchImplementationTab(tab)
                this.msedit.editor.revealLine(line)
                this.msedit.editor.setPosition({ column: 0, lineNumber: line })
            }
            if (codepoint != undefined) {


                    let pos = this.findLineAndPosition(codepoint, this.implementation[this.activeImplementationTab])
                    this.msedit.editor.revealLine(pos.lineNumber)
    
                    let modelMarkers = []
                    modelMarkers.push({ severity: monaco.MarkerSeverity.Error, startLineNumber: pos.lineNumber*1, startColumn: pos.linePosition*1, endLineNumber: pos.lineNumber*1, endColumn: pos.linePosition+codepoint.length, message: message})
                    monaco.editor.setModelMarkers(monaco.editor.getModels()[0], '', modelMarkers)
                 let newDecorator = {
                     range: new monaco.Range(pos.lineNumber, pos.linePosition, pos.lineNumber, pos.linePosition+codepoint.length),
                     options: { inlineClassName: 'highlight' }
                 };
                 this.currentDecorationId = this.msedit.editor.getModel().deltaDecorations([], [newDecorator]);

            }
        }
    }

    removeDecoration() {
        if (this.currentDecorationId) {
            this.msedit.editor.getModel().deltaDecorations([this.currentDecorationId], []);
            this.currentDecorationId = null;  // Reset the id after removal
        }
    }
    /* chooseServer() {
         Resource.cls('dialog').createDialog('choose.server', {submitlabel:"Choose Server"}).then( server => {
             resource.action('choose_server',{server:server.server}).then( server => {
                 this.resetEditor(true)
                 this.updateResources()
                 this.currentServer = server
                 this.update('this.currentServer')
                 this.update('this.getRcMenuActions')
                 this.update('this.getRCMenuName')
             })
         });
     }*/
    getRcMenuActions() {
        let chooseServerName = this.currentServer && this.currentServer.id ? this.currentServer.title : 'Servers';

        let helpMode = {
            name: (window.helpMode$ == true ? 'editor_menu_help_mode_on' : 'editor_menu_help_mode_off'), fn: function () {
                Tag.publish('helpMode$', !window.helpMode$)
                this.update('this.getRcMenuActions')
                if (window.helpMode$) {
                    toast$.show(translate('help_mode_activation_message'), false, 'success')
                    localStorage.helpmode = 'on'
                } else {

                    toast$.show(translate('help_mode_deactivation_message'), false, 'success')

                }
            }.bind(this)
        };

        if (this.isWP) {
            let items = [
                {
                    name: 'editor_menu_render_html_tutorial', fn: () => {
                        window.location = 'https://www.metayota.com/documentation'
                    }
                },
                helpMode
            ];
            return items;
        }

        let items = [
            {
                name: 'editor_menu_servers', fn: () => {
                    router$.goto('/editor/view/server.admin')
                }
            },
            {
                name: (this.currentTask && this.currentTask.id ? translate('editor_menu_current_task') + this.currentTask.title : 'editor_menu_tasks'), fn: function () {
                    if (this.currentTask && this.currentTask.id) {
                        router$.goto('/tasks/' + this.currentTask.id)
                    } else {
                        router$.goto('/editor/view/website.tasks')
                    }
                }.bind(this)
            },
            { value: 'account', name: 'editor_menu_account' },
            { value: 'your_skills', name: 'editor_menu_your_skills' },
            { value: 'render_html_tutorial', name: 'editor_menu_render_html_tutorial' },
            { value: 'support', name: 'editor_menu_support' },
            {
                name: 'editor_menu_buy_coins', fn: function () {
                    window.location = '/page/rc.web.payments/60'
                }
            },helpMode

        ];

        if (loggedInUser$.usergroup_id == "3") {
            items.push({
                name: translate('admin'), fn: function () {
                    router$.goto('/editor/view/rc.admin')
                }
            })
        }
        if (loggedInUser$.id != undefined) {
            items.push({
                name: translate('logout'), fn: function () {
                    Resource.action('login', 'logout').then(x => {
                        window.loggedInUser$ = {}
                        document.location = '/'
                    })
                }.bind(this)
            })
        } else {
            items.push({
                name: translate('login'), fn: function () {
                    document.location = '/login'
                }
            })
        }
        if (this.currentServer && this.currentServer.id) {
            items.unshift({
                name: editor$.currentServer.http_host, fn: function () {
                    window.location = editor$.currentServer.http_host
                }
            })
        }
        return items;
    }
    logoclick() {
        let n = Math.round(Math.random() * 9 + 1)
        if (n > 5) {
            n = 1
        }
        let animation = 'visible' + n
        this.rlogo.classList.remove(animation)
        this.rlogo.classList.add(animation)
        window.setTimeout(function () {
            this.rlogo.classList.remove(animation)
        }.bind(this), 4000)
    }

    updateResources() {
        return resource.action('list-resources').then(resources => {

            this.allResources = resources
            this.projectResources = resources.filter((v) => {
                return (v.project_id == this.selectedProject)
            })
            this.projectResourceOptions = resources.map((v) => { return { title: v.title, name: v.name, value: v.name, project_id: v.project_id, type: v.type } })
            this.update('this.allResources')
            this.update('this.projectResources')
            this.update('this.projectResourceOptions')

            this.publish('tags$', this.projectResourceOptions);
        });
    }

    resetEditor(update = false) {
        this.selectedTagObj = null
        this.activeImplementation = null
        this.implementationTabs = null
        this.selectedTag = null
        this.update('this.selectedTagObj')
        this.update('this.selectedTag')
        this.update('this.activeImplementation')
        this.update('this.implementationTabs')
        this.unsaved = false
        this.update('this.unsaved')
        this.doLayout()
    }

    setup() {
        this.initUser()

        let spinner = document.getElementById('main-spinner')
        if (spinner) spinner.remove()
    }

    initUser() {
        let user = Resource.cls('user_obj')
        if (user) {
            user.updateLoggedInUser();
        } else {
            console.warn('user is not defined')
        }
    }

    viewFullscreen() {
        this.fullscreen = !this.fullscreen
        this.update('this.fullscreen')
    }

    goto(resource, tab, line) {

        let tabHistory = {}
        tabHistory[tab] = {
            position: { lineNumber: line, column: 1 }
        }
        this.history[resource] = {
            activeTab: tab,
            tab_history: tabHistory
        }
        this.switchImplementationTab(tab)
        this.loadTabAndScrollPosition()
        /*  if (this.selectedResource == resource) {
              this.switchTab('vscode')
              
              this.loadTabAndScrollPosition()
              window.history.pushState({}, '/editor/resource/' + resource + '/vscode', );
          } else {*/
        router$.goto('/editor/resource/' + resource + '/vscode')
        this.msedit.getErrors()
        //}    
        /*
        }
        this.resourceSelected(resource).then(result => {
            //this.switchTab('vscode')
           // 
            setTimeout( () => {
            this.switchImplementationTab(tab)
            this.msedit.editor.revealLine(line)
            this.msedit.editor.setPosition({ column: 0, lineNumber: line })
            //window.history.pushState({}, "", );
            },0);
        })*/
    }

    showEditor() {
        this.hidden = false
        localStorage.editorHidden = false
        this.update('this.hidden')
        this.doLayout(this.tag)
        window.setTimeout(function () {
            this.doLayout(value)
        }.bind(this), 20)
    }

    set hidden(h) {
        this._hidden = h
        this.update('this.hidden')
    }

    get hidden() {
        return this._hidden
    }

    action(name, element) {
        element.value = null

        if (name != 'search') {
            this.searchDialog.close();
        }

        if (name == 'website') {
            window.location = '/'
        }

        if (name == 'account') {
            router$.goto('/editor/view/website.account')
        }

        if (name == 'support') {
            //router$.goto('/editor/resource/rc:website.contact/view')
            window.location = ('/contact')
        }

        if (name == 'your_skills') {
            router$.goto('/editor/view/website.admin.skills')
        }

        if (name == 'update') {
            router$.goto('/editor/view/rc.sync')
        }

        if (name == 'translator') {
            router$.goto('/editor/translator')
        }

        if (name == 'admin_menu') {
            router$.goto('/editor/view/admin-menus')
        }
        

        if (name == 'errors') {
            router$.goto('/editor/view/errors')
        }

        if (name == 'submit_work') {
            router$.goto('/submit/' + this.selectedTag)
        }

        if (name == 'open_project') {
            Resource.cls('dialog').createDialog('rc.project.select', { submitlabel: "Open Project" }).then(project => {
                let projectId = project['project_id']
                localStorage.selectedProject = projectId
                this.selectedProject = projectId
                this.resetEditor(true)
                this.updateResources()
                this.update('this.selectedProject')
            })
        }

        if (name == 'fullscreen') {
            this.editorFullscreen()
        }

        if (name == 'todos') {
            router$.goto('/editor/view/rc.all.todo')
        }

        if (name == 'create_project') {
            DialogForm.createDialog('rc.project', { label: translate('create_project_label'), submitlabel: translate('create_project_submit_label') }).then(project => {
                resource.action('create_project', { project: project })
            })
        }

        if (name == 'create_tab') {

            Resource.cls('dialog').createDialog('rc.editor.tab.create', { label: translate('create_tab_label'), submitlabel: translate('create_tab_submit_label') }).then(project => {
                if (project.technology || project.custom) {

                    let resourceName = project.custom != '' && project.custom != null ? project.custom : project.technology
                    this.activeImplementationTab = resourceName
                    this.activeImplementation = ''
                    if (!this.implementation) {
                        this.implementation = {}
                    }
                    this.implementation[resourceName] = '';
                    this.implementationTabs.push({ name: resourceName })

                    this.update('this.implementationTabs')
                    this.update('this.activeImplementationTab')
                    this.update('this.activeImplementation')
                    this.resourceChanged();
                }
            })
        }
        if (name == 'rename_resource') {
            let resourceName = prompt(translate('resource_name_prompt'));
            if (resourceName) {

            }
        }

        if (name == 'render_html_tutorial') {
            window.location = '/documentation';
        }

        if (name == 'search') {
            this.searchDialog.show();
        }
        if (name == 'add_resource') {
            router$.goto('/editor/view/editor.addresource')
            //this.addresource.visible = true
        }
        if (name == 'delete_resource') {
            let confirmed = confirm(translate('resource_deletion_confirmation', { title: this.selectedTagObj.title, name: this.selectedTagObj.name }));
            if (confirmed) {
                resource.action('delete_resource', { id: this.selectedTagObj.id }).then(deleted => {
                    this.resetEditor(true)
                    this.updateResources()
                    this.tagDropdown.deleteRecentlyViewed(this.selectedTagObj.name)
                })
            }
        }
        if (name == 'backup') {
            this.makeBackup();
        }

    }

    findDocument() {
        this.searchDialog.toggle();
    }

    checkSave() {
        if (this.unsaved) {
            if (translate('save_changes_confirmation')) {
                this.save();
            } else {
                this.tagToSave = null
                this.unsaved = false
                this.update('this.tagToSave')
                this.update('this.unsaved')
                return false;
            }
        }
        this.unsaved = false
        this.update('this.tagToSave')
        this.update('this.unsaved')
        return true;
    }

    showAddResource() {
        this.addresource.visible = true
    }

    gotoImpl(value) {
        router$.goto('/editor/resource/' + value + '/vscode')
        this.selectedResource = value
        this.update('this.selectedResource')
        document.title = value
    }

    gotoView(value) {
        router$.goto('/editor/resource/' + value + '/view')
        this.selectedResource = value
        this.update('this.selectedResource')
    }

    showTag(value) {
        this.tagToShow = value
        this.update('this.tagToShow')
        this.tab = 'showtag'
        this.update('this.tab')
    }

    loadTabAndScrollPosition() {
        if (this.history[this.selectedResource] != undefined) {
            const storedData = this.history[this.selectedResource];
            this.tab_history = storedData.tab_history
        } else {
            this.tab_history = {}
        }
        this.loadTabScrollPosition()
        /*     if(storedData.activeTab !== undefined) {
                 this.activeImplementationTab = storedData.activeTab;
                 this.gotoTab(storedData.activeTab)
             }*/

        /*   const editor = this.msedit.editor;//.getModels()[0];
           if (editor && storedData.position && storedData.scrollPosition !== undefined) {
               editor.setPosition(storedData.position);
               editor.setScrollTop(storedData.scrollPosition);
               return;
           }*/


        /*  const editor = this.msedit.editor;
          if(editor) {
              editor.setPosition({ lineNumber: 1, column: 1 });
              editor.setScrollTop(0);
          }*/
    }

    saveTabAndScrollPosition() {
        if (this.selectedResource != undefined && this.activeImplementationTab != undefined) {
            this.saveTabScrollPosition()
            //   if(editor) {
            //     const position = editor.getPosition();
            //   const scrollPosition = editor.getScrollTop();
            this.history[this.selectedResource] = {
                activeTab: this.activeImplementationTab,
                tab_history: this.tab_history
            }
            //  }
        }
    }

    gotoResource(value) {
        this.saveTabAndScrollPosition()

        if (value == this.selectedResource) { return }
        if (this.tab == null || this.tab == 'null') {
            this.tab = 'overview'
            this.tabDropdown.value = 'overview'
        }
        router$.goto('/editor/resource/' + value + '/' + this.tab)
        this.selectedResource = value
        this.update('this.selectedResource')
        document.title = value
    }

    gotoTab(value) {
        router$.goto('/editor/resource/' + this.selectedTag + '/' + value)
    }

    resourceSelected(value) {
        if (!value) {
            return false;
        }

        this.selectedResource = value
        this.update('this.selectedResource')

        this.checkSave()
        this.resetEditor()
        this.loading = true
        this.update('this.loading')
        this.tagToSave = null
        this.update('this.tagToSave')

        this.selectedTag = value
        this.update('this.selectedTag')

        if (this.tab != this.tabDropdown.value) {
            if (this.tabDropdown.value != null) {
                this.tab = this.tabDropdown.value
            }
            this.gotoTab(this.tab)
            this.tabChanged(this.tab)
        }

        return resource.action('load-resource', { name: value }).then(resource => {
            if (resource) {
                //this.gotoTab(this.tabDropdown.value)
                this.resourceLoaded(resource);
                window.setTimeout(function () {
                    this.update('this.loading')
                }.bind(this), 300)
                this.loading = false
                this.updateLineNumberHash()


            }
        })
    }

    resourceLoaded(resource) {
        try {
            if (resource.implementation) {
                this.implementation = JSON.parse(resource.implementation)
            } else {
                this.implementation = {}
            }
        } catch (error) {
            console.error('implementation could not be parsed', error, resource.implementation)
            this.implementation = {}
        }

        if (this.implementation) {
            this.implementationTabs = Object.keys(this.implementation).map(v => { return { name: v } });
        } else {
            this.implementationTabs = []
        }

        if (this.implementationTabs.length > 0) {

            if (this.history[resource.name] != undefined) {
                this.activeImplementationTab = this.history[resource.name].activeTab
            } else {
                this.activeImplementationTab = this.implementationTabs[0].name
            }

            this.activeImplementation = this.implementation[this.activeImplementationTab]
        } else {
            this.activeImplementation = ''
        }

        this.update('this.implementationTabs')
        this.update('this.activeImplementation')
        this.update('this.activeImplementationTab')
        this.update('this.implementation')

        this.updateDependencies(resource)
        this.updateParameters(resource)

        this.update('this.selectedTag')

        this.selectedTagObj = resource
        this.update('this.selectedTagObj')

        this.unsaved = false;
        this.update('this.unsaved');
        this.update('this.getDesign')

        this.doLayout(this.tab);

        this.tagToSave = resource

        this.loadTabAndScrollPosition()
        return true
    }

    updateTabs() {
        if (this.isWP) {
            this.tabs = [
                { name: 'editor_menu_overview', value: 'overview' },
                { name: 'editor_menu_config', value: 'config' },
                /*{ name: 'editor_menu_defaults', value: 'defaults' },*/
                { name: 'editor_menu_vscode', value: 'vscode' },
                { name: 'editor_menu_parameters', value: 'parameters' },
                { name: 'editor_menu_dependencies', value: 'dependencies' },
                { name: 'editor_menu_view', value: 'view' },
                { name: 'editor_menu_access', value: 'access' },
            ];
        } else {
            this.tabs = [
                { name: 'editor_menu_overview', value: 'overview' },
                { name: 'editor_menu_config', value: 'config' },
                { name: 'editor_menu_defaults', value: 'defaults' },
                { name: 'editor_menu_vscode', value: 'vscode' },
                { name: 'editor_menu_parameters', value: 'parameters' },
                { name: 'editor_menu_dependencies', value: 'dependencies' },
                { name: 'editor_menu_view', value: 'view' },
                { name: 'editor_menu_designer', value: 'designer' },
                { name: 'editor_menu_development', value: 'development' },
                { name: 'editor_menu_todo', value: 'todo' },
                { name: 'editor_menu_debug', value: 'debug' },
                { name: 'editor_menu_access', value: 'access' },
            ];
        }

        if (this.currentTask) {
            this.tabs.push({ name: 'Changes', value: 'changes' })
        }
        this.update('this.tabs')
        this.update('this.tab')
    }

    updateParameters(resource) {
        if (resource.parameters) {
            try {
                this.parameters = JSON.parse(resource.parameters);
            } catch (e) {
                this.parameters = []
            }
        } else {
            this.parameters = [];
        }
        this.update('this.parameters');
    }

    updateDependencies(resource) {
        if (resource.dependencies && resource.dependencies != '') {
            try {
                this.dependencies = JSON.parse(resource.dependencies);
            } catch (e) {
                this.dependencies = []
            }
        } else {
            this.dependencies = null;
        }
        if (!Array.isArray(this.dependencies)) {
            this.dependencies = [];
        }
        this.update('this.dependencies');
    }

    resourceUpdated(resourceObj, name = null) {
        if (name !== null) {
            let newResourceObj = {}
            newResourceObj[name] = resourceObj
            resourceObj = newResourceObj
        }

        Object.assign(this.tagToSave, resourceObj)
        this.resourceChanged()
    }

    resourceChanged() {
        this.removeDecoration()
        this.unsaved = true;
        this.update('this.unsaved');
    }

    updateTagChanged(tag) {
        this.tagToSave = Object.assign({}, tag)
        this.resourceChanged()
    }

    documentationUpdated(doc) {
        this.tagToSave.documentation = doc
        this.resourceChanged()
    }

    designUpdated(design, html) {
        if (!this.implementation) {
            this.implementation = {}
        }
        this.implementation.design = design
        this.implementation.html = html
        this.resourceChanged()
    }

    paramsChanged(parameters) {
        this.tagToSave.parameters = parameters
        this.resourceChanged()
        this.save()
    }

    todosChanged(todos) {
        this.tagToSave.todos = JSON.stringify(todos)
        this.resourceChanged()
    }

    testsChanged(tests) {
        this.tests = tests
        this.resourceChanged()
    }

    switchAndReloadView() {
        //	this.lastTab()
        // Tag.clear(this.selectedTagObj.name)
        this.gotoTab('view')

    }

    viewResourceWithTab(resource, tab) {
        this.resourceSelected(resource)
        this.tabChanged(tab)
    }

    switchTab(value) {
        this.tabChanged(value)
    }

    tabChanged(value) {
        if (value != 'view') {
            this.previousTab = value
        }
        if (value == 'vscode') {
            this.msedit.getErrors()
        }
        this.checkSave();

        this.tab = value;
        this.update('this.tab');
        this.update('this.tabs');

        this.unsaved = false

        this.update('this.unsaved')

        this.doLayout(value)
        this.updateLineNumberHash()
    }

    doLayout() {
        this.compact = false
        this.update('this.compact')
        if (this.tab == 'vscode' && this.msedit && this.msedit.editor) {
            this.msedit.editor.layout();
        }
    }

    save() {
        if (this.activeImplementationTab && this.tab == 'vscode') {
            this.implementation[this.activeImplementationTab] = this.msedit.value;
        }
        if (this.designer) {
            this.implementation.design = JSON.stringify(this.designer.value, null, 2)
        }

        let data = {}
        data.implementation = JSON.stringify(this.implementation);
        data.documentation = this.tagToSave.documentation;
        data.title = this.tagToSave.title;
        data.extends_resource = this.tagToSave.extends_resource;
        data.dependencies = this.tagToSave.dependencies;
        data.parameters = this.tagToSave.parameters;
        data.tests = JSON.stringify(this.tests);
        data.version = this.tagToSave.version;
        data.license = this.tagToSave.license;
        data.todos = this.tagToSave.todos;
        data.id = this.tagToSave.id;
        data.hash = this.tagToSave.hash


        GlobalResource.call('resource.update', data).then(result => {
            if (result.new_hash != undefined) {
                this.tagToSave.hash = result.new_hash
            }
            if (result.error != undefined) {
                toast$.show(result.error, true)
                alert(result.error)
                return
            }
            if (this.tab == 'designer') {
                this.designer.refreshPreview();
            }
            if (this.tab == 'vscode') {
                this.msedit.getErrors()
            }
            if (this.tab == 'vscode') {
                Resource.call('check.dependencies', { 'resource': this.tagToSave.name }).then(result => {
                    if (result.length > 0) {
                        toast$.show('missing_dependencies', true, 'error', '/editor/view/check.dependencies/' + this.tagToSave.name, 'add_dependencies');
                    }
                })
            }
            if (this.tab == 'designer') {
                Resource.call('check.dependencies', { 'resource': this.tagToSave.name }).then(result => {
                    if (result.length > 0) {
                        Resource.action('check.dependencies', 'add_dependencies', { resource: this.tagToSave.name, dependencies: result }).then(addResult => {

                            let msg = translate('resources_added_to_dependencies_message', { resource_count: result.length });
                            toast$.show(msg, false, 'success');
                            this.designer.refreshPreview();
                        })
                    }
                })
            }
            this.reloadView();
            this.unsaved = false;
            this.update('this.unsaved');
        })
        // this.tagToSave = null



    }

    reloadView() {
        this.setAttribute('reloading', true)
        Tag.clear(this.tagToSave.name)
        this.setAttribute('reloading', false)
    }

    lastTab() {
        if (router$.active && router$.active.content && router$.active.content.tag == 'website.view') {
            router$.goto('/editor/')
        } else if (this.includeTag != null) {
            this.includeTag = null
            this.update('this.includeTag')
            this.doLayout(this.tag)
        } else if (this.previousTab) {
            //this.switchTab(this.previousTab)
            this.gotoTab(this.previousTab)
        }
    }

    getDesign() {
        if (!this.implementation) {
            return {}
        }
        if (this.implementation.design) {
            try {
                return JSON.parse(this.implementation.design)
            } catch (error) {
                console.log(error)
            }
        }
        return {}
    }

    onKeypress(event) {
        if (event.key == 'F10') {
            this.tagDropdown.toggleOptions();
        }
        if (event.key == 'F9') {
            this.tabDropdown.toggleOptions();
        }
        if (event.key == 'F8') {
            this.menuDropdown.toggleOptions();
        }
        if (event.key == 'F7') {
            this.logoclick()
        }
        if ((event.metaKey || event.ctrlKey)) {

            if (event.key == 's') {
                this.save();
                event.preventDefault();
                event.stopPropagation();
                return;
            }
        }
        /*	if (event.metaKey || event.ctrlKey) {
                if (event.key == 'i') {
                    this.tabChanged('implementation')
                } else if (event.key == 'o') {
                    this.tabChanged('overview')
                } else {
                    return;
                }
                event.preventDefault();
                event.stopPropagation();
            }*/
    }
    switchImplementationTab(tab) {
        this.implementationTabChanged(tab)
    }

    saveTabScrollPosition() {
        const editor = this.msedit.editor;//.getModels()[0];
        if (editor) {
            const position = editor.getPosition();
            const scrollPosition = editor.getScrollTop();
            this.tab_history[this.activeImplementationTab] = {
                position: position,
                scrollPosition: scrollPosition
            }
        }
    }

    loadTabScrollPosition() {
        const editor = this.msedit.editor;//.getModels()[0]; 
        if (editor) {
            let storedData = this.tab_history[this.activeImplementationTab]
            if (storedData && storedData.position) {
                editor.revealLine(storedData.position.lineNumber)
                editor.setPosition(storedData.position);
                if (storedData.scrollPosition !== undefined) {
                    editor.setScrollTop(storedData.scrollPosition);
                }
                return;
            }
            editor.setPosition({ lineNumber: 1, column: 1 });
            editor.setScrollTop(0);
        } else {
            console.log('editor is not defined')
        }

    }

    implementationTabChanged(tab) {
        if (this.implementation != undefined) {
            this.saveTabScrollPosition();
            let keepUnsaved = this.unsaved
            if (this.activeImplementationTab) {
                this.implementation[this.activeImplementationTab] = this.msedit.value;
            }
            this.activeImplementationTab = tab;
            this.activeImplementation = this.implementation[this.activeImplementationTab]
            this.update('this.activeImplementation');
            this.unsaved = keepUnsaved
            this.update('this.unsaved')
        }
        this.update('this.activeImplementationTab');
        this.loadTabScrollPosition()
    }

    preventDefault(e) {
        e.preventDefault()
    }
    disableSnapback() {
        document.addEventListener(
            'touchstart',
            this.preventDefault,
            { passive: false }
        )
        document.addEventListener(
            'touchmove',
            this.preventDefault,
            { passive: false }
        )
    }
}