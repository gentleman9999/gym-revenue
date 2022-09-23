<template>
    <Button class="twilio-ui-button hidden" secondary @click="$emit('toggle')">
        <font-awesome-icon :icon="['fa', 'user-md-chat']" size="lg" />
    </Button>
</template>
<style scoped>
.twilio-ui-button {
    @apply h-12 w-12 rounded-full z-10;
}
</style>
<script setup>
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faUserMdChat } from "@fortawesome/pro-solid-svg-icons";
import Button from "@/Components/Button.vue";
library.add(faUserMdChat);
</script>
