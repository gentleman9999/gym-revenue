<template>
    <div class="wrapper">
        <slot name="pre" />
        <table class="table table-compact w-full" :class="{ 'table-zebra': zebra }">
            <thead>
                <slot name="thead">
                    <tr>
                        <th
                            v-for="(header, index) in customizedFields"
                            :key="index"
                            scope="col"
                            :class="{
                                'position-unset':
                                    index === 0 && !stickyFirstCol,
                            }"
                        >
                            {{ header.label }}
                        </th>
                        <th
                            v-if="
                                actions &&
                                (Object.values(actions).length === 0 ||
                                    Object.values(actions).filter(
                                        (action) => action
                                    ).length)
                            "
                        >
                            <button @click="$emit('open-customizer')">
                                <font-awesome-icon
                                    :icon="['fas', 'align-left']"
                                    size="lg"
                                />
                            </button>
                        </th>
                    </tr>
                </slot>
            </thead>
            <tbody>
                <component
                    v-for="row in data"
                    :key="row.id"
                    :is="rowComponent"
                    v-bind="{ [modelKey]: row }"
                    :data="row"
                    :fields="customizedFields"
                    :titleField="titleField"
                    :actions="actions"
                    :model-name="modelName"
                    :model-key="modelKey"
                    :model-name-plural="modelNamePlural"
                    :base-route="baseRoute"
                    :has-preview-component="!!previewComponent"
                />

                <tr v-if="!data?.length">
                    <td
                        :colspan="
                            fields.length +
                            (Object.values(actions).length ? 1 : 0)
                        "
                    >
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
    @apply py-2 align-middle;
    @apply flex flex-col -my-2 sm:-mx-6 lg:-mx-8 sm:px-6 lg:px-8 min-w-full;
    max-width: 100vw;
}
table{
    @apply shadow shadow-lg;
    th {
        @apply text-white bg-base-300;
    }
    tr {
        @apply hover;
    }
}

</style>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { isObject } from "lodash";
import AutoDataRow from "@/Components/CRUD/AutoDataRow";
import { getFields } from "./helpers/getFields";
import { getData } from "./helpers/getData";
import {getCustomizedFields} from "@/Components/CRUD/helpers/getCustomizedFields";

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
        baseRoute: {
            type: String,
            // required: true,
        },
        modelName: {
            type: String,
            default: "Record",
        },
        modelNamePlural: {
            type: String,
        },
        modelKey: {
            type:String,
            required: true,
        },
        titleField: {
            type: String,
        },
        rowComponent: {
            type: Object,
            default: AutoDataRow,
        },
        actions: {
            type: [Object, Boolean],
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
        previewComponent: {
            type: Object
        }
    },
    setup(props) {
        const fields = getFields(props);
        const customizedFields = getCustomizedFields(fields, props.modelKey);
        const data = getData(props);

        let __modelNamePlural = props.modelNamePlural || props.modelName + "s";
        return { modelNamePlural: __modelNamePlural, fields, customizedFields, isObject, data };
    },
};
</script>
