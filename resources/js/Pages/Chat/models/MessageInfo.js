export default class MessageInfo {
    /**
     * @param {string} body
     * @param {Date} datetime
     * @param {string} creator
     * @param {string} sid
     */
    constructor(body, datetime, creator, sid) {
        this.body = body;
        this.datetime = new Date(datetime);
        this.creator = creator;
        this.sid = sid;
        this.date = new Date();
    }

    /**
     * @param {Message} message
     */
    static buildFromMessage(message) {
        return new MessageInfo(
            message.body,
            message.dateCreated,
            message.author,
            message.participantSid
        );
    }

    dateFormatted() {
        if (
            this.datetime.setHours(0, 0, 0, 0) ===
            this.date.setHours(0, 0, 0, 0)
        ) {
            return this.datetime
                .toLocaleTimeString()
                .replace(/(:\d{2})( [AP]M)$/, "$2");
        }

        return this.datetime.toLocaleDateString();
    }
}
