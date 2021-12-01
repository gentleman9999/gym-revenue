import {useFlash} from "./useFlash";
import { watchEffect, computed, ref} from "vue";

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
