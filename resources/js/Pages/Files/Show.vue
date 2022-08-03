<template>
    <LayoutHeader title="File Management">
        <h2 class="font-semibold text-xl leading-tight">File Manager</h2>
    </LayoutHeader>
    <div class="flex flex-row space-x-3">
        <Button primary size="sm" @click="displayMode = 'desktop'"
            >desktop</Button
        >
        <Button primary size="sm" @click="displayMode = 'list'">list</Button>
    </div>
    <div class="flex flex-row flex-wrap">
        <file-item
            v-for="file in files.data"
            :file="file"
            :key="file?.id"
            :mode="displayMode"
        />
    </div>
    <gym-revenue-crud
        base-route="files"
        model-name="File"
        model-key="file"
        :fields="fields"
        :resource="files"
        titleField="filename"
        :card-component="FileDataCard"
        :actions="{
            edit: false,
            rename: {
                label: 'Rename',
                handler: ({ data }) => {
                    selectedFile = data;
                },
            },
            permissions: {
                label: 'Permissions',
                handler: ({ data }) => {
                    selectedFilePermissions = data;
                },
            },
        }"
        :top-actions="{
            create: {
                label: 'Upload',
                handler: () => {
                    Inertia.visitInModal(route('files.upload'));
                },
            },
        }"
    />
    <daisy-modal
        id="filenameModal"
        ref="filenameModal"
        @close="selectedFile = null"
    >
        <file-form
            :file="selectedFile"
            v-if="selectedFile"
            @success="filenameModal.close"
        />
    </daisy-modal>

    <daisy-modal
        ref="permissionsModal"
        id="permissionsModal"
        @close="selectedFilePermissions = null"
    >
        <h1 class="font-bold mb-4">Modify File Permissions</h1>
        <Permissions-Form
            :file="selectedFilePermissions"
            v-if="selectedFilePermissions"
            @success="permissionsModal.close"
        />
    </daisy-modal>
</template>

<style scoped>
td > div {
    @apply h-16;
}
</style>

<script setup>
import { defineComponent, watchEffect, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import FileForm from "./Partials/FileForm.vue";
import PermissionsForm from "./Partials/PermissionsForm.vue";
import FileDataCard from "./Partials/FileDataCard.vue";
import FilenameField from "./Partials/FilenameField.vue";
import { Inertia } from "@inertiajs/inertia";
import DaisyModal from "@/Components/DaisyModal.vue";
import FileItem from "@/Components/FileItem/index.vue";
import Button from "@/Components/Button.vue";

const props = defineProps({
    sessions: {
        type: Array,
    },
    files: {
        type: Array,
    },
    title: {
        type: String,
    },
    isClientUser: {
        type: Boolean,
    },
    filters: {
        type: Array,
    },
});
const selectedFile = ref(null);
const selectedFilePermissions = ref(null);

const filenameModal = ref(null);
const permissionsModal = ref(null);

watchEffect(() => {
    if (selectedFile.value) {
        filenameModal.value.open();
    }
    if (selectedFilePermissions.value) {
        permissionsModal.value.open();
    }
});

const displayMode = ref("desktop");
const fields = [
    { name: "filename", component: FilenameField },
    "size",
    "created_at",
    "updated_at",
];

console.log("FILE UI");
console.log(props.files);
</script>
