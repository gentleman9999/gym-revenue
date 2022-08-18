<template>
    <div class="folder-icon-wrapper">
        <font-awesome-icon
            icon="folder"
            :size="iconSize"
            class="text-secondary"
        />
    </div>
</template>
<style scoped>
.folder-icon-wrapper {
    @apply relative w-min;
}
</style>
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faFolder } from "@fortawesome/pro-solid-svg-icons";

library.add(faFolder);

const props = defineProps({
    iconSize: {
        type: String,
    },
    extension: {
        type: String,
    },
    folderUrl: {
        type: String,
    },
    showExtension: {
        type: Boolean,
        default: true,
    },
});
</script>
