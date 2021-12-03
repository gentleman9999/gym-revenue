<template>
    <sweet-modal
        :title="title"
        width="85%"
        overlayTheme="dark"
        modal-theme="dark"
        hideCloseButton
        ref="modal"
    >
        <slot />
        <template #button >
            <slot name="confirmButton">
                <button @click="emit('confirm'); modal.close()" class="btn btn-success">
                    Confirm
                </button>
            </slot>
            <slot name="cancelButton">
                <button @click="emit('cancel'); modal.close()" class="btn btn-error">
                    Cancel
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
