<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">File Manager</h2>
        </template>
        <gym-revenue-crud
            modelName="file"
            :fields="tableHeaders"
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
            }"
            :top-actions="{
                create: {
                    label: 'Upload',
                    handler: () => {
                        Inertia.visit(route('files.upload'));
                    },
                },
            }"
        />
        <sweet-modal
            title="Rename File"
            width="85%"
            overlayTheme="dark"
            modal-theme="dark"
            enable-mobile-fullscreen
            ref="modal"
        >
            <file-form
                :file="selectedFile"
                v-if="selectedFile"
                @success="modal.close"
            />
        </sweet-modal>
    </app-layout>
</template>

<style scoped>
td > div {
    @apply h-16;
}
</style>

<script>
import { defineComponent, watchEffect, ref } from "vue";
import prettyBytes from "pretty-bytes";
import AppLayout from "@/Layouts/AppLayout.vue";
import Button from "@/Components/Button.vue";
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import SearchFilter from "@/Components/SearchFilter";
import FileForm from "./Partials/FileForm";
import FileExtensionIcon from "./Partials/FileExtensionIcon";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import mapValues from "lodash/mapValues";
import SweetModal from "@/Components/SweetModal3/SweetModal";
import FileDataCard from "./Partials/FileDataCard";
import FilenameTableColumn from "./Partials/FilenameTableColumn";
import { Inertia } from "@inertiajs/inertia";

export default defineComponent({
    components: {
        AppLayout,
        Button,
        GymRevenueTable,
        GymRevenueCrud,
        SearchFilter,
        FileExtensionIcon,
        SweetModal,
        FileForm,
        FileDataCard,
    },
    props: ["sessions", "files", "title", "isClientUser", "filters"],
    setup() {
        const form = ref({});
        const selectedFile = ref(null);
        const modal = ref(null);
        watchEffect(() => {
            if (selectedFile.value) {
                modal.value.open();
            }
        });

        const tableHeaders = [
            { label: "filename", component: FilenameTableColumn },
            "size",
            "created_at",
            "updated_at",
        ];
        return {
            prettyBytes,
            modal,
            selectedFile,
            tableHeaders,
            FileDataCard,
            Inertia,
        };
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get(this.route("files"), pickBy(this.form), {
                    preserveState: true,
                    preserveScroll: true,
                });
            }, 150),
        },
    },
    data() {
        return {
            form: {
                // search: this.filters.search,
                // trashed: this.filters.trashed,
            },
        };
    },

    methods: {
        reset() {
            this.form = mapValues(this.form, () => null);
        },
    },
    mounted() {},
});
</script>
