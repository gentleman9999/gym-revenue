<template>
    <div
        class="folder"
        :class="{
            'folder-desktop': props.mode === 'desktop',
            'folder-list': props.mode === 'list',
        }"
        :draggable="true"
        @dragstart="handleDrag($event)"
        @contextmenu="$event.preventDefault()"
        @dragover="preventDragDefault($event)"
        @dragenter="preventDragDefault($event)"
        @drop="handleDrop()"
        @dblclick="browseFolder()"
    >
        <folder-icon
            :icon-size="iconSize"
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
            :handleRestore="handleRestore"
            :handlePermissions="handlePermissions"
            :showTrash="showTrash"
        />

        <folder-context-menu
            ref="subMenu"
            :handleRename="handleRename"
            :handleTrash="handleTrash"
            :handleRestore="handleRestore"
            :handlePermissions="handlePermissions"
            :showTrash="showTrash"
        />
    </div>
</template>
<style scoped>
.folder {
    @apply relative cursor-pointer select-all;
}
.folder:-moz-drag-over {
    @apply bg-primary/50;
}
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
import { computed, ref, watch } from "vue";
import FolderIcon from "./Folder.vue";
import FolderDetail from "./FolderDetail.vue";
import FolderContextMenu from "./FolderContextMenu.vue";
import { useEventsBus } from "@/utils";

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
    handleShare: {
        type: Function,
    },
    handleRestore: {
        type: Function,
    },
    moveFileToFolder: {
        type: Function,
    },
});

const handleRename = () => {
    props.handleRename(props.folder, "folder");
    subMenu.value.blur();
};

const handlePermissions = () => {
    props.handlePermissions(props.folder);
    subMenu.value.blur();
};

const handleTrash = () => {
    props.handleTrash(props.folder, "folder");
    subMenu.value.blur();
};

const handleShare = () => {
    console.log("handleShare");
    props.handleShare(props.folder);
    subMenu.value.blur();
};

const handleRestore = () => {
    props.handleRestore(props.folder, "folder");
    subMenu.value.blur();
};

const subMenu = ref(null);

const handleClick = (event) => {
    if (props.mode !== "desktop") {
        browseFolder();
        return;
    }
    if (event.buttons !== 2) return;

    event.preventDefault();
    event.stopPropagation();
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
        let nameLimit = props.mode === "desktop" ? 14 : 30;
        if (ret.length > nameLimit) {
            ret = ret.split(".")[0].substr(0, nameLimit - 1) + "~";
        }
        return ret;
    },
});

const selectedItem = ref(null);
const handleDrop = () => {
    if (selectedItem.value.type === "file") {
        props.moveFileToFolder(selectedItem.value.data.id, props.folder.id);
    }
};

const setFile = (data) => {
    selectedItem.value = data[0];
};

const { emit, bus } = useEventsBus();
watch(() => bus.value.get("selected_item"), setFile);

const handleDrag = (e) => {
    emit("selected_item", { type: "folder", data: props.folder });
};

const preventDragDefault = (e) => {
    if (selectedItem.value.type === "file") {
        e.preventDefault();
    }
};

const vueEmit = defineEmits(["browse-folder"]);
const browseFolder = () => {
    // TODO browse to the folder
    vueEmit("browse-folder", props.folder.id);
};

const showTrash = props.folder.deleted_at ? false : true;
</script>
