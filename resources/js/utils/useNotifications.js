import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import axios from "axios";
import { usePage } from "@inertiajs/inertia-vue3";
const notifications = ref([]);
const unreadCount = ref(0);

try {
    const page = usePage();
    if (page?.props?.value?.user) {
        const notificationResponse = await axios.get(route("notifications"));
        const unreadCountResponse = await axios.get(
            route("notifications.unread")
        );
        notifications.value = notificationResponse.data.data;
        unreadCount.value = unreadCountResponse.data;
    }
} catch (e) {
    if (e?.response?.status === 401) {
        console.log("got 401, logging out");
        Inertia.post(route("logout"));
    } else {
        console.error("error", e);
    }
}

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
