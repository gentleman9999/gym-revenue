<template>
    <section class="m-auto max-w-5xl mb-16">
        <div
            class="bg-secondary border-secondary border py-2 rounded-t-md max-w-full"
            :class="{ 'rounded-b-md': !extended }"
        >
            <div class="flex justify-between items-center mx-4">
                <span
                    v-if="icon !== undefined && icon !== null"
                    class="inline-block fill-base-content"
                >
                    <component :is="icon" />
                </span>
                <h2 class="text-xl font-bold text-center capitalize">
                    {{ type }}
                    {{ type === "email" ? "templates" : "scripts" }}
                </h2>
                <button
                    @click="toggleContainer"
                    class="fill-white p-2 opacity-75 hover:opacity-100 active:scale-90 active:opacity-50 bg-black bg-opacity-0 hover:bg-opacity-10 rounded-full transition-all"
                >
                    <Plus height="24" width="24" v-if="!extended" />
                    <Minus height="24" width="24" v-if="extended" />
                </button>
            </div>
        </div>

        <div
            v-if="extended"
            class="bg-black border-x border-secondary p-2 px-4 max-w-full"
        >
            <h3 class="pt-2 pb-4 ml-8 text-lg">Current</h3>

            <div
                class="grid gap-4 min-h-[12rem]"
                :class="{
                    'grid-cols-[1fr]': !permissions.create,
                    'grid-cols-[0.75fr_0.25fr]': permissions.create,
                }"
            >
                <div
                    class="p-4 border row-start-1 relative border-secondary bg-white bg-opacity-10 rounded-md w-full block max-w-full overflow-hidden"
                >
                    <!-- Scroll Right Overlay -->
                    <OverlayScrollButton
                        v-if="visibleWidth + 2 <= containerWidth"
                        :handleClick="
                            () => updateScrollPosition(visibleWidth, 0)
                        "
                        :idleOpacity="rightOpac"
                        :idleWidth="rightWidth"
                        :through="scrollProgress"
                        side="right"
                        class="group"
                    >
                        <ChevRight
                            class="fill-white group-hover:scale-125 scale-50 jumpy-curve"
                            height="36px"
                            width="100%"
                        />
                    </OverlayScrollButton>

                    <!-- Scroll Left Overlay -->
                    <OverlayScrollButton
                        v-if="visibleWidth + 2 <= containerWidth"
                        :handleClick="
                            () =>
                                updateScrollPosition(-Math.abs(visibleWidth), 0)
                        "
                        :idleOpacity="leftOpac"
                        :idleWidth="leftWidth"
                        :through="scrollProgress"
                        side="left"
                        class="group"
                    >
                        <ChevRight
                            class="fill-white group-hover:scale-125 scale-50 rotate-180 jumpy-curve"
                            height="36px"
                            width="100%"
                        />
                    </OverlayScrollButton>

                    <div>
                        <ul
                            @scroll="updateScrollPosition"
                            ref="displayContainer"
                            class="flex items-center overflow-x-auto scroll-smooth"
                        >
                            <slot name="current_templates">
                                <li>No templates created</li>
                            </slot>
                        </ul>
                    </div>
                </div>
                <div
                    v-if="permissions.create"
                    class="min-w-[10rem] row-start-1"
                >
                    <button
                        @click="handleCreateTemplate"
                        class="group h-full w-full bg-primary-content bg-opacity-0 hover:bg-opacity-10 active:bg-opacity-5 active:scale-90 rounded-md border border-secondary transition-all"
                    >
                        <div
                            class="border-2 m-auto jumpy-curve scale-50 -translate-y-16 rotate-45 group-hover:rotate-0 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 group-active:border-secondary-600 group-hover:scale-110 border-secondary rounded-full h-20 w-20 flex justify-center items-center"
                        >
                            <span class="text-7xl pb-1">+</span>
                        </div>
                        <p
                            class="mt-2 -translate-y-14 inline-block group-hover:translate-y-2 scale-100 group-hover:scale-125 jumpy-curve"
                        >
                            Create New
                        </p>
                    </button>
                </div>
            </div>
        </div>
        <div
            v-if="extended"
            class="bg-black border-x border-b rounded-b-md border-secondary p-2 px-4 max-w-full"
        >
            <div class="pt-2 pb-4 ml-8 flex items-center">
                <h3 class="text-lg">Trashed</h3>
                <button
                    @click="toggleTrashed"
                    class="fill-white ml-2 p-2 opacity-75 hover:opacity-100 active:scale-90 active:opacity-50 bg-white bg-opacity-0 hover:bg-opacity-10 rounded-full transition-all"
                >
                    <Plus height="16" width="16" v-if="!trashExtended" />
                    <Minus height="16" width="16" v-if="trashExtended" />
                </button>
            </div>

            <div
                v-if="trashExtended"
                class="p-4 border mb-4 border-secondary bg-white bg-opacity-10 rounded-md"
            >
                <ul class="flex items-center overflow-x-auto scroll-smooth">
                    <slot name="trashed_templates">
                        <li>No expiring templates</li>
                    </slot>
                </ul>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { range, invlerp } from "../components/helpers";

import MailSvg from "./svg/MailSvg.vue";
import PhoneSvg from "./svg/PhoneSvg.vue";
import SmsSvg from "./svg/SmsSvg.vue";
import ChevRight from "./svg/ChevRight.vue";
import OverlayScrollButton from "./OverlayScrollButton.vue";
import Plus from "./svg/Plus.vue";
import Minus from "./svg/Minus.vue";

const emit = defineEmits(["create"]);

const props = defineProps({
    type: {
        type: String,
        default: "email",
    },
    item: {
        type: [Object, String, Boolean],
        default: {},
    },
    permissions: {
        type: Object,
        default: {},
    },
});

const displayContainer = ref(null);
const extended = ref(true);
const trashExtended = ref(false);
const scrollPosition = ref(0);
const containerWidth = ref(200);
const visibleWidth = ref(100);

/** percent progressed through the container */
const scrollProgress = computed(() => {
    let max = containerWidth.value - visibleWidth.value;
    return invlerp(0, max, scrollPosition.value) * 100;
});

/** width props */
const rightOverlayWidth = computed(() => {
    return range(0, 100, 4, 2, scrollProgress.value).toFixed(2);
});

const leftOverlayWidth = computed(() => {
    return range(0, 100, 2, 4, scrollProgress.value).toFixed(2);
});

/** opacity props */
const rightOverlayOpacity = computed(() => {
    if (scrollProgress.value < 80) return 0.4;
    return range(60, 100, 0.5, 0.2, scrollProgress.value);
});

const leftOverlayOpacity = computed(() => {
    if (scrollProgress > 20) return 0.4;
    return range(40, 0, 0.5, 0.2, scrollProgress.value);
});

/**
 * props for overlay buttons for scrolling */
const rightOpac = ref(0.4);
const rightWidth = ref("2.25rem");
const leftOpac = ref(0.4);
const leftWidth = ref("2.25rem");

watch([scrollPosition], () => {
    rightOpac.value = rightOverlayOpacity.value;
    rightWidth.value = `${rightOverlayWidth.value}rem`;

    leftOpac.value = leftOverlayOpacity.value;
    leftWidth.value = `${leftOverlayWidth.value}rem`;
});

const icon = computed(() => {
    let componentToUse = null;

    switch (props.type) {
        case "email":
            componentToUse = MailSvg;
            break;
        case "call":
            componentToUse = PhoneSvg;
            break;
        case "sms":
            componentToUse = SmsSvg;
            break;
        default:
            componentToUse = null;
            break;
    }

    return componentToUse;
});

const handleCreateTemplate = () => {
    emit("create", props.type);
};

const updateScrollPosition = (x, y) => {
    if (typeof x === "number") {
        displayContainer.value.scrollBy(x, y);
    }
    scrollPosition.value = displayContainer.value.scrollLeft;
};

const toggleContainer = () => {
    extended.value = !extended.value;
};

const toggleTrashed = () => {
    trashExtended.value = !trashExtended.value;
};

onMounted(() => {
    let container = displayContainer.value;
    containerWidth.value = container.scrollWidth;
    visibleWidth.value = container.clientWidth;
    scrollPosition.value = container?.scrollLeft;
});
</script>

<style scoped>
.jumpy-curve {
    @apply duration-500 transition-all;
    transition-timing-function: cubic-bezier(0.63, -0.87, 0, 1.7);
}
</style>
