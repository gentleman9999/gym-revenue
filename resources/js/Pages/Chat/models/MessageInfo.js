let foo = 0;
export default class MessageInfo {
    /**
     * @param {Message} message
     */
    constructor(message) {
        this.date = new Date();
        this.message = message;
    }

    /**
     * @returns {string}
     */
    get body() {
        return this.message.body;
    }

    /**
     * @returns {Date}
     */
    get dateCreated() {
        return this.message.dateCreated;
    }

    /**
     * @returns {string}
     */
    get author() {
        return this.message.author;
    }

    /**
     * @returns {string}
     */
    get participantSid() {
        return this.message.participantSid;
    }

    /**
     * @returns {Object}
     */
    get attributes() {
        return this.message.attributes;
    }

    /**
     * @returns {void}
     */
    markAsRead() {
        if (!this.attributes.read) {
            this.message.updateAttributes({ read: true });
        }
    }

    dateFormatted() {
        const time = this.dateCreated
            .toLocaleTimeString()
            .replace(/(:\d{2})( [AP]M)$/, "$2");
        if (
            this.dateCreated.setHours(0, 0, 0, 0) ===
            this.date.setHours(0, 0, 0, 0)
        ) {
            return time;
        }

        return this.dateCreated.toLocaleDateString();
    }
}
