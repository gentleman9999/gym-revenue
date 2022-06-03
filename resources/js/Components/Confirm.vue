<template>
    <daisy-modal id="confirmModal" ref="confirmModal">
        <slot />

        <div class="flex w-full justify-between mt-8">
            <button
                @click="handleClickCancel"
                class="btn btn-error hover:text-white"
            >
                Cancel
            </button>

            <button
                @click="handleClickConfirm"
                class="btn btn-success hover:text-white ml-2"
            >
                Confirm
            </button>
        </div>
    </daisy-modal>
</template>
<script>
import { defineComponent, ref, onMounted } from "vue";
import DaisyModal from "@/Components/DaisyModal";

export default defineComponent({
    components: { DaisyModal },
    props: {
        title: {
            type: String,
            required: true,
        },
        width: {
            type: String,
            required: false,
            default: "85%",
        },
    },
    setup(props, { emit }) {
        const confirmModal = ref(null);

        const handleClickCancel = () => {
            confirmModal.value.close();
            emit("cancel");
        };

        const handleClickConfirm = () => {
            confirmModal.value.close();
            emit("confirm");
        };

        onMounted(() => {
            confirmModal.value.open();
        });

        return { confirmModal, emit, handleClickCancel, handleClickConfirm };
    },
    emits: ["confirm"],
});
</script>
