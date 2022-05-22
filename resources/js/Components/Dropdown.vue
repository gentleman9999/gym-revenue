<template>
    <div class="dropdown" :class="alignmentClasses">
        <div class="h-full" tabindex="0">
            <slot name="trigger"></slot>
        </div>
        <div
            class="dropdown-content mt-2 shadow rounded-box"
            :class="[widthClass, contentClasses]"
            tabindex="0"
        >
            <slot name="content"></slot>
        </div>
    </div>
</template>

<script>
import { defineComponent, onMounted, onUnmounted, ref } from "vue";

export default defineComponent({
    props: {
        align: {
            default: "start",
        },
        width: {
            default: "48",
        },
        contentClasses: {
            default: () => ["py-1", "bg-secondary"],
        },
    },

    setup() {
        const closeOnEscape = (e) => {
            // document.activeElement.blur();
        };

        onMounted(() => document.addEventListener("keydown", closeOnEscape));
        onUnmounted(() =>
            document.removeEventListener("keydown", closeOnEscape)
        );

        return {};
    },

    computed: {
        widthClass() {
            return {
                48: "w-48",
            }[this.width.toString()];
        },

        alignmentClasses() {
            if (this.align === "left") {
                return "dropdown-left";
            } else if (this.align === "right") {
                return "dropdown-right";
            } else if (this.align === "start") {
                return "dropdown-start";
            } else if (this.align === "end") {
                return "dropdown-end";
            }
        },
    },
});
</script>
