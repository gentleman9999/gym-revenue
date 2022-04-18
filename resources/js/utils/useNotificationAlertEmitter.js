import {useFlash} from "./useFlash";
import {ref, watch, watchEffect, onBeforeUnmount} from "vue";
import {useUser} from "@/utils/useUser";
import {useNotifications} from "@/utils/useNotifications";

export const useNotificationAlertEmitter = () => {
    const user = useUser();
    const channel = ref();
    const { notifications, unreadCount, dismissNotification, incrementUnreadCount } = useNotifications();

    const handleIncomingNotification = (e) => {
        console.log({e});
        incrementUnreadCount();
        let alert = {
            type: e?.state || 'info',
            theme: "sunset",
            text: e?.text,
            timeout: e?.timeout || false,
            callbacks: {
                onClose: async() => {
                    console.log('we can figure out how to route based on event type here!');
                    // axios.post(route('notifications.dismiss', e.notification_id));
                    await dismissNotification(e.notification_id);
                }
            }
        };
        new Noty(alert).show();
    }
    onBeforeUnmount(()=>{
        //cleanup old listeners
        Echo.leave(channel.value.name);
    })
    watchEffect(() => {
        if (channel.value?.name && channel.value?.name !== `users.${user?.value?.id}`) {
            console.log('leaving channel:', channel.value?.name)
            //leave channel when we log out
            Echo.leave(channel.value.name);
        }

        if (user.value?.id && !channel.value && !window.Echo.connector.channels[`App.Models.User.${user?.value?.id}`]) {
            channel.value = Echo.private(`App.Models.User.${user?.value?.id}`);
            channel.value.notification(handleIncomingNotification);
        }
    });
}
