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
        return this.datetime.toLocaleTimeString();
    }
}
