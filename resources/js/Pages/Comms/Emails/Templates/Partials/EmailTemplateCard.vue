<template>
    <div class="flex flex-col gap-4">
        <button
            class="flex flex-col hover:border-secondary hover:border-2 rounded flex-grow overflow-hidden"
            @click="actions.edit?.handler({ ...props, data: data })"
        >
            <img
                :src="data?.thumbnail?.url"
                v-if="data?.thumbnail?.url"
                alt="thumbnail"
            />
            <div
                v-else
                class="flex flex-col items-center justify-center p-4 bg-base-content bg-opacity-10 w-full flex-grow"
            >
                <font-awesome-icon icon="image" size="6x" class="opacity-50" />
                <span class="text text-sm text-base-content text-opacity-30">
                    Preview Not Yet Generated
                </span>
            </div>
        </button>

        <div class="flex flex-row">
            <div class="flex flex-col flex-grow">
                <span>{{ data?.name }}</span>
                <span>
                    {{ new Date(data?.updated_at).toLocaleDateString() }}
                </span>
            </div>
            <crud-actions
                :actions="actions"
                :data="data"
                :base-route="baseRoute"
                :has-preview-component="hasPreviewComponent"
                :model-name="modelName"
                :model-key="modelKey"
            />
        </div>
    </div>
</template>
<script setup>
import CrudActions from "@/Components/CRUD/CrudActions.vue";

import { faImage } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { getActions } from "@/Components/CRUD/helpers/actions";

import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faChevronDoubleLeft,
    faEllipsisH,
} from "@fortawesome/pro-regular-svg-icons";

library.add(faChevronDoubleLeft, faEllipsisH, faImage);

const props = defineProps({
    template: {
        type: Array,
    },
    data: {
        type: Object,
    },
    fields: {
        type: Array,
    },
    update: {
        type: Boolean,
        default: true,
    },
    delete: {
        type: Boolean,
        default: true,
    },
    baseRoute: {
        type: String,
    },
    modelName: {
        type: String,
        default: "Record",
    },
    modelKey: {
        type: String,
        required: true,
    },
    modelNamePlural: {
        type: String,
        default: "Records",
    },
    titleField: {
        type: String,
        default: "Record",
    },
    actions: {
        type: [Object, Boolean],
        default: {},
    },
    hasPreviewComponent: {
        type: Boolean,
    },
});

const actions = getActions(props);
</script>
