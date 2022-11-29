<template>
    <div class="flex flex-row flex-wrap mt-4" draggable="false">
        <folder-item
            v-for="folder in folders"
            :folder="folder"
            :key="folder?.id"
            :mode="displayMode"
            :handleRename="handleRename"
            :handlePermissions="handlePermissions"
            :moveFileToFolder="moveFileToFolder"
            :handleShare="handleShare"
            @browse="(id) => $emit('browse', id)"
            :handleRestore="handleRestore"
            :handleTrash="handleTrash"
        />
        <file-item
            v-for="file in files.data"
            :file="file"
            :key="file?.id"
            :mode="displayMode"
            :handleRename="handleRename"
            :handlePermissions="handlePermissions"
            :handleTrash="handleTrash"
            :handleRestore="handleRestore"
        />
        <recycle-bin-item :handleTrash="handleTrash" :mode="displayMode" />
    </div>
</template>
<script setup>
import FileItem from "@/Components/FileItem/index.vue";
import FolderItem from "@/Components/FolderItem/index.vue";
import RecycleBinItem from "@/Components/RecycleBinItem/index.vue";
import { Inertia } from "@inertiajs/inertia";
const props = defineProps({
    files: {
        type: Object,
    },
    folders: {
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
    handleShare: {
        type: Function,
    },
    handleRestore: {
        type: Function,
    },
});

const moveFileToFolder = (file_id, folder_id) => {
    Inertia.put(route("files.update.folder", file_id), {
        folder: folder_id,
    });
};
</script>
