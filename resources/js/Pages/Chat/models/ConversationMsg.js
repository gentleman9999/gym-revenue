export default class ConversationMsg {
    /**
     * @param {Conversation} conversation
     * @param {string} name
     * @param {string} sid
     * @param {Array<MessageInfo>} messages
     * @param {Boolean} hasMultiParticipants
     */
    constructor(conversation, name, sid, messages, hasMultiParticipants) {
        this.conversation = conversation;
        this.name = name;
        this.sid = sid;
        this.messages = messages;
        this.hasMultiParticipants = hasMultiParticipants;
    }

    /**
     * @returns {MessageInfo}
     */
    get lastMessage() {
        return this.messages.at(-1);
    }
}
