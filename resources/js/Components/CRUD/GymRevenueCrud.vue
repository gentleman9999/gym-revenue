<template>
    <jet-bar-container class="relative">
        <div
            class="grid grid-cols-3 items-center flex-wrap mb-4 gap-x-4 gap-y-6"
        >
            <slot name="top-actions">
                <div class="flex flex-row col-span-3 lg:col-span-2 gap-2">
                    <template v-for="action in Object.values(topActions)">
                        <button
                            v-if="
                                action?.shouldRender
                                    ? action?.shouldRender()
                                    : true
                            "
                            class="btn btn-sm text-xs"
                            :class="action.class"
                            @click.prevent="
                                () => action.handler({ data, baseRoute })
                            "
                        >
                            {{ action.label }}
                        </button>
                    </template>
                </div>
            </slot>
            <slot name="filter">
                <simple-search-filter
                    v-model:modelValue="form.search"
                    class="w-full max-w-md mr-4 col-span-3 lg:col-span-1"
                    @reset="reset"
                    @clear-filters="clearFilters"
                    @clear-search="clearSearch"
                >
                    <div
                        class="block py-2 text-xs text-base-content text-opacity-80"
                    >
                        Trashed:
                    </div>
                    <select
                        v-model="form.trashed"
                        class="mt-1 w-full form-select"
                    >
                        <option :value="null" />
                        <option value="with">With Trashed</option>
                        <option value="only">Only Trashed</option>
                    </select>
                </simple-search-filter>
            </slot>
        </div>

        <template v-if="tableComponent && cardsComponent">
            <div class="hidden lg:block">
                <component :is="tableComponent" v-bind="$props" :form="form"/>
            </div>
            <div class="lg:hidden">
                <component :is="cardsComponent" v-bind="$props" :form="form"/>
            </div>
        </template>
        <template v-else>
            <component :is="tableComponent || cardsComponent" v-bind="$props" :form="form"/>
        </template>

        <slot name="pagination">
            <pagination class="mt-4" :links="resource.links" />
        </slot>
    </jet-bar-container>
    <preview-modal
        v-if="previewComponent"
        :preview-component="previewComponent"
        :model-name="modelName"
        :model-key="modelKey"
    />
</template>

<script>
import { defineComponent } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { merge } from "lodash";
import Pagination from "@/Components/Pagination";
import GymRevenueDataCards from "./GymRevenueDataCards";
import GymRevenueDataTable from "./GymRevenueDataTable";
import SimpleSearchFilter from "@/Components/CRUD/SimpleSearchFilter";
import JetBarContainer from "@/Components/JetBarContainer";
import PreviewModal from "@/Components/CRUD/PreviewModal";
import LeadForm from "@/Pages/Leads/Partials/LeadForm";
import { useSearchFilter } from "./helpers/useSearchFilter";
import {flattenObj} from "@/Components/CRUD/helpers/getData";

export default defineComponent({
    components: {
        GymRevenueDataCards,
        GymRevenueDataTable,
        Pagination,
        SimpleSearchFilter,
        JetBarContainer,
        LeadForm,
        PreviewModal,
    },
    props: {
        fields: {
            type: Array,
        },
        resource: {
            type: Object,
        },
        baseRoute: {
            type: String,
            // required: true,
        },
        modelName: {
            type: String,
            default: "record",
        },
        modelKey: {
            type: String,
            required: true,
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        cardComponent: {
            type: Object,
        },
        rowComponent: {
            type: Object,
        },
        actions: {
            type: [Object, Boolean],
            default: {},
        },
        topActions: {
            type: [Object, Boolean],
            default: {},
        },
        tableComponent: {
            type: Object,
            default: GymRevenueDataTable,
        },
        cardsComponent: {
            type: Object,
            default: GymRevenueDataCards,
        },
        previewComponent: {
            type: Object,
        },
    },

    setup(props) {
        const { form, reset, clearFilters, clearSearch } = useSearchFilter(
            props.baseRoute
        );

        const exportToCsv = (data) => {
            //strip out fields we dont need bc they aren't shown on current page
            const exportable_fields = props.fields.filter(
                (field) => field?.export !== false
            );
            //get flat fields
            const fields = exportable_fields.map(
                (field) => field?.name || field
            );
            const field_labels = exportable_fields.map(
                (field) => field?.label || field
            );

            const filtered = data.map((row) => {
                //put in same order as headers
                //convert to Map based on fields order. Map guarantees iteration order based on insertion order
                const ordered = new Map();
                fields.forEach((field) => ordered.set(field, row[field]));
                return ordered;
            });
            //now apply export function, fall back to transforms on fields that declare them
            const transformed = filtered.map((map) => {
                const row = [];
                map.forEach((value, key) => {
                    const field = exportable_fields.find(
                        (field) => field?.name === key
                    );
                    row.push(
                        field?.export
                            ? field.export(value)
                            : field?.transform
                            ? field.transform(value)
                            : value
                    );
                });
                return row;
            });
            const csvContent = transformed
                .map(
                    (row) =>
                        Object.values(row)
                            .map(String) // convert every value to String
                            .map((v) =>
                                v === "null" || v === "undefined" ? "" : v
                            ) // make null and undefineds empty string
                            .map((v) => v.replaceAll('"', '""')) // escape double colons
                            .map((v) => `"${v}"`) // quote it
                            .join(",") // comma-separated);
                )
                .join("\r\n"); // rows starting on new lines)
            const csv = `data:text/csv;charset=utf-8,${field_labels.join(
                ","
            )}\r\n${csvContent}`;
            //create uri for download
            const encodedUri = encodeURI(csv);
            //create a hidden link for downloading
            const link = document.createElement("a");
            link.style.display = "none";
            link.setAttribute("href", encodedUri);
            const date = new Date()
                .toISOString()
                .replace("T", " ")
                .substring(0, 19);
            //set filename
            link.setAttribute("download", `${props.modelKey}s-${date}.csv`);
            document.body.appendChild(link); // Required for FF
            //start the download
            link.click();
        };

        const defaultTopActions = {
            create: {
                label: `Create ${props.modelName}`,
                handler: () => {
                    Inertia.visit(route(`${props.baseRoute}.create`));
                },
                class: ["btn-primary"],
            },
            export: {
                label: "Export",
                handler: () => exportToCsv(props.resource.data),
                shouldRender: () => !!props.resource.total,
            },
            exportAll: {
                label: "Export All",
                handler: async () => {
                    const response = await axios.get(route(`${props.baseRoute}.export`));
                    //TODO:some error handling stuff
                    const data = merge(response.data, response.data?.map(flattenObj))
                    exportToCsv(data)
                },
                shouldRender: () => !!props.resource.total,
            },
        };
        let topActions = [];
        if (props.topActions) {
            topActions = Object.values(
                merge({ ...defaultTopActions }, props.topActions)
            )
                .filter((action) => action)
                .filter((action) =>
                    action?.shouldRender ? action.shouldRender(props) : true
                );
        }
        return { form, topActions, reset, clearFilters, clearSearch };
    },
});
</script>
