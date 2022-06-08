import { useFlash } from "./useFlash";
import { ref, watch, watchEffect } from "vue";
import { useUser } from "@/utils/useUser";
import { generateToast } from "@/utils/createToast";

export const useFlashAlertEmitter = () => {
    const flash = useFlash();

    const alerts = ref(flash.value.alerts);

    //we compare json here to prevent double display of the alert
    watchEffect(() => {
        const newAlert = flash.value.alerts;
        if (JSON.stringify(alerts.value) !== JSON.stringify(newAlert)) {
            console.log({
                oldAlert: JSON.stringify(alerts.value),
                newAlert: JSON.stringify(newAlert),
            });
            alerts.value = newAlert;
        }
    });

    watchEffect(
        () => {
            // console.log(alerts.value, 'alert changed');
            Object.keys(alerts.value).forEach((type) => {
                let messages = new Set(alerts.value[type]);
                console.log({ type, messages });

                messages.forEach(function (text) {
                    let toastText =
                        typeof text === "string" && text.length > 0
                            ? text
                            : "Feature Coming Soon!";
                    generateToast(type, toastText);
                });
            });
        },
        {
            flush: "sync", // needed so updates are immediate.
        }
    );
};
