import { computed, watch } from "vue";
import { generateToast } from "@/utils/createToast";
import { usePage } from "@inertiajs/inertia-vue3";

export const useFlashAlertEmitter = () => {
    const alerts = computed(() => usePage().props.value.flash.alerts);

    watch(
        alerts,
        (newAlerts) => {
            console.log("new alerts", newAlerts);
            Object.keys(alerts.value).forEach((type) => {
                let messages = new Set(alerts.value[type]);

                console.log({ type, messages });

                messages.forEach(function (text) {
                    let toastText =
                        typeof text === "string" && text.length > 0
                            ? text
                            : "Feature Coming Soon!";
                    generateToast(toastText, { type });
                });
            });
        },
        {
            flush: "sync", // needed so updates are immediate.
        }
    );
};
