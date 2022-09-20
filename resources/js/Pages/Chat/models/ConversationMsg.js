export default class ConversationMsg {
    /**
     * @param {Conversation} conversation
     * @param {string} name
     * @param {string} sid
     * @param {Array<MessageInfo>} messages
     */
    constructor(conversation, name, sid, messages) {
        this.conversation = conversation;
        this.name = name;
        this.sid = sid;
        this.messages = messages;
    }

    /**
     * @returns {MessageInfo}
     */
    get lastMessage() {
        return this.messages.at(-1);
    }
}
