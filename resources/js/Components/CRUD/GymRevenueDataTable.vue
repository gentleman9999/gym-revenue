<template>
    <div class="wrapper">
        <slot name="pre" />
        <slot name="title"></slot>
        <div class="tablehead">
            <table>
                <thead>
                    <slot name="thead">
                        <tr>
                            <th
                                v-for="(header, index) in customizedFields"
                                :key="index"
                                class="mx-8"
                            >
                                <sortable-header
                                    v-if="form && header?.sortable"
                                    :form="form"
                                    :field="header"
                                    v-model="form.sort"
                                    v-model:direction="form.dir"
                                />
                                <template v-else>
                                    {{ header.label }}
                                </template>
                            </th>
                            <th
                                class="mx-8"
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
            </table>
        </div>

        <div class="tablebody">
            <table>
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
                        :on-double-click="onDoubleClick"
                        :on-click="onClick"
                    />

                    <tr v-if="!data?.length">
                        <td
                            :colspan="
                                fields.length +
                                (Object.values(actions).length ? 1 : 0)
                            "
                        >
                            <div class="tabledata">
                                No
                                {{ __modelNamePlural || "Records" }}
                                found.
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.wrapper {
    @apply px-8 pb-8 pt-2  border-2 border-secondary rounded-lg shadow-lg mt-16;

    .tablehead {
        @apply mb-4 mt-2 pr-14 text-secondary;
    }

    .tablebody {
        @apply max-h-64 overflow-hidden overflow-y-scroll pr-10;

        td {
            @apply text-center bg-black text-white my-2 mx-8 whitespace-nowrap overflow-hidden text-ellipsis py-4;

            .tabledata {
                @apply p-2 whitespace-nowrap overflow-hidden text-ellipsis;
            }
        }
    }
}

table {
    @apply w-full table-fixed border-separate;
    border-spacing: 0;
}
</style>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { isObject } from "lodash";
import AutoDataRow from "@/Components/CRUD/AutoDataRow.vue";
import { getFields } from "./helpers/getFields";
import { getData } from "./helpers/getData";
import SortableHeader from "@/Components/CRUD/SortableHeader.vue";
import { getCustomizedFields } from "@/Components/CRUD/helpers/getCustomizedFields";

library.add(faAlignLeft);

export default {
    components: {
        FontAwesomeIcon,
        AutoDataRow,
        SortableHeader,
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
            type: String,
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
            type: Object,
        },
        form: {
            type: Object,
        },
        onDoubleClick: {
            type: Function,
        },
        onClick: {
            type: Function,
        },
    },
    setup(props) {
        const fields = getFields(props);
        const customizedFields = getCustomizedFields(fields, props.modelKey);
        const data = getData(props);

        let __modelNamePlural = props.modelNamePlural || props.modelName + "s";
        return {
            modelNamePlural: __modelNamePlural,
            fields,
            customizedFields,
            isObject,
            data,
        };
    },
};
</script>
