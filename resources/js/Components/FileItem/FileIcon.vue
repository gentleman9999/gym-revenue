<template>
    <div class="file-icon-wrapper">
        <font-awesome-icon
            icon="file"
            :size="iconSize"
            class="text-secondary"
        />
        <span v-if="showExtension" class="file-ext">{{ extension }}</span>
    </div>
</template>
<style scoped>
.file-icon-wrapper {
    @apply relative w-min;
}
.file-ext {
    @apply absolute inset-0 text-base-content flex items-center justify-center text-xs font-bold uppercase;
}
</style>
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faFile } from "@fortawesome/pro-solid-svg-icons";

library.add(faFile);

const props = defineProps({
    iconSize: {
        type: String,
    },
    extension: {
        type: String,
    },
    showExtension: {
        type: Boolean,
        default: true,
    },
});
</script>
