<template>
    <div
        class="folder"
        :class="{
            'folder-desktop': props.mode === 'desktop',
            'folder-list': props.mode === 'list',
        }"
        @contextmenu="$event.preventDefault()"
        @dragover="$event.preventDefault()"
        @dragenter="$event.preventDefault()"
        @drop="handleDrop()"
        @dblclick="browseRecycleBin()"
    >
        <recycle-bin :icon-size="iconSize" @mousedown="handleClick($event)" />
        <div class="folder-name">Recycle Bin</div>
    </div>
</template>
<style scoped>
.folder {
    @apply relative cursor-pointer;
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
import RecycleBin from "./RecycleBin.vue";
import { Inertia } from "@inertiajs/inertia";
import { useEventsBus } from "@/utils";

const props = defineProps({
    mode: {
        type: String,
        default: "desktop",
    },
    handleTrash: {
        type: Function,
    },
});

const handleClick = (event) => {
    if (props.mode !== "desktop") {
        browseRecycleBin();
        return;
    }
};

const iconSize = computed({
    get() {
        return props.mode === "desktop" ? "3x" : "2x";
    },
});

const selectedItem = ref(null);
const setItem = (data) => {
    selectedItem.value = data[0];
};

const { bus } = useEventsBus();
watch(() => bus.value.get("selected_item"), setItem);

const browseRecycleBin = () => {
    // Inertia.get(route("folders.viewFiles", props.folder.id));
};
const handleDrop = () => {
    props.handleTrash(selectedItem.value.data, selectedItem.value.type);
};
</script>
