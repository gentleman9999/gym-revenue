<template>
    <div
        class="file-icon-wrapper"
        v-if="!['jpg', 'jpeg', 'png', 'svg', 'webp'].includes(extension)"
    >
        <font-awesome-icon
            icon="file"
            :size="iconSize"
            class="text-secondary"
        />
        <span v-if="showExtension" class="file-ext">{{ extension }}</span>
    </div>
    <img
        :src="fileUrl"
        class="object-cover rounded-sm"
        :class="{
            'w-6 h-8': iconSize == '2x',
            'w-9 h-12': iconSize == '3x',
        }"
        v-else
    />
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
    fileUrl: {
        type: String,
    },
    showExtension: {
        type: Boolean,
        default: true,
    },
});
</script>
