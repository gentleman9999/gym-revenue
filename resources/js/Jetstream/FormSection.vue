<template>
    <div class="">
        <jet-section-title v-if="$slots?.title || $slots?.description">
            <template #title>
                <slot name="title"></slot>
                <button
                    v-if="collapsable"
                    type="button"
                    @click="toggleCollapsed"
                    class="px-4"
                >
                    <font-awesome-icon
                        icon="plus-square"
                        size="1x"
                        class="opacity-50 hover:opacity-100 transition-opacity"
                        v-if="isCollapsed"
                    />
                    <font-awesome-icon
                        icon="minus-square"
                        size="1x"
                        class="opacity-50 hover:opacity-100 transition-opacity"
                        v-else
                    />
                </button>
            </template>
            <template #description>
                <slot name="description"></slot>
            </template>
        </jet-section-title>

        <div class="mt-5 md:mt-0 md:col-span-2" v-if="!isCollapsed">
            <form
                @submit.prevent="$emit('submitted')"
                class="shadow border border-secondary border-1 rounded"
                :class="hasActions ? '' : 'sm:rounded-md'"
            >
                <div
                    class="px-4 py-5 sm:p-6"
                    :class="hasActions ? '' : 'sm:rounded-md'"
                >
                    <div class="grid grid-cols-6 gap-6">
                        <slot name="form"></slot>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center px-4 py-3 bg-base-200 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md gap-4"
                    v-if="hasActions"
                >
                    <slot name="actions" @close="$emit('close')"></slot>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { defineComponent, ref } from "vue";
import JetSectionTitle from "./SectionTitle.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faMinusSquare, faPlusSquare } from "@fortawesome/pro-solid-svg-icons";

library.add(faMinusSquare, faPlusSquare);

export default defineComponent({
    emits: ["submitted"],

    components: {
        JetSectionTitle,
        FontAwesomeIcon,
    },

    computed: {
        hasActions() {
            return !!this.$slots.actions;
        },
    },
    props: {
        collapsable: {
            type: Boolean,
            default: false,
        },
        initiallyCollapsed: {
            type: Boolean,
            default: false,
        },
    },
    setup(props) {
        const isCollapsed = ref(props.initiallyCollapsed);
        const toggleCollapsed = () => {
            isCollapsed.value = !isCollapsed.value;
        };
        return { toggleCollapsed, isCollapsed };
    },
});
</script>
