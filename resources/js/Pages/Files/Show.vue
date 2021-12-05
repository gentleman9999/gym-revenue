<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">File Manager</h2>
        </template>
        <jet-bar-container class="relative">
            <div class="flex flex-row items-center mb-4">
                <search-filter
                    v-model:modelValue="form.search"
                    class="w-full max-w-md mr-4"
                    @reset="reset"
                >
                    <div class="block py-2 text-xs text-gray-400">Trashed:</div>
                    <select
                        v-model="form.trashed"
                        class="mt-1 w-full form-select"
                    >
                        <option :value="null" />
                        <option value="with">With Trashed</option>
                        <option value="only">Only Trashed</option>
                    </select>
                </search-filter>
                <div class="flex-grow" />
                <inertia-link
                    class="btn btn-primary justify-self-end"
                    :href="route('files.upload')"
                >
                    <span>Upload</span>
                </inertia-link>
            </div>
            <gym-revenue-data-cards
                modelName="file"
                :fields="tableHeaders"
                :resource="files"
                titleField="filename"
                :card-component="FileDataCard"
                :actions="{edit: {label: 'Rename'}}"
            />
            <!--            <gym-revenue-table :headers="tableHeaders" :resource="files">-->
            <!--                <tr-->
            <!--                    class="hover"-->
            <!--                    v-for="file in files?.data"-->
            <!--                    :key="file.id"-->
            <!--                    @dblclick="selectedFile = file.deletedAt ? null : file"-->
            <!--                >-->
            <!--                    &lt;!&ndash;                    <td v-if="!isClientUser">{{ file.client.name }}</td>&ndash;&gt;-->
            <!--                    <td>-->
            <!--                        <div-->
            <!--                            class="flex flex-row nowrap items-center gap-4 truncate"-->
            <!--                        >-->
            <!--                            <file-extension-icon-->
            <!--                                :extension="file.extension"-->
            <!--                                v-if="-->
            <!--                                    ![-->
            <!--                                        'jpg',-->
            <!--                                        'jpeg',-->
            <!--                                        'png',-->
            <!--                                        'svg',-->
            <!--                                        'webp',-->
            <!--                                    ].includes(file.extension)-->
            <!--                                "-->
            <!--                                class="h-16 w-16"-->
            <!--                            />-->
            <!--                            <img-->
            <!--                                :src="file.url"-->
            <!--                                class="h-16 w-16 object-cover rounded-sm"-->
            <!--                                v-else-->
            <!--                            />-->
            <!--                            <a-->
            <!--                                :href="file.url"-->
            <!--                                :download="file.filename"-->
            <!--                                class="link link-hover"-->
            <!--                                >{{ file.filename }}</a-->
            <!--                            >-->
            <!--                        </div>-->
            <!--                    </td>-->
            <!--                    <td>{{ new Date(file.created_at).toLocaleString() }}</td>-->
            <!--                    <td>{{ prettyBytes(file.size) }}</td>-->
            <!--                    <td class="flex flex-row justify-center space-x-2">-->
            <!--                        &lt;!&ndash;                        <inertia-link :href="route('locations.trash', location.id)" class="text-gray-400 hover:text-gray-500">&ndash;&gt;-->
            <!--                        &lt;!&ndash;@todo: We need to add a confirmation before deleting to avoid accidental deletes&ndash;&gt;-->
            <!--                        <div-->
            <!--                            class="flex flex-row nowrap justify-center items-center gap-4"-->
            <!--                        >-->
            <!--                            <button-->
            <!--                                @click="selectedFile = file"-->
            <!--                                class="text-gray-400 hover:text-gray-300"-->
            <!--                                v-if="!file?.deleted_at"-->
            <!--                            >-->
            <!--                                <jet-bar-icon type="pencil" fill />-->
            <!--                            </button>-->
            <!--                            <button-->
            <!--                                @click="-->
            <!--                                    file?.deleted_at-->
            <!--                                        ? $inertia.post(-->
            <!--                                              route('files.restore', file.id)-->
            <!--                                          )-->
            <!--                                        : $inertia.delete(-->
            <!--                                              route('files.trash', file.id)-->
            <!--                                          )-->
            <!--                                "-->
            <!--                                class="text-gray-400 hover:text-gray-300"-->
            <!--                            >-->
            <!--                                <jet-bar-icon-->
            <!--                                    :type="-->
            <!--                                        file?.deleted_at ? 'untrash' : 'trash'-->
            <!--                                    "-->
            <!--                                    fill-->
            <!--                                />-->
            <!--                            </button>-->
            <!--                        </div>-->
            <!--                        &lt;!&ndash;                        </inertia-link>&ndash;&gt;-->
            <!--                    </td>-->
            <!--                </tr>-->

            <!--                <tr v-if="!files?.data?.length">-->
            <!--                    <td :colspan="tableHeaders.lenth">No Files found.</td>-->
            <!--                </tr>-->
            <!--            </gym-revenue-table>-->
        </jet-bar-container>
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
        <!--        <rename-file-modal :file="selectedFile" v-if="selectedFile" ref="modal"/>-->
    </app-layout>
</template>

<style scoped>
td > div {
    @apply h-16;
}
</style>

<script>
import { defineComponent, watch, watchEffect, ref } from "vue";
import prettyBytes from "pretty-bytes";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import Button from "@/Components/Button.vue";
import JetBarContainer from "@/Components/JetBarContainer";
import JetBarAlert from "@/Components/JetBarAlert";
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable";
import GymRevenueDataCards from "@/Components/CRUD/GymRevenueDataCards";
import JetBarBadge from "@/Components/JetBarBadge";
import JetBarIcon from "@/Components/JetBarIcon";
import SearchFilter from "@/Components/SearchFilter";
import FileForm from "./Partials/FileForm";
import FileExtensionIcon from "./Partials/FileExtensionIcon";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import mapValues from "lodash/mapValues";
import SweetModal from "@/Components/SweetModal3/SweetModal";
import FileDataCard from "./Partials/FileDataCard";

export default defineComponent({
    components: {
        AppLayout,
        JetSectionBorder,
        Button,
        JetBarContainer,
        JetBarAlert,
        GymRevenueTable,
        GymRevenueDataCards,
        JetBarBadge,
        JetBarIcon,
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
                console.log({ modal: modal.value });
                modal.value.open();
            }
        });

        const tableHeaders = ["filename", "created_at", "size", ""];
        return { prettyBytes, modal, selectedFile, tableHeaders, FileDataCard };
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
