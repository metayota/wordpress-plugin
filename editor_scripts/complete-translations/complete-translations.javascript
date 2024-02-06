class CompleteTranslations extends Tag {
    init() {
        this.waiting = false
        this.getMissingTranslations()
    }
    getMissingTranslations() {
        resource.action('missing_translations').then(result=> {
            let transformedResult = result.map(item => {
                
                item.cases = item.cases.map(str => {
                    return {
                        name: translate(str),
                        value: str
                    };
                })
                
                return item;
            });
            this.setAttribute('missing_translations',transformedResult)
        })
    }
    setCase(missing_translation, event) {
        missing_translation.case = event
        this.update('this.missing_translations')
    }

    performActions() {
        this.setAttribute('waiting',true)
        resource.action('perform_actions',{'missing_translations':this.missing_translations}).then(result=> {
            this.setAttribute('missing_translations',[])
            this.setAttribute('waiting',false)
            this.getMissingTranslations()
        })
    }
}