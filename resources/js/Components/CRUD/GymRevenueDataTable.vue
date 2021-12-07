<template>
    <div class="wrapper">
        <slot name="pre" />
        <table class="table w-full" :class="{ 'table-zebra': zebra }">
            <thead>
                <slot name="thead">
                    <tr>
                        <th
                            v-for="(header, index) in fieldKeys"
                            :key="index"
                            scope="col"
                            :class="{
                                'position-unset':
                                    index === 0 && !stickyFirstCol,
                            }"
                        >
                            {{ isObject(header) ? header.name : header }}
                        </th>
                        <th>
                            <font-awesome-icon
                                :icon="['fas', 'align-left']"
                                size="lg"
                            />
                        </th>
                    </tr>
                </slot>
            </thead>
            <tbody>
                <component
                    v-for="row in resource?.data || []"
                    :is="rowComponent"
                    v-bind="{ [modelName]: row }"
                    :data="row"
                    :fields="fields"
                    :titleField="titleField"
                    :actions="actions"
                    :model-name="modelName"
                    :model-name-plural="modelNamePlural"
                />

                <tr v-if="!resource?.data?.length">
                    <td colspan="">
                        No
                        {{ __modelNamePlural || "Records" }}
                        found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
.wrapper {
    @apply py-2 align-middle shadow border-b border-base-100 bg-base-300;
    @apply flex flex-col -my-2 sm:-mx-6 lg:-mx-8 sm:px-6 lg:px-8 min-w-full ;
    max-width: 100vw;
}
th {
    @apply text-white;
}
tr {
    @apply hover;
}
</style>

<script>
import { computed } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { isObject } from "lodash";
import AutoDataRow from "@/Components/CRUD/AutoDataRow";
import { defaults as defaultTransforms } from "./transforms";

library.add(faAlignLeft);

export default {
    components: {
        FontAwesomeIcon,
        AutoDataRow,
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
        rowComponent: {
            type: Object,
            default: AutoDataRow,
        },
        actions: {
            type: Object,
            default: {},
        },
        stickyFirstCol: {
            type: Boolean,
            default: false,
        },
        zebra: {
            type: Boolean,
            default: true,
        },
    },
    setup(props) {
        const fieldKeys = computed(() => {
            let __fields = [];
            if (props.fields) {
                __fields = props.fields.map((header) =>
                    isObject(header) ? header.label : header
                );
            } else if (props.resource.data.length) {
                __fields = Object.keys(props.resource.data[0]);
            }

            __fields = __fields.map((field) =>
                field in defaultTransforms
                    ? { name: field, transform: defaultTransforms[field] }
                    : field
            );

            return __fields;
        });
        let __modelNamePlural = props.modelNamePlural || props.modelName + "s";

        return { modelNamePlural: __modelNamePlural, fieldKeys, isObject };
    },
};
</script>
