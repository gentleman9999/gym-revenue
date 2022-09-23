export default class ConversationMsg {
    /**
     * @param {Conversation} conversation
     * @param {string} name
     * @param {string} sid
     * @param {Array<MessageInfo>} messages
     * @param {Boolean} hasMultiParticipants
     * @param {Date} updatedAt
     */
    constructor(
        conversation,
        name,
        sid,
        messages,
        hasMultiParticipants,
        updatedAt
    ) {
        this.conversation = conversation;
        this.name = name;
        this.sid = sid;
        this.messages = messages;
        this.hasMultiParticipants = hasMultiParticipants;
        this.updatedAt = updatedAt;
    }

    /**
     * @returns {MessageInfo}
     */
    get lastMessage() {
        return this.messages.at(-1);
    }

    get unreadMessagesCount() {
        return this.messages.filter((msg) => !msg.attributes.read).length;
    }

    touch() {
        this.updatedAt = new Date();
    }
}
