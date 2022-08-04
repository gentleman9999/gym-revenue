<template>
    <div
        class="relative"
        :class="{
            'file-desktop': props.mode === 'desktop',
            'file-list': props.mode === 'list',
        }"
        @contextmenu="$event.preventDefault()"
    >
        <file-icon
            :icon-size="iconSize"
            :extension="file.extension"
            :showExtension="props.mode === 'desktop'"
            :fileUrl="file.url"
            @mousedown="handleClick($event)"
        />
        <div class="file-name">{{ filename }}</div>
        <file-detail
            v-if="props.mode === 'list'"
            :file="file"
            :handleRename="handleRename"
            :handleTrash="handleTrash"
            :handlePermissions="handlePermissions"
        />

        <file-context-menu
            ref="subMenu"
            :handleRename="handleRename"
            :handleTrash="handleTrash"
            :handlePermissions="handlePermissions"
        />
    </div>
</template>
<style scoped>
.file-desktop {
    @apply sm:w-1/12 w-1/3 flex flex-col items-center;
}
.file-list {
    @apply w-full flex flex-row items-center space-x-2;
    .file-name {
        @apply w-6/12;
    }
}
</style>
<script setup>
import { computed, ref } from "vue";
import FileIcon from "./FileIcon.vue";
import FileDetail from "./FileDetail.vue";
import FileContextMenu from "./FileContextMenu.vue";
const props = defineProps({
    file: {
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
    props.handleRename(props.file);
    subMenu.value.blur();
};
const handlePermissions = () => {
    props.handlePermissions(props.file);
    subMenu.value.blur();
};

const handleTrash = () => {
    props.handleTrash(props.file);
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

const filename = computed({
    get() {
        let ret = props.file.filename;
        let nameLimit = props.mode === "desktop" ? 10 : 30;
        if (ret.length > nameLimit) {
            ret =
                ret.split(".")[0].substr(0, nameLimit - 4) +
                "~." +
                props.file.extension;
        }
        return ret;
    },
});
</script>
