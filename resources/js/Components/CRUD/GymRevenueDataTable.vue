<template>
    <div class="wrapper">
        <slot name="pre" />
        <table class="table w-full" :class="{ 'table-zebra': zebra }">
            <thead>
                <slot name="thead">
                    <tr>
                        <th
                            v-for="(header, index) in fields"
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
                    v-for="row in data"
                    :key="row.id"
                    :is="rowComponent"
                    v-bind="{ [modelName]: row }"
                    :data="row"
                    :fields="fields"
                    :titleField="titleField"
                    :actions="actions"
                    :model-name="modelName"
                    :model-name-plural="modelNamePlural"
                    :base-route="baseRoute"
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
    },
    setup(props) {
        const fields = getFields(props);
        const data = getData(props);

        let __modelNamePlural = props.modelNamePlural || props.modelName + "s";

        return { modelNamePlural: __modelNamePlural, fields, isObject, data };
    },
};
</script>
