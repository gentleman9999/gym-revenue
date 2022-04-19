import { ref } from "vue";

const notifications = ref([]);
const unreadCount = ref(0);

const notificationResponse = await axios.get(route("notifications"));
const unreadCountResponse = await axios.get(route("notifications.unread"));
notifications.value = notificationResponse.data.data;
unreadCount.value = unreadCountResponse.data;

const dismissNotification = async (id) => {
    const response = await axios.post(route("notifications.dismiss", id));
    const newUnreadCount = response.data;
    console.log({ response, newUnreadCount });
    unreadCount.value = newUnreadCount;
};

const incrementUnreadCount = () => {
    unreadCount.value = unreadCount.value + 1;
};

export const useNotifications = () => {
    return {
        notifications,
        unreadCount,
        dismissNotification,
        incrementUnreadCount,
    };
};
