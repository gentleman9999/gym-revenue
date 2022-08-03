<template>
    <div
        :class="{
            'file-desktop': props.mode === 'desktop',
            'file-list': props.mode === 'list',
        }"
    >
        <file-icon
            :icon-size="iconSize"
            :extension="file.extension"
            :showExtension="props.mode === 'desktop'"
        />
        <div class="file-name">{{ filename }}</div>
        <file-detail v-if="props.mode === 'list'" :file="file" />
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
import { computed } from "vue";
import FileIcon from "./FileIcon.vue";
import FileDetail from "./FileDetail.vue";
const props = defineProps({
    file: {
        type: Object,
        required: true,
    },
    mode: {
        type: String,
        default: "desktop",
    },
});

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
