import { ref, watchEffect, onBeforeUnmount } from "vue";
import { useUser } from "@/utils/useUser";
import { useNotifications } from "@/utils/useNotifications";
import { generateToast } from "@/utils/createToast";
import { parseNotificationResponse } from "@/utils";

export const useNotificationAlertEmitter = () => {
    const user = useUser();
    const channel = ref();
    const { incrementUnreadCount } = useNotifications();

    const handleIncomingNotification = (e) => {
        console.log({ e });
        incrementUnreadCount();
        const response = parseNotificationResponse(e.payload);
        if (response !== null) {
            generateToast(response.text, response.options);
        }
    };
    onBeforeUnmount(() => {
        //cleanup old listeners
        if (channel.value?.name) {
            Echo.leave(channel.value.name);
        }
    });
    watchEffect(() => {
        const privateUserChannel = `private-App.Domain.Users.Models.User.${user?.value?.id}`;
        if (channel.value?.name && channel.value?.name !== privateUserChannel) {
            console.log("leaving channel:", privateUserChannel);
            //leave channel when we log out
            if (channel.value?.name) {
                Echo.leave(channel.value.name);
            }
        }

        if (
            user.value?.id &&
            !channel.value &&
            !window.Echo?.connector?.channels[privateUserChannel]
        ) {
            channel.value = window.Echo.private(
                `App.Domain.Users.Models.User.${user?.value?.id}`
            );
            channel.value.notification(handleIncomingNotification);
        }
    });
};
