<template>
    <div class="flex flex-col m">
        <div class="-my-2 sm:-mx-6 lg:-mx-8 max-w-screen">
            <div
                class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
            >
                <div class="shadow border-b border-base-100 bg-base-300">
                    <slot name="prethead" />
                    <div class="py-4 px-4">
                        <table
                            class="table w-full"
                            :class="{ 'table-zebra': zebra }"
                        >
                            <slot name="thead" :headers="headers">
                                <thead>
                                    <tr>
                                        <th
                                            v-for="(header, index) in __headers"
                                            :key="index"
                                            scope="col"
                                            class="text-white position-unset"
                                            :class="{
                                                'position-unset':
                                                    index === 1 &&
                                                    stickyFirstCol,
                                            }"
                                        >
                                            <template v-if="header !== ''">
                                                {{ header }}
                                            </template>
                                            <template v-else>
                                                <font-awesome-icon
                                                    :icon="[
                                                        'fas',
                                                        'align-left',
                                                    ]"
                                                    size="lg"
                                                />
                                            </template>
                                        </th>
                                    </tr>
                                </thead>
                            </slot>
                            <tbody>
                                <slot>
                                    <tr
                                        v-for="row in resource?.data || []"
                                        class="hover"
                                    >
                                        <td
                                            v-for="(header, index) in __headers"
                                        >
                                            <component
                                                v-if="headers.component"
                                                :is="headers[index].component"
                                            >
                                                {{ row[header] }}
                                            </component>
                                            <template>
                                                {{ row[header] }}
                                            </template>
                                        </td>
                                    </tr>
                                    <tr v-if="resource?.data?.length">
                                        <td :colspan="headers.length">
                                            No
                                            {{
                                                modelNamePlural ||
                                                `${modelName}s`
                                            }}
                                            found.
                                        </td>
                                    </tr>
                                </slot>
                            </tbody>
                        </table>
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
import Pagination from "@/Components/Pagination.vue";
import { isObject } from "lodash";

library.add(faAlignLeft);

export default {
    name: "GymRevenueTable",
    components: {
        FontAwesomeIcon,
        Pagination,
    },
    props: {
        headers: {
            type: Array,
            required: true,
        },
        zebra: {
            type: Boolean,
            default: true,
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
        stickyFirstCol: {
            type: Boolean,
            default: false,
        },
    },
    setup(props) {
        let __headers;
        __headers = props.headers.map((header) =>
            isObject(header) ? header.label : header
        );
        return { __headers };
    },
};
</script>
