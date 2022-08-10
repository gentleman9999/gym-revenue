<template>
    <div class="flex flex-row flex-wrap mt-4">
        <file-item
            v-for="file in files.data"
            :file="file"
            :key="file?.id"
            :mode="displayMode"
            :handleRename="handleRename"
            :handlePermissions="handlePermissions"
            :handleTrash="handleTrash"
        />
    </div>
</template>
<script setup>
import FileItem from "@/Components/FileItem/index.vue";
const props = defineProps({
    files: {
        type: Object,
    },
    displayMode: {
        type: String,
    },
    handleRename: {
        type: Function,
    },
    handlePermissions: {
        type: Function,
    },
    handleTrash: {
        type: Function,
    },
});
</script>
