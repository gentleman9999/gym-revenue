import { useFlash } from "./useFlash";
import { ref, watch, watchEffect, onBeforeUnmount } from "vue";
import { useUser } from "@/utils/useUser";
import { useNotifications } from "@/utils/useNotifications";
import { generateToast } from "@/utils/createToast";
import { parseNotificationResponse } from "@/utils";

export const useNotificationAlertEmitter = () => {
    const user = useUser();
    const channel = ref();
    const {
        notifications,
        unreadCount,
        dismissNotification,
        incrementUnreadCount,
    } = useNotifications();

    const handleIncomingNotification = (e) => {
        console.log({ e });
        incrementUnreadCount();
        const { text, state, timeout } = parseNotificationResponse(e);

        generateToast(state, text, {
            onClose: async () => {
                console.log(
                    "we can figure out how to route based on event type here!"
                );
                // axios.post(route('notifications.dismiss', e.notification_id));
                await dismissNotification(e.notification_id);
            },
            timeout,
        });

        // let alert = {
        //     type: state || "info",
        //     theme: "sunset",
        //     text,
        //     timeout: timeout || false,
        //     callbacks: {
        //         onClose: async () => {
        //             console.log(
        //                 "we can figure out how to route based on event type here!"
        //             );
        //             // axios.post(route('notifications.dismiss', e.notification_id));
        //             await dismissNotification(e.notification_id);
        //         },
        //     },
        // };
        // new Noty(alert).show();
    };
    onBeforeUnmount(() => {
        //cleanup old listeners
        Echo.leave(channel.value.name);
    });
    watchEffect(() => {
        const privateUserChannel = `private-App.Models.User.${user?.value?.id}`;
        if (channel.value?.name && channel.value?.name !== privateUserChannel) {
            console.log("leaving channel:", privateUserChannel);
            //leave channel when we log out
            Echo.leave(channel.value.name);
        }

        if (
            user.value?.id &&
            !channel.value &&
            !window.Echo?.connector?.channels[privateUserChannel]
        ) {
            channel.value = window.Echo.private(
                `App.Models.User.${user?.value?.id}`
            );
            channel.value.notification(handleIncomingNotification);
        }
    });
};
