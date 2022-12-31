<template>
    <div class="rounded-full border" :style="borderStyle">
        <font-awesome-icon
            icon="user-circle"
            size="6x"
            class="self-center opacity-10"
        />
    </div>
</template>
<script setup>
import { computed } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faUserCircle } from "@fortawesome/pro-solid-svg-icons";
import { library } from "@fortawesome/fontawesome-svg-core";
library.add(faUserCircle);

const props = defineProps({
    opportunity: {
        type: String,
    },
});
const borderStyle = computed({
    get() {
        let color = "transparent";
        switch (props.opportunity) {
            case 3:
                color = "green";
                break;

            case 2:
                color = "yellow";
                break;

            case 1:
                color = "red";
                break;
            default:
                color = "gray";
        }

        return {
            "border-color": color,
            "border-width": "5px",
        };
    },
});
</script>
