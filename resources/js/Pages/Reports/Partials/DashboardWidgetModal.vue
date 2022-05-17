<template>
    <daisy-modal ref="modal">
        <h1 class="text text-secondary text-2xl">{{ title }}</h1>
        <slot />
        <inertia-link
            class="btn btn-secondary mt-8"
            :href="route('reports.page', pageRoute)"
            v-if="pageRoute"
            >View More</inertia-link
        >
    </daisy-modal>
</template>

<script>
import { ref, defineComponent } from "vue";
import DaisyModal from "@/Components/DaisyModal";

export default defineComponent({
    props: {
        title: {
            type: String,
            required: true,
        },
        pageRoute: {
            type: String,
        },
    },
    components: {
        DaisyModal,
    },
    setup() {
        const modal = ref();

        const open = () => {
            if (modal.value?.open) {
                modal.value?.open();
            }
        };
        const close = () => {
            if (modal.value?.close) {
                modal.value?.close();
            }
        };

        const handleClickViewMore = () => {};

        return { open, close, modal };
    },
});
</script>
