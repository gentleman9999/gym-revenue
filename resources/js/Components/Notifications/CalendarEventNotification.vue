<template>
    <base-notification v-bind="$props" @click="handleClick">
        <div slot="icon">
            <font-awesome-icon icon="calendar" size="lg" />
        </div>
        {{ text }}
    </base-notification>
</template>

<script>
import { defineComponent, computed } from "vue";
import BaseNotification from "@/Components/Notifications/BaseNotification";
import { Inertia } from "@inertiajs/inertia";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faCalendar } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { parseNotificationResponse } from "@/utils/parseNotificationResponse";
library.add(faCalendar);

export default defineComponent({
    props: {
        notification: {
            type: Object,
            required: true,
        },
    },
    components: {
        BaseNotification,
        FontAwesomeIcon,
    },
    setup(props) {
        const text = computed(
            () => parseNotificationResponse(props.notification).text
        );

        const handleClick = () => {
            Inertia.visit(route("calendar"));
        };
        return {
            handleClick,
            text,
        };
    },
});
</script>
