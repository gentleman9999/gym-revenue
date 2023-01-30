<template>
    <div class="min-w-[12rem] mx-1 relative inline-block" :id="temp_id">
        <template v-if="template_type === 'email'">
            <p
                class="max-w-[10rem] overflow-hidden text-ellipsis whitespace-nowrap"
            >
                {{ template_item.name }}
            </p>

            <div
                :style="`background-image: url(${thumbsrc})`"
                class="h-48 w-40 bg-white border-secondary border relative bg-no-repeat bg-contain bg-center"
            >
                <button
                    @click="$emit('submit', temp_id)"
                    class="bg-secondary bg-opacity-50 absolute top-0 left-0 h-full w-full opacity-0 hover:opacity-100 transition-all duration-300"
                    :class="{ 'opacity-70': selected }"
                >
                    <div
                        :id="thumbId"
                        class="text-base-content h-14 w-14 flex justify-center items-center mx-auto border-2 border-base-content rounded-full"
                    >
                        <font-awesome-icon icon="check" />
                    </div>
                </button>
            </div>
        </template>

        <template v-if="template_type === 'sms' || template_type === 'call'">
            <button
                class="bg-base-content bg-opacity-0 hover:bg-opacity-20 relative rounded-md py-8 px-4 transition-all duration-300 w-full"
                :class="{ '!bg-secondary !bg-opacity-50': selected }"
                @click="$emit('submit', temp_id)"
            >
                <div
                    v-if="selected"
                    :id="thumbId"
                    class="text-base-content h-14 w-14 flex justify-center items-center mx-auto border-2 border-base-content rounded-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
                >
                    <font-awesome-icon icon="check" />
                </div>
                <div
                    class="max-w-[10rem] max-h-20 p-4 mx-auto rounded-lg bg-white bg-opacity-30 relative whitespace-pre"
                >
                    <p
                        v-if="template_type === 'sms'"
                        class="text-xs text-ellipsis overflow-hidden"
                    >
                        {{ template_item?.markup }}
                    </p>
                    <p
                        v-if="template_type === 'call'"
                        class="text-xs text-ellipsis overflow-hidden"
                    >
                        {{ template_item?.script }}
                    </p>
                </div>

                <p
                    class="text-secondary py-1 mt-1 whitespace-nowrap text-ellipsis overflow-hidden"
                >
                    {{ title }}
                </p>
                <p>{{ date }}</p>
            </button>
        </template>

        <div class="my-2">
            <button
                @click="contextMenuOpen = true"
                class="px-2 mx-auto block border border-transparent hover:border-primary hover:bg-neutral rounded-md transition-all"
            >
                <font-awesome-icon icon="ellipsis-h" size="sm" />
            </button>
        </div>

        <template v-if="contextMenuOpen">
            <div
                class="context-menu absolute bottom-4 bg-neutral right-0 rounded-md"
                v-click-outside="contextMenuOpen && closeContextMenu"
            >
                <div class="flex flex-col">
                    <button
                        v-if="currentTemplatePermission.update"
                        @click="$emit('edit', temp_id)"
                        class="ctx-btn rounded-t-md"
                    >
                        Edit
                    </button>
                    <!-- <button @click="$emit('rename', temp_id)" class="ctx-btn">
                        Rename
                    </button> -->
                    <button
                        v-if="currentTemplatePermission.trash"
                        @click="$emit('trash', temp_id)"
                        class="ctx-btn rounded-b-md"
                    >
                        Trash
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>

<style>
.ctx-btn {
    @apply px-8 py-2;
    @apply hover:bg-neutral-content hover:bg-opacity-25;
}
</style>

<script setup>
import { ref, computed } from "vue";
import { faCheck, faEllipsisH } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";

library.add([faCheck, faEllipsisH]);

const props = defineProps({
    title: {
        type: String,
        defaut: "Default Title",
    },
    date: {
        type: [Date, String],
        default: "Feb. 06, 2021",
    },
    temp_id: {
        type: String,
        default: "0",
    },
    selected: {
        type: Boolean,
        default: false,
    },
    thumbsrc: {
        type: String,
        default: "",
    },
    template_type: {
        type: [String, undefined],
        default: undefined,
    },
    template_item: {
        type: Object,
        default: {},
    },
    permissions: {
        type: Object,
        default: {},
    },
});

const thumbId = ref(`thumb-${props.temp_id}`);
const contextMenuOpen = ref(false);

const closeContextMenu = () => {
    contextMenuOpen.value = false;
};

const currentTemplatePermission = computed(() => {
    return props.permissions[props.template_type];
});
</script>
