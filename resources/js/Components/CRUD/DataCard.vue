<template>
    <div class="rounded-lg bg-base-50 flex flex-col shadow shadow-xl">
        <div
            class="flex flex-row nowrap rounded-t-xl p-4 border-b border-gray-500"
        >
            <slot name="title" />
            <div class="flex-grow" />
            <slot name="actions" />
        </div>
        <div class="grid grid-cols-3 md:grid-cols-6 p-4">
            <slot />
        </div>
    </div>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import Pagination from "@/Components/Pagination.vue";
import { isObject } from "lodash";
import {
    transforms,
    defaults as defaultTransforms,
} from "./helpers/transforms";
import VueJsonPretty from "vue-json-pretty";
import "vue-json-pretty/lib/styles.css";

library.add(faAlignLeft);

export default {
    components: {
        FontAwesomeIcon,
        Pagination,
        VueJsonPretty,
    },
    props: {
        modelName: {
            type: String,
            default: "record",
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
    },
};
</script>
