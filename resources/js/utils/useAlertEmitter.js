import {useFlash} from "./useFlash";
import {watchEffect, computed, ref, watch} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";
import {useUser} from "@/utils/useUser";

export const useAlertEmitter = () => {
    const flash = useFlash();

    const alerts = ref(flash.value.alerts);

    //we compare json here to prevent double display of the alert
    watchEffect(()=>{
        const newAlert = flash.value.alerts
        if(JSON.stringify(alerts.value) !== JSON.stringify(newAlert)){
            console.log({oldAlert: JSON.stringify(alerts.value), newAlert: JSON.stringify(newAlert)})
            alerts.value=newAlert;
        }
    })

    watchEffect(() => {
        // console.log(alerts.value, 'alert changed');
        Object.keys(alerts.value).forEach((type)=>{
            let messages = new Set(alerts.value[type]);
            console.log({type, messages});

            messages.forEach(function (text) {
                let alert = {
                    type: "warning",
                    theme: "sunset",
                    text: "Feature Coming Soon!",
                    timeout: 6500,
                };
                alert["type"] = type;
                alert["text"] = text;
                new Noty(alert).show();
            });
        })
    }, {
        flush: 'sync' // needed so updates are immediate.
    });
}

export const useNotificationAlertEmitter = () => {
    const user = useUser();
    // console.log({page});
    const channel = ref();
    // watch([page], () => {
    //     console.log(`page changed now`, {page})
    //
    // });
    // watch([user_id], () => {
    //     console.log(`user_id now .${user_id.value}`)
    //
    // });
    // watchEffect(()=>{
    //     console.log(`Listening to users.${user_id.value}`)
    //     channel.value = Echo.channel(`users.${user_id.value}`);
    // });
    watchEffect(()=>{
        console.log(`user watcheffect`, {user: user.value});
        if(user.value?.id){
            channel.value = Echo.channel(`users.${user?.value?.id}`);
            channel.value.listen('NotificationCreated', () => window.alert('HADEEEEM'));
            console.log({channel: channel.value})
            console.log(`listening to ${channel.value.name}. NotificationCreated`);

        }
    });
    watch([user, channel], ()=>{
        if(channel.value?.name !== `users.${user?.value?.id}`){
            //since this method only fires when
            console.log('*******user changed, cleaning up old subscriptions');
            // console
            console.log(`leaving ${channel.value.name}`)
            Echo.leave(`users.${user?.value?.id}`);
        }
    });
    // Echo.channel(`users.${this.order.id}`)
    //     .listen('OrderShipmentStatusUpdated', (e) => {
    //         console.log(e.order.name);
    //     });
    // alert('hello!');
}
