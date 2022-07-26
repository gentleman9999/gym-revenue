<template>
    <LayoutHeader title="Folder Management">
        <h2 class="font-semibold text-xl leading-tight">Folder Manager</h2>
    </LayoutHeader>
    <gym-revenue-crud
        base-route="folders"
        model-name="Folder"
        model-key="folder"
        :fields="fields"
        :resource="folders"
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
import { Inertia } from "@inertiajs/inertia";
import DaisyModal from "@/Components/DaisyModal.vue";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        DaisyModal,
    },
    props: ["folders", "title", "isClientUser", "filters"],
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

        const fields = ["name", "created_at", "updated_at"];
        return {
            fields,
            Inertia,
        };
    },
});
</script>
