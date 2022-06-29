<template>
    <daisy-modal id="confirmModal" ref="confirmModal" :closable="false">
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
                :disabled="disabled"
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
        disabled: {
            type: Boolean,
            required: false,
        },
    },
    setup(props, { emit }) {
        const confirmModal = ref(null);

        const handleModalClosed = () => {
            emit("cancel");
            console.log("CLOSE ME!");
        };

        const handleClickCancel = () => {
            confirmModal.value?.close();
            emit("cancel");
        };

        const handleClickConfirm = () => {
            confirmModal.value.close();
            emit("confirm");
        };

        onMounted(() => {
            confirmModal.value.open();
        });

        return {
            confirmModal,
            emit,
            handleClickCancel,
            handleClickConfirm,
            handleModalClosed,
        };
    },
    emits: ["confirm"],
});
</script>
