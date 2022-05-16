<template>
    <div
        class="card card-compact bg-base-100 shadow-2xl border-secondary border-[1px] rounded"
    >
        <div class="card-body">
            <div class="card-title flex flex-row items-center">
                <h1 class="flex-grow">{{ title }}</h1>
                <div class="flex flex-row justify-self-end">
                    <button
                        class="btn btn-sm btn-ghost transition transition-opacity transition-colors"
                        :class="{
                            'opacity-30': !isFavorited,
                            'text-secondary': isFavorited,
                        }"
                        @click="handleClickFavorited"
                    >
                        <font-awesome-icon icon="star" size="lg" />
                    </button>
                    <button
                        class="btn btn-sm btn-ghost"
                        @click="toggleCollapsed"
                    >
                        <font-awesome-icon
                            icon="caret-down"
                            size="lg"
                            class="transform transition transition-transform"
                            :class="{
                                'rotate-180': isCollapsed,
                                'rotate-0': !isCollapsed,
                            }"
                        />
                    </button>
                </div>
            </div>
            <slot />
            <div class="card-actions justify-end">
                <slot name="actions" />
                <button
                    class="btn btn-secondary"
                    @click="handleClickGo"
                    v-if="showGoBtn"
                >
                    Go
                </button>
            </div>
        </div>
    </div>
    <component :is="modalComponent" v-if="modalComponent" ref="modal" />
</template>

<script>
import { ref, computed } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faCaretDown, faStar } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faStar, faCaretDown);

export default {
    components: {
        FontAwesomeIcon,
    },
    props: {
        title: {
            type: String,
            required: true,
        },
        isFavorited: {
            type: Boolean,
            default: false,
        },
        modalComponent: {
            type: Object,
        },
        onClickGo: {
            type: Function,
        },
    },
    setup(props, context) {
        //fake a server call to mark as favorited
        const isFavorited = ref(props.isFavorited);
        const handleClickFavorited = () =>
            (isFavorited.value = !isFavorited.value);

        const modal = ref();

        const handleClickGo = () => {
            if (props.onClickGo) {
                props.onClickGo();
                return;
            }
            if (modal.value) {
                console.log({ modal: modal.value });
                modal.value.open();
            }
        };

        const showGoBtn = computed(
            () => props.modalComponent || props.onClickGo
        );

        const isCollapsed = ref(false);

        const toggleCollapsed = () => (isCollapsed.value = !isCollapsed.value);

        return {
            handleClickFavorited,
            isFavorited,
            modal,
            handleClickGo,
            showGoBtn,
            isCollapsed,
            toggleCollapsed,
        };
    },
};
</script>

<style scoped></style>
