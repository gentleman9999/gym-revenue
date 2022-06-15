<template>
    <daisy-modal ref="modal" class="rounded-lg max-w-fit">
        <h1 class="text text-secondary text-2xl pb-2">{{ title }}</h1>
        <slot />
        <div class="dashboard-modal-action bottom-[-25px]">
            <inertia-link
                class="btn btn-secondary mt-8 btn-sm rounded"
                :href="route('reports.page', pageRoute)"
                v-if="pageRoute"
                >View More</inertia-link
            >
        </div>
    </daisy-modal>
</template>
<style scoped>
.dashboard-modal-action {
    @apply w-full flex justify-end sticky pb-8 bg-base-200 z-20 pr-3;
}
</style>
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
