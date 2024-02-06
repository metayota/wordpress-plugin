class VSCodeInc extends Tag {
    static initialize() {
        if (VSCodeInc.included) {
            return true
        }
        let promise = new Promise(function (resolve, reject) {
            VSCodeInc.resolved = resolve
        });

        var script = document.createElement('script');
        script.type = 'text/javascript';
        let path = Resource.wp ? 'wp-content/plugins/metayota/' : '';
        script.src = path + 'libs/monaco-editor/min/vs/loader.js';
        script.onload = function () {
            require.config({ paths: { 'vs': path + 'libs/monaco-editor/min/vs' } });
            require(['vs/editor/editor.main'], function () {
                VSCodeInc.included = true
                VSCodeInc.resolved(true)
            })
        }
        document.head.appendChild(script);



        return promise
    }

}