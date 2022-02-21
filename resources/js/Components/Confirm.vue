<template>
    <sweet-modal
        :title="title"
        :width="width"
        overlayTheme="dark"
        modal-theme="dark"
        hideCloseButton
        ref="modal"
    >
        <slot />
        <template #button >
            <slot name="cancelButton">
                <button @click="emit('cancel'); modal.close()" class="btn btn-error hover:text-white">
                    Cancel
                </button>
            </slot>
            <slot name="confirmButton">
                <button @click="emit('confirm'); modal.close()" class="btn btn-success hover:text-white ml-2">
                    Confirm
                </button>
            </slot>
        </template>
    </sweet-modal>
</template>
<script>
import { defineComponent, ref, onMounted } from "vue";
import SweetModal from "@/Components/SweetModal3/SweetModal";
export default defineComponent({
    props: {
        title: {
            type: String,
            required: true,
        },
        width: {
            type: String,
            required: false,
            default: '85%'
        }
    },
    emits: ["confirm"],
    components: { SweetModal },
    setup(props, { emit }) {
        const modal = ref(null);
        onMounted(() => {
            modal.value.open();
        });
        return { modal, emit };
    },
});
</script>
