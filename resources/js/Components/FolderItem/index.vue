<template>
    <div
        class="relative"
        :class="{
            'folder-desktop': props.mode === 'desktop',
            'folder-list': props.mode === 'list',
        }"
        @contextmenu="$event.preventDefault()"
    >
        <folder-icon
            :icon-size="iconSize"
            :extension="folder.extension"
            :showExtension="props.mode === 'desktop'"
            :folderUrl="folder.url"
            @mousedown="handleClick($event)"
        />
        <div class="folder-name">{{ foldername }}</div>
        <folder-detail
            v-if="props.mode === 'list'"
            :folder="folder"
            :handleRename="handleRename"
            :handleTrash="handleTrash"
            :handlePermissions="handlePermissions"
        />

        <folder-context-menu
            ref="subMenu"
            :handleRename="handleRename"
            :handleTrash="handleTrash"
            :handlePermissions="handlePermissions"
        />
    </div>
</template>
<style scoped>
.folder-desktop {
    @apply sm:w-1/12 w-1/3 flex flex-col items-center;
}
.folder-list {
    @apply w-full flex flex-row items-center space-x-2;
    .folder-name {
        @apply w-6/12;
    }
}
</style>
<script setup>
import { computed, ref } from "vue";
import FolderIcon from "./Folder.vue";
import FolderDetail from "./FolderDetail.vue";
import FolderContextMenu from "./FolderContextMenu.vue";
const props = defineProps({
    folder: {
        type: Object,
        required: true,
    },
    mode: {
        type: String,
        default: "desktop",
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

const handleRename = () => {
    props.handleRename(props.folder);
    subMenu.value.blur();
};
const handlePermissions = () => {
    props.handlePermissions(props.folder);
    subMenu.value.blur();
};

const handleTrash = () => {
    props.handleTrash(props.folder);
    subMenu.value.blur();
};

const subMenu = ref(null);

const handleClick = (event) => {
    if (props.mode !== "desktop") {
        return;
    }
    if (event.which == 3) {
        event.preventDefault();
        event.stopPropagation();
    }
    subMenu.value.focus();
};
const iconSize = computed({
    get() {
        return props.mode === "desktop" ? "3x" : "2x";
    },
});

const foldername = computed({
    get() {
        let ret = props.folder.name;
        let nameLimit = props.mode === "desktop" ? 10 : 30;
        if (ret.length > nameLimit) {
            ret =
                ret.split(".")[0].substr(0, nameLimit - 4) +
                "~." +
                props.folder.extension;
        }
        return ret;
    },
});
</script>
