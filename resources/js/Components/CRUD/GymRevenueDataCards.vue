<template>
    <div class="flex flex-col m">
        <div class="-my-2 sm:-mx-6 lg:-mx-8 max-w-screen">
            <div
                class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
            >
                <div
                    class="shadow border-b border-base-100 bg-base-300 grid grid-cols-1 md:grid-cols-2 gap-4"
                >
                    <slot name="pre" />
                    <component
                        v-for="row in resource?.data || []"
                        :is="cardComponent"
                        v-bind="{ [modelName]: row }"
                        :data="row"
                        :fields="fields"
                        :titleField="titleField"
                        :actions="actions"
                        :model-name="modelName"
                        :model-name-plural="modelNamePlural"
                    />
                    <div v-if="!resource?.data?.length">
                        <div>
                            No
                            {{ modelNamePlural || "Records" }}
                            found.
                        </div>
                    </div>

                    <slot name="pagination">
                        <pagination class="mt-4" :links="resource.links" />
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import Pagination from "@/Components/Pagination";
import AutoDataCard from "@/Components/CRUD/AutoDataCard";

library.add(faAlignLeft);

export default {
    components: {
        FontAwesomeIcon,
        Pagination,
        AutoDataCard,
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
            default: AutoDataCard,
        },
        actions: {
            type: Object,
            default: {},
        },
    },
    setup(props) {
        let __modelNamePlural = props.modelNamePlural || props.modelName + "s";

        return { modelNamePlural: __modelNamePlural };
    },
};
</script>
