import { useNotifications } from "@/utils/useNotifications.js";
import ConversationNotification from "@/Pages/Chat/components/ConversationNotification.vue";

const { dismissNotification } = useNotifications();

const isToday = (date) => {
    const today = new Date();
    return (
        date.getDate() === today.getDate() &&
        date.getMonth() === today.getMonth() &&
        date.getFullYear() === today.getFullYear()
    );
};

const isThisWeek = (date) => {
    const todayObj = new Date();
    const todayDate = todayObj.getDate();
    const todayDay = todayObj.getDay();

    // get first date of week
    const firstDayOfWeek = new Date(todayObj.setDate(todayDate - todayDay));

    // get last date of week
    const lastDayOfWeek = new Date(firstDayOfWeek);
    lastDayOfWeek.setDate(lastDayOfWeek.getDate() + 6);

    // if date is equal or within the first and last dates of the week
    return date >= firstDayOfWeek && date <= lastDayOfWeek;
};

const getDayOfWeek = (date) => {
    switch (date.getDay()) {
        case 0:
            return "Sunday";
            break;
        case 1:
            return "Monday";
            break;
        case 2:
            return "Tuesday";
            break;
        case 3:
            return "Wednesday";
            break;
        case 4:
            return "Thursday";
            break;
        case 5:
            return "Friday";
            break;
        case 6:
            return "Saturday";
            break;
    }
};

export const NOTIFICATION_TYPES = Object.freeze({
    /** @see App\Domain\Notifications::TYPE_CALENDAR_EVENT_REMINDER */
    TYPE_CALENDAR_EVENT_REMINDER: "CALENDAR_EVENT_REMINDER",
    /** @see App\Domain\Notifications::TYPE_NEW_CONVERSATION */
    TYPE_NEW_CONVERSATION: "NEW_CONVERSATION",
    /** @see App\Domain\Notifications::TYPE_DEFAULT */
    TYPE_DEFAULT: "DEFAULT_NOTIFICATION",
    /** @see App\Domain\Notifications::TYPE_NEW_MESSAGE */
    TYPE_TASK_OVERDUE: "TASK_OVERDUE",
    /** @see App\Domain\Notifications::TYPE_TASK_OVERDUE */
    TYPE_NEW_MESSAGE: "NEW_MESSAGE",
});

/**
 *
 * @param {{ text:string|object, id:string, state:string, type:string, entity_type?:string, entity?:object}} notification
 * @param options
 * @returns {{options: {timeout: boolean}, text: (string|Object)}}
 */
function notificationResponse(notification, options = {}) {
    // Allow onClose call without disrupting dismissNotification
    const onClose = options.onClose;
    options.onClose = () => {
        dismissNotification(notification.id);
        typeof onClose === "function" && onClose();
    };

    return {
        text: notification.text,
        options,
    };
}

/**
 * Builds notification response for TYPE_CALENDAR_EVENT_REMINDER
 *
 * @param {{ text:string, id:string, state:string, type:string, entity: {start: string, title: string }}} notification
 * @returns {{options: {onClose: (function(): Promise<void>), timeout: boolean}, text: string, state: string}}
 */
function buildCalenderNotification(notification) {
    const start = new Date(notification.entity.start);
    const date = start.toLocaleDateString();
    const time = start.toLocaleTimeString();

    let start_at;
    if (isToday(start)) {
        start_at = `at ${time}`;
    } else if (isThisWeek(start)) {
        start_at = `on ${getDayOfWeek(start)} at ${time}`;
    } else {
        start_at = `on ${date} at ${time}`;
    }

    return notificationResponse(
        notification,
        `${notification.entity.title} ${start_at}`
    );
}

/**
 * @param {{ text:string, id:string, state:string, type:string}} notification
 *
 * @returns {{options: {onClose: (function(): Promise<void>), timeout: boolean}, text: string, state: string}|null}
 */
function buildConversationNotification(notification) {
    const chatElement = document.querySelector(
        '.chat-container[data-chat-external-target-attr="chat-container"]'
    );
    // If chatElement is set, that means we are on the chat page.
    // We don't want to trigger any notifications while at chat page, instead we want to refresh the chat list
    // and silently delete the notification.
    if (chatElement !== null) {
        chatElement.dispatchEvent(
            new CustomEvent("refresh", { detail: notification })
        );
        dismissNotification(notification.id);
        return null;
    }

    notification.text = {
        component: ConversationNotification,
        props: { message: notification.text },
    };

    return notificationResponse(notification, {
        closeOnClick: false, // We want to close with the button in our component
        position: "bottom-left",
        closeButton: false,
        icon: false,
        toastClassName: "conversation-container",
    });
}

/**
 * @param {{ text:string, id:string, state:string, type:string, entity:object}} notification
 *
 * @returns {{options: {onClose: (function(): Promise<void>), timeout: boolean}, text: string, state: string}|null}
 */
function resolveOverdueTask(notification) {
    return notificationResponse(notification, {
        onClose: () => {
            let gotoUrl = new URL("../tasks", window.location);
            gotoUrl.searchParams.append("start", notification.entity.start);

            window.location = gotoUrl.href;
        },
    });
}

/**
 * @param {{ text:string, id:string, state:string, type:string, entity_type?:string, entity?:object}} notification
 *
 * @returns {{options: {onClose: (function(): Promise<void>), timeout: boolean}, text: string, state: string}|null}
 */
export const parseNotificationResponse = (notification) => {
    console.log({ notification });

    switch (notification.type) {
        case NOTIFICATION_TYPES.TYPE_CALENDAR_EVENT_REMINDER:
            return buildCalenderNotification(notification);
        case NOTIFICATION_TYPES.TYPE_NEW_CONVERSATION:
        case NOTIFICATION_TYPES.TYPE_NEW_MESSAGE:
            return buildConversationNotification(notification);
        case NOTIFICATION_TYPES.TYPE_TASK_OVERDUE:
            return resolveOverdueTask(notification);
        default:
            return notificationResponse(notification);
    }
};
