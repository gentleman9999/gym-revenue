<template>
    <div class="twilio-header">
        <Button
            v-if="mode === 'chat'"
            class="twilio-header-back"
            @click="$emit('toggleMode')"
        >
            <font-awesome-icon :icon="['fa', 'arrow-left']" size="md" />
        </Button>
        {{ header }}
    </div>
</template>
<style scoped>
.twilio-header {
    @apply flex flex-row bg-secondary text-base-content p-2 h-16 items-center;
}
.twilio-header-back {
    @apply p-1 h-6 bg-transparent border-none;
}
</style>
<script setup>
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faArrowLeft } from "@fortawesome/pro-solid-svg-icons";
import Button from "@/Components/Button.vue";
library.add(faArrowLeft);

const props = defineProps({
    mode: {
        type: String,
        default: "user",
    },
    header: String,
});
</script>
