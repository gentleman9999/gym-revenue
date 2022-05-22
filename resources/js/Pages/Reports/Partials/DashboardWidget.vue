<template>
    <div
        class="card card-compact bg-base-100 shadow-2xl border-secondary border-[1px] rounded max-h-[300px]"
    >
        <div class="card-body h-full">
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
            <template v-if="amount">
                <div class="text-secondary text-4xl font-bold">
                    {{ amountPretty }}
                </div>
                <div class="divider opacity-30" />
            </template>
            <div class="flex-grow flex-shrink overflow-auto">
                <slot />
            </div>
            <div class="card-actions justify-end !mt-1">
                <slot name="actions" />
                <button
                    class="btn btn-secondary btn-sm"
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
        amount: {
            type: Number,
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

        const formatter = new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",

            // These options are needed to round to whole numbers if that's what you want.
            //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
            maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
        });

        const amountPretty = computed(() => formatter.format(props.amount));

        return {
            handleClickFavorited,
            isFavorited,
            modal,
            handleClickGo,
            showGoBtn,
            isCollapsed,
            toggleCollapsed,
            amountPretty,
        };
    },
};
</script>

<style scoped></style>
