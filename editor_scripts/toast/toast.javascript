class Toast extends Tag {
    init() {
        this.messages = [];
        Tag.publish('toast$', this)
        window.addEventListener('message', function (e) {
            if (e.data.type == 'toast') {
                toast$.show(e.data.message, e.data.sticky, e.data.type, e.data.link, e.data.button)
            }
        }.bind(this))
    }

    gotoLink(message) {
        if (message.callback != undefined) {
            message.callback();
        } else if (message.link.startsWith('http://') || message.link.startsWith('https://')) {
            window.location = message.link;
        } else {
            router$.goto(message.link)
            this.hide(message)
        }
    }

    addMessage(newMessage, isConfirm) {
        for (let msg of this.messages) {
            if (msg.message == newMessage.message && msg.link == newMessage.link) {
                return true
            }
        }


        this.messages.push(newMessage);
        this.update('this.messages');
        if (!isConfirm) {
            setTimeout(() => {
                this.hide(newMessage);
            }, 3500 + newMessage.message.length * 150);
        }
    }

    getButtonName(message) {
        return translate(message.buttonName ? message.buttonName : 'ok_btn')
    }

    showCallback(message, callback, buttonName = "", cssClass = "") {
        const newMessage = {
            message: message,
            isConfirm: true,
            link: "",
            buttonName: buttonName,
            cssClass: cssClass,
            callback: callback
        };

        this.addMessage(newMessage, true)
    }

    display(msg) {
        if (msg.message != undefined) {
            let confirm = msg.confirm;
            if (msg.confirm == undefined) {
                confirm = msg.type == undefined || msg.type == 'error'
            }
            let link = msg.link == undefined ? '' : msg.link
            let btnName = msg.button_name == undefined ? '' : msg.button_name
            this.show(translate(msg.message), confirm, msg.type, link)
        }
    }

    show(message, isConfirm = false, cssClass = "", link = "", buttonName = "") {
        const newMessage = {
            message: message,
            isConfirm: isConfirm,
            link: link,
            buttonName: buttonName,
            cssClass: cssClass
        };

        this.addMessage(newMessage, isConfirm)
    }

    hide(message) {
        if (message.callback != undefined) {
            message.callback()
        }
        const index = this.messages.indexOf(message);
        if (index !== -1) {
            this.messages.splice(index, 1);
            this.update('this.messages');
        }
    }
}