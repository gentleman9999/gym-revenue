<template>
    <LayoutHeader title="Locations">
        <h2 class="font-semibold text-xl leading-tight">Locations</h2>
    </LayoutHeader>
    <gym-revenue-crud
        base-route="locations"
        model-name="Location"
        model-key="location"
        :fields="fields"
        :resource="locations"
        :actions="{
            trash: {
                label: 'Close Club',
                handler: ({ data }) => handleClickTrash(data.id),
            },
        }"
        :top-actions="topActions"
        :preview-component="LocationPreview"
    >
        <template #filter>
            <simple-search-filter
                v-model:modelValue="form.search"
                class="w-full max-w-md mr-4"
                @reset="reset"
                @clear-filters="clearFilters"
                @clear-search="clearSearch"
            >
                <template #content>
                    <div class="py-2 text-xs w-60">
                        <div class="block py-2 text-xs text-white">
                            Closed Clubs:
                        </div>
                        <select
                            v-model="form.trashed"
                            class="mt-1 w-full form-select"
                        >
                            <option :value="null" />
                            <option value="with">With Closed</option>
                            <option value="only">Only Closed</option>
                        </select>
                        <div class="block py-2 text-xs text-white">State:</div>
                        <select
                            v-model="form.state"
                            class="mt-1 w-full form-select"
                        >
                            <option :value="null" />
                            <option
                                v-for="state in this.$page.props.eachstate"
                                :value="state.state"
                                :key="state.state"
                            >
                                {{ state.state }}
                            </option>
                        </select>
                    </div>
                </template>
            </simple-search-filter>
        </template>
    </gym-revenue-crud>
    <confirm
        title="Really Close This Club?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
    >
        Are you sure you want to Close this Club?
    </confirm>

    <daisy-modal
        ref="importLocation"
        id="importLocation"
        class="lg:max-w-5xl bg-base-300"
    >
        <file-manager
            @submitted="closeModals"
            :client-id="$page.props.clientId"
        />
    </daisy-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import SimpleSearchFilter from "@/Components/CRUD/SimpleSearchFilter.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";
import Button from "@/Components/Button.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";
import LocationPreview from "@/Pages/Locations/Partials/LocationPreview.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import FileManager from "./Partials/FileManager.vue";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        SimpleSearchFilter,
        DaisyModal,
        FileManager,
    },
    props: [
        "sessions",
        "locations",
        "title",
        "isClientUser",
        "filters",
        "useSearchFilter",
        "SearchFilter",
        "clientId",
    ],
    setup(props) {
        const baseRoute = "locations";
        const { form, reset, clearFilters, clearSearch } = useSearchFilter(
            baseRoute,
            {
                //  preserveState: false,
            }
        );

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            Inertia.delete(route("locations.trash", confirmTrash.value));
            confirmTrash.value = null;
        };

        const importLocation = ref();
        const handleClickImport = () => {
            importLocation.value.open();
        };

        const closeModals = () => {
            importLocation.value.close();
        };

        const topActions = {
            import: {
                label: "Import",
                handler: handleClickImport,
                class: "btn-primary",
            },
        };

        return {
            handleClickTrash,
            confirmTrash,
            handleConfirmTrash,
            Inertia,
            form,
            reset,
            clearFilters,
            clearSearch,
            LocationPreview,
            closeModals,
            handleClickImport,
            importLocation,
            topActions,
        };
    },
    computed: {
        fields() {
            if (this.isClientUser) {
                return ["name", "city", "state", "active"];
            }
            return ["name", "city", "state", "active"];
        },
    },
});
</script>
