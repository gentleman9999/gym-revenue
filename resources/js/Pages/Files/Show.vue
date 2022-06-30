<template>
    <LayoutHeader title="File Management">
        <h2 class="font-semibold text-xl leading-tight">File Manager</h2>
    </LayoutHeader>
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

<script>
import { defineComponent, watchEffect, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import FileForm from "./Partials/FileForm.vue";
import PermissionsForm from "./Partials/PermissionsForm.vue";
import FileDataCard from "./Partials/FileDataCard.vue";
import FilenameField from "./Partials/FilenameField.vue";
import { Inertia } from "@inertiajs/inertia";
import DaisyModal from "@/Components/DaisyModal.vue";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        FileForm,
        DaisyModal,
        PermissionsForm,
    },
    props: ["sessions", "files", "title", "isClientUser", "filters"],
    setup() {
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

        const fields = [
            { name: "filename", component: FilenameField },
            "size",
            "created_at",
            "updated_at",
        ];
        return {
            filenameModal,
            selectedFile,
            permissionsModal,
            selectedFilePermissions,
            fields,
            FileDataCard,
            Inertia,
        };
    },
});
</script>
