<template>
    <div class="flex flex-col m">
        <div class="-my-2 sm:-mx-6 lg:-mx-8 max-w-screen">
            <div
                class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
            >
                <div
                    class="shadow border-b border-base-100 bg-base-300 grid grid-cols-1 lg:grid-cols-2 gap-4"
                >
                    <slot name="pre" />
                    <template v-if="cardComponent">
                        <component :is="cardComponent" v-bind="{[modelName]: row}" :data="row" :fields="fields" :titleField="titleField" v-for="row in resource?.data || []"/>
                    </template>
                    <template v-else>
                        <auto-data-card
                            v-for="row in resource?.data || []"
                            :data="row"
                            :fields="fields"
                            :titleField="titleField"
                        />

                        <div v-if="!resource?.data?.length">
                            <div>
                                No
                                {{ modelNamePlural || `${modelName}s` }}
                                found.
                            </div>
                        </div>
                    </template>

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
import { isObject } from "lodash";
import AutoDataCard from "@/Components/CRUD/DataCard";

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
            default: "record",
        },
        modelNamePlural: {
            type: String,
        },
        titleField:{
            type: String
        },
        cardComponent:{
            type: Object
        }
    },
    setup(props) {
        const ___fields = props.fields.map((field) =>
            isObject(field) ? field.label : field
        );
        return { ___fields };
    },
};
</script>
