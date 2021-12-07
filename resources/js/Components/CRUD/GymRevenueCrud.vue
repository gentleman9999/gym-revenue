<template>
    <jet-bar-container class="relative">
        <div class="flex flex-row items-center mb-4">
            <slot name="filter">
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
            </slot>
            <div class="flex-grow" />
            <slot name="top-actions">
                <button
                    v-for="action in Object.values(topActions)"
                    class="btn"
                    :class="action.class"
                    @click.prevent="() => action.handler({ data, baseUrl })"
                >
                    {{ action.label }}
                </button>
            </slot>
        </div>

        <div class="hidden lg:block">
            <gym-revenue-data-table v-bind="$props" />
        </div>
        <div class="lg:hidden">
            <gym-revenue-data-cards v-bind="$props" />
        </div>
        <slot name="pagination">
            <pagination class="mt-4" :links="resource.links" />
        </slot>
    </jet-bar-container>
</template>

<script>
import { ref, defineComponent, watch } from "vue";
import Pagination from "@/Components/Pagination";
import GymRevenueDataCards from "./GymRevenueDataCards";
import GymRevenueDataTable from "./GymRevenueDataTable";
import SearchFilter from "@/Components/SearchFilter";
import JetBarContainer from "@/Components/JetBarContainer";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import { Inertia } from "@inertiajs/inertia";
import { merge } from "lodash";

export default defineComponent({
    components: {
        GymRevenueDataCards,
        GymRevenueDataTable,
        Pagination,
        SearchFilter,
        JetBarContainer,
    },
    props: {
        fields: {
            type: Array,
        },
        resource: {
            type: Object,
        },
        modelName: {
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
            type: Object,
            default: {},
        },
        topActions: {
            type: Object,
            default: {},
        },
        baseUrl: {
            type: String,
        },
    },
    setup(props) {
        const form = ref({});
        let __baseUrl =
            props.baseUrl || props.modelNamePlural || props.modelName + "s";

        const formHandler = throttle(function () {
            Inertia.get(route(__baseUrl), pickBy(form.value), {
                preserveState: true,
                preserveScroll: true,
            });
        }, 150);
        watch([form], formHandler, { deep: true });
        const defaultTopActions = {
            create: {
                label: "Create",
                handler: () => Inertia.visit(route(`${__baseUrl}.create`)),
                class: ["btn-primary"],
            },
        };
        const __topActions = Object.values(
            merge(defaultTopActions, props.topActions)
        )
            .filter((action) => action)
            .filter((action) =>
                action?.shouldRender ? action.shouldRender(props) : true
            );
        return { form, topActions: __topActions };
    },
});
</script>
